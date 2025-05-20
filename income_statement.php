<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Accounting.php';

$accounting = new Accounting($conn);

$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Get revenue accounts
$sql = "SELECT 
            coa.account_id,
            coa.account_code,
            coa.account_name,
            COALESCE(SUM(CASE 
                WHEN jed.debit_amount > 0 THEN jed.debit_amount 
                ELSE 0 
            END), 0) as total_debit,
            COALESCE(SUM(CASE 
                WHEN jed.credit_amount > 0 THEN jed.credit_amount 
                ELSE 0 
            END), 0) as total_credit
        FROM chart_of_accounts coa
        LEFT JOIN journal_entry_details jed ON coa.account_id = jed.account_id
        LEFT JOIN journal_entries je ON jed.entry_id = je.entry_id
        WHERE coa.is_active = 1
        AND coa.account_type = 'revenue'
        AND (je.entry_date IS NULL OR je.entry_date BETWEEN ? AND ?)
        GROUP BY coa.account_id
        ORDER BY coa.account_code";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$revenues = $stmt->get_result();

// Get expense accounts
$sql = "SELECT 
            coa.account_id,
            coa.account_code,
            coa.account_name,
            COALESCE(SUM(CASE 
                WHEN jed.debit_amount > 0 THEN jed.debit_amount 
                ELSE 0 
            END), 0) as total_debit,
            COALESCE(SUM(CASE 
                WHEN jed.credit_amount > 0 THEN jed.credit_amount 
                ELSE 0 
            END), 0) as total_credit
        FROM chart_of_accounts coa
        LEFT JOIN journal_entry_details jed ON coa.account_id = jed.account_id
        LEFT JOIN journal_entries je ON jed.entry_id = je.entry_id
        WHERE coa.is_active = 1
        AND coa.account_type = 'expense'
        AND (je.entry_date IS NULL OR je.entry_date BETWEEN ? AND ?)
        GROUP BY coa.account_id
        ORDER BY coa.account_code";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$expenses = $stmt->get_result();

// Calculate totals
$totalRevenue = 0;
$totalExpenses = 0;

while ($row = $revenues->fetch_assoc()) {
    $totalRevenue += $row['total_credit'] - $row['total_debit'];
}
$revenues->data_seek(0);

while ($row = $expenses->fetch_assoc()) {
    $totalExpenses += $row['total_debit'] - $row['total_credit'];
}
$expenses->data_seek(0);

$netIncome = $totalRevenue - $totalExpenses;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Statement - Accounting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Accounting System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="journal_entry.php">Journal Entry</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ledger.php">Ledger</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trial_balance.php">Trial Balance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="income_statement.php">Income Statement</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="balance_sheet.php">Balance Sheet</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Income Statement</h2>
        
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" 
                       value="<?php echo $startDate; ?>" required>
            </div>
            <div class="col-md-5">
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

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>Income Statement
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Account</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Revenue Section -->
                                    <tr class="table-light">
                                        <td colspan="2" class="fw-bold">Revenue</td>
                                    </tr>
                                    <?php while ($row = $revenues->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $row['account_code']; ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo $row['account_name']; ?></small>
                                            </td>
                                            <td class="text-end">
                                                <?php echo number_format($row['total_credit'] - $row['total_debit'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <tr class="table-light">
                                        <td class="text-end fw-bold">Total Revenue</td>
                                        <td class="text-end fw-bold"><?php echo number_format($totalRevenue, 2); ?></td>
                                    </tr>

                                    <!-- Expense Section -->
                                    <tr class="table-light">
                                        <td colspan="2" class="fw-bold">Expenses</td>
                                    </tr>
                                    <?php while ($row = $expenses->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $row['account_code']; ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo $row['account_name']; ?></small>
                                            </td>
                                            <td class="text-end">
                                                <?php echo number_format($row['total_debit'] - $row['total_credit'], 2); ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <tr class="table-light">
                                        <td class="text-end fw-bold">Total Expenses</td>
                                        <td class="text-end fw-bold"><?php echo number_format($totalExpenses, 2); ?></td>
                                    </tr>

                                    <!-- Net Income -->
                                    <tr class="table-primary">
                                        <td class="text-end fw-bold">Net Income</td>
                                        <td class="text-end fw-bold">
                                            <?php echo number_format($netIncome, 2); ?>
                                            <?php if ($netIncome < 0): ?>
                                                <span class="badge bg-danger ms-2">Loss</span>
                                            <?php else: ?>
                                                <span class="badge bg-success ms-2">Profit</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-arrow-up-circle me-2"></i>Total Revenue
                                        </h6>
                                        <h3 class="mb-0"><?php echo number_format($totalRevenue, 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-arrow-down-circle me-2"></i>Total Expenses
                                        </h6>
                                        <h3 class="mb-0"><?php echo number_format($totalExpenses, 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card <?php echo $netIncome >= 0 ? 'bg-primary' : 'bg-warning'; ?> text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-cash-stack me-2"></i>Net Income
                                        </h6>
                                        <h3 class="mb-0"><?php echo number_format($netIncome, 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 