<?php

class FinancialStatementsController extends Controller {
    public function index() {
        $this->view('financial-statements/index');
    }

    public function incomeStatement() {
        $data = [];
        $period = isset($_GET['period']) ? $_GET['period'] : date('Y-m');
        
        try {
            // Get start and end dates
            $startDate = $period . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
            
            // Get revenues
            $sql = "SELECT a.account_code, a.name, 
                    COALESCE(SUM(ji.credit - ji.debit), 0) as amount
                    FROM accounts a
                    LEFT JOIN journal_items ji ON a.id = ji.account_id
                    LEFT JOIN journal_entries je ON ji.journal_id = je.id
                    JOIN account_types at ON a.type_id = at.id
                    WHERE at.category = 'Revenue'
                    AND (je.date IS NULL OR (je.date BETWEEN '" . $this->db->escape($startDate) . "' 
                    AND '" . $this->db->escape($endDate) . "' AND je.status = 'Posted'))
                    GROUP BY a.id, a.account_code, a.name
                    ORDER BY a.account_code";
            
            $result = $this->db->query($sql);
            $data['revenues'] = [];
            while ($row = $result->fetch_assoc()) {
                $data['revenues'][] = $row;
            }
            
            // Get expenses
            $sql = "SELECT a.account_code, a.name, 
                    COALESCE(SUM(ji.debit - ji.credit), 0) as amount
                    FROM accounts a
                    LEFT JOIN journal_items ji ON a.id = ji.account_id
                    LEFT JOIN journal_entries je ON ji.journal_id = je.id
                    JOIN account_types at ON a.type_id = at.id
                    WHERE at.category = 'Expense'
                    AND (je.date IS NULL OR (je.date BETWEEN '" . $this->db->escape($startDate) . "' 
                    AND '" . $this->db->escape($endDate) . "' AND je.status = 'Posted'))
                    GROUP BY a.id, a.account_code, a.name
                    ORDER BY a.account_code";
            
            $result = $this->db->query($sql);
            $data['expenses'] = [];
            while ($row = $result->fetch_assoc()) {
                $data['expenses'][] = $row;
            }
            
            // Calculate totals
            $data['total_revenue'] = array_sum(array_column($data['revenues'], 'amount'));
            $data['total_expenses'] = array_sum(array_column($data['expenses'], 'amount'));
            $data['net_income'] = $data['total_revenue'] - $data['total_expenses'];
            
            // Add period information
            $data['period'] = [
                'start' => $startDate,
                'end' => $endDate,
                'formatted' => date('F Y', strtotime($startDate))
            ];
            
        } catch (Exception $e) {
            $data['error'] = "Database error: " . $e->getMessage();
        }
        
        $this->view('financial-statements/income', $data);
    }

    public function equityStatement() {
        $data = [];
        $period = isset($_GET['period']) ? $_GET['period'] : date('Y-m');
        
        try {
            // Get beginning balance of Owner's Equity
            $startDate = $period . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));
            
