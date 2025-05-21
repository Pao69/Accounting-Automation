<h2>New Journal Entry</h2>

<div class="card">
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/journal/create" method="POST" id="journalForm">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required 
                           value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4">
                    <label for="reference_no" class="form-label">Reference No.</label>
                    <input type="text" class="form-control" id="reference_no" name="reference_no" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2" required></textarea>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-bordered" id="entriesTable">
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
                                <select name="items[0][account_id]" class="form-select" required>
                                    <option value="">Select Account</option>
                                    <?php foreach ($accounts as $account): ?>
                                        <option value="<?php echo $account['id']; ?>">
                                            <?php echo $account['account_code'] . ' - ' . $account['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][debit]" class="form-control debit" step="0.01" min="0">
                            </td>
                            <td>
                                <input type="number" name="items[0][credit]" class="form-control credit" step="0.01" min="0">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <button type="button" class="btn btn-success btn-sm" id="addRow">
                                    <i class="fa fa-plus"></i> Add Row
                                </button>
                            </td>
                            <td class="text-end"><strong>Total:</strong> <span id="totalDebit">0.00</span></td>
                            <td class="text-end"><strong>Total:</strong> <span id="totalCredit">0.00</span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-end">
                <a href="<?php echo BASE_URL; ?>/journal" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Journal Entry</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    let rowCount = 1;

    // Add new row
    $('#addRow').click(function() {
        // Get the account options from the first row's select element
        const accountOptions = $('select[name="items[0][account_id]"]').html();
        
        const newRow = `
            <tr>
                <td>
                    <select name="items[${rowCount}][account_id]" class="form-select" required>
                        ${accountOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][debit]" class="form-control debit" step="0.01" min="0">
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][credit]" class="form-control credit" step="0.01" min="0">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#entriesTable tbody').append(newRow);
        rowCount++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        if ($('#entriesTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotals();
        }
    });

    // Calculate totals
    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        $('.debit').each(function() {
            totalDebit += parseFloat($(this).val() || 0);
        });

        $('.credit').each(function() {
            totalCredit += parseFloat($(this).val() || 0);
        });

        $('#totalDebit').text(totalDebit.toFixed(2));
        $('#totalCredit').text(totalCredit.toFixed(2));
    }

    // Update totals when values change
    $(document).on('input', '.debit, .credit', calculateTotals);

    // Form validation
    $('#journalForm').submit(function(e) {
        const totalDebit = parseFloat($('#totalDebit').text());
        const totalCredit = parseFloat($('#totalCredit').text());

        if (totalDebit !== totalCredit) {
            e.preventDefault();
            alert('Total debits must equal total credits!');
            return false;
        }
        return true;
    });
});
</script> 