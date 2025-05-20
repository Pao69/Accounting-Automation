<?php
require_once 'config/database.php';

// Get total accounts
$sql = "SELECT COUNT(*) as total FROM chart_of_accounts WHERE is_active = 1";
$totalAccounts = $conn->query($sql)->fetch_assoc()['total'];

// Get total journal entries
$sql = "SELECT COUNT(*) as total FROM journal_entries";
$totalEntries = $conn->query($sql)->fetch_assoc()['total'];

// Get recent journal entries
$sql = "SELECT je.*, u.username as created_by_name 
        FROM journal_entries je 
        LEFT JOIN users u ON je.created_by = u.id 
        ORDER BY je.entry_date DESC, je.entry_id DESC 
        LIMIT 5";
$recentEntries = $conn->query($sql);

// Get account balances
$sql = "SELECT coa.account_code, coa.account_name, coa.account_type, 
        SUM(l.balance) as current_balance
        FROM chart_of_accounts coa
        LEFT JOIN ledger l ON coa.account_id = l.account_id
        WHERE coa.is_active = 1
        GROUP BY coa.account_id
        ORDER BY coa.account_type, coa.account_code
        LIMIT 5";
$accountBalances = $conn->query($sql);
?>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <h2 class="mb-0">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard Overview
                </h2>
                <p class="text-muted mt-2">Welcome to your accounting system dashboard</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
            <i class="bi bi-list-ul"></i>
            <div class="stat-value"><?php echo $totalAccounts; ?></div>
            <div class="stat-label">Active Accounts</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
            <i class="bi bi-journal-text"></i>
            <div class="stat-value"><?php echo $totalEntries; ?></div>
            <div class="stat-label">Journal Entries</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);">
            <i class="bi bi-calendar-check"></i>
            <div class="stat-value"><?php echo date('M Y'); ?></div>
            <div class="stat-label">Current Period</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body quick-actions">
                <a class="btn btn-primary w-100" data-page="journal_entry">
                    <i class="bi bi-journal-plus"></i>New Journal Entry
                </a>
                <a class="btn btn-success w-100" data-page="chart_of_accounts">
                    <i class="bi bi-plus-circle"></i>Add New Account
                </a>
                <a class="btn btn-info w-100 text-white" data-page="trial_balance">
                    <i class="bi bi-scale"></i>View Trial Balance
                </a>
                <a class="btn btn-warning w-100 text-white" data-page="income_statement">
                    <i class="bi bi-graph-up"></i>View Income Statement
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Journal Entries -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Recent Journal Entries
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Description</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($entry = $recentEntries->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($entry['entry_date'])); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?php echo $entry['reference_number']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $entry['description']; ?></td>
                                    <td><?php echo $entry['created_by_name'] ?? 'System'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Account Balances -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-wallet2 me-2"></i>Account Balances
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Type</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($account = $accountBalances->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $account['account_code']; ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo $account['account_name']; ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo $account['account_type']; ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <?php echo number_format($account['current_balance'], 2); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 