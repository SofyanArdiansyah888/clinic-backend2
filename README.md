# Clinic Backend 2

A Laravel-based backend application for clinic management system.

## Features

- **User Management**: Authentication and authorization system
- **Patient Management**: Complete patient records and information
- **Queue Management**: Patient queue and appointment scheduling
- **Treatment Management**: Medical treatments and procedures
- **Inventory Management**: Stock management for medical supplies
- **Reporting**: Comprehensive reporting and analytics

## Technology Stack

- **Framework**: Laravel 10
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum
- **API**: RESTful API design
- **Architecture**: Repository Pattern with Service Layer

## Project Structure

```
clinic-backend2/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # API Controllers
│   │   ├── Middleware/      # Custom Middleware
│   │   ├── Requests/        # Form Requests
│   │   └── Resources/       # API Resources
│   ├── Models/              # Eloquent Models
│   ├── Services/            # Business Logic Layer
│   └── Repositories/        # Data Access Layer
├── database/
│   ├── migrations/          # Database Migrations
│   ├── seeders/            # Database Seeders
│   └── factories/          # Model Factories
├── routes/
│   ├── api.php             # API Routes
│   ├── web.php             # Web Routes
│   └── modules/            # Module-specific Routes
└── config/                 # Configuration Files
```

## Modules

The application is organized into the following modules:

1. **Antrian** - Queue management
2. **Pasien** - Patient management
3. **User** - User authentication and management
4. **Treatment** - Medical treatment management
5. **Barang** - Inventory management
6. **Appointment** - Appointment scheduling
7. **Perawatan** - Medical care management
8. **Staff** - Staff management
9. **Supplier** - Supplier management
10. **Bank** - Banking information
11. **Membership** - Membership management
12. **Voucher** - Voucher and promotion management

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd clinic-backend2
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   # Edit .env file with your database credentials
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## API Endpoints

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh` - Refresh token

### Patients
- `GET /api/pasien` - List all patients
- `POST /api/pasien` - Create new patient
- `GET /api/pasien/{id}` - Get patient details
- `PUT /api/pasien/{id}` - Update patient
- `DELETE /api/pasien/{id}` - Delete patient

### Queue
- `GET /api/antrian` - List all queues
- `POST /api/antrian` - Create new queue
- `GET /api/antrian/{id}` - Get queue details
- `PUT /api/antrian/{id}` - Update queue
- `DELETE /api/antrian/{id}` - Delete queue

## Development

### Code Style
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add proper documentation and comments

### Testing
- Write unit tests for services and repositories
- Write feature tests for API endpoints
- Maintain good test coverage

### Database
- Use migrations for database schema changes
- Create seeders for test data
- Follow naming conventions for tables and columns

## Contributing

1. Create a feature branch
2. Make your changes
3. Write tests for new functionality
4. Submit a pull request

## License

This project is licensed under the MIT License.
