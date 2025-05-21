<?php

class ClosingController extends Controller {
    public function index() {
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        try {
            // Get revenue accounts
            $sql = "SELECT a.*, at.name as type_name,
                    COALESCE(SUM(ji.credit - ji.debit), 0) as balance
                    FROM accounts a
                    JOIN account_types at ON a.type_id = at.id
                    LEFT JOIN journal_items ji ON a.id = ji.account_id
                    LEFT JOIN journal_entries je ON ji.journal_id = je.id
                    WHERE at.category = 'Revenue'
                    AND (je.date IS NULL OR (YEAR(je.date) = $year AND je.status = 'Posted'))
                    GROUP BY a.id, a.account_code, a.name
                    HAVING balance <> 0
                    ORDER BY a.account_code";
            
            $result = $this->db->query($sql);
            $data['revenues'] = [];
            while ($row = $result->fetch_assoc()) {
                $data['revenues'][] = $row;
            }
            
            // Get expense accounts
            $sql = "SELECT a.*, at.name as type_name,
                    COALESCE(SUM(ji.debit - ji.credit), 0) as balance
                    FROM accounts a
                    JOIN account_types at ON a.type_id = at.id
                    LEFT JOIN journal_items ji ON a.id = ji.account_id
                    LEFT JOIN journal_entries je ON ji.journal_id = je.id
                    WHERE at.category = 'Expense'
                    AND (je.date IS NULL OR (YEAR(je.date) = $year AND je.status = 'Posted'))
                    GROUP BY a.id, a.account_code, a.name
                    HAVING balance <> 0
                    ORDER BY a.account_code";
            
            $result = $this->db->query($sql);
            $data['expenses'] = [];
            while ($row = $result->fetch_assoc()) {
                $data['expenses'][] = $row;
            }
            
            // Get retained earnings account
            $sql = "SELECT a.* 
                    FROM accounts a 
                    JOIN account_types at ON a.type_id = at.id
                    WHERE at.category = 'Equity'
                    AND a.name LIKE '%Retained Earnings%'
                    LIMIT 1";
            
            $result = $this->db->query($sql);
            $data['retained_earnings'] = $result->fetch_assoc();
            
            // Calculate totals
            $data['total_revenue'] = array_sum(array_column($data['revenues'], 'balance'));
            $data['total_expenses'] = array_sum(array_column($data['expenses'], 'balance'));
            $data['net_income'] = $data['total_revenue'] - $data['total_expenses'];
            
            // Add year information
            $data['year'] = $year;
            $data['available_years'] = $this->getAvailableYears();
            
            $this->view('closing/index', $data);
            
        } catch (Exception $e) {
            $this->view('closing/index', ['error' => $e->getMessage()]);
        }
    }
    
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/closing');
            return;
        }
        
        $year = (int)$_POST['year'];
        $retainedEarningsId = (int)$_POST['retained_earnings_id'];
        
        try {
            $this->db->beginTransaction();
            
            // Create closing entry for revenues
            $date = $year . '-12-31';
            $reference = 'CLOSE-REV-' . $year;
            $description = 'Closing entry for revenues - ' . $year;
            
            $sql = "INSERT INTO journal_entries (date, reference_no, description, status) 
                    VALUES ('$date', '$reference', '$description', 'Posted')";
            $this->db->query($sql);
            $revenueJournalId = $this->db->getLastInsertId();
            
            // Close revenue accounts
            $sql = "INSERT INTO journal_items (journal_id, account_id, debit, credit)
                    SELECT $revenueJournalId, a.id,
                           COALESCE(SUM(ji.credit - ji.debit), 0), 0
                    FROM accounts a
                    JOIN account_types at ON a.type_id = at.id
                    LEFT JOIN journal_items ji ON a.id = ji.account_id
                    LEFT JOIN journal_entries je ON ji.journal_id = je.id
                    WHERE at.category = 'Revenue'
                    AND (je.date IS NULL OR (YEAR(je.date) = $year AND je.status = 'Posted'))
                    GROUP BY a.id
                    HAVING COALESCE(SUM(ji.credit - ji.debit), 0) <> 0";
            $this->db->query($sql);
            
            // Create closing entry for expenses
            $reference = 'CLOSE-EXP-' . $year;
            $description = 'Closing entry for expenses - ' . $year;
            
            $sql = "INSERT INTO journal_entries (date, reference_no, description, status) 
                    VALUES ('$date', '$reference', '$description', 'Posted')";
            $this->db->query($sql);
            $expenseJournalId = $this->db->getLastInsertId();
            
            // Close expense accounts
            $sql = "INSERT INTO journal_items (journal_id, account_id, debit, credit)
                    SELECT $expenseJournalId, a.id,
                           0, COALESCE(SUM(ji.debit - ji.credit), 0)
                    FROM accounts a
                    JOIN account_types at ON a.type_id = at.id
                    LEFT JOIN journal_items ji ON a.id = ji.account_id
                    LEFT JOIN journal_entries je ON ji.journal_id = je.id
                    WHERE at.category = 'Expense'
                    AND (je.date IS NULL OR (YEAR(je.date) = $year AND je.status = 'Posted'))
                    GROUP BY a.id
                    HAVING COALESCE(SUM(ji.debit - ji.credit), 0) <> 0";
            $this->db->query($sql);
            
            // Calculate net income/loss
            $sql = "SELECT 
                    (SELECT COALESCE(SUM(ji.credit - ji.debit), 0)
                     FROM journal_items ji
                     JOIN accounts a ON ji.account_id = a.id
                     JOIN account_types at ON a.type_id = at.id
                     JOIN journal_entries je ON ji.journal_id = je.id
                     WHERE at.category = 'Revenue'
                     AND YEAR(je.date) = $year
                     AND je.status = 'Posted') -
                    (SELECT COALESCE(SUM(ji.debit - ji.credit), 0)
                     FROM journal_items ji
                     JOIN accounts a ON ji.account_id = a.id
                     JOIN account_types at ON a.type_id = at.id
                     JOIN journal_entries je ON ji.journal_id = je.id
                     WHERE at.category = 'Expense'
                     AND YEAR(je.date) = $year
                     AND je.status = 'Posted') as net_income";
            
            $result = $this->db->query($sql);
            $netIncome = $result->fetch_assoc()['net_income'];
            
            // Create closing entry to retained earnings
            $reference = 'CLOSE-RE-' . $year;
            $description = 'Transfer net income/loss to retained earnings - ' . $year;
            
            $sql = "INSERT INTO journal_entries (date, reference_no, description, status) 
                    VALUES ('$date', '$reference', '$description', 'Posted')";
            $this->db->query($sql);
            $retainedEarningsJournalId = $this->db->getLastInsertId();
            
            // Add retained earnings entry
            if ($netIncome > 0) {
                $sql = "INSERT INTO journal_items (journal_id, account_id, debit, credit)
                        VALUES ($retainedEarningsJournalId, $retainedEarningsId, 0, $netIncome)";
            } else {
                $sql = "INSERT INTO journal_items (journal_id, account_id, debit, credit)
                        VALUES ($retainedEarningsJournalId, $retainedEarningsId, " . abs($netIncome) . ", 0)";
            }
            $this->db->query($sql);
            
            $this->db->commit();
            $_SESSION['success'] = 'Year-end closing entries have been created successfully.';
            
        } catch (Exception $e) {
            $this->db->rollback();
            $_SESSION['error'] = 'Error creating closing entries: ' . $e->getMessage();
        }
        
        $this->redirect('/closing');
    }
    
    private function getAvailableYears() {
        $sql = "SELECT DISTINCT YEAR(date) as year 
                FROM journal_entries 
                WHERE status = 'Posted'
                ORDER BY year DESC";
        
        $result = $this->db->query($sql);
        $years = [];
        while ($row = $result->fetch_assoc()) {
            $years[] = $row['year'];
        }
        return $years;
    }
} 