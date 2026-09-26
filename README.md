## Pharmacy Management System
A robust Pharmacy Management Application built using native PHP, MySQL, and styled with Bootstrap 5. This system is designed to streamline inventory control, automate sales processing, and secure user access through strict role-based permissions. It helps pharmacies reduce manual errors, track stock levels in real time, and maintain optimal operational efficiency.
------------------------------
## 🚀 Core Features & Architecture
The application is structured around a secure, role-based architecture featuring two distinct user levels: Admin (Sudo User) and Pharmacist.

                                  ┌───────────────────────────┐
                                  │   Pharmacy Management     │
                                  └─────────────┬─────────────┘
                                                │
                       ┌────────────────────────┴────────────────────────┐
                       ▼                                                 ▼
          ┌─────────────────────────┐                       ┌─────────────────────────┐
          │    Admin (Sudo User)    │                       │       Pharmacist        │
          └────────────┬────────────┘                       └────────────┬────────────┘
                       │                                                 │
      ┌────────────────┼────────────────┐               ┌────────────────┼────────────────┐
      ▼                ▼                ▼               ▼                ▼                ▼
┌───────────┐    ┌───────────┐    ┌───────────┐   ┌───────────┐    ┌───────────┐    ┌───────────┐
│ Full User │    │ Audit &   │    │ System    │   │ Stock &   │    │ POS/Sales │    │ Customer  │
│ Mgmt      │    │ Analytics │    │ Overrides │   │ Inventory │    │ Billing   │    │ Directory │
└───────────┘    └───────────┘    └───────────┘   └───────────┘    └───────────┘    └───────────┘

## 👤 Role-Based Access Control (RBAC)## 1. Admin Module (Sudo Access)
The Admin acts as the ultimate authority with unrestricted system override capabilities.

* Comprehensive User Management: Create, update, suspend, and delete user profiles (Pharmacists, Cashiers, Auditing accounts).
* System Settings & Configuration: Manage core business variables, tax percentages, currency parameters, and system backups.
* Financial Data & Analytics: Access comprehensive sales records, gross margin analytics, and operational audit logs.

## 2. Pharmacist Module (Operations)
The Pharmacist handles day-to-day workflow and transactional activities.

* Stock & Inventory Control: Track, add, update, and manage medicine batches, expiry dates, and shelf locations.
* POS & Billing: Generate real-time sales receipts, handle point-of-sale operations, and calculate discounts.
* Customer Directory: Register new customers, update patient profiles, and log purchase histories for chronic prescriptions.

------------------------------
## 📦 Key Functional Modules## 1. Stock & Inventory Management

* Expiry Tracking: Automated alerts for near-expiry medications to minimize financial waste.
* Low Stock Alerts: Intelligent notifications when drug quantities drop below the safety threshold.
* Batch Classification: Organize items by batch numbers, manufacturers, and medical categories (e.g., Antibiotics, Analgesics).

## 2. Sales & Billing Process

* Instant Invoicing: Dynamic calculation of prices, taxes, and customer discounts.
* Payment Processing: Support for multiple payment modes (Cash, Card, Digital Wallet).
* Return & Refund Handling: Structured validation for processing item returns and tracking spoiled stock.

## 3. User & Security Management

* Secure Authentication: Password hashing using PHP's native password_hash() and secure session management.
* Activity Logs: Automatic logging of high-impact actions (e.g., stock deletions, price changes) to ensure transparency.

------------------------------
## 🛠️ Technology Stack

* Frontend: Bootstrap 5, HTML5, CSS3, JavaScript (Vanilla / jQuery for AJAX requests)
* Backend: Native PHP (Object-Oriented Programming, PDO for database interactions)
* Database: MySQL (Relational management with foreign keys for transactional integrity)
* Server Environment: Apache (XAMPP)

------------------------------
## 🏁 Getting Started## Prerequisites

* PHP 8.0+
* MySQL 5.7+ or MariaDB
* A local web server environment like XAMPP, WAMP, or Laragon.

## Installation & Local Setup

   1. Clone the repository:
   Move into your local server's root directory (htdocs or www) and clone the repo:
   
   cd /path/to/your/local/server/htdocs
   git clone https://github.com
   cd pharmacy-management-system
   
   2. Database Setup:
   * Open phpMyAdmin (http://localhost/phpmyadmin).
      * Create a new database named pharmaco.
      * Import the database schema from the Backup folder provided in this repository.
   3. Configure Database Connection:
   Open the configuration file (usually config/connection.php or config.php) and update your local MySQL credentials:
   
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'pharmaco');
   
   4. Run the Application:
   Open your browser and navigate to:
   
   http://localhost/pharmaCodex
   
   5. Default credentilas
      Username : admin
      Password: 1
      Username: pharmacist
      Password: 123

      or you can confirm from PhamrCodex > Credentials.txt 
      
   
------------------------------
