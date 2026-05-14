# QMT Inventory and Sales Management System

A modern, full-stack inventory and sales management system built with Laravel (backend) and Vue.js (frontend).

## 🚀 Features

- **Authentication System**: Secure login and registration with Laravel Sanctum
- **Role-Based Access Control**: Manager and Sales Officer roles with different permissions
- **Modern UI**: Beautiful, responsive interface built with Vue 3
- **RESTful API**: Clean API architecture with proper error handling
- **Token-Based Authentication**: Secure API authentication using Bearer tokens

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.3
- **Composer** (PHP package manager)
- **Node.js** >= 20.19.0 or >= 22.12.0
- **MySQL** >= 5.7 or **MariaDB**
- **npm** or **yarn** (Node package manager)

## 🛠️ Installation

### Backend Setup (Laravel)

1. Navigate to the server directory:
```bash
cd server
```

2. Install PHP dependencies:
```bash
composer install
```

3. Configure environment variables:
```bash
cp .env.example .env
```

4. Update the `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hackathon
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run database migrations:
```bash
php artisan migrate
```

7. Seed the database with test users:
```bash
php artisan db:seed
```

8. Start the Laravel development server:
```bash
php artisan serve
```

The backend will be available at `http://localhost:8000`

### Frontend Setup (Vue.js)

1. Navigate to the client directory:
```bash
cd client
```

2. Install Node dependencies:
```bash
npm install
```

3. Start the development server:
```bash
npm run dev
```

The frontend will be available at `http://localhost:5173`

## 👥 Test Users

After seeding the database, you can login with these credentials:

### Manager Account
- **Email**: manager@fitse.com
- **Password**: password123
- **Permissions**: Full access to all features

### Sales Officer Account
- **Email**: sales@fitse.com
- **Password**: password123
- **Permissions**: Limited access (can create sales, view reports)

## 🔑 API Endpoints

### Public Endpoints
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `GET /api/roles` - Get available roles

### Protected Endpoints (Requires Authentication)
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user profile
- `GET /api/manager/users` - Get all users (Manager only)
- `GET /api/manager/dashboard` - Manager dashboard data (Manager only)
- `GET /api/sales/my-sales` - Get user's sales data (Manager & Sales Officer)

## 🏗️ Project Structure

```
hackathon/
├── client/                 # Vue.js frontend
│   ├── src/
│   │   ├── views/         # Page components
│   │   ├── stores/        # Pinia state management
│   │   ├── router/        # Vue Router configuration
│   │   └── axios.js       # Axios HTTP client setup
│   └── package.json
│
└── server/                # Laravel backend
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   └── Middleware/
    │   └── Models/
    ├── routes/
    │   └── api.php        # API routes
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    └── composer.json
```

## 🔐 Authentication Flow

1. User submits login credentials
2. Backend validates and returns a Bearer token
3. Token is stored in localStorage
4. Token is automatically attached to all API requests
5. Backend validates token on protected routes

## 🎨 Technologies Used

### Frontend
- **Vue 3** - Progressive JavaScript framework
- **Vue Router** - Official router for Vue.js
- **Pinia** - State management
- **Axios** - HTTP client
- **Vite** - Build tool

### Backend
- **Laravel 13** - PHP framework
- **Laravel Sanctum** - API authentication
- **MySQL** - Database
- **PHP 8.3** - Programming language

## 📝 Development

### Running Tests
```bash
# Backend tests
cd server
php artisan test

# Frontend tests (if configured)
cd client
npm run test
```

### Code Formatting
```bash
# Frontend
cd client
npm run format

# Backend
cd server
./vendor/bin/pint
```

## 🐛 Troubleshooting

### CORS Issues
If you encounter CORS errors, ensure:
1. The frontend URL is listed in `server/config/cors.php`
2. `supports_credentials` is set to `true`

### Database Connection Issues
1. Verify MySQL is running
2. Check database credentials in `.env`
3. Ensure the database exists: `CREATE DATABASE hackathon;`

### Token Authentication Issues
1. Clear localStorage in browser
2. Restart both frontend and backend servers
3. Check that Sanctum is properly installed: `composer show laravel/sanctum`

## 📄 License

This project is open-source and available under the MIT License.

## 👨‍💻 Contributors

- Development Team - Hackathon 2026

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

---

**Happy Coding! 🚀**
