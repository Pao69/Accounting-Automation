<h2>Dashboard</h2>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Assets</h5>
                <h3 class="mb-0">$<?php echo number_format($total_assets ?? 0, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">Total Liabilities</h5>
                <h3 class="mb-0">$<?php echo number_format($total_liabilities ?? 0, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Equity</h5>
                <h3 class="mb-0">$<?php echo number_format($total_equity ?? 0, 2); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Journal Entries</h5>
                <a href="<?php echo BASE_URL; ?>/journal" class="btn btn-primary btn-sm">
                    <i class="fa fa-list"></i> View All
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_journals)): ?>
                    <p class="text-center text-muted">No recent journal entries found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_journals as $journal): ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d', strtotime($journal['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($journal['reference_no']); ?></td>
                                        <td><?php echo htmlspecialchars($journal['description']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $journal['status'] === 'Posted' ? 'success' : 'warning'; ?>">
                                                <?php echo $journal['status']; ?>
                                            </span>
                                        </td>
                                        <td>$<?php echo number_format($journal['total_amount'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
 