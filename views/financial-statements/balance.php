<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">Balance Sheet</h2>
            
            <!-- Date Selection -->
            <form method="GET" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label for="as_of" class="form-label">As of Date</label>
                        <input type="date" id="as_of" name="as_of" class="form-control" 
                               value="<?php echo date('Y-m-d', strtotime($as_of_date)); ?>">
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
                        Balance Sheet
                        <br>
                        <small>As of <?php echo $as_of_date; ?></small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <!-- Assets Section -->
                                <tr class="table-light">
                                    <th colspan="2">Assets</th>
                                </tr>
                                <?php foreach ($assets as $asset): ?>
                                <tr>
                                    <td class="ps-4"><?php echo $asset['account_code'] . ' - ' . $asset['name']; ?></td>
                                    <td class="text-end"><?php echo number_format($asset['balance'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-secondary">
                                    <td class="fw-bold">Total Assets</td>
                                    <td class="text-end fw-bold"><?php echo number_format($total_assets, 2); ?></td>
                                </tr>

                                <!-- Liabilities Section -->
                                <tr class="table-light">
                                    <th colspan="2">Liabilities</th>
                                </tr>
                                <?php foreach ($liabilities as $liability): ?>
                                <tr>
                                    <td class="ps-4"><?php echo $liability['account_code'] . ' - ' . $liability['name']; ?></td>
                                    <td class="text-end"><?php echo number_format($liability['balance'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-secondary">
                                    <td class="fw-bold">Total Liabilities</td>
                                    <td class="text-end fw-bold"><?php echo number_format($total_liabilities, 2); ?></td>
                                </tr>

                                <!-- Owner's Equity Section -->
                                <tr class="table-light">
                                    <th colspan="2">Owner's Equity</th>
                                </tr>
                                <?php foreach ($equity as $account): ?>
                                <tr>
                                    <td class="ps-4"><?php echo $account['account_code'] . ' - ' . $account['name']; ?></td>
                                    <td class="text-end"><?php echo number_format($account['balance'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-secondary">
                                    <td class="fw-bold">Total Owner's Equity</td>
                                    <td class="text-end fw-bold"><?php echo number_format($total_equity, 2); ?></td>
                                </tr>

                                <!-- Total Liabilities and Equity -->
                                <tr class="table-primary">
                                    <td class="fw-bold">Total Liabilities and Owner's Equity</td>
                                    <td class="text-end fw-bold">
                                        <?php echo number_format($total_liabilities + $total_equity, 2); ?>
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
                        <li>This statement reflects the financial position as of <?php echo $as_of_date; ?>.</li>
                        <li>All amounts are recorded using the accrual basis of accounting.</li>
                        <li>Account balances include all posted transactions up to the selected date.</li>
                        <?php if ($total_assets != ($total_liabilities + $total_equity)): ?>
                        <li class="text-danger">Warning: The accounting equation is not balanced. Total Assets (<?php echo number_format($total_assets, 2); ?>) should equal Total Liabilities and Owner's Equity (<?php echo number_format($total_liabilities + $total_equity, 2); ?>).</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight the active menu item
    document.querySelector('a[href="<?php echo BASE_URL; ?>/financial-statements/balance"]')
        .closest('li').classList.add('active');
});
</script> 