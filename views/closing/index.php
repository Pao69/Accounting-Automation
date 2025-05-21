<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Year-End Closing</h2>
                <?php if (isset($retained_earnings)): ?>
                <button type="button" class="btn btn-success" onclick="printPreview()">
                    <i class="fa fa-print"></i> Print Preview
                </button>
                <?php endif; ?>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <!-- Year Selection -->
            <form method="GET" class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="year" class="form-label">Select Year</label>
                            <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                                <?php 
                                // Add current year if not in available years
                                $currentYear = date('Y');
                                if (!in_array($currentYear, $available_years)) {
                                    array_unshift($available_years, $currentYear);
                                }
                                foreach ($available_years as $available_year): 
                                ?>
                                <option value="<?php echo $available_year; ?>" 
                                    <?php echo $year == $available_year ? 'selected' : ''; ?>>
                                    <?php echo $available_year; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <div class="alert alert-info mb-0">
                                <i class="fa fa-info-circle"></i> 
                                Showing closing preview for transactions from January 1, <?php echo $year; ?> to December 31, <?php echo $year; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Closing Preview -->
            <div class="card mb-4" id="closingPreview">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        Closing Preview for <?php echo $year; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Revenue Accounts -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Revenue Accounts to Close</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Account</th>
                                                    <th class="text-end">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($revenues)): ?>
                                                <tr>
                                                    <td colspan="2" class="text-center">No revenue accounts to close</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($revenues as $revenue): ?>
                                                <tr>
                                                    <td><?php echo $revenue['account_code'] . ' - ' . $revenue['name']; ?></td>
                                                    <td class="text-end"><?php echo number_format($revenue['balance'], 2); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                                <tr class="table-success">
                                                    <td class="fw-bold">Total Revenue</td>
                                                    <td class="text-end fw-bold"><?php echo number_format($total_revenue, 2); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Expense Accounts -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">Expense Accounts to Close</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Account</th>
                                                    <th class="text-end">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($expenses)): ?>
                                                <tr>
                                                    <td colspan="2" class="text-center">No expense accounts to close</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($expenses as $expense): ?>
                                                <tr>
                                                    <td><?php echo $expense['account_code'] . ' - ' . $expense['name']; ?></td>
                                                    <td class="text-end"><?php echo number_format($expense['balance'], 2); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                                <tr class="table-danger">
                                                    <td class="fw-bold">Total Expenses</td>
                                                    <td class="text-end fw-bold"><?php echo number_format($total_expenses, 2); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Net Income Summary -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">
                                        <?php echo $net_income >= 0 ? 'Net Income' : 'Net Loss'; ?> for <?php echo $year; ?>
                                    </h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <h5 class="mb-0 <?php echo $net_income >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo number_format(abs($net_income), 2); ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Process Closing -->
            <?php if ($retained_earnings): ?>
            <form method="POST" action="<?php echo BASE_URL; ?>/closing/process" class="card" id="closingForm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Process Closing Entries</h5>
                </div>
                <div class="card-body">
                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                    <input type="hidden" name="retained_earnings_id" value="<?php echo $retained_earnings['id']; ?>">
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fa fa-info-circle"></i> The following entries will be created:</h6>
                        <ol class="mb-0">
                            <li>Close all revenue accounts (<?php echo count($revenues); ?> accounts)</li>
                            <li>Close all expense accounts (<?php echo count($expenses); ?> accounts)</li>
                            <li>Transfer net <?php echo $net_income >= 0 ? 'income' : 'loss'; ?> to <?php echo $retained_earnings['name']; ?></li>
                        </ol>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="fa fa-warning"></i> Important Notes:</h6>
                        <ul class="mb-0">
                            <li>This process cannot be undone</li>
                            <li>Make sure you have backed up your data</li>
                            <li>All entries will be dated December 31, <?php echo $year; ?></li>
                            <li>Only posted transactions will be included</li>
                        </ul>
                    </div>
                    
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.location.href='<?php echo BASE_URL; ?>/closing'">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" onclick="return confirmClosing()">
                            <i class="fa fa-check"></i> Process Closing Entries
                        </button>
                    </div>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-danger">
                <h6 class="alert-heading"><i class="fa fa-warning"></i> Error: Retained Earnings Account Not Found</h6>
                <p class="mb-0">Please create a Retained Earnings account in the Equity category before processing closing entries.</p>
                <hr>
                <a href="<?php echo BASE_URL; ?>/accounts/create" class="btn btn-danger">
                    <i class="fa fa-plus"></i> Create Retained Earnings Account
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight the active menu item
    document.querySelector('a[href="<?php echo BASE_URL; ?>/closing"]')
        .closest('li').classList.add('active');
});

function confirmClosing() {
    return confirm(
        'Are you sure you want to process the closing entries for <?php echo $year; ?>?\n\n' +
        'This will:\n' +
        '1. Close all revenue accounts\n' +
        '2. Close all expense accounts\n' +
        '3. Transfer net <?php echo $net_income >= 0 ? "income" : "loss"; ?> of <?php echo number_format(abs($net_income), 2); ?> to Retained Earnings\n\n' +
        'This process cannot be undone.'
    );
}

function printPreview() {
    const content = document.getElementById('closingPreview').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Closing Preview - <?php echo $year; ?></title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { padding: 20px; }
                @media print {
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h3 class="mb-4">Year-End Closing Preview - <?php echo $year; ?></h3>
                ${content}
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
    }
    .table {
        border: 1px solid #dee2e6;
    }
    .table td, .table th {
        background-color: white !important;
        color: black !important;
    }
}
</style> 