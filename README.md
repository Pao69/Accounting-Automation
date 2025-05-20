# Accounting System

This is a simple web-based accounting system developed using PHP and Bootstrap.

## Features

-   **Dashboard**: Overview of key accounting metrics.
-   **Journal Entry**: Record daily transactions.
-   **Ledger**: View detailed transactions for each account.
-   **Trial Balance**: Summarize debit and credit balances for all accounts.
-   **Income Statement**: Report on revenues, expenses, and net income.
-   **Balance Sheet**: Present assets, liabilities, and equity.
-   **Chart of Accounts**: Manage accounts within the system.

## Setup Instructions

1.  **Prerequisites**:
    *   A web server with PHP support (e.g., Apache, Nginx).
    *   MySQL database server.
    *   phpMyAdmin or a similar tool for database management.

2.  **Clone or Download the Project**:
    *   Place the project files in your web server's document root directory (e.g., `htdocs` for XAMPP).

3.  **Database Setup**:
    *   Open your database management tool (e.g., phpMyAdmin).
    *   Create a new database named `accounting_system`.
    *   Import the `database.sql` file located in the project's root directory. This will create the necessary tables and populate some initial data (admin user and default accounts).

4.  **Configure Database Connection**:
    *   Open the `config/database.php` file.
    *   Update the database connection details (`DB_SERVER`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME`) if they are different from your MySQL setup.

5.  **Run the Application**:
    *   Open your web browser and navigate to the URL where you placed the project files (e.g., `http://localhost/accounting_system/`).

## Usage

-   The system features a sidebar navigation on the left for easy access to all pages.
-   Currently, there is no login requirement, making it suitable for demonstration or exam purposes.
-   Use the respective pages to record journal entries, view financial statements, and manage your chart of accounts.

## Note

This system was developed for educational purposes and has had the login requirement removed to simplify usage for exams. 