# online-campus-selection-system
An end-to-end web application that simplifies the campus placement process by connecting students and companies. Built using **PHP**, **MySQL**, **HTML/CSS**, and **JavaScript** with **XAMPP** as the local development environment.

---

## 📌 Features

### 👨‍🎓 Student Module
- Student registration and login
- Upload profile picture and update personal info (CGPA, skills, etc.)
- View available jobs and apply
- Check application status

### 🏢 Company Module
- Company registration and login
- Upload company logo and details
- Post new jobs, update or delete existing ones
- View list of applicants

### 🌐 General Features
- Homepage for users
- Role-based dashboards (student/company)
- Job search with filter options
- Secure authentication using sessions
- File uploads handled safely (images/logos)

---

## 🛠️ Technologies Used

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Server**: XAMPP (Apache + MySQL)
- **Others**: Bootstrap (for responsive UI), FontAwesome (for icons)

---

## 🧑‍💻 Project Structure
/OCSS
│
├── images/
| ├── logos/                      # Company and student profile logos
│
├── phpmailer/                      # PHPMailer library for email functionality
│   ├── Exception.php
│   ├── PHPMailer.php
│   ├── POP3.php
│   └── SMTP.php
│
├── resumes/                        # Uploaded student resumes
│
├── aboutus.html                    # Static About Us page
├── contactus.html                  # Static Contact Us page
├── index.php                       # Landing page
├── connection.php                  # DB connection
│
├── company_register.php            # Company registration
├── company_login.php               # Company login
├── company_profile.php             # Company profile management
├── company_forgot_password.php     # Company password reset
├── post_job.php                    # Company posts a job
├── delete_job.php                  # Delete job
├── edit_job.php                    # Edit posted job
├── view_applicants.php             # Company views applicants
│
├── student_register.php            # Student registration
├── student_login.php               # Student login
├── student_profile.php             # Student profile management
├── student_forgot_password.php     # Student password reset
├── view_application.php            # Student views application status
├── applied_jobs.php                # Student applied jobs list
├── all_jobs.php                    # List all jobs for students
├── apply.php                       # Job application form
│
├── edit_company.php                # Edit company details
├── edit_student.php                # Edit student details
├── details.php                     # Job or user details
│
└── README.md                       # You're here!


## ⚙️ How to Run the Project

1. **Install XAMPP** from [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html)
2. Place the project folder in the `htdocs/` directory.
3. Start **Apache** and **MySQL** from the XAMPP Control Panel.
4. Open **phpMyAdmin** and import the provided `database.sql` file.
5. Visit `http://localhost/your_project_folder` in your browser.

---
