# Accounting Automation System

A PHP-based accounting system designed for educational purposes, specifically for group-based accounting exercises and financial statement generation.

## System Overview

The system automates the accounting cycle and generates financial statements based on journal entries. Each group can maintain their own set of journal entries and generate their own financial reports.

## Features

### 1. Chart of Accounts
- Predefined account categories (Asset, Liability, Equity, Revenue, Expense)
- Account management (create, edit, view accounts)
- Account code and balance tracking

### 2. Journal Entries
- Create and manage 15-20 journal entries per group
- Automatic double-entry validation
- Support for debit and credit entries
- Reference number tracking
- Transaction descriptions

### 3. General Ledger
- T-account format
- Running balance calculation
- Transaction history by account
- Account statement generation

### 4. Financial Statements

The system generates six essential sheets for financial reporting:

1. **Journal Entries Sheet**
   - Chronological record of all transactions
   - Debit and credit columns
   - Transaction descriptions and references

2. **Ledger/T-Account Sheet**
   - Individual account transactions
   - Running balances
   - Debit and credit history

3. **Income Statement**
   - Revenue section
   - Expense section
   - Net income/loss calculation

4. **Statement of Changes in Equity**
   - Beginning balance
   - Net income/loss
   - Owner's withdrawals/investments
   - Ending balance

5. **Balance Sheet**
   - Assets section
   - Liabilities section
   - Owner's equity section

6. **Closing Entries Sheet**
   - Revenue closing entries
   - Expense closing entries
   - Income summary
   - Retained earnings updates

### 5. Group Management
- Each group maintains separate journal entries
- Individual financial statement generation
- Group-specific reporting

## Technical Requirements

- PHP 7.4 or higher
- MySQL/MariaDB
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. Clone the repository
2. Import the database schema (setup.sql)
3. Configure database connection in config/config.php
4. Set up virtual host or place in web server directory
5. Access through web browser

## Usage Instructions

### 1. Initial Setup
- Create a group for your accounting exercises
- Verify the chart of accounts
- Ensure Retained Earnings account exists in Equity category

### 2. Recording Transactions
- Enter 15-20 journal entries with proper documentation
- Ensure each entry follows double-entry principles
- Include clear descriptions and references

### 3. Generating Reports
- Access the Financial Statements section
- Select your group
- Choose the reporting period
- Generate all six required sheets
- Export to Excel for submission

### 4. Year-End Closing
- Verify all transactions are posted
- Run the closing process
- Generate closing entries
- Update retained earnings

## Best Practices

1. **Journal Entries**
   - Use clear, descriptive narratives
   - Include proper references
   - Verify debits equal credits
   - Document any special transactions

2. **Account Management**
   - Use standardized account codes
   - Maintain proper categorization
   - Regular balance verification

3. **Financial Reporting**
   - Regular backup of data
   - Verify report accuracy
   - Cross-check between statements
   - Save exports with clear naming

## Support

For technical support or questions:
- Check documentation in the /docs folder
- Contact system administrator
- Report issues through the issue tracker

## License

This project is licensed for educational purposes only. All rights reserved. 