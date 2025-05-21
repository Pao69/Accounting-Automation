<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Edit Account</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>/accounts/edit/<?php echo $account['id']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="account_code" class="form-label">Account Code</label>
                            <input type="text" class="form-control" id="account_code" name="account_code" 
                                   value="<?php echo $account['account_code']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Account Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo $account['name']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="type_id" class="form-label">Account Type</label>
                            <select class="form-select" id="type_id" name="type_id" required>
                                <option value="">Select Account Type</option>
                                <?php foreach ($account_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>" 
                                        <?php echo ($type['id'] == $account['type_id']) ? 'selected' : ''; ?>>
                                    <?php echo $type['category'] . ' - ' . $type['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo BASE_URL; ?>/accounts" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Account</button>
                        </div>
                    </form>
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