            $sql = "SELECT SUM(CASE 
                        WHEN a.type_id IN (SELECT id FROM account_types WHERE category = 'Equity')
                        THEN a.balance 
                        ELSE 0 
                    END) as beginning_equity
                    FROM accounts a
                    WHERE a.created_at < '" . $this->db->escape($startDate) . "'";
            
            $result = $this->db->query($sql);
            $data['beginning_balance'] = $result->fetch_assoc()['beginning_equity'] ?? 0;

            // Get net income/loss for the period
            $sql = "SELECT 
                    (SELECT COALESCE(SUM(ji.credit - ji.debit), 0)
                     FROM journal_items ji
                     JOIN accounts a ON ji.account_id = a.id
                     JOIN account_types at ON a.type_id = at.id
                     JOIN journal_entries je ON ji.journal_id = je.id
                     WHERE at.category = 'Revenue'
                     AND je.date BETWEEN '" . $this->db->escape($startDate) . "' AND '" . $this->db->escape($endDate) . "'
                     AND je.status = 'Posted') as total_revenue,
                    (SELECT COALESCE(SUM(ji.debit - ji.credit), 0)
                     FROM journal_items ji
                     JOIN accounts a ON ji.account_id = a.id
                     JOIN account_types at ON a.type_id = at.id
                     JOIN journal_entries je ON ji.journal_id = je.id
                     WHERE at.category = 'Expense'
                     AND je.date BETWEEN '" . $this->db->escape($startDate) . "' AND '" . $this->db->escape($endDate) . "'
                     AND je.status = 'Posted') as total_expenses";
            
            $result = $this->db->query($sql);
            $result = $result->fetch_assoc();
            
            $data['net_income'] = $result['total_revenue'] - $result['total_expenses'];

            // Get owner's withdrawals/investments
            $sql = "SELECT 
                    COALESCE(SUM(CASE 
                        WHEN ji.debit > 0 THEN -ji.debit
                        ELSE ji.credit
                    END), 0) as net_owner_transactions
                    FROM journal_items ji
                    JOIN accounts a ON ji.account_id = a.id
                    JOIN account_types at ON a.type_id = at.id
                    JOIN journal_entries je ON ji.journal_id = je.id
                    WHERE at.category = 'Equity'
                    AND je.date BETWEEN '" . $this->db->escape($startDate) . "' AND '" . $this->db->escape($endDate) . "'
                    AND je.status = 'Posted'
                    AND a.name NOT LIKE '%Retained Earnings%'";
            
            $result = $this->db->query($sql);
            $data['owner_transactions'] = $result->fetch_assoc()['net_owner_transactions'];

            // Calculate ending balance
            $data['ending_balance'] = $data['beginning_balance'] + $data['net_income'] + $data['owner_transactions'];
            
            // Add period information
            $data['period'] = [
                'start' => $startDate,
                'end' => $endDate,
                'formatted' => date('F Y', strtotime($startDate))
            ];

        } catch (Exception $e) {
            $data['error'] = "Database error: " . $e->getMessage();
        }

        $this->view('financial-statements/equity', $data);
    }

    public function balanceSheet() {
        $data = [];
        $asOfDate = isset($_GET['as_of']) ? $_GET['as_of'] : date('Y-m-d');
        
        try {
            // Get assets
            $sql = "SELECT a.account_code, a.name, a.balance
                    FROM accounts a
                    JOIN account_types at ON a.type_id = at.id
                    WHERE at.category = 'Asset'
                    ORDER BY a.account_code";
            
            $result = $this->db->query($sql);
            $data['assets'] = [];
            while ($row = $result->fetch_assoc()) {
                $data['assets'][] = $row;
            }
            
            // Get liabilities
            $sql = "SELECT a.account_code, a.name, a.balance
                    FROM accounts a
                    JOIN account_types at ON a.type_id = at.id
                    WHERE at.category = 'Liability'
                    ORDER BY a.account_code";
            
            $result = $this->db->query($sql);
            $data['liabilities'] = [];
            while ($row = $result->fetch_assoc()) {
                $data['liabilities'][] = $row;
            }
            
            // Get equity accounts
            $sql = "SELECT a.account_code, a.name, a.balance
                    FROM accounts a
                    JOIN account_types at ON a.type_id = at.id
                    WHERE at.category = 'Equity'
                    ORDER BY a.account_code";
            
            $result = $this->db->query($sql);
            $data['equity'] = [];
            while ($row = $result->fetch_assoc()) {
                $data['equity'][] = $row;
            }
            
            // Calculate totals
            $data['total_assets'] = array_sum(array_column($data['assets'], 'balance'));
            $data['total_liabilities'] = array_sum(array_column($data['liabilities'], 'balance'));
            $data['total_equity'] = array_sum(array_column($data['equity'], 'balance'));
            
            // Add date information
            $data['as_of_date'] = date('F d, Y', strtotime($asOfDate));
            
        } catch (Exception $e) {
            $data['error'] = "Database error: " . $e->getMessage();
        }
        
        $this->view('financial-statements/balance', $data);
    }
} 