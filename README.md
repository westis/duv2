# DUV API Migration

A modernized REST API for DUV Ultramarathon Statistics that standardizes the JSON format, provides interactive documentation, and secures access with API keys.

## Project Overview

This project implements a new `/api/v1/` API that runs alongside the current `/json/*.php` scripts with these key features:

- Standardized JSON field names and formats based on the SQL schema
- API Key authentication for restricted data
- Interactive, self-documenting Swagger UI
- Gradual endpoint-by-endpoint migration without disruption

## Core Endpoints

- `/api/v1/races` - Races list with filtering options
- `/api/v1/races/{raceId}` - Details for a specific race
- `/api/v1/results` - Performance results filtered by race or runner
- `/api/v1/runners/{runnerId}` - Runner profile with history
- `/api/v1/records` - Records for various distances and categories

## Admin Endpoints

- `/api/v1/admin/submitted-races` - User-submitted races pending approval
- `/api/v1/admin/submitted-races/{submissionId}` - Details for a specific submission
- `/api/v1/admin/users` - User management (admin access only)

## Setup Instructions

### Prerequisites

- XAMPP (v8.2+) or equivalent with PHP and MySQL
- Node.js LTS (v20+) and npm
- Composer for PHP dependencies
- Git for version control

### Installation

1. Clone the repository:

   ```
   git clone https://github.com/your-username/duv2.git
   cd duv2
   ```

2. Set up the database:

   - Start MySQL server
   - Create a new database
   - Import schema from `duv.sql`
   - Copy `.env.example` to `.env` and update credentials

3. Proof-of-Concept API Demo:
   - **XAMPP**: Copy or symlink the `duv2` folder into `C:\xampp\htdocs` and ensure Apache & MySQL are running
   - **Built-in PHP server**: Run `php -S localhost:8000` from the project root
   - **Test** the endpoint at `http://localhost/duv2/api/v1/example.php`
4. (Optional) Install dependencies:
   ```
   composer install
   npm install
   ```
5. Access Swagger documentation:
   - Visit `https://your-username.github.io/duv2/` for online docs
   - Or view locally at `http://localhost:8000/docs/`

## Documentation

API documentation is available via Swagger UI, hosted on GitHub Pages and can be accessed [here](https://your-username.github.io/duv2/).

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
