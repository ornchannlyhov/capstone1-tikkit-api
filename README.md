# Capstone Tikkit API 🎟️

A modern Laravel API designed for event ticketing and management, providing a robust backend solution for the Tikkit mobile application and admin dashboard.

## 📚 About the Project
This project serves as a capstone initiative, showcasing a scalable Laravel backend. It follows best practices in RESTful API development and integrates seamlessly with the Tikkit mobile and web platforms.

### Key Features
- Secure user authentication with Laravel Sanctum
- Role-based access control for Admin, Client, and Buyer portals
- Automated order and payment processing
- QR code generation for unique e-tickets
- Comprehensive transaction logging

---

## 🚀 Getting Started

### Note
- In this project, we don't call the main branch 'main'; we call it the ***develop*** branch instead.

### Prerequisites
Before you begin, ensure you have the following installed:
- [PHP 8.1+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [MySQL](https://www.mysql.com/)
- [Laravel 10+](https://laravel.com/)
- [Node.js](https://nodejs.org/) and npm (for frontend assets)

### Installation

## 💾 Cloning Project

1. Clone the repository:
   ```bash
   git clone https://github.com/ornchannlyhov/capstone1-tikkit-api.git
   ```
2. Navigate to the project directory:
   ```bash
   cd capstone1-tikkit-api
   ```
3. Install dependencies:
   ```bash
   composer install
   ```
4. Copy the environment file:
   ```bash
   cp .env.example .env
   ```
5. Generate the application key:
   ```bash
   php artisan key:generate
   ```
6. Set up the database in the `.env` file and run migrations:
   ```bash
   php artisan migrate --seed
   ```
7. Serve the application:
   ```bash
   php artisan serve
   ```

# 🛠️ Development Workflow

## Project Structure
We follow best practices in Laravel development to ensure maintainability and scalability.

- **/app**: Core application logic
- **/routes/api.php**: Defines API routes
- **/database/migrations/**: Database schema definitions
- **/resources/views/**: Blade templates for the dashboard
- **/public/**: Public assets like images and CSS
- **/config/**: Configuration files

## Branch Strategy
- **develop**: Main branch for ongoing development.
- **feature/***: Feature-specific branches for modular development.
- **master**: Stable, production-ready branch.

## 📖 Resources

### Learn Laravel
- **Laravel Documentation**: [https://laravel.com/docs](https://laravel.com/docs)
- **Laravel News**: Latest Laravel updates and tutorials
- **Laravel GitHub**: Official Laravel repository

## 🤝 Contributors
We’re a collaborative team of developers working together to deliver this project. Feel free to reach out to any of us for assistance or queries!

- **[Noun Sopheap]** - Role: [Project Manager]
- **[Mengthong Ly]** - Role: [UI/UX Designer && Frontend Developer]
- **[Tiveon Kong]** - Role: [Frontend Developer]
- **[Sovichet Thy]** - Role: [UI/UX Designer]
- **[Ornchann Lyhov]** - Role: [Backend Developer]
- **[Pay Panha]** - Role: [Backend Developer]

## 📧 Contact
For inquiries or support, contact us at [ornchannlyhov@email.com].

