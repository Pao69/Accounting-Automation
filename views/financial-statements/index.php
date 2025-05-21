<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-4">Financial Statements</h2>

            <div class="row">
                <!-- Income Statement Card -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fa fa-line-chart text-primary"></i> Income Statement
                            </h5>
                            <p class="card-text">
                                View the company's financial performance, including revenues, expenses, and net income/loss for a specific period.
                            </p>
                            <a href="<?php echo BASE_URL; ?>/financial-statements/income" class="btn btn-primary">
                                View Statement
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statement of Changes in Owner's Equity Card -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fa fa-exchange text-success"></i> Changes in Owner's Equity
                            </h5>
                            <p class="card-text">
                                Track changes in owner's equity, including investments, withdrawals, and net income/loss impact.
                            </p>
                            <a href="<?php echo BASE_URL; ?>/financial-statements/equity" class="btn btn-success">
                                View Statement
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Balance Sheet Card -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fa fa-balance-scale text-info"></i> Balance Sheet
                            </h5>
                            <p class="card-text">
                                See the company's financial position, including assets, liabilities, and owner's equity at a specific date.
                            </p>
                            <a href="<?php echo BASE_URL; ?>/financial-statements/balance" class="btn btn-info text-white">
                                View Statement
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Tips Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fa fa-lightbulb-o"></i> Quick Tips
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Income Statement</h6>
                            <ul>
                                <li>Shows profitability over time</li>
                                <li>Includes all revenues and expenses</li>
                                <li>Helps track business performance</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Changes in Owner's Equity</h6>
                            <ul>
                                <li>Tracks owner's investments</li>
                                <li>Records withdrawals</li>
                                <li>Shows impact of operations</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Balance Sheet</h6>
                            <ul>
                                <li>Snapshot of financial position</li>
                                <li>Must always balance</li>
                                <li>Shows what you own and owe</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight the active menu item
    document.querySelector('a[href="#financialSubmenu"]').click();
});
</script> 