<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: #343a40;
            color: #fff;
            transition: all 0.3s;
        }
        #sidebar.active {
            margin-left: -250px;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #2c3136;
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul li a {
            padding: 10px 20px;
            font-size: 1.1em;
            display: block;
            color: #fff;
            text-decoration: none;
        }
        #sidebar ul li a:hover {
            background: #2c3136;
        }
        #sidebar ul li.active > a {
            background: #2c3136;
        }
        .dropdown-toggle::after {
            display: block;
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
        }
        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .wrapper {
            display: flex;
            width: 100%;
        }
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
            }
            #sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><?php echo APP_NAME; ?></h3>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="<?php echo BASE_URL; ?>">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/journal">
                        <i class="fa fa-book"></i> Journal Entries
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/accounts">
                        <i class="fa fa-list"></i> Chart of Accounts
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/ledger">
                        <i class="fa fa-table"></i> General Ledger
                    </a>
                </li>
                <li>
                    <a href="#financialSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="fa fa-bar-chart"></i> Financial Statements
                    </a>
                    <ul class="collapse list-unstyled" id="financialSubmenu">
                        <li>
                            <a href="<?php echo BASE_URL; ?>/financial-statements/income">
                                <i class="fa fa-angle-right"></i> Income Statement
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/financial-statements/equity">
                                <i class="fa fa-angle-right"></i> Changes in Equity
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>/financial-statements/balance">
                                <i class="fa fa-angle-right"></i> Balance Sheet
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/closing">
                        <i class="fa fa-clock-o"></i> Closing Entries
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dark">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>
            </nav>
            
            <div class="container-fluid">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?> 