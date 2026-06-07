# ClinicDesk

Clinic Management Dashboard built with PHP.

## Technologies

- PHP (pure, no framework)
- MySQL
- AdminLTE 3

## Installation

1. Clone the repository
2. Import `database/clinicdesk_db.sql` into MySQL via phpMyAdmin
3. Configure `config/database.php` with your DB credentials
4. Place the project folder inside `xampp/htdocs/`
5. Run on XAMPP and open `http://localhost/clinicdesk`

## Login Credentials

| Role    | Email                          | Password     |
|---------|--------------------------------|--------------|
| Admin   | admin@gmail.com                | Admin@1234   |
| Doctor  | doctor@gmail.com               | Doctor@1234  |
| Patient | sufferer@gmail.com             | Patient@1234 |

## Features

- Role-based access control (Admin, Doctor, Patient)
- Appointment booking with conflict check
- Prescription management with PDF upload
- Reports with CSV export
- CSRF protection on all forms
- Prepared statements (no SQL injection)
