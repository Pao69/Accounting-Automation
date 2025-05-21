<?php

class LedgerController extends Controller {
    public function index() {
        $accountId = isset($_GET['account_id']) ? (int)$_GET['account_id'] : null;
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
        
        try {
            // Get all accounts for the dropdown, grouped by category
            $sql = "SELECT a.*, at.name as type_name, at.category,
                    COALESCE(
                        (SELECT SUM(CASE 
                            WHEN ji.debit > 0 THEN ji.debit 
                            ELSE -ji.credit 
                        END)
                        FROM journal_items ji
                        JOIN journal_entries je ON ji.journal_id = je.id
                        WHERE ji.account_id = a.id
                        AND je.status = 'Posted'
                        ) + a.balance, a.balance
                    ) as current_balance
                    FROM accounts a 
                    JOIN account_types at ON a.type_id = at.id 
                    ORDER BY at.category, a.account_code";
            
            $result = $this->db->query($sql);
            $accounts = [];
            while ($row = $result->fetch_assoc()) {
                $accounts[] = $row;
            }
            
            $data = [
                'accounts' => $accounts,
                'start_date' => $startDate,
                'end_date' => $endDate
            ];
            
            // If an account is selected, get its transactions
            if ($accountId) {
                // Get account details
                $sql = "SELECT a.*, at.name as type_name, at.category,
                        COALESCE(
                            (SELECT SUM(CASE 
                                WHEN ji.debit > 0 THEN ji.debit 
                                ELSE -ji.credit 
                            END)
                            FROM journal_items ji
                            JOIN journal_entries je ON ji.journal_id = je.id
                            WHERE ji.account_id = a.id
                            AND je.status = 'Posted'
                            ) + a.balance, a.balance
                        ) as current_balance
                        FROM accounts a 
                        JOIN account_types at ON a.type_id = at.id 
                        WHERE a.id = $accountId";
                
                $result = $this->db->query($sql);
                $data['selected_account'] = $result->fetch_assoc();
                
                // Get opening balance
                $sql = "SELECT 
                        COALESCE(
                            (SELECT balance 
                             FROM accounts 
                             WHERE id = $accountId) +
                            COALESCE(SUM(
                                CASE 
                                    WHEN ji.debit > 0 THEN ji.debit 
                                    ELSE -ji.credit 
                                END
                            ), 0),
                            (SELECT balance 
                             FROM accounts 
                             WHERE id = $accountId)
                        ) as opening_balance
                        FROM journal_items ji
                        JOIN journal_entries je ON ji.journal_id = je.id
                        WHERE ji.account_id = $accountId
                        AND je.date < '" . $this->db->escape($startDate) . "'
                        AND je.status = 'Posted'";
                
                $result = $this->db->query($sql);
                $data['opening_balance'] = $result->fetch_assoc()['opening_balance'];
                
                // Get transactions
                $sql = "SELECT 
                        je.id as journal_id,
                        je.date,
                        je.reference_no,
                        je.description,
                        ji.debit,
                        ji.credit,
                        je.status,
                        @running_balance := @running_balance + (CASE 
                            WHEN ji.debit > 0 THEN ji.debit 
                            ELSE -ji.credit 
                        END) as running_balance
                        FROM journal_items ji
                        JOIN journal_entries je ON ji.journal_id = je.id
                        CROSS JOIN (SELECT @running_balance := " . $data['opening_balance'] . ") as vars
                        WHERE ji.account_id = $accountId
                        AND je.date BETWEEN '" . $this->db->escape($startDate) . "' 
                        AND '" . $this->db->escape($endDate) . "'
                        ORDER BY je.date, je.id";
                
                $result = $this->db->query($sql);
                $data['transactions'] = [];
                while ($row = $result->fetch_assoc()) {
                    $data['transactions'][] = $row;
                }
                
                // Calculate totals
                $data['total_debit'] = array_sum(array_column($data['transactions'], 'debit'));
                $data['total_credit'] = array_sum(array_column($data['transactions'], 'credit'));
                $data['ending_balance'] = $data['opening_balance'] + $data['total_debit'] - $data['total_credit'];
            }
            
            $this->view('ledger/index', $data);
            
        } catch (Exception $e) {
            $this->view('ledger/index', ['error' => $e->getMessage()]);
        }
    }
    
    public function export() {
        // TODO: Implement Excel export functionality
    }
} 