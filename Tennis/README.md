# Tennis Online Store Project

## Overview
This project is an online store for tennis-related products. It is built using JavaScript, HTML, and PHP, with Bootstrap for styling. The application connects to a MySQL database to manage products, user accounts, and shopping cart functionality.

## Project Structure
```
Tennis
├── includes
│   ├── head.php         # Contains the HTML head section with Bootstrap and custom styles
│   ├── menu.php         # Navigation menu for the website
│   ├── footer.php       # Footer section with copyright and links
│   └── db_connect.php   # Database connection file
├── public
│   ├── index.php        # Homepage displaying featured products
│   ├── products.php     # Page listing all products
│   ├── product_detail.php# Detailed view of a specific product
│   ├── cart.php         # Shopping cart page
│   ├── checkout.php     # Checkout process page
│   ├── login.php        # User login page
│   ├── register.php     # User registration page
│   └── logout.php       # User logout functionality
├── assets
│   ├── css
│   │   └── styles.css   # Custom CSS styles
│   └── js
│       └── scripts.js    # Custom JavaScript for interactivity
├── database
│   └── tennis_db_structure.txt # MySQL database structure
└── README.md            # Project documentation
```

## Setup Instructions
1. **Clone the Repository**: Download or clone the project files to your local server.
2. **Database Setup**:
   - Create a MySQL database named `tennis_store`.
   - Import the database structure from `database/tennis_db_structure.txt` to create the necessary tables.
3. **Database Connection**:
   - Ensure the database connection in `includes/db_connect.php` uses the following credentials:
     - User: `root`
     - Password: `rootroot`
4. **Run the Application**: Access the application through your web server (e.g., `http://localhost/Tennis/public/index.php`).

## Features
- User authentication (login and registration)
- Product listing with details
- Shopping cart functionality
- Checkout process

## Technologies Used
- HTML
- CSS (Bootstrap)
- JavaScript
- PHP
- MySQL

## License
This project is open-source and available for modification and distribution.