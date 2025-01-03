# Gas-Filling-Station-Salesforce

A comprehensive Customer Relationship Management (CRM) system for gas stations, built with PHP, MySQL, and Bootstrap.

## Features

- Dashboard with real-time analytics
- Fuel inventory management
- Customer management
- Sales tracking
- Employee management
- Reports generation
- Payment processing
- Loyalty program

## Technology Stack

- Frontend: HTML5, CSS3, JavaScript, Bootstrap 5
- Backend: PHP 8.0
- Database: MySQL
- Additional: Chart.js for analytics, FPDF for report generation

## Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/gas-station-crm.git
```

2. Import the database
```bash
mysql -u username -p database_name < database/gas_station_db.sql
```

3. Configure database connection
- Navigate to `config/database.php`
- Update the database credentials

4. Start the application
```bash
php -S localhost:8000
```

## Directory Structure

```
gas-station-crm/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   └── footer.php
├── modules/
│   ├── customers/
│   ├── inventory/
│   ├── sales/
│   └── reports/
├── database/
│   └── gas_station_db.sql
└── index.php
```

## Screenshots

1. Dashboard
   - Real-time fuel inventory levels
   - Daily sales overview
   - Customer activity metrics

2. Inventory Management
   - Current stock levels
   - Price management
   - Low stock alerts

3. Customer Management
   - Customer profiles
   - Purchase history
   - Loyalty points tracking

4. Reports
   - Sales reports
   - Inventory reports
   - Customer analytics

## Security Features

- Role-based access control
- Secure password hashing
- SQL injection prevention
- XSS protection
- CSRF protection

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a pull request
