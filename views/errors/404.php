<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>

<div class="text-center mt-5">
    <h1 class="display-1">404</h1>
    <h2 class="mb-4">Page Not Found</h2>
    <p class="lead mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
    <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">
        <i class="fa fa-home"></i> Go to Homepage
    </a>
</div> 