# Indian Shopping Cart System

A complete e-commerce shopping cart system built with PHP, featuring both customer and admin interfaces, REST APIs, and Razorpay payment integration.

## Features

### Customer Features
- Browse and search products
- Add/remove items from cart
- User-friendly checkout process
- Multiple payment options (Razorpay, COD)
- Customer registration and login
- Order history and tracking
- Personal account management
- Responsive design

### Admin Features
- Secure admin login
- Product management (CRUD operations)
- Order management and status updates
- Dashboard with key metrics
- User-friendly admin interface

### API Features
- RESTful API endpoints
- JSON data format
- CORS support
- Authentication for admin endpoints
- Comprehensive API documentation

## Installation

1. **Clone or download** the project files
2. **Set up the database**:
   - Open phpMyAdmin or MySQL command line
   - **For new installations:**
     - Run the SQL script in `setup_database.sql` first:
       ```sql
       SOURCE setup_database.sql;
       ```
     - Then run the main schema in `database.sql`:
       ```sql
       SOURCE database.sql;
       ```
   - **For existing installations (with data):**
     - Run the migration script to update your existing database:
       ```sql
       SOURCE migrate_database.sql;
       ```
   - Update database credentials in `config.php` if needed
3. **Install dependencies** using Composer:
   ```bash
   composer install
   ```
4. **Configure Razorpay** (optional):
   - Update `config.php` with your Razorpay API keys
   - Get keys from https://dashboard.razorpay.com/app/keys
5. **Start the server**:
   ```bash
   php -S localhost:8000
   ```
6. **Test the database setup** (optional):
   - Visit `http://localhost:8000/test_database.php` to verify everything is working
7. **Access the application**:
   - Store: http://localhost:8000/index.php
   - Admin: http://localhost:8000/admin.php

## Default Credentials

**Admin Login:**
- Username: `admin`
- Password: `admin123`

**Customer Registration:**
- Customers can register new accounts
- Login with username or email
- Secure password hashing

## File Structure

```
shopping-cart-system/
├── index.php                 # Main store page
├── cart.php                  # Shopping cart page
├── checkout.php              # Checkout process
├── payment_success.php       # Payment confirmation
├── admin.php                 # Admin login
├── admin_dashboard.php       # Admin dashboard
├── admin_products.php        # Product management
├── admin_orders.php          # Order management
├── admin_logout.php          # Admin logout
├── api_products.php          # Products API
├── api_orders.php            # Orders API
├── api_auth.php              # Authentication API
├── api_cart.php              # Cart API
├── functions.php             # Core functions
├── config.php                # Configuration
├── style.css                 # Stylesheets
├── products.json             # Product data
├── orders.json               # Order data
├── composer.json             # PHP dependencies
├── API_DOCUMENTATION.md      # API docs
└── README.md                 # This file
```

## API Endpoints

### Authentication
- `POST /api_auth.php` - Admin login

### Products
- `GET /api_products.php` - Get all products
- `GET /api_products.php?id=1` - Get specific product
- `POST /api_products.php` - Create product (Admin)
- `PUT /api_products.php?id=1` - Update product (Admin)
- `DELETE /api_products.php?id=1` - Delete product (Admin)

### Cart
- `GET /api_cart.php` - Get cart contents
- `POST /api_cart.php` - Add to cart
- `PUT /api_cart.php` - Update cart item
- `DELETE /api_cart.php?product_id=1` - Remove from cart

### Orders
- `GET /api_orders.php` - Get all orders (Admin)
- `POST /api_orders.php` - Create order
- `PUT /api_orders.php?id=0` - Update order status (Admin)

## Usage Examples

### Admin Login
```javascript
fetch('/api_auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        username: 'admin',
        password: 'admin123'
    })
});
```

### Get Products
```javascript
fetch('/api_products.php')
.then(response => response.json())
.then(data => console.log(data));
```

### Add to Cart
```javascript
fetch('/api_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        product_id: '1',
        quantity: 2
    })
});
```

## Data Storage

The system uses MySQL database for data persistence:
- `products` table - Stores product catalog
- `orders` table - Stores order information
- `order_items` table - Stores order line items
- `users` table - Stores admin user accounts
- `cart_sessions` table - Stores persistent cart data

## Payment Integration

Integrated with Razorpay for secure payments:
- Supports multiple payment methods
- Webhook support for payment confirmation
- Automatic order status updates

## Security Features

- Session-based authentication
- Input validation and sanitization
- CSRF protection
- Secure password handling
- Admin access control

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Payment**: Razorpay API
- **Styling**: Custom CSS with responsive design

## Browser Support

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Support

For support or questions:
- Check the API documentation
- Review the code comments
- Create an issue in the repository

---

**Made with ❤️ for India** 🇮🇳
