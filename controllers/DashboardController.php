<?php
class DashboardController extends Controller {
    public function index() {
        // Get total assets
        $assetsQuery = "SELECT COALESCE(SUM(balance), 0) as total_assets 
                       FROM accounts a 
                       JOIN account_types at ON a.type_id = at.id 
                       WHERE at.category = 'Asset'";
        $assetsResult = $this->db->query($assetsQuery);
        $totalAssets = $assetsResult->fetch_assoc()['total_assets'];

        // Get total liabilities
        $liabilitiesQuery = "SELECT COALESCE(SUM(balance), 0) as total_liabilities 
                            FROM accounts a 
                            JOIN account_types at ON a.type_id = at.id 
                            WHERE at.category = 'Liability'";
        $liabilitiesResult = $this->db->query($liabilitiesQuery);
        $totalLiabilities = $liabilitiesResult->fetch_assoc()['total_liabilities'];

        // Get total equity
        $equityQuery = "SELECT COALESCE(SUM(balance), 0) as total_equity 
                       FROM accounts a 
                       JOIN account_types at ON a.type_id = at.id 
                       WHERE at.category = 'Equity'";
        $equityResult = $this->db->query($equityQuery);
        $totalEquity = $equityResult->fetch_assoc()['total_equity'];

        // Get recent journal entries
        $journalQuery = "SELECT 
                            je.id,
                            je.reference_no,
                            je.description,
                            je.status,
                            je.created_at,
                            COALESCE(SUM(ji.debit), 0) as total_amount,
                            COUNT(ji.id) as entries_count
                        FROM journal_entries je 
                        LEFT JOIN journal_items ji ON je.id = ji.journal_id 
                        GROUP BY je.id, je.reference_no, je.description, je.status, je.created_at
                        ORDER BY je.created_at DESC 
                        LIMIT 5";
        
        $journalResult = $this->db->query($journalQuery);
        $recentJournals = [];
        
        if ($journalResult) {
            while ($row = $journalResult->fetch_assoc()) {
                $recentJournals[] = $row;
            }
        }

        $this->view('dashboard/index', [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'recent_journals' => $recentJournals
        ]);
    }
} 