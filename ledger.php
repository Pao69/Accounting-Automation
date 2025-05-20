<?php
require_once 'config/database.php';

// Get all active accounts for filter
$accounts = $conn->query("SELECT account_id, account_code, account_name FROM chart_of_accounts WHERE is_active = 1 ORDER BY account_code");

// Get selected account's transactions
$selectedAccount = isset($_GET['account_id']) ? (int)$_GET['account_id'] : null;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

$transactions = [];
$openingBalance = 0;
$closingBalance = 0;

if ($selectedAccount) {
    // Get opening balance
    $sql = "SELECT COALESCE(SUM(balance), 0) as balance 
            FROM ledger 
            WHERE account_id = ? AND entry_date < ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $selectedAccount, $startDate);
    $stmt->execute();
    $openingBalance = $stmt->get_result()->fetch_assoc()['balance'];

    // Get transactions
    $sql = "SELECT l.*, je.reference_number, je.entry_date, je.description
            FROM ledger l
            JOIN journal_entries je ON l.entry_id = je.entry_id
            WHERE l.account_id = ? AND je.entry_date BETWEEN ? AND ?
            ORDER BY je.entry_date, je.entry_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $selectedAccount, $startDate, $endDate);
    $stmt->execute();
    $transactions = $stmt->get_result();

    // Calculate closing balance
    $closingBalance = $openingBalance;
    while ($row = $transactions->fetch_assoc()) {
        $closingBalance += $row['balance'];
    }
    $transactions->data_seek(0); // Reset result pointer
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger - Accounting System</title>
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
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.125);
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
        .form-control, .form-select {
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
        }
        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
        }
        .btn-primary {
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        .account-info {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .account-info h5 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .account-info p {
            margin-bottom: 0;
            opacity: 0.9;
        }
        .table-responsive {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
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
        .badge {
            padding: 0.5em 0.75em;
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
                        <a class="nav-link active" href="ledger.php">
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
                        <a class="nav-link" href="chart_of_accounts.php">
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
                <i class="bi bi-book me-2"></i>Ledger
            </h2>
        </div>
        
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-book me-2"></i>Account Ledger
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Account</label>
                                <select class="form-select" name="account_id" required>
                                    <option value="">Select Account</option>
                                    <?php while ($account = $accounts->fetch_assoc()): ?>
                                        <option value="<?php echo $account['account_id']; ?>" 
                                                <?php echo $selectedAccount == $account['account_id'] ? 'selected' : ''; ?>>
                                            <?php echo $account['account_code'] . ' - ' . $account['account_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" 
                                       value="<?php echo $startDate; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" 
                                       value="<?php echo $endDate; ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search me-2"></i>View
                                </button>
                            </div>
                        </form>

                        <?php if ($selectedAccount): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Description</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Credit</th>
                                            <th class="text-end">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-light">
                                            <td colspan="5" class="text-end fw-bold">Opening Balance</td>
                                            <td class="text-end fw-bold">
                                                <?php echo number_format($openingBalance, 2); ?>
                                            </td>
                                        </tr>
                                        <?php while ($row = $transactions->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($row['entry_date'])); ?></td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        <?php echo $row['reference_number']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $row['description']; ?></td>
                                                <td class="text-end">
                                                    <?php echo $row['debit_amount'] > 0 ? number_format($row['debit_amount'], 2) : ''; ?>
                                                </td>
                                                <td class="text-end">
                                                    <?php echo $row['credit_amount'] > 0 ? number_format($row['credit_amount'], 2) : ''; ?>
                                                </td>
                                                <td class="text-end fw-bold">
                                                    <?php echo number_format($row['balance'], 2); ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                        <tr class="table-light">
                                            <td colspan="5" class="text-end fw-bold">Closing Balance</td>
                                            <td class="text-end fw-bold">
                                                <?php echo number_format($closingBalance, 2); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Please select an account to view its ledger.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 