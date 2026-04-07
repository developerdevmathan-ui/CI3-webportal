# CI3 Web Portal

## Project Overview

- This project is a web portal built with CodeIgniter 3, PHP 7.3, MySQL 8, and Stripe.
- It provides an admin panel for managing users and products, including product stock.
- It allows users to register, log in, browse products, add items to a session-based cart, complete payment through Stripe Checkout, and view invoices and receipts inside the portal.
- It also exposes token-based APIs for login, invoice retrieval, and receipt retrieval.

## Implemented Features

### Admin Panel

- Create users
- Assign user roles: `admin` or `user`
- Create products
- Edit products
- Delete products
- Maintain product stock
- View all invoices
- View all receipts

### User Module

- Register and log in
- Log out securely
- Browse available products
- View product price and available stock
- Add products to cart
- Update cart quantity
- Remove products from cart
- Review cart before checkout
- View only their own invoices
- View only their own receipts

### Payment System

- Stripe Checkout integration
- Multi-product checkout using Stripe `line_items`
- Success redirection after payment
- Cancel redirection after payment cancellation
- Secure webhook verification
- Ngrok support for local webhook testing
- Order, order items, and payment data stored in MySQL
- Stock reduction after successful payment confirmation

### Invoice and Receipt Module

- Invoice generated automatically after successful payment confirmation
- Receipt generated automatically after successful payment confirmation
- Invoice numbering format: `INV-YYYYMMDD-XXXX`
- Receipt numbering format: `REC-YYYYMMDD-XXXX`

### API Module

- Token-based authentication
- Login API
- Invoices API
- Receipts API
- JSON response structure for external or mobile clients

### Reporting Note

- Admin screens currently show invoices and receipts through the portal.
- Stripe payment records are stored in the `payments` table and webhook activity is written through application logging.
- A dedicated admin transaction-log screen can be added as the next reporting enhancement.

## Tech Stack

### Backend

- CodeIgniter 3
- PHP 7.3

### Database

- MySQL 8

### Payment

- Stripe Checkout
- Stripe Webhooks

### Tools

- Composer
- Ngrok
- Bootstrap
- Apache / XAMPP
- CI3 file cache driver for lightweight product caching

## Installation Guide

### 1. Clone the project or UNZIP the project

```bash
git clone <repository-url>
cd CI3-WEBPORTAL
```

### 2. Place the project in Apache web root

Example XAMPP path:

```text
c:\xampp\htdocs\CI3-WEBPORTAL
```

### 3. Install dependencies

```bash
composer install
```

### 4. Setup Apache

- Start Apache from XAMPP
- Enable `mod_rewrite`
- Keep `.htaccess` in the project root
- Open the project in the browser:

```text
http://localhost/CI3-WEBPORTAL/
```

### 5. Setup MySQL

- Start MySQL from XAMPP
- Create a new database

Example:

```sql
CREATE DATABASE ci3_webportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Import the database

Import SQL files in this order:
RUN each file in DB

1. `application/sql/day1_auth.sql`
2. `application/sql/day2_admin_products.sql`
3. `application/sql/day3_payments.sql`
4. `application/sql/day4_invoice_receipts.sql`
5. `application/sql/day5_api.sql`
6. `application/sql/day6_cart_stock.sql`

### 7. Configure application settings

Review and update:

- `application/config/config.php`
- `application/config/database.php`
- `application/config/custom.php`

Update these values:

- `base_url`
- DB host
- DB name
- DB username
- DB password
- Stripe keys
- session settings if needed

### 8. Configure environment

The project supports:

- `.env`
- `.env.development`
- `.env.testing`
- `.env.production`

Set:

```dotenv
APP_ENV=development
```

### 9. Run the project

- Start Apache
- Start MySQL
- Open:

```text
http://localhost/CI3-WEBPORTAL/
```

### Default admin account

- Email: `admin@example.com`
- Password: `Admin@123`

## Stripe Setup

### Test mode keys

Get Stripe test keys from the Stripe dashboard:

- Publishable key
- Secret key
- Webhook signing secret

Set them in:

- `application/config/custom.php`
- or the active `.env` file if you are overriding them there

Example:

```php
$config['stripe_secret'] = 'sk_test_xxx';
$config['stripe_publishable'] = 'pk_test_xxx';
$config['stripe_webhook_secret'] = 'whsec_xxx';
```

### Configure webhook with Ngrok

Start Ngrok:

```bash
ngrok http 80
```

Take the generated HTTPS URL and register this webhook URL in Stripe:

```text
https://your-ngrok-url/payment/webhook
```

Recommended event:

- `checkout.session.completed`

### Stripe test card

Use this common Stripe test card:

```text
Card Number: 4242 4242 4242 4242
Expiry: any future date
CVC: any 3 digits
ZIP: any valid value
```

## Application Flow

```text
User Login
  ->
