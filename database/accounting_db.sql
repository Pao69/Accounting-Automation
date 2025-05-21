-- Create database if not exists
CREATE DATABASE IF NOT EXISTS accounting_db;
USE accounting_db;

-- Chart of Accounts
CREATE TABLE IF NOT EXISTS account_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    category ENUM('Asset', 'Liability', 'Equity', 'Revenue', 'Expense') NOT NULL
);

CREATE TABLE IF NOT EXISTS accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    type_id INT NOT NULL,
    balance DECIMAL(15, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_id) REFERENCES account_types(id)
);

-- Journal Entries
CREATE TABLE IF NOT EXISTS journal_entries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL,
    reference_no VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('Draft', 'Posted') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    posted_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS journal_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    journal_id INT NOT NULL,
    account_id INT NOT NULL,
    debit DECIMAL(15, 2) DEFAULT 0.00,
    credit DECIMAL(15, 2) DEFAULT 0.00,
    FOREIGN KEY (journal_id) REFERENCES journal_entries(id),
    FOREIGN KEY (account_id) REFERENCES accounts(id)
);

-- Fiscal Periods
CREATE TABLE IF NOT EXISTS fiscal_periods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Open', 'Closed') DEFAULT 'Open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default account types
INSERT INTO account_types (name, category) VALUES
('Current Assets', 'Asset'),
('Fixed Assets', 'Asset'),
('Current Liabilities', 'Liability'),
('Long-term Liabilities', 'Liability'),
('Owner\'s Equity', 'Equity'),
('Revenue', 'Revenue'),
('Operating Expenses', 'Expense');

-- Insert some common accounts
INSERT INTO accounts (account_code, name, type_id) VALUES
('1000', 'Cash', 1),
('1100', 'Accounts Receivable', 1),
('1200', 'Inventory', 1),
('1500', 'Equipment', 2),
('2000', 'Accounts Payable', 3),
('3000', 'Owner\'s Capital', 5),
('4000', 'Sales Revenue', 6),
('5000', 'Cost of Goods Sold', 7),
('5100', 'Salaries Expense', 7),
('5200', 'Rent Expense', 7); 