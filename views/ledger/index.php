<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>General Ledger</h2>
                <div>
                    <?php if (isset($selected_account)): ?>
                    <button type="button" class="btn btn-success me-2" onclick="printLedger()">
                        <i class="fa fa-print"></i> Print Ledger
                    </button>
                    <button type="button" class="btn btn-info text-white" onclick="exportToExcel()">
                        <i class="fa fa-file-excel-o"></i> Export to Excel
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Account and Period Selection -->
            <form method="GET" class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="account_id" class="form-label">Select Account</label>
                            <select name="account_id" id="account_id" class="form-select" required>
                                <option value="">-- Select Account --</option>
                                <?php 
                                $categories = [];
                                foreach ($accounts as $account) {
                                    if (!isset($categories[$account['category']])) {
                                        $categories[$account['category']] = [];
                                    }
                                    $categories[$account['category']][] = $account;
                                }
                                foreach ($categories as $category => $categoryAccounts):
                                ?>
                                <optgroup label="<?php echo $category; ?>">
                                    <?php foreach ($categoryAccounts as $account): ?>
                                    <option value="<?php echo $account['id']; ?>" 
                                        <?php echo (isset($_GET['account_id']) && $_GET['account_id'] == $account['id']) ? 'selected' : ''; ?>>
                                        <?php echo $account['account_code'] . ' - ' . $account['name']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" 
                                   value="<?php echo $start_date; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" 
                                   value="<?php echo $end_date; ?>" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> View Ledger
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <?php if (isset($selected_account)): ?>
            <!-- Account Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <?php echo $selected_account['account_code'] . ' - ' . $selected_account['name']; ?>
                            <small class="d-block"><?php echo $selected_account['type_name']; ?></small>
                        </h5>
                        <div class="text-end">
                            <h6 class="mb-0">Current Balance</h6>
                            <h4 class="mb-0"><?php echo number_format($ending_balance, 2); ?></h4>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="ledgerTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                    <th class="text-end">Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Opening Balance -->
                                <tr class="table-light">
                                    <td><?php echo $start_date; ?></td>
                                    <td>-</td>
                                    <td><strong>Opening Balance</strong></td>
                                    <td class="text-end">-</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end"><?php echo number_format($opening_balance, 2); ?></td>
                                    <td>-</td>
                                </tr>
                                
                                <!-- Transactions -->
                                <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d', strtotime($transaction['date'])); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>/journal/view/<?php echo $transaction['journal_id']; ?>" 
                                           class="text-primary" title="View Journal Entry">
                                            <?php echo $transaction['reference_no']; ?>
                                        </a>
                                    </td>
                                    <td><?php echo $transaction['description']; ?></td>
                                    <td class="text-end">
                                        <?php echo $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php echo $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '-'; ?>
                                    </td>
                                    <td class="text-end"><?php echo number_format($transaction['running_balance'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $transaction['status'] == 'Posted' ? 'success' : 'warning'; ?>">
                                            <?php echo $transaction['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                                <!-- Totals -->
                                <tr class="table-secondary">
                                    <td colspan="3" class="fw-bold">Period Totals</td>
                                    <td class="text-end fw-bold"><?php echo number_format($total_debit, 2); ?></td>
                                    <td class="text-end fw-bold"><?php echo number_format($total_credit, 2); ?></td>
                                    <td class="text-end fw-bold"><?php echo number_format($ending_balance, 2); ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Notes</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Opening balance is calculated as of <?php echo $start_date; ?></li>
                        <li>Only posted transactions are included in the running balance</li>
                        <li>Ending balance as of <?php echo $end_date; ?>: <?php echo number_format($ending_balance, 2); ?></li>
                        <li>Click on reference numbers to view the complete journal entry</li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight the active menu item
    document.querySelector('a[href="<?php echo BASE_URL; ?>/ledger"]')
        .closest('li').classList.add('active');
        
    // Initialize date range validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });
    
    endDate.addEventListener('change', function() {
        startDate.max = this.value;
        if (startDate.value && startDate.value > this.value) {
            startDate.value = this.value;
        }
    });
});

function printLedger() {
    window.print();
}

function exportToExcel() {
    const table = document.getElementById('ledgerTable');
    const ws = XLSX.utils.table_to_sheet(table);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Ledger');
    
    // Get account name and date range for filename
    const accountName = '<?php echo isset($selected_account) ? $selected_account['name'] : ''; ?>';
    const startDate = '<?php echo $start_date; ?>';
    const endDate = '<?php echo $end_date; ?>';
    const fileName = `Ledger_${accountName}_${startDate}_${endDate}.xlsx`;
    
    XLSX.writeFile(wb, fileName);
}
</script>

<!-- Add Excel export library -->
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

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