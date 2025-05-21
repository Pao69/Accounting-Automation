<h2>Journal Entries</h2>

<div class="mb-3">
    <a href="<?php echo BASE_URL; ?>/journal/create" class="btn btn-primary">
        <i class="fa fa-plus"></i> New Journal Entry
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($journals)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No journal entries found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($journals as $journal): ?>
                            <tr>
                                <td><?php echo date('Y-m-d', strtotime($journal['date'])); ?></td>
                                <td><?php echo htmlspecialchars($journal['reference_no']); ?></td>
                                <td><?php echo htmlspecialchars($journal['description']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $journal['status'] === 'Posted' ? 'success' : 'warning'; ?>">
                                        <?php echo $journal['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($journal['total_amount'], 2); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/journal/view/<?php echo $journal['id']; ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <?php if ($journal['status'] !== 'Posted'): ?>
                                        <a href="<?php echo BASE_URL; ?>/journal/edit/<?php echo $journal['id']; ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/journal/post/<?php echo $journal['id']; ?>" 
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Are you sure you want to post this entry?')">
                                            <i class="fa fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> 