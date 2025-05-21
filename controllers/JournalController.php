<?php
class JournalController extends Controller {
    public function index() {
        $sql = "SELECT 
                    je.*, 
                    COUNT(ji.id) as entries_count,
                    SUM(ji.debit) as total_amount 
                FROM journal_entries je 
                LEFT JOIN journal_items ji ON je.id = ji.journal_id 
                GROUP BY je.id 
                ORDER BY je.date DESC";
        
        $result = $this->db->query($sql);
        $journals = [];
        
        while ($row = $result->fetch_assoc()) {
            $journals[] = $row;
        }
        
        $this->view('journal/index', ['journals' => $journals]);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();
                
                // Insert journal entry
                $date = $this->db->escape($_POST['date']);
                $reference = $this->db->escape($_POST['reference_no']);
                $description = $this->db->escape($_POST['description']);
                
                $sql = "INSERT INTO journal_entries (date, reference_no, description) 
                        VALUES ('$date', '$reference', '$description')";
                $this->db->query($sql);
                
                $journalId = $this->db->getLastInsertId();
                
                // Insert journal items
                foreach ($_POST['items'] as $item) {
                    $accountId = (int)$item['account_id'];
                    $debit = (float)$item['debit'];
                    $credit = (float)$item['credit'];
                    
                    $sql = "INSERT INTO journal_items (journal_id, account_id, debit, credit) 
                            VALUES ($journalId, $accountId, $debit, $credit)";
                    $this->db->query($sql);
                }
                
                $this->db->commit();
                $this->redirect('/journal');
            } catch (Exception $e) {
                $this->db->rollback();
                die("Error: " . $e->getMessage());
            }
        } else {
            // Get all accounts for the form
            $sql = "SELECT a.*, at.category 
                    FROM accounts a 
                    JOIN account_types at ON a.type_id = at.id 
                    ORDER BY a.account_code";
            $result = $this->db->query($sql);
            $accounts = [];
            
            while ($row = $result->fetch_assoc()) {
                $accounts[] = $row;
            }
            
            $this->view('journal/create', ['accounts' => $accounts]);
        }
    }
} 