🏥 MSCareWorld – Hospital Management System
📌 Project Overview

MSCareWorld is a web-based Hospital Management System designed to manage daily hospital operations efficiently. The system includes three main roles: Admin, Doctor, and Patient, allowing smooth handling of appointments, prescriptions, user profiles, and reports.

🚀 Features
👨‍💼 Admin Panel
Add and manage doctors
Manage departments and specializations
View dashboard analytics
Profile management
👨‍⚕️ Doctor Panel
View appointments
Add prescriptions
Access patient details
Update profile
🧑‍🤝‍🧑 Patient Panel
Search doctors and book appointments
View appointment history
Download prescriptions
Manage profile
Online payment system
🔐 Authentication System
User registration
Login / Logout
Forgot password (OTP verification)
Secure authentication system
📄 Additional Features
PDF report generation using FPDF
Role-based access control
Responsive user interface
🛠️ Technologies Used
Frontend: HTML, CSS, JavaScript
Backend: PHP
Database: MySQL
Library: FPDF (for PDF generation)
📂 Project Structure
MSCareWorld/
│── admin/              # Admin panel files
│── doctor/             # Doctor panel files
│── patient/            # Patient panel files
│── auth/               # Authentication system
│── config/             # Database configuration
│── database/           # Database files
│── assets/             # CSS, JS, Images
│── api/                # Backend APIs
│── uploads/            # Uploaded files
│── pdf/                # PDF library (FPDF)
⚙️ Installation Guide
Download or extract the project
Place the folder inside htdocs (XAMPP/WAMP server)

Open in browser:

http://localhost/MSCareWorld
Setup Database:
Open phpMyAdmin
Create a new database (e.g., mscareworld)
Import the SQL file from the database folder

Update database configuration in config/connection.php:

$host = "localhost";
$user = "root";
$pass = "";
$db   = "mscareworld";
🔑 User Roles
Admin
Doctor
Patient
📌 Future Improvements
Payment gateway integration
Email notifications
Improved mobile responsiveness
AI-based doctor recommendation system
🤝 Contribution

If you want to contribute:

Fork the repository
Make changes
Submit a pull request
📜 License

This project is created for educational purposes only.# MSCareWorld
