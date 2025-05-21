-- Reset tables
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE journal_items;
TRUNCATE TABLE journal_entries;
TRUNCATE TABLE accounts;
TRUNCATE TABLE account_types;
SET FOREIGN_KEY_CHECKS = 1;

-- Insert Account Types
INSERT INTO account_types (name, category) VALUES
('Current Assets', 'Asset'),
('Fixed Assets', 'Asset'),
('Current Liabilities', 'Liability'),
('Long-term Liabilities', 'Liability'),
('Owner\'s Equity', 'Equity'),
('Revenue', 'Revenue'),
('Operating Expenses', 'Expense');

-- Insert Accounts
INSERT INTO accounts (account_code, name, type_id) VALUES
-- Asset Accounts
('1000', 'Cash', 1),
('1100', 'Accounts Receivable', 1),
('1200', 'Supplies', 1),
('1300', 'Prepaid Insurance', 1),
('1500', 'Equipment', 2),
('1600', 'Building', 2),

-- Liability Accounts
('2000', 'Accounts Payable', 3),
('2100', 'Wages Payable', 3),
('2200', 'Interest Payable', 3),
('2500', 'Bank Loan', 4),

-- Equity Accounts
('3000', 'Owner\'s Capital', 5),
('3100', 'Retained Earnings', 5),
('3200', 'Owner\'s Drawing', 5),

-- Revenue Accounts
('4000', 'Service Revenue', 6),
('4100', 'Rental Revenue', 6),

-- Expense Accounts
('5000', 'Wages Expense', 7),
('5100', 'Rent Expense', 7),
('5200', 'Utilities Expense', 7),
('5300', 'Supplies Expense', 7),
('5400', 'Insurance Expense', 7);

-- Sample Journal Entries for January 2024
INSERT INTO journal_entries (date, reference_no, description, status) VALUES
('2024-01-01', 'JE-2024-001', 'Initial capital investment', 'Posted'),
('2024-01-05', 'JE-2024-002', 'Purchase of office equipment on credit', 'Posted'),
('2024-01-10', 'JE-2024-003', 'Cash received for services', 'Posted'),
('2024-01-15', 'JE-2024-004', 'Paid rent for January', 'Posted'),
('2024-01-20', 'JE-2024-005', 'Purchased supplies on account', 'Posted'),
('2024-01-25', 'JE-2024-006', 'Paid employee wages', 'Posted'),
('2024-01-28', 'JE-2024-007', 'Received rental income', 'Posted'),
('2024-01-30', 'JE-2024-008', 'Paid utility bill', 'Posted');

-- Journal Items for each entry
-- Initial capital investment
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(1, (SELECT id FROM accounts WHERE account_code = '1000'), 50000, 0), -- Cash
(1, (SELECT id FROM accounts WHERE account_code = '3000'), 0, 50000); -- Owner's Capital

-- Purchase of office equipment
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(2, (SELECT id FROM accounts WHERE account_code = '1500'), 15000, 0), -- Equipment
(2, (SELECT id FROM accounts WHERE account_code = '2000'), 0, 15000); -- Accounts Payable

-- Cash received for services
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(3, (SELECT id FROM accounts WHERE account_code = '1000'), 5000, 0), -- Cash
(3, (SELECT id FROM accounts WHERE account_code = '4000'), 0, 5000); -- Service Revenue

-- Paid rent
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(4, (SELECT id FROM accounts WHERE account_code = '5100'), 2000, 0), -- Rent Expense
(4, (SELECT id FROM accounts WHERE account_code = '1000'), 0, 2000); -- Cash

-- Purchased supplies
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(5, (SELECT id FROM accounts WHERE account_code = '1200'), 1500, 0), -- Supplies
(5, (SELECT id FROM accounts WHERE account_code = '2000'), 0, 1500); -- Accounts Payable

-- Paid wages
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(6, (SELECT id FROM accounts WHERE account_code = '5000'), 3000, 0), -- Wages Expense
(6, (SELECT id FROM accounts WHERE account_code = '1000'), 0, 3000); -- Cash

-- Received rental income
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(7, (SELECT id FROM accounts WHERE account_code = '1000'), 3500, 0), -- Cash
(7, (SELECT id FROM accounts WHERE account_code = '4100'), 0, 3500); -- Rental Revenue

-- Paid utilities
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(8, (SELECT id FROM accounts WHERE account_code = '5200'), 800, 0), -- Utilities Expense
(8, (SELECT id FROM accounts WHERE account_code = '1000'), 0, 800); -- Cash

-- Add more transactions for February 2024
INSERT INTO journal_entries (date, reference_no, description, status) VALUES
('2024-02-01', 'JE-2024-009', 'Received payment from client', 'Posted'),
('2024-02-05', 'JE-2024-010', 'Purchased insurance for the year', 'Posted'),
('2024-02-10', 'JE-2024-011', 'Paid partial amount to creditor', 'Posted'),
('2024-02-15', 'JE-2024-012', 'Received advance payment for services', 'Posted');

-- Journal Items for February entries
-- Received payment from client
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(9, (SELECT id FROM accounts WHERE account_code = '1000'), 4500, 0), -- Cash
(9, (SELECT id FROM accounts WHERE account_code = '4000'), 0, 4500); -- Service Revenue

-- Purchased insurance
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(10, (SELECT id FROM accounts WHERE account_code = '1300'), 6000, 0), -- Prepaid Insurance
(10, (SELECT id FROM accounts WHERE account_code = '1000'), 0, 6000); -- Cash

-- Paid creditor
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(11, (SELECT id FROM accounts WHERE account_code = '2000'), 5000, 0), -- Accounts Payable
(11, (SELECT id FROM accounts WHERE account_code = '1000'), 0, 5000); -- Cash

-- Received advance payment
INSERT INTO journal_items (journal_id, account_id, debit, credit) VALUES
(12, (SELECT id FROM accounts WHERE account_code = '1000'), 3000, 0), -- Cash
(12, (SELECT id FROM accounts WHERE account_code = '4000'), 0, 3000); -- Service Revenue 