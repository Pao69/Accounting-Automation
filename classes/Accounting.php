<?php
require_once __DIR__ . '/../config/database.php';

class Accounting {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Create a new journal entry
    public function createJournalEntry($date, $reference, $description, $userId, $entries) {
        try {
            $this->conn->begin_transaction();
            
            // Insert journal entry
            $sql = "INSERT INTO journal_entries (entry_date, reference_number, description, created_by) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $date, $reference, $description, $userId);
            $stmt->execute();
            $entryId = $this->conn->insert_id;
            
            // Insert journal entry details
            foreach ($entries as $entry) {
                $sql = "INSERT INTO journal_entry_details (entry_id, account_id, debit_amount, credit_amount) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("iidd", $entryId, $entry['account_id'], $entry['debit'], $entry['credit']);
                $stmt->execute();
                
                // Update ledger
                $this->updateLedger($entry['account_id'], $date, $reference, $description, 
                                  $entry['debit'], $entry['credit']);
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    // Update ledger for an account
    private function updateLedger($accountId, $date, $reference, $description, $debit, $credit) {
        // Get the last balance
        $sql = "SELECT balance FROM ledger WHERE account_id = ? ORDER BY ledger_id DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();
        $lastBalance = $result->fetch_assoc()['balance'] ?? 0;
        
        // Calculate new balance
        $newBalance = $lastBalance + $debit - $credit;
        
        // Insert new ledger entry
        $sql = "INSERT INTO ledger (account_id, entry_date, reference_number, description, 
                debit_amount, credit_amount, balance) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssddd", $accountId, $date, $reference, $description, 
                         $debit, $credit, $newBalance);
        $stmt->execute();
    }
    
    // Generate Trial Balance
    public function generateTrialBalance($asOfDate) {
        $sql = "SELECT 
                    coa.account_code,
                    coa.account_name,
                    SUM(l.debit_amount) as total_debit,
                    SUM(l.credit_amount) as total_credit,
                    SUM(l.balance) as balance
                FROM chart_of_accounts coa
                LEFT JOIN ledger l ON coa.account_id = l.account_id
                WHERE l.entry_date <= ?
                GROUP BY coa.account_id
                ORDER BY coa.account_code";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $asOfDate);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Generate Income Statement
    public function generateIncomeStatement($startDate, $endDate) {
        $sql = "SELECT 
                    coa.account_code,
                    coa.account_name,
                    SUM(l.debit_amount) as total_debit,
                    SUM(l.credit_amount) as total_credit
                FROM chart_of_accounts coa
                LEFT JOIN ledger l ON coa.account_id = l.account_id
                WHERE l.entry_date BETWEEN ? AND ?
                AND coa.account_type IN ('Revenue', 'Expense')
                GROUP BY coa.account_id
                ORDER BY coa.account_type, coa.account_code";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Generate Balance Sheet
    public function generateBalanceSheet($asOfDate) {
        $sql = "SELECT 
                    coa.account_code,
                    coa.account_name,
                    coa.account_type,
                    SUM(l.balance) as balance
                FROM chart_of_accounts coa
                LEFT JOIN ledger l ON coa.account_id = l.account_id
                WHERE l.entry_date <= ?
                AND coa.account_type IN ('Asset', 'Liability', 'Equity')
                GROUP BY coa.account_id
                ORDER BY coa.account_type, coa.account_code";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $asOfDate);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?> 