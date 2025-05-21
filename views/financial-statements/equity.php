<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">Statement of Changes in Owner's Equity</h2>
            
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
                        Statement of Changes in Owner's Equity
                        <br>
                        <small>For the Month Ended <?php echo date('F d, Y', strtotime($period['end'])); ?></small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <!-- Beginning Balance -->
                                <tr>
                                    <td class="fw-bold">Owner's Equity, Beginning of Period</td>
                                    <td class="text-end">
                                        <?php echo number_format($beginning_balance, 2); ?>
                                    </td>
                                </tr>

                                <!-- Add: Net Income/Loss -->
                                <tr>
                                    <td class="ps-4">
                                        <?php if ($net_income >= 0): ?>
                                            Add: Net Income for the Period
                                        <?php else: ?>
                                            Less: Net Loss for the Period
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php echo number_format(abs($net_income), 2); ?>
                                    </td>
                                </tr>

                                <!-- Owner's Transactions -->
                                <?php if ($owner_transactions != 0): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php if ($owner_transactions > 0): ?>
                                                Add: Owner's Investments
                                            <?php else: ?>
                                                Less: Owner's Withdrawals
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?php echo number_format(abs($owner_transactions), 2); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <!-- Ending Balance -->
                                <tr class="table-secondary">
                                    <td class="fw-bold">Owner's Equity, End of Period</td>
                                    <td class="text-end fw-bold">
                                        <?php echo number_format($ending_balance, 2); ?>
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
                        <li>This statement reflects changes in the owner's equity for the period from <?php echo date('F d, Y', strtotime($period['start'])); ?> to <?php echo date('F d, Y', strtotime($period['end'])); ?>.</li>
                        <li>Net <?php echo $net_income >= 0 ? 'income' : 'loss'; ?> is calculated from the Income Statement for the same period.</li>
                        <li>Owner's transactions include all capital contributions and withdrawals during the period.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight the active menu item
    document.querySelector('a[href="<?php echo BASE_URL; ?>/financial-statements/equity"]')
        .closest('li').classList.add('active');
});
</script> 