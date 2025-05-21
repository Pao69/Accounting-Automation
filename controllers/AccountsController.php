<?php

class AccountsController extends Controller {
    public function index() {
        try {
            // Get all account types
            $sql = "SELECT * FROM account_types ORDER BY category, name";
            $result = $this->db->query($sql);
            $accountTypes = [];
            while ($row = $result->fetch_assoc()) {
                $accountTypes[$row['id']] = $row;
            }
            
            // Get all accounts with their types
            $sql = "SELECT a.*, at.name as type_name, at.category 
                    FROM accounts a 
                    JOIN account_types at ON a.type_id = at.id 
                    ORDER BY a.account_code";
            
            $result = $this->db->query($sql);
            $accounts = [];
            
            // Group accounts by category
            while ($row = $result->fetch_assoc()) {
                $category = $row['category'];
                if (!isset($accounts[$category])) {
                    $accounts[$category] = [];
                }
                $accounts[$category][] = $row;
            }
            
            $this->view('accounts/index', [
                'account_types' => $accountTypes,
                'accounts' => $accounts
            ]);
            
        } catch (Exception $e) {
            $this->view('accounts/index', ['error' => $e->getMessage()]);
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $accountCode = $this->db->escape($_POST['account_code']);
                $name = $this->db->escape($_POST['name']);
                $typeId = (int)$_POST['type_id'];
                
                $sql = "INSERT INTO accounts (account_code, name, type_id) 
                        VALUES ('$accountCode', '$name', $typeId)";
                
                $this->db->query($sql);
                header('Location: ' . BASE_URL . '/accounts');
                exit;
                
            } catch (Exception $e) {
                $this->view('accounts/create', [
                    'error' => $e->getMessage(),
                    'account_types' => $this->getAccountTypes()
                ]);
                return;
            }
        }
        
        $this->view('accounts/create', [
            'account_types' => $this->getAccountTypes()
        ]);
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $accountCode = $this->db->escape($_POST['account_code']);
                $name = $this->db->escape($_POST['name']);
                $typeId = (int)$_POST['type_id'];
                $id = (int)$id;
                
                $sql = "UPDATE accounts 
                        SET account_code = '$accountCode',
                            name = '$name',
                            type_id = $typeId
                        WHERE id = $id";
                
                $this->db->query($sql);
                header('Location: ' . BASE_URL . '/accounts');
                exit;
                
            } catch (Exception $e) {
                $account = $this->getAccount($id);
                $this->view('accounts/edit', [
                    'error' => $e->getMessage(),
                    'account' => $account,
                    'account_types' => $this->getAccountTypes()
                ]);
                return;
            }
        }
        
        $account = $this->getAccount($id);
        if (!$account) {
            $this->error404();
            return;
        }
        
        $this->view('accounts/edit', [
            'account' => $account,
            'account_types' => $this->getAccountTypes()
        ]);
    }

    private function getAccountTypes() {
        $sql = "SELECT * FROM account_types ORDER BY category, name";
        $result = $this->db->query($sql);
        $types = [];
        while ($row = $result->fetch_assoc()) {
            $types[] = $row;
        }
        return $types;
    }

    private function getAccount($id) {
        $id = (int)$id;
        $sql = "SELECT a.*, at.name as type_name, at.category 
                FROM accounts a 
                JOIN account_types at ON a.type_id = at.id 
                WHERE a.id = $id";
        
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }
} 