<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Chart of Accounts</h2>
                <a href="<?php echo BASE_URL; ?>/accounts/create" class="btn btn-primary">
                    <i class="fa fa-plus"></i> New Account
                </a>
            </div>

            <!-- Account Categories -->
            <div class="row">
                <?php foreach ($accounts as $category => $categoryAccounts): ?>
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0"><?php echo $category; ?></h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Account Code</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th class="text-end">Balance</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categoryAccounts as $account): ?>
                                        <tr>
                                            <td><?php echo $account['account_code']; ?></td>
                                            <td><?php echo $account['name']; ?></td>
                                            <td><?php echo $account['type_name']; ?></td>
                                            <td class="text-end">
                                                <?php echo number_format($account['balance'], 2); ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?php echo BASE_URL; ?>/ledger?account_id=<?php echo $account['id']; ?>" 
                                                   class="btn btn-sm btn-info text-white" title="View Ledger">
                                                    <i class="fa fa-book"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/accounts/edit/<?php echo $account['id']; ?>" 
                                                   class="btn btn-sm btn-warning" title="Edit Account">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Quick Summary -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Account Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6>Total Assets</h6>
                                    <h4>
                                        <?php
                                        $totalAssets = 0;
                                        if (isset($accounts['Asset'])) {
                                            foreach ($accounts['Asset'] as $account) {
                                                $totalAssets += $account['balance'];
                                            }
                                        }
                                        echo number_format($totalAssets, 2);
                                        ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6>Total Liabilities</h6>
                                    <h4>
                                        <?php
                                        $totalLiabilities = 0;
                                        if (isset($accounts['Liability'])) {
                                            foreach ($accounts['Liability'] as $account) {
                                                $totalLiabilities += $account['balance'];
                                            }
                                        }
                                        echo number_format($totalLiabilities, 2);
                                        ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6>Total Equity</h6>
                                    <h4>
                                        <?php
                                        $totalEquity = 0;
                                        if (isset($accounts['Equity'])) {
                                            foreach ($accounts['Equity'] as $account) {
                                                $totalEquity += $account['balance'];
                                            }
                                        }
                                        echo number_format($totalEquity, 2);
                                        ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning">
                                <div class="card-body">
                                    <h6>Account Balance</h6>
                                    <h4>
                                        <?php
                                        $difference = $totalAssets - ($totalLiabilities + $totalEquity);
                                        echo number_format(abs($difference), 2);
                                        if ($difference != 0) {
                                            echo ' <i class="fa fa-warning text-danger" title="Accounts are not balanced"></i>';
                                        }
                                        ?>
                                    </h4>
                                </div>
                            </div>
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
    document.querySelector('a[href="<?php echo BASE_URL; ?>/accounts"]')
        .closest('li').classList.add('active');
});
</script> 