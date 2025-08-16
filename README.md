# Fresh Fish Shop - Web Project

A modern, responsive fish shop website built with HTML, CSS, JavaScript frontend and PHP/MySQL backend.

## 🐟 Features

### Frontend
- **Responsive Design**: Mobile-first approach with modern UI/UX
- **Interactive Navigation**: Smooth scrolling and mobile hamburger menu
- **Product Catalog**: Dynamic product loading with search and filtering
- **Shopping Cart**: Local storage-based cart functionality
- **Newsletter Subscription**: Email subscription form
- **Modern Styling**: Clean, professional design with CSS animations

### Backend
- **MySQL Database**: Structured database with products, categories, orders, and customers
- **RESTful API**: PHP endpoints for products, cart, and newsletter
- **Database Security**: Prepared statements and input validation
- **Session Management**: Guest cart functionality

## 🚀 Quick Start

### Prerequisites
- Web server (Apache/Nginx) with PHP support
- MySQL/MariaDB database
- PHP 7.4 or higher
- Modern web browser

### Installation

1. **Clone or download the project**
   ```bash
   git clone https://github.com/vishnu2007mdk-ai/fish-shop.git
   cd fish-shop
   ```

2. **Set up the database**
   - Create a MySQL database named `fish_shop`
   - Import the schema: `php/database/schema.sql`
   - Update database credentials in `php/config/database.php`

3. **Configure the web server**
   - Point your web server to the project directory
   - Ensure PHP is enabled and configured

4. **Test the installation**
   - Open `index.html` in your browser
   - Test the API endpoints (e.g., `/php/api/products.php`)

## 📁 Project Structure

```
fish-shop/
├── index.html              # Homepage
├── products.html           # Products page
├── css/
│   └── style.css          # Main stylesheet
├── js/
│   ├── main.js            # Homepage JavaScript
│   └── products.js        # Products page JavaScript
├── php/
│   ├── config/
│   │   └── database.php   # Database configuration
│   ├── database/
│   │   └── schema.sql     # Database schema
│   └── api/
│       ├── products.php   # Products API
│       ├── newsletter.php # Newsletter API
│       └── cart.php       # Cart API
└── README.md              # This file
```

## 🗄️ Database Schema

### Tables
- **categories**: Product categories (Salmon, Cod, Tuna, etc.)
- **products**: Fish products with details and stock information
- **customers**: Customer information and addresses
- **orders**: Order records with status tracking
- **order_items**: Individual items in each order
- **newsletter_subscriptions**: Email subscriptions
- **guest_cart**: Shopping cart for non-registered users

### Sample Data
The schema includes sample categories and products to get you started.

## 🔧 Configuration

### Database Settings
Edit `php/config/database.php`:
```php
define('DB_HOST', 'localhost');     // Database host
define('DB_NAME', 'fish_shop');     // Database name
define('DB_USER', 'root');          // Database username
define('DB_PASS', '');              // Database password
```

### API Endpoints
- **Products**: `GET /php/api/products.php` - List products with filtering
- **Newsletter**: `POST /php/api/newsletter.php` - Subscribe to newsletter
- **Cart**: `GET/POST/PUT/DELETE /php/api/cart.php` - Cart operations

## 🎨 Customization

### Styling
- Modify `css/style.css` to change colors, fonts, and layout
- Update CSS variables for consistent theming
- Add custom animations and transitions

### Content
- Edit HTML files to change text and images
- Update product data in the database
- Modify JavaScript for custom functionality

### Backend
- Extend PHP APIs for additional features
- Add user authentication and registration
- Implement payment processing

## 📱 Responsive Design

The website is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🔒 Security Features

- **SQL Injection Protection**: Prepared statements
- **Input Validation**: Server-side validation for all inputs
- **CORS Headers**: Proper cross-origin resource sharing
- **Error Handling**: Secure error messages

## 🚀 Performance

- **Optimized CSS**: Efficient selectors and minimal redundancy
- **JavaScript Optimization**: Event delegation and efficient DOM manipulation
- **Database Indexing**: Proper indexes for fast queries
- **Image Optimization**: Responsive images and lazy loading support

## 🧪 Testing

### Frontend Testing
- Test responsive design on different screen sizes
- Verify JavaScript functionality in different browsers
- Check form validation and user interactions

### Backend Testing
- Test API endpoints with various inputs
- Verify database operations
- Check error handling and edge cases

## 📈 Future Enhancements

- User authentication and accounts
- Payment gateway integration
- Order tracking system
- Admin panel for inventory management
- Email notifications
- Product reviews and ratings
- Advanced search and filtering
- Multi-language support

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🆘 Support

If you encounter any issues:
1. Check the database connection
2. Verify PHP configuration
3. Check web server logs
4. Ensure all files are in the correct locations

## 🎯 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Internet Explorer 11+

---

**Happy coding! 🐟✨**
