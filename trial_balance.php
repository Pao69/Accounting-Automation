<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Accounting.php';

$accounting = new Accounting($conn);

$asOfDate = $_GET['date'] ?? date('Y-m-d');
$trialBalance = $accounting->generateTrialBalance($asOfDate);

// Get date range
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Get all active accounts with their balances
$sql = "SELECT 
            coa.account_id,
            coa.account_code,
            coa.account_name,
            coa.account_type,
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
        AND (je.entry_date IS NULL OR je.entry_date BETWEEN ? AND ?)
        GROUP BY coa.account_id
        ORDER BY coa.account_type, coa.account_code";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$accounts = $stmt->get_result();

// Calculate totals
$totalDebit = 0;
$totalCredit = 0;
$totalsByType = [];

while ($row = $accounts->fetch_assoc()) {
    $totalDebit += $row['total_debit'];
    $totalCredit += $row['total_credit'];
    
    // Add to type totals
    if (!isset($totalsByType[$row['account_type']])) {
        $totalsByType[$row['account_type']] = [
            'debit' => 0,
            'credit' => 0
        ];
    }
    $totalsByType[$row['account_type']]['debit'] += $row['total_debit'];
    $totalsByType[$row['account_type']]['credit'] += $row['total_credit'];
}

$accounts->data_seek(0); // Reset result pointer
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Balance - Accounting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                        <a class="nav-link" href="ledger.php">
                            <i class="bi bi-book me-1"></i>Ledger
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="trial_balance.php">
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
                <i class="bi bi-scale me-2"></i>Trial Balance
            </h2>
        </div>

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-scale me-2"></i>Trial Balance
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3 mb-4">
                            <div class="col-md-5">
                                <label class="form-label">
                                    <i class="bi bi-calendar3 me-1"></i>Start Date
                                </label>
                                <input type="date" class="form-control" name="start_date" 
                                       value="<?php echo $startDate; ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">
                                    <i class="bi bi-calendar3 me-1"></i>End Date
                                </label>
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

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th>Type</th>
                                        <th class="text-end">Debit</th>
                                        <th class="text-end">Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $currentType = '';
                                    while ($row = $accounts->fetch_assoc()): 
                                        if ($currentType !== $row['account_type']):
                                            $currentType = $row['account_type'];
                                    ?>
                                        <tr class="table-light">
                                            <td colspan="5" class="fw-bold">
                                                <?php echo ucfirst($currentType); ?> Accounts
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                        <tr>
                                            <td><?php echo $row['account_code']; ?></td>
                                            <td><?php echo $row['account_name']; ?></td>
                                            <td><?php echo ucfirst($row['account_type']); ?></td>
                                            <?php
                                            $totalDebit = $row['total_debit'];
                                            $totalCredit = $row['total_credit'];
                                            
                                            if ($totalDebit > 0) {
                                                echo '<td class="text-end">' . number_format($totalDebit, 2) . '</td>';
                                                echo '<td class="text-end"></td>';
                                            } else {
                                                echo '<td class="text-end"></td>';
                                                echo '<td class="text-end">' . number_format(abs($totalCredit), 2) . '</td>';
                                            }
                                            ?>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold"><?php echo number_format($totalDebit, 2); ?></td>
                                        <td class="text-end fw-bold"><?php echo number_format($totalCredit, 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php if ($totalDebit !== $totalCredit): ?>
                            <div class="alert alert-danger mt-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Trial balance is not balanced! Please check your journal entries.
                            </div>
                        <?php endif; ?>

                        <!-- Summary Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-arrow-down-circle me-2"></i>Total Debit
                                        </h6>
                                        <h3 class="mb-0"><?php echo number_format($totalDebit, 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-arrow-up-circle me-2"></i>Total Credit
                                        </h6>
                                        <h3 class="mb-0"><?php echo number_format($totalCredit, 2); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card <?php echo $totalDebit === $totalCredit ? 'bg-info' : 'bg-warning'; ?> text-white">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-check-circle me-2"></i>Status
                                        </h6>
                                        <h3 class="mb-0">
                                            <?php echo $totalDebit === $totalCredit ? 'Balanced' : 'Unbalanced'; ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Totals by Account Type -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-pie-chart me-2"></i>Totals by Account Type
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($totalsByType as $type => $totals): ?>
                                        <div class="col-md-3 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title text-muted mb-2">
                                                        <?php echo ucfirst($type); ?>
                                                    </h6>
                                                    <h4 class="mb-0">
                                                        <?php echo number_format($totals['debit'], 2); ?>
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
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