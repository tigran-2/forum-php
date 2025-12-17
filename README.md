# PHP Forum (MVC) — Educational Project

A simple forum application built with PHP 8.3, conforming to modern standards and MVC architecture.

## Features

### Core
- **MVC Architecture**: Separation of concerns using Model-View-Controller pattern.
- **PSR-4 Autoloading**: Standardized class loading via Composer.
- **Security**:
    - CSRF Protection (Token rotation).
    - Session-based Authentication.
    - Password Hashing (Argon2/Bcrypt).
    - Input Validation & Sanitization.
    - Prepared Statements (PDO) for SQL Injection prevention.

### Functionality
- **User System**:
    - Registration with validation (Email, Password match, Age 18+, etc.).
    - Login/Logout.
    - User Profiles.
- **Topics**:
    - Create, Read, Update, Delete (CRUD).
    - Pagination for topic lists.
    - Search functionality.
    - "New/Old" indicators.
- **Comments**:
    - Add comments to topics.
    - Edit own comments.
    - Markdown-like support (basic).

## Requirements
- **PHP**: 8.3+
- **MySQL**: 8.0+ (or MariaDB)
- **Composer**: For dependency management.
- **Docker** (Optional but recommended): For easy deployment.

## Structure

```text
├── config/             # Configuration (Database, environment)
├── docker/             # Docker configuration files (Nginx, etc.)
├── public/             # Entry point (index.php), Static assets (CSS, JS)
├── src/                # Application Source Code
│   ├── Controllers/    # Request handlers
│   ├── Core/           # Framework core (Router, Session, View, Database)
│   ├── Helpers/        # Utilities (Validator, Messages)
│   └── Models/         # Data access layer
├── templates/          # View templates (PHP files)
├── database/           # SQL schema
├── vendor/             # Composer packages
├── .env                # Environment variables
├── docker-compose.yml  # Docker services definition
└── Dockerfile          # PHP-FPM container definition
```

## Setup

### 1. Using Docker Compose (Recommended)

1.  **Clone details**:
    Clone the repository and enter the directory.

2.  **Environment Setup**:
    Copy the example environment file:
    ```bash
    cp .env.example .env
    ```
    *Note: Adjust database credentials in `.env` if necessary, but the default Docker defaults usually work out of the box.*

3.  **Start Services**:
    Build and run the containers:
    ```bash
    docker compose up --build -d
    ```

4.  **Access**:
    Open [http://localhost:8080](http://localhost:8080) in your browser.

    *The database schema is automatically imported on the first run.*

### 2. Manual Installation

1.  **Database**:
    - Create a MySQL database (e.g., `forum_db`).
    - Import `database/schema.sql`.

2.  **Environment**:
    - Copy `.env.example` to `.env`.
    - Edit `.env` to match your local database credentials:
      ```ini
      DB_HOST=localhost
      DB_NAME=forum_db
      DB_USER=root
      DB_PASS=your_password
      ```

3.  **Dependencies**:
    Install Composer dependencies:
    ```bash
    composer install
    ```

4.  **Run**:
    Start a local PHP server from the `public/` directory:
    ```bash
    cd public
    php -S localhost:8000
    ```
    Open [http://localhost:8000](http://localhost:8000).

## Development

- **Formatting**: Run `composer cs-fix` to fix code style.
- **Testing**: Run `composer test` (if tests are implemented).

## License

MIT
