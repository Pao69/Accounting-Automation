<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">Income Statement</h2>
            
            <!-- Period Selection -->
            <form method="GET" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label for="period" class="form-label">Select Period</label>
                        <input type="month" id="period" name="period" class="form-control" 
                               value="<?php echo date('Y-m', strtotime($period['start'])); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-refresh"></i> Update
                        </button>
                    </div>
                </div>
            </form>

            <!-- Statement Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        Income Statement
                        <br>
                        <small>For the Month Ended <?php echo date('F d, Y', strtotime($period['end'])); ?></small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <!-- Revenues Section -->
                                <tr class="table-light">
                                    <th colspan="2">Revenues</th>
                                </tr>
                                <?php foreach ($revenues as $revenue): ?>
                                <tr>
                                    <td class="ps-4"><?php echo $revenue['account_code'] . ' - ' . $revenue['name']; ?></td>
                                    <td class="text-end"><?php echo number_format($revenue['amount'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-secondary">
                                    <td class="fw-bold">Total Revenues</td>
                                    <td class="text-end fw-bold"><?php echo number_format($total_revenue, 2); ?></td>
                                </tr>

                                <!-- Expenses Section -->
                                <tr class="table-light">
                                    <th colspan="2">Expenses</th>
                                </tr>
                                <?php foreach ($expenses as $expense): ?>
                                <tr>
                                    <td class="ps-4"><?php echo $expense['account_code'] . ' - ' . $expense['name']; ?></td>
                                    <td class="text-end"><?php echo number_format($expense['amount'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-secondary">
                                    <td class="fw-bold">Total Expenses</td>
                                    <td class="text-end fw-bold"><?php echo number_format($total_expenses, 2); ?></td>
                                </tr>

                                <!-- Net Income/Loss -->
                                <tr class="table-primary">
                                    <td class="fw-bold">
                                        <?php echo $net_income >= 0 ? 'Net Income' : 'Net Loss'; ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <?php echo number_format(abs($net_income), 2); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Notes</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>This statement shows the financial performance for the period from <?php echo date('F d, Y', strtotime($period['start'])); ?> to <?php echo date('F d, Y', strtotime($period['end'])); ?>.</li>
                        <li>All amounts are recorded using the accrual basis of accounting.</li>
                        <li>Only posted transactions are included in this statement.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight the active menu item
    document.querySelector('a[href="<?php echo BASE_URL; ?>/financial-statements/income"]')
        .closest('li').classList.add('active');
});
</script> 