Browse Products
  ->
Add Products To Cart
  ->
View Cart / Update Quantity
  ->
Checkout Review
  ->
Create Pending Order + Order Items
  ->
Redirect To Stripe Checkout
  ->
Stripe Payment Attempt
  ->
Success / Cancel Redirect
  ->
Webhook Verification
  ->
Update Order + Payment
  ->
Reduce Stock
  ->
Generate Invoice
  ->
Generate Receipt
  ->
User/Admin View Documents
```

## ER Diagram

```text
Users (1) ------ (M) Orders
Products (1) --- (M) Order_Items
Orders (1) ----- (M) Order_Items
Orders (1) ----- (1/M) Payments
Orders (1) ----- (1) Invoices
Payments (1) --- (1) Receipts
```

### Relationship Explanation

- One user can place many orders
- One order belongs to one user
- One order can contain many order items
- One product can appear in many order items
- One order can have payment records for Stripe session tracking
- One paid order generates one invoice
- One successful payment generates one receipt

## Folder Structure

```text
application/
|-- config/
|   |-- config.php
|   |-- custom.php
|   |-- database.php
|   `-- routes.php
|-- controllers/
|   |-- Auth.php
|   |-- Products.php
|   |-- Cart.php
|   |-- Payment.php
|   |-- Account.php
|   |-- Admin/
|   |   |-- Dashboard.php
|   |   |-- Products.php
|   |   |-- Users.php
|   |   `-- Invoices.php
|   `-- Api/
|       |-- Auth.php
|       |-- Invoices.php
|       `-- Receipts.php
|-- core/
|   `-- MY_Controller.php
|-- models/
|   |-- User_model.php
|   |-- Product_model.php
|   |-- Order_model.php
|   |-- Order_item_model.php
|   |-- Payment_model.php
|   |-- Invoice_model.php
|   `-- Receipt_model.php
|-- sql/
|   |-- day1_auth.sql
|   |-- day2_admin_products.sql
|   |-- day3_payments.sql
|   |-- day4_invoice_receipts.sql
|   |-- day5_api.sql
|   `-- day6_cart_stock.sql
`-- views/
    |-- admin/
    |-- auth/
    |-- cart/
    |-- payment/
    |-- products/
    `-- user/
```

## Security Implementation

- Passwords are hashed using bcrypt through `password_hash()`
- Password validation uses `password_verify()`
- Web users use session-based authentication
- API clients use bearer token authentication
- Role-based access is enforced for `admin` and `user`
- Stripe webhook requests are verified using Stripe signature validation
- Users can only view their own invoices and receipts
- Stock is revalidated before checkout
- Stock reduction uses guarded SQL updates so stock cannot go below zero
- Checkout fulfillment uses DB transactions and a MySQL named lock per order
- CSRF protection is enabled for browser-based forms

## API Documentation

### 1. Login API

Endpoint:

```text
POST /api/login
```

Request:

```bash
curl -X POST http://localhost/CI3-WEBPORTAL/api/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"user@example.com\",\"password\":\"User@123\"}"
```

Response:

```json
{
	"status": true,
	"message": "Login successful.",
	"data": {
		"token": "generated_api_token_here",
		"user": {
			"id": 2,
			"name": "Portal User",
			"email": "user@example.com",
			"role": "user"
		}
	}
}
```

### 2. Get Invoices API

Endpoint:

```text
GET /api/invoices
```

Request:

```bash
curl -X GET http://localhost/CI3-WEBPORTAL/api/invoices \
  -H "Authorization: Bearer generated_api_token_here"
```

### 3. Get Receipts API

Endpoint:

```text
GET /api/receipts
```

Request:

```bash
curl -X GET http://localhost/CI3-WEBPORTAL/api/receipts \
  -H "Authorization: Bearer generated_api_token_here"
```

### Standard API response format

```json
{
	"status": true,
	"message": "Success",
	"data": []
}
```

## Extra Design

- The cart system is used instead of direct purchase so users can review multiple products, change quantities, and complete a single multi-item checkout.
- Stock consistency is maintained by checking stock while adding to cart, rechecking before checkout, and reducing stock only after successful payment confirmation.
- Concurrency is handled through DB transactions, guarded stock updates, unique payment session handling, and MySQL named locks during paid order fulfillment.
- The system scales better for multiple users because checkout state is separated into orders and order items rather than assuming one product per order.

## Notes

- Product list caching is implemented with CI3 file cache for lightweight read optimization.
- If a Checkout attempt is retried after cart changes, a new order should be created to avoid Stripe idempotency-key conflicts.
