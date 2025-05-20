<?php
session_start();
require_once 'config/database.php';
require_once 'classes/Accounting.php';

$accounting = new Accounting($conn);

// Get all accounts for dropdown
$sql = "SELECT account_id, account_code, account_name FROM chart_of_accounts WHERE is_active = 1 ORDER BY account_code";
$accounts = $conn->query($sql);

// Get recent journal entries
$entries = $conn->query("
    SELECT je.*, 
           COUNT(jed.detail_id) as entry_count,
           SUM(jed.debit_amount) as total_debit,
           SUM(jed.credit_amount) as total_credit
    FROM journal_entries je
    LEFT JOIN journal_entry_details jed ON je.entry_id = jed.entry_id
    GROUP BY je.entry_id
    ORDER BY je.entry_date DESC, je.entry_id DESC
    LIMIT 10
");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $reference = $_POST['reference'];
            $date = $_POST['date'];
            $description = $_POST['description'];
            $accounts = $_POST['accounts'];
            $debits = $_POST['debits'];
            $credits = $_POST['credits'];

            // Start transaction
            $conn->begin_transaction();

            try {
                // Insert journal entry
                $stmt = $conn->prepare("INSERT INTO journal_entries (reference_number, entry_date, description) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $reference, $date, $description);
                $stmt->execute();
                $entry_id = $conn->insert_id;

                // Insert journal entry details
                $stmt = $conn->prepare("INSERT INTO journal_entry_details (entry_id, account_id, debit_amount, credit_amount) VALUES (?, ?, ?, ?)");
                
                for ($i = 0; $i < count($accounts); $i++) {
                    if ($accounts[$i] && ($debits[$i] > 0 || $credits[$i] > 0)) {
                        $stmt->bind_param("iidd", $entry_id, $accounts[$i], $debits[$i], $credits[$i]);
                        $stmt->execute();

                        // Update ledger
                        $balance = $debits[$i] - $credits[$i];
                        $stmt2 = $conn->prepare("INSERT INTO ledger (account_id, entry_id, debit_amount, credit_amount, balance) VALUES (?, ?, ?, ?, ?)");
                        $stmt2->bind_param("iiddd", $accounts[$i], $entry_id, $debits[$i], $credits[$i], $balance);
                        $stmt2->execute();
                    }
                }

                $conn->commit();
                $success = "Journal entry created successfully!";
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Error creating journal entry: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Entry - Accounting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Accounting System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="journal_entry.php">Journal Entry</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ledger.php">Ledger</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trial_balance.php">Trial Balance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="income_statement.php">Income Statement</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="balance_sheet.php">Balance Sheet</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Create Journal Entry</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-journal-plus me-2"></i>New Journal Entry
                        </h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newEntryModal">
                            <i class="bi bi-plus-circle me-2"></i>Create New Entry
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Description</th>
                                        <th>Entries</th>
                                        <th>Total Debit</th>
                                        <th>Total Credit</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($entry = $entries->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($entry['entry_date'])); ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?php echo $entry['reference_number']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $entry['description']; ?></td>
                                            <td><?php echo $entry['entry_count']; ?></td>
                                            <td class="text-end"><?php echo number_format($entry['total_debit'], 2); ?></td>
                                            <td class="text-end"><?php echo number_format($entry['total_credit'], 2); ?></td>
                                            <td>
                                                <?php if ($entry['total_debit'] == $entry['total_credit']): ?>
                                                    <span class="badge bg-success">Balanced</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Unbalanced</span>
                                                <?php endif; ?>
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

        <!-- New Entry Modal -->
        <div class="modal fade" id="newEntryModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-journal-plus me-2"></i>New Journal Entry
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" id="journalEntryForm">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="create">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" name="reference" required 
                                           value="JE-<?php echo date('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" name="date" required 
                                           value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="2" required></textarea>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="entryTable">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="form-select" name="accounts[]" required>
                                                    <option value="">Select Account</option>
                                                    <?php while ($account = $accounts->fetch_assoc()): ?>
                                                        <option value="<?php echo $account['account_id']; ?>">
                                                            <?php echo $account['account_code'] . ' - ' . $account['account_name']; ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control debit-amount" name="debits[]" 
                                                       step="0.01" min="0" value="0">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control credit-amount" name="credits[]" 
                                                       step="0.01" min="0" value="0">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <button type="button" class="btn btn-success btn-sm" id="addRow">
                                                    <i class="bi bi-plus-circle me-2"></i>Add Row
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        Total Debit: <span id="totalDebit">0.00</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        Total Credit: <span id="totalCredit">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Entry</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('journalEntryForm');
        const table = document.getElementById('entryTable');
        const tbody = table.querySelector('tbody');
        const addRowBtn = document.getElementById('addRow');
        const accounts = <?php echo json_encode($accounts->fetch_all(MYSQLI_ASSOC)); ?>;

        // Add new row
        addRowBtn.addEventListener('click', function() {
            const newRow = tbody.rows[0].cloneNode(true);
            newRow.querySelectorAll('input').forEach(input => input.value = '0');
            newRow.querySelector('select').value = '';
            tbody.appendChild(newRow);
            updateTotals();
        });

        // Remove row
        tbody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                if (tbody.rows.length > 1) {
                    e.target.closest('tr').remove();
                    updateTotals();
                }
            }
        });

        // Update totals when amounts change
        tbody.addEventListener('input', function(e) {
            if (e.target.classList.contains('debit-amount') || e.target.classList.contains('credit-amount')) {
                const row = e.target.closest('tr');
                const debitInput = row.querySelector('.debit-amount');
                const creditInput = row.querySelector('.credit-amount');

                if (e.target.classList.contains('debit-amount') && e.target.value > 0) {
                    creditInput.value = '0';
                } else if (e.target.classList.contains('credit-amount') && e.target.value > 0) {
                    debitInput.value = '0';
                }

                updateTotals();
            }
        });

        // Update totals
        function updateTotals() {
            let totalDebit = 0;
            let totalCredit = 0;

            tbody.querySelectorAll('tr').forEach(row => {
                totalDebit += parseFloat(row.querySelector('.debit-amount').value) || 0;
                totalCredit += parseFloat(row.querySelector('.credit-amount').value) || 0;
            });

            document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
            document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);
        }

        // Form validation
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const totalDebit = parseFloat(document.getElementById('totalDebit').textContent);
            const totalCredit = parseFloat(document.getElementById('totalCredit').textContent);

            if (totalDebit !== totalCredit) {
                alert('Total debits must equal total credits!');
                return;
            }

            if (totalDebit === 0) {
                alert('Please enter at least one debit or credit amount!');
                return;
            }

            this.submit();
        });
    });
    </script>
</body>
</html> 