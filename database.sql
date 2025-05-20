-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS accounting_system;
USE accounting_system;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create chart_of_accounts table
CREATE TABLE IF NOT EXISTS chart_of_accounts (
    account_id INT PRIMARY KEY AUTO_INCREMENT,
    account_code VARCHAR(20) UNIQUE NOT NULL,
    account_name VARCHAR(100) NOT NULL,
    account_type ENUM('Asset', 'Liability', 'Equity', 'Revenue', 'Expense') NOT NULL,
    parent_account_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_account_id) REFERENCES chart_of_accounts(account_id)
);

-- Create journal_entries table
CREATE TABLE IF NOT EXISTS journal_entries (
    entry_id INT PRIMARY KEY AUTO_INCREMENT,
    entry_date DATE NOT NULL,
    reference_number VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Create journal_entry_details table
CREATE TABLE IF NOT EXISTS journal_entry_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    entry_id INT,
    account_id INT,
    debit_amount DECIMAL(15,2) DEFAULT 0.00,
    credit_amount DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (entry_id) REFERENCES journal_entries(entry_id),
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(account_id)
);

-- Create ledger table
CREATE TABLE IF NOT EXISTS ledger (
    ledger_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT,
    entry_date DATE NOT NULL,
    reference_number VARCHAR(50) NOT NULL,
    description TEXT,
    debit_amount DECIMAL(15,2) DEFAULT 0.00,
    credit_amount DECIMAL(15,2) DEFAULT 0.00,
    balance DECIMAL(15,2) DEFAULT 0.00,
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(account_id)
);

-- Create financial_periods table
CREATE TABLE IF NOT EXISTS financial_periods (
    period_id INT PRIMARY KEY AUTO_INCREMENT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_closed BOOLEAN DEFAULT FALSE,
    closed_at TIMESTAMP NULL,
    closed_by INT,
    FOREIGN KEY (closed_by) REFERENCES users(id)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, email) VALUES 
('admin', '$2y$10$8K1p/a0dR1xqM8K3qQZz1OQZz1OQZz1OQZz1OQZz1OQZz1OQZz1OQZ', 'admin@example.com');

-- Insert some default chart of accounts
INSERT INTO chart_of_accounts (account_code, account_name, account_type) VALUES
('1000', 'Cash', 'Asset'),
('1100', 'Accounts Receivable', 'Asset'),
('1200', 'Inventory', 'Asset'),
('2000', 'Accounts Payable', 'Liability'),
('2100', 'Loans Payable', 'Liability'),
('3000', 'Capital', 'Equity'),
('4000', 'Sales Revenue', 'Revenue'),
('5000', 'Cost of Goods Sold', 'Expense'),
('5100', 'Operating Expenses', 'Expense'); 