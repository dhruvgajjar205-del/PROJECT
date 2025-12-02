# Shopping Cart API Documentation

## Base URL
All API endpoints are relative to your domain root.

## Authentication
Admin endpoints require authentication. Use the `/api_auth.php` endpoint to login and maintain session.

## Endpoints

### Authentication
#### POST /api_auth.php
Login as admin user.

**Request Body:**
```json
{
    "username": "admin",
    "password": "admin123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful"
}
```

### Products
#### GET /api_products.php
Get all products or a specific product.

**Query Parameters:**
- `id` (optional): Product ID to fetch specific product

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": "1",
            "name": "Apple iPhone 14 Pro",
            "price": 60000,
            "description": "Latest iOS smartphone with advanced features",
            "image": "https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=200&h=150&fit=crop"
        }
    ]
}
```

#### POST /api_products.php
Create a new product (Admin only).

**Request Body:**
```json
{
    "name": "New Product",
    "price": 1000,
    "description": "Product description",
    "image": "https://example.com/image.jpg"
}
```

#### PUT /api_products.php?id=1
Update a product (Admin only).

**Request Body:**
```json
{
    "name": "Updated Product",
    "price": 1200,
    "description": "Updated description",
    "image": "https://example.com/updated-image.jpg"
}
```

#### DELETE /api_products.php?id=1
Delete a product (Admin only).

### Cart
#### GET /api_cart.php
Get current cart contents.

**Response:**
```json
{
    "success": true,
    "data": {
        "items": {
            "1": {
                "name": "Apple iPhone 14 Pro",
                "price": 60000,
                "quantity": 1,
                "image": "https://..."
            }
        },
        "total": 60000,
        "itemCount": 1
    }
}
```

#### POST /api_cart.php
Add product to cart.

**Request Body:**
```json
{
    "product_id": "1",
    "quantity": 2
}
```

#### PUT /api_cart.php
Update product quantity in cart.

**Request Body:**
```json
{
    "product_id": "1",
    "quantity": 3
}
```

#### DELETE /api_cart.php?product_id=1
Remove product from cart.

### Orders
#### GET /api_orders.php
Get all orders (Admin only).

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "receipt": "rcpt_1234567890",
            "customer": {
                "name": "John Doe",
                "email": "john@example.com",
                "address": "123 Main St",
                "city": "Mumbai",
                "pincode": "400001"
            },
            "items": [...],
            "total": 60000,
            "status": "pending",
            "date": "2023-12-01 10:30:00"
        }
    ]
}
```

#### POST /api_orders.php
Create a new order.

**Request Body:**
```json
{
    "customer": {
        "name": "John Doe",
        "email": "john@example.com",
        "address": "123 Main St",
        "city": "Mumbai",
        "pincode": "400001"
    },
    "items": [...],
    "total": 60000,
    "status": "pending"
}
```

#### PUT /api_orders.php?id=0
Update order status (Admin only).

**Request Body:**
```json
{
    "status": "shipped"
}
```

## Error Responses
All endpoints return errors in this format:
```json
{
    "success": false,
    "message": "Error description"
}
```

## Status Codes
- 200: Success
- 401: Unauthorized (for admin endpoints)
- 404: Not Found
- 405: Method Not Allowed

## Usage Examples

### JavaScript Fetch Examples

#### Login
```javascript
fetch('/api_auth.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        username: 'admin',
        password: 'admin123'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

#### Get Products
```javascript
fetch('/api_products.php')
.then(response => response.json())
.then(data => console.log(data));
```

#### Add to Cart
```javascript
fetch('/api_cart.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        product_id: '1',
        quantity: 1
    })
})
.then(response => response.json())
.then(data => console.log(data));
