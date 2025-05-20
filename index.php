<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Accounting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #2c3e50;
            padding-top: 1rem;
            transition: all 0.3s;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 1rem;
            color: white;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-menu {
            padding: 1rem 0;
        }
        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        .sidebar-menu .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .sidebar-menu .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left: 4px solid #3498db;
        }
        .sidebar-menu .nav-link i {
            margin-right: 0.8rem;
            font-size: 1.1rem;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: all 0.3s;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
        }
        .stat-card {
            border-radius: 0.5rem;
            padding: 1.5rem;
            color: white;
            margin-bottom: 1.5rem;
        }
        .stat-card i {
            font-size: 2rem;
            opacity: 0.8;
        }
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0.5rem 0;
        }
        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .quick-actions .btn {
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: left;
        }
        .quick-actions .btn i {
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }
        .content-area {
            display: none;
        }
        .content-area.active {
            display: block;
        }
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loading.active {
            display: flex;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .main-content.active {
                margin-left: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Spinner -->
    <div class="loading">
        <div class="spinner"></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0">
                <i class="bi bi-calculator me-2"></i>Accounting
            </h4>
        </div>
        <div class="sidebar-menu">
            <a class="nav-link active" data-page="dashboard">
                <i class="bi bi-speedometer2"></i>Dashboard
            </a>
            <a class="nav-link" data-page="journal_entry">
                <i class="bi bi-journal-text"></i>Journal Entry
            </a>
            <a class="nav-link" data-page="ledger">
                <i class="bi bi-book"></i>Ledger
            </a>
            <a class="nav-link" data-page="trial_balance">
                <i class="bi bi-scale"></i>Trial Balance
            </a>
            <a class="nav-link" data-page="income_statement">
                <i class="bi bi-graph-up"></i>Income Statement
            </a>
            <a class="nav-link" data-page="balance_sheet">
                <i class="bi bi-file-earmark-text"></i>Balance Sheet
            </a>
            <a class="nav-link" data-page="chart_of_accounts">
                <i class="bi bi-list-ul"></i>Chart of Accounts
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 id="page-title">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </h2>
            <button class="btn btn-primary d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <!-- Content Areas -->
        <div id="dashboard" class="content-area active">
            <?php include 'dashboard.php'; ?>
        </div>

        <!-- Other content areas will be loaded here -->
        <div id="journal_entry" class="content-area"></div>
        <div id="ledger" class="content-area"></div>
        <div id="trial_balance" class="content-area"></div>
        <div id="income_statement" class="content-area"></div>
        <div id="balance_sheet" class="content-area"></div>
        <div id="chart_of_accounts" class="content-area"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        });

        // Function to load page content
        function loadPage(page) {
            const loading = document.querySelector('.loading');
            const contentArea = document.getElementById(page);
            const pageTitle = document.getElementById('page-title');
            
            // Show loading spinner
            loading.classList.add('active');
            
            // Update active nav link
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-page') === page) {
                    link.classList.add('active');
                }
            });

            // Update page title
            const titles = {
                'dashboard': '<i class="bi bi-speedometer2 me-2"></i>Dashboard',
                'journal_entry': '<i class="bi bi-journal-text me-2"></i>Journal Entry',
                'ledger': '<i class="bi bi-book me-2"></i>Ledger',
                'trial_balance': '<i class="bi bi-scale me-2"></i>Trial Balance',
                'income_statement': '<i class="bi bi-graph-up me-2"></i>Income Statement',
                'balance_sheet': '<i class="bi bi-file-earmark-text me-2"></i>Balance Sheet',
                'chart_of_accounts': '<i class="bi bi-list-ul me-2"></i>Chart of Accounts'
            };
            pageTitle.innerHTML = titles[page];

            // Hide all content areas
            document.querySelectorAll('.content-area').forEach(area => {
                area.classList.remove('active');
            });

            // Load content via AJAX
            fetch(page + '.php')
                .then(response => response.text())
                .then(html => {
                    contentArea.innerHTML = html;
                    contentArea.classList.add('active');
                    loading.classList.remove('active');
                })
                .catch(error => {
                    console.error('Error loading page:', error);
                    loading.classList.remove('active');
                });
        }

        // Add click event listeners to all nav links
        document.querySelectorAll('.nav-link, .quick-actions .btn').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                if (page) {
                    loadPage(page);
                }
            });
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function(e) {
            const page = e.state ? e.state.page : 'dashboard';
            loadPage(page);
        });
    </script>
</body>
</html> 