<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Product Import and Synchronization

This project is designed to import product data from CSV, JSON, XML files, and an external API.

## Features

- Import products from CSV, JSON, and XML files.
- Soft delete outdated products that are no longer in the source files or API.
- Fetch product data from an external API.
- Insert product variations (color, size, quantity, availability) into the database.

## Requirements

- PHP 8.x or higher
- Laravel 11 or higher
- MySQL or SQLite database (or any other database supported by Laravel)
- Composer for dependency management


## Installation

### 1. Clone the Repository

Clone the repository to your local machine or server:

```bash
git clone https://github.com/yourusername/product-import-synchronization.git
cd product-import-synchronization
```

### 2. Install Dependencies

Install the project dependencies using Composer:

```bash
composer install
```

### 3. Set Up Environment Variables

Copy the .env.example file to .env:

```bash
cp .env.example .env
```

Update the .env file with your database credentials and any necessary configurations (e.g., API URLs).

### 4. Set Up Database

Run the migrations to set up the database:

```bash
php artisan migrate
```

This will create the necessary tables for the products and their variations in your database.

### 5. Run the Import Command Manually

If you want to run the product import manually, you can execute the command:

```bash
php artisan products:import {source}
```

Replace `{source}` with `csv`, `json`, `xml`, or `api` to specify the data source.

#### Command Usage


The main functionality is encapsulated in the `products:import` command, which imports and synchronizes products from different sources.

### Example CSV Format

The CSV file used for import should follow this format:

```
"Wireless Mouse","MOUSE123","active","color: black, size: medium",25.99,"USD"
"Mechanical Keyboard","KEYB456","active","color: white, size: large",79.99,"USD"
"Gaming Headset","HEAD789","inactive","color: red, size: standard",49.99,"USD"
"USB-C Charger","CHARG101","active","color: black, size: standard",19.99,"USD"
"Portable SSD","SSD202","active","color: grey, size: 1TB",129.99,"USD"
```

### Example JSON Format

The JSON file should be structured as follows:


```json
[
    {
        "id": 1,
        "name": "Product A",
        "sku": "PROD_A_123",
        "status": "active",
        "price": 19.99,
        "currency": "USD"
    },
    {
        "id": 2,
        "name": "Product B",
        "sku": "PROD_B_456",
        "status": "inactive",
        "price": 29.99,
        "currency": "EUR"
    }
]

```


### Example XML Format

The XML file should follow this structure:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<products>
    <product>
        <id>1</id>
        <name>Product A XML</name>
        <sku>PROD_A_123</sku>
        <status>active</status>
        <price>19.99</price>
        <currency>USD</currency>
    </product>
    <product>
        <id>2</id>
        <name>Product B XML</name>
        <sku>PROD_B_456</sku>
        <status>inactive</status>
        <price>29.99</price>
        <currency>EUR</currency>
    </product>
</products>

```

### External API Integration

The command will also fetch data from an external API at:

```bash
https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products
```

### Soft Deletion of Outdated Products

Any products in the database that are no longer in the CSV file or the API response will be soft deleted (i.e., marked with a ```deleted_at``` timestamp). You can identify these products by checking the ```status``` field, which will be updated to ```deleted_due_to_sync```.

## Architecture

This system follows the MVC architecture and uses the **Strategy Pattern** to handle multiple data sources efficiently:

- **Product Model**: Handles the `products` table, where product details (name, SKU, price, etc.) are stored.
- **ProductVariation Model**: Handles the `product_variations` table, which stores product variations like color, size, quantity, etc.
- **ImportProducts Command**: A custom Artisan command responsible for importing products from multiple sources dynamically.
- **Strategy Pattern**: Ensures easy extension for new import sources with minimal code changes.
- **Database Migrations**: Define the structure for the `products` and `product_variations` tables.


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
