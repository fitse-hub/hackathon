# System Architecture

## 🏗️ High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT BROWSER                          │
│                     http://localhost:5173                       │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ HTTP/HTTPS
                             │ JSON API
                             │
┌────────────────────────────▼────────────────────────────────────┐
│                      VUE.JS FRONTEND                            │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Vue Router (SPA Routing)                                │  │
│  │  ├── / (Home)                                            │  │
│  │  ├── /login (Login Page)                                 │  │
│  │  ├── /register (Registration Page)                       │  │
│  │  └── /dashboard (Protected - User Dashboard)            │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Pinia Store (State Management)                          │  │
│  │  └── auth.js (User, Token, Permissions)                  │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Axios HTTP Client                                        │  │
│  │  ├── Request Interceptor (Add Bearer Token)              │  │
│  │  └── Response Interceptor (Handle 401 Errors)            │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ Bearer Token
                             │ Authorization Header
                             │
┌────────────────────────────▼────────────────────────────────────┐
│                     LARAVEL BACKEND                             │
│                   http://localhost:8000                         │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  API Routes (routes/api.php)                             │  │
│  │  ├── POST /api/auth/login                                │  │
│  │  ├── POST /api/auth/register                             │  │
│  │  ├── POST /api/auth/logout (Protected)                   │  │
│  │  ├── GET  /api/auth/me (Protected)                       │  │
│  │  ├── GET  /api/roles                                     │  │
│  │  ├── GET  /api/manager/users (Manager Only)             │  │
│  │  ├── GET  /api/manager/dashboard (Manager Only)         │  │
│  │  └── GET  /api/sales/my-sales (Auth Required)           │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Middleware Stack                                         │  │
│  │  ├── CORS Middleware                                      │  │
│  │  ├── Sanctum Auth Middleware                             │  │
│  │  └── Role Middleware (Manager/Sales Officer)             │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Controllers                                              │  │
│  │  └── AuthController                                       │  │
│  │      ├── login()                                          │  │
│  │      ├── register()                                       │  │
│  │      ├── logout()                                         │  │
│  │      ├── me()                                             │  │
│  │      └── getRoles()                                       │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Models                                                   │  │
│  │  └── User (HasApiTokens, HasFactory, Notifiable)         │  │
│  └──────────────────────────────────────────────────────────┘  │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ Eloquent ORM
                             │
┌────────────────────────────▼────────────────────────────────────┐
│                       MYSQL DATABASE                            │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Tables                                                   │  │
│  │  ├── users (id, name, email, password, role)             │  │
│  │  ├── personal_access_tokens (Sanctum tokens)             │  │
│  │  ├── sessions                                             │  │
│  │  ├── cache                                                │  │
│  │  └── migrations                                           │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔐 Authentication Flow

```
┌──────────┐                                    ┌──────────┐
│  Client  │                                    │  Server  │
└────┬─────┘                                    └────┬─────┘
     │                                               │
     │  1. POST /api/auth/login                     │
     │  { email, password }                         │
     ├──────────────────────────────────────────────>
     │                                               │
     │                    2. Validate credentials    │
     │                       Hash::check()           │
     │                                               │
     │                    3. Create token            │
     │                       $user->createToken()    │
     │                                               │
     │  4. Return token + user data                 │
     │  { success, user, permissions, token }       │
     <──────────────────────────────────────────────┤
     │                                               │
     │  5. Store token in localStorage               │
     │     localStorage.setItem('auth_token', ...)   │
     │                                               │
     │  6. Redirect to /dashboard                    │
     │                                               │
     │  7. GET /api/auth/me                          │
     │  Authorization: Bearer {token}                │
     ├──────────────────────────────────────────────>
     │                                               │
     │                    8. Verify token            │
     │                       Sanctum middleware      │
     │                                               │
     │  9. Return user data                          │
     │  { success, user, permissions }               │
     <──────────────────────────────────────────────┤
     │                                               │
     │  10. Display dashboard                        │
     │                                               │
```

---

## 🔄 Request/Response Cycle

### Successful Login Request

