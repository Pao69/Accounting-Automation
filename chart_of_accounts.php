<?php
require_once 'config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $accountCode = $_POST['account_code'];
            $accountName = $_POST['account_name'];
            $accountType = $_POST['account_type'];
            $parentAccountId = !empty($_POST['parent_account_id']) ? $_POST['parent_account_id'] : null;
            
            $sql = "INSERT INTO chart_of_accounts (account_code, account_name, account_type, parent_account_id) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $accountCode, $accountName, $accountType, $parentAccountId);
            
            if ($stmt->execute()) {
                $success = "Account added successfully!";
            } else {
                $error = "Error adding account: " . $conn->error;
            }
        } elseif ($_POST['action'] === 'edit') {
            $accountId = $_POST['account_id'];
            $accountCode = $_POST['account_code'];
            $accountName = $_POST['account_name'];
            $accountType = $_POST['account_type'];
            $parentAccountId = !empty($_POST['parent_account_id']) ? $_POST['parent_account_id'] : null;
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            $sql = "UPDATE chart_of_accounts 
                    SET account_code = ?, account_name = ?, account_type = ?, 
                        parent_account_id = ?, is_active = ? 
                    WHERE account_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiii", $accountCode, $accountName, $accountType, 
                            $parentAccountId, $isActive, $accountId);
            
            if ($stmt->execute()) {
                $success = "Account updated successfully!";
            } else {
                $error = "Error updating account: " . $conn->error;
            }
        }
    }
}

// Get all accounts
$sql = "SELECT a.*, p.account_name as parent_name 
        FROM chart_of_accounts a 
        LEFT JOIN chart_of_accounts p ON a.parent_account_id = p.account_id 
        ORDER BY a.account_code";
$accounts = $conn->query($sql);

// Get parent accounts for dropdown
$sql = "SELECT account_id, account_code, account_name 
        FROM chart_of_accounts 
        WHERE is_active = 1 
        ORDER BY account_code";
$parentAccounts = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart of Accounts - Accounting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
            margin-bottom: 1.5rem;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
        }
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }
        .nav-link {
            padding: 0.5rem 1rem;
            color: rgba(255,255,255,.85) !important;
        }
        .nav-link:hover {
            color: #fff !important;
        }
        .nav-link.active {
            color: #fff !important;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-calculator me-2"></i>Accounting System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="journal_entry.php">
                            <i class="bi bi-journal-text me-1"></i>Journal Entry
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ledger.php">
                            <i class="bi bi-book me-1"></i>Ledger
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trial_balance.php">
                            <i class="bi bi-scale me-1"></i>Trial Balance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="income_statement.php">
                            <i class="bi bi-graph-up me-1"></i>Income Statement
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="balance_sheet.php">
                            <i class="bi bi-file-earmark-text me-1"></i>Balance Sheet
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="chart_of_accounts.php">
                            <i class="bi bi-list-ul me-1"></i>Chart of Accounts
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>Chart of Accounts
            </h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                <i class="bi bi-plus-circle me-1"></i>Add New Account
            </button>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th>Type</th>
                            <th>Parent Account</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($account = $accounts->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $account['account_code']; ?></td>
                                <td><?php echo $account['account_name']; ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo $account['account_type']; ?>
                                    </span>
                                </td>
                                <td><?php echo $account['parent_name'] ?? '-'; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $account['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $account['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            onclick="editAccount(<?php echo htmlspecialchars(json_encode($account)); ?>)">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>Add New Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="account_code" class="form-label">Account Code</label>
                            <input type="text" class="form-control" id="account_code" name="account_code" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="account_name" class="form-label">Account Name</label>
                            <input type="text" class="form-control" id="account_name" name="account_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="account_type" class="form-label">Account Type</label>
                            <select class="form-select" id="account_type" name="account_type" required>
                                <option value="Asset">Asset</option>
                                <option value="Liability">Liability</option>
                                <option value="Equity">Equity</option>
                                <option value="Revenue">Revenue</option>
                                <option value="Expense">Expense</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="parent_account_id" class="form-label">Parent Account</label>
                            <select class="form-select" id="parent_account_id" name="parent_account_id">
                                <option value="">None</option>
                                <?php while ($parent = $parentAccounts->fetch_assoc()): ?>
                                    <option value="<?php echo $parent['account_id']; ?>">
                                        <?php echo $parent['account_code'] . ' - ' . $parent['account_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Add Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal fade" id="editAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>Edit Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="account_id" id="edit_account_id">
                        
                        <div class="mb-3">
                            <label for="edit_account_code" class="form-label">Account Code</label>
                            <input type="text" class="form-control" id="edit_account_code" name="account_code" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_account_name" class="form-label">Account Name</label>
                            <input type="text" class="form-control" id="edit_account_name" name="account_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_account_type" class="form-label">Account Type</label>
                            <select class="form-select" id="edit_account_type" name="account_type" required>
                                <option value="Asset">Asset</option>
                                <option value="Liability">Liability</option>
                                <option value="Equity">Equity</option>
                                <option value="Revenue">Revenue</option>
                                <option value="Expense">Expense</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_parent_account_id" class="form-label">Parent Account</label>
                            <select class="form-select" id="edit_parent_account_id" name="parent_account_id">
                                <option value="">None</option>
                                <?php 
                                $parentAccounts->data_seek(0);
                                while ($parent = $parentAccounts->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $parent['account_id']; ?>">
                                        <?php echo $parent['account_code'] . ' - ' . $parent['account_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active">
                                <label class="form-check-label" for="edit_is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editAccount(account) {
            document.getElementById('edit_account_id').value = account.account_id;
            document.getElementById('edit_account_code').value = account.account_code;
            document.getElementById('edit_account_name').value = account.account_name;
            document.getElementById('edit_account_type').value = account.account_type;
            document.getElementById('edit_parent_account_id').value = account.parent_account_id || '';
            document.getElementById('edit_is_active').checked = account.is_active == 1;
            
            new bootstrap.Modal(document.getElementById('editAccountModal')).show();
        }
    </script>
</body>
</html> 