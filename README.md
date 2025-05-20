# 📊 Accounting System

A modern web-based accounting system developed using PHP and Bootstrap, designed to streamline financial management and reporting.

## 🚀 Features

### 📈 Core Functionality
- **Dashboard**: Real-time overview of key financial metrics and system status
- **Journal Entry**: Record and manage daily financial transactions
- **Ledger**: Detailed transaction history and account balances
- **Trial Balance**: Comprehensive summary of all account balances
- **Income Statement**: Revenue, expense, and net income reporting
- **Balance Sheet**: Assets, liabilities, and equity presentation
- **Chart of Accounts**: Complete account management system

### 💡 Key Benefits
- User-friendly interface with modern design
- Real-time financial data updates
- Comprehensive financial reporting
- Efficient transaction management
- Secure data handling

## 🛠️ Technical Requirements

### 🔧 Prerequisites
- Web server (Apache/Nginx) with PHP 7.4 or higher
- MySQL 5.7 or higher
- phpMyAdmin (recommended for database management)
- Modern web browser (Chrome, Firefox, Safari, Edge)

### 📦 Required PHP Extensions
- PDO
- MySQLi
- JSON
- BCMath (for precise financial calculations)

## 🚀 Installation Guide

### 1️⃣ Server Setup
1. Ensure your web server (Apache/Nginx) is running
2. Verify PHP and MySQL are properly installed
3. Enable required PHP extensions

### 2️⃣ Project Deployment
1. Clone or download the project files
2. Place files in your web server's document root:
   - For XAMPP: `htdocs/accounting_system/`
   - For WAMP: `www/accounting_system/`
   - For Linux: `/var/www/html/accounting_system/`

### 3️⃣ Database Configuration
1. Create a new MySQL database named `accounting_system`
2. Import the `database.sql` file:
   ```bash
   mysql -u username -p accounting_system < database.sql
   ```
   Or use phpMyAdmin to import the file

### 4️⃣ System Configuration
1. Navigate to `config/database.php`
2. Update database credentials:
   ```php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'your_username');
   define('DB_PASSWORD', 'your_password');
   define('DB_NAME', 'accounting_system');
   ```

## 📱 Usage Guide

### 🔑 Getting Started
1. Access the system through your web browser
2. Navigate to the dashboard for an overview
3. Use the sidebar menu for quick access to all features

### 📝 Common Operations
1. **Creating Journal Entries**
   - Click "New Journal Entry" from dashboard
   - Fill in transaction details
   - Add debit and credit entries
   - Save and post the entry

2. **Managing Accounts**
   - Access Chart of Accounts
   - Add new accounts with proper codes
   - Modify existing accounts as needed

3. **Generating Reports**
   - Select desired report type
   - Choose date range
   - View or export report

## 🔒 Security Considerations

### 🛡️ Best Practices
- Regularly backup your database
- Keep PHP and MySQL updated
- Use strong passwords
- Implement SSL for production use
- Regular security audits

### ⚠️ Important Notes
- This is a demonstration system
- Add authentication for production use
- Implement proper access controls
- Regular data backups recommended

## 🤝 Contributing

### 📋 Guidelines
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

### 🐛 Bug Reports
- Use the issue tracker
- Provide detailed description
- Include steps to reproduce
- Specify environment details

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📞 Support

For support, please:
- Check the documentation
- Open an issue
- Contact the development team

---

Made with ❤️ for better accounting management 