```
REQUEST:
POST http://localhost:8000/api/auth/login
Content-Type: application/json

{
  "email": "manager@fitse.com",
  "password": "password123"
}

RESPONSE:
HTTP/1.1 200 OK
Content-Type: application/json

{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Abebe Kebede",
    "email": "manager@fitse.com",
    "role": "manager"
  },
  "permissions": {
    "can_manage_users": true,
    "can_create_sales": true,
    "can_approve_sales": true,
    "can_view_reports": true,
    "can_manage_settings": true,
    "full_access": true
  },
  "token": "1|abc123def456...",
  "token_type": "Bearer"
}
```

### Protected Endpoint Request

```
REQUEST:
GET http://localhost:8000/api/auth/me
Authorization: Bearer 1|abc123def456...
Accept: application/json

RESPONSE:
HTTP/1.1 200 OK
Content-Type: application/json

{
  "success": true,
  "user": {
    "id": 1,
    "name": "Abebe Kebede",
    "email": "manager@fitse.com",
    "role": "manager"
  },
  "permissions": {
    "can_manage_users": true,
    "can_create_sales": true,
    ...
  }
}
```

---

## 🎯 Component Interaction

### Frontend Component Flow

```
┌─────────────────────────────────────────────────────────────┐
│                         App.vue                             │
│                    (Root Component)                         │
└────────────────────────────┬────────────────────────────────┘
                             │
                             │ <router-view />
                             │
         ┌───────────────────┼───────────────────┐
         │                   │                   │
         ▼                   ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Home.vue   │     │ Login.vue   │     │Register.vue │
│             │     │             │     │             │
│ - Hero      │     │ - Form      │     │ - Form      │
│ - CTA       │     │ - Validate  │     │ - Validate  │
│             │     │ - Submit    │     │ - Submit    │
└─────────────┘     └──────┬──────┘     └──────┬──────┘
                           │                   │
                           │ useAuthStore()    │
                           │                   │
                           ▼                   ▼
                    ┌──────────────────────────────┐
                    │      auth.js (Pinia)         │
                    │                              │
                    │  - state (user, token)       │
                    │  - actions (login, logout)   │
                    │  - getters (isManager, etc)  │
                    └──────────┬───────────────────┘
                               │
                               │ axios instance
                               │
                               ▼
                    ┌──────────────────────────────┐
                    │       axios.js               │
                    │                              │
                    │  - baseURL config            │
                    │  - interceptors              │
                    │  - token injection           │
                    └──────────────────────────────┘
```

### Backend Request Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    Incoming Request                         │
│              POST /api/auth/login                           │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                    Middleware Stack                         │
│  1. CORS Middleware (check origin)                          │
│  2. Sanctum Middleware (verify token if present)            │
│  3. Role Middleware (check user role if required)           │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                    Route Handler                            │
│              routes/api.php                                 │
│  Route::post('/auth/login', [AuthController, 'login'])      │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                    Controller                               │
│              AuthController@login                           │
│  1. Validate input                                          │
│  2. Find user by email                                      │
│  3. Verify password                                         │
│  4. Create token                                            │
│  5. Return response                                         │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                    Model Layer                              │
│              User::where('email', $email)->first()          │
│              $user->createToken('auth-token')               │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                    Database                                 │
│  SELECT * FROM users WHERE email = ?                        │
│  INSERT INTO personal_access_tokens ...                     │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│                    JSON Response                            │
│  { success, user, permissions, token }                      │
└─────────────────────────────────────────────────────────────┘
```

---

## 🗄️ Database Schema

```sql
-- Users Table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Personal Access Tokens (Sanctum)
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_tokenable (tokenable_type, tokenable_id),
    INDEX idx_token (token)
);
```

---

## 🔒 Security Layers

```
┌─────────────────────────────────────────────────────────────┐
│                    Security Layers                          │
└─────────────────────────────────────────────────────────────┘

Layer 1: Transport Security
├── HTTPS/TLS encryption
└── Secure cookie flags

Layer 2: CORS Protection
├── Allowed origins whitelist
├── Credentials support
└── Preflight handling

Layer 3: Authentication
├── Password hashing (bcrypt)
├── Token-based auth (Sanctum)
└── Token expiration

Layer 4: Authorization
├── Role-based access control
├── Permission checking
└── Middleware guards

Layer 5: Input Validation
├── Request validation rules
├── Type checking
└── Sanitization

Layer 6: Database Security
├── Prepared statements
├── SQL injection prevention
└── Mass assignment protection

Layer 7: Application Security
├── CSRF protection
├── XSS prevention
└── Rate limiting (ready)
```

---

## 📊 Data Flow Diagram

### User Registration Flow

```
User Input → Frontend Validation → API Request → Backend Validation
                                                         ↓
                                                   Hash Password
                                                         ↓
                                                   Create User
                                                         ↓
                                                   Generate Token
                                                         ↓
                                                   Store in DB
                                                         ↓
Frontend ← JSON Response ← Format Response ← Return Data
    ↓
Store Token
    ↓
Redirect to Dashboard
```

### Protected Route Access

```
User Action → Check localStorage for token
                    ↓
              Token exists?
                    ↓
            Yes ←───┴───→ No
             ↓              ↓
    Add to request    Redirect to login
             ↓
    Send to API
             ↓
    Verify token (Sanctum)
             ↓
    Valid? ←─┴─→ Invalid
      ↓              ↓
Return data    Return 401
      ↓              ↓
Display page   Clear token & redirect
```

---

## 🔄 State Management

### Pinia Store Structure

```javascript
// auth.js store
{
  state: {
    user: null,              // Current user object
    permissions: null,       // User permissions object
    isAuthenticated: false,  // Auth status boolean
    loading: false          // Loading state boolean
  },
  
  getters: {
    isManager: (state) => ...,      // Check if user is manager
    isSalesOfficer: (state) => ..., // Check if user is sales officer
    hasPermission: (state) => ...   // Check specific permission
  },
  
  actions: {
    login(credentials),      // Login user
    register(userData),      // Register new user
    logout(),               // Logout user
    checkAuth(),            // Verify authentication
    clearAuth()             // Clear auth state
  }
}
```

---

## 🌐 API Architecture

### RESTful Principles

```
Resource: Users
├── GET    /api/auth/me          (Read current user)
├── POST   /api/auth/login       (Authenticate)
├── POST   /api/auth/register    (Create user)
└── POST   /api/auth/logout      (Destroy session)

Resource: Roles
└── GET    /api/roles            (List roles)

Resource: Manager
├── GET    /api/manager/users    (List all users)
└── GET    /api/manager/dashboard (Get stats)

Resource: Sales
└── GET    /api/sales/my-sales   (List user sales)
```

---

## 🎨 Frontend Architecture

### Component Hierarchy

```
App.vue (Root)
└── router-view
    ├── Home.vue
    │   ├── Header
    │   ├── Hero Section
    │   └── CTA Button
    │
    ├── Login.vue
    │   ├── Header
    │   ├── Form Container
    │   │   ├── Email Input
    │   │   ├── Password Input
    │   │   ├── Submit Button
    │   │   └── Register Link
    │   └── Logo Section
    │
    ├── Register.vue
    │   ├── Form Container
    │   │   ├── Name Input
    │   │   ├── Email Input
    │   │   ├── Role Select
    │   │   ├── Password Input
    │   │   ├── Confirm Password
    │   │   └── Submit Button
    │   └── Login Link
    │
    └── Dashboard.vue
        ├── Header
        │   ├── Logo
        │   ├── User Info
        │   └── Logout Button
        ├── Welcome Section
        ├── Stats Grid
        ├── Permissions Section
        └── Quick Actions
```

---

## 🚀 Deployment Architecture

### Production Setup

```
┌─────────────────────────────────────────────────────────────┐
│                    Load Balancer (Optional)                 │
└────────────────────────────┬────────────────────────────────┘
                             │
         ┌───────────────────┼───────────────────┐
         │                   │                   │
         ▼                   ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Nginx     │     │   Nginx     │     │   Nginx     │
│  (Frontend) │     │  (Backend)  │     │  (Backend)  │
└──────┬──────┘     └──────┬──────┘     └──────┬──────┘
       │                   │                   │
       │                   ▼                   ▼
       │            ┌─────────────┐     ┌─────────────┐
       │            │  PHP-FPM    │     │  PHP-FPM    │
       │            │  (Laravel)  │     │  (Laravel)  │
       │            └──────┬──────┘     └──────┬──────┘
       │                   │                   │
       │                   └───────┬───────────┘
       │                           │
       ▼                           ▼
┌─────────────┐           ┌─────────────┐
│   Static    │           │   MySQL     │
│   Files     │           │  Database   │
│   (Vue)     │           │             │
└─────────────┘           └─────────────┘
```

---

This architecture provides a solid foundation for a scalable, secure, and maintainable application.
