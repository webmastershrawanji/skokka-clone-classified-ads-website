# Skokka Clone

This is a simple classified ads website clone built with PHP, MySQL, and Bootstrap.

## Features

- User registration and login
- Post classified ads with categories and locations
- Search and filter ads by category, location, and keywords
- View ad details
- Responsive design with Bootstrap

## Setup Instructions

1. Import the database schema:

```bash
mysql -u root -p < schema.sql
```

2. Configure database connection in `config.php`:

- Update the `$user` and `$pass` variables with your MySQL credentials.

3. Run the PHP built-in server (for development):

```bash
php -S localhost:8000
```

4. Access the site in your browser at `http://localhost:8000/ads.php`

## Notes

- This is a basic implementation for demonstration purposes.
- No image upload or advanced features included.
- For production, consider adding more security, input validation, and features.
