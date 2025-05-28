# 🛠️ Mini Support System

A simple PHP-based support ticket system built without a framework. This project demonstrates MVC structure, custom routing, role-based access control, rate-limiting, and file uploads — suitable for junior dev interviews and small internal tools.

---

## 🚀 Features

 ✅ User authentication via middleware (Admin/User)<br>
 🧾 Ticket creation with file uploads<br>
 🧑‍💼 Department management (admin only)<br>
 📌 Ticket update, view, delete (based on ownership or admin access)<br>
 🧃 Rate-limiting for ticket creation (10 per hour)<br>
 📂 Ticket notes support (if implemented)<br>
 🌐 Clean API responses with JSON<br>
 🧪 Includes simple unit testing script<br>

---

## 🛠️ Tech Stack

- PHP 8+
- MySQL / MariaDB
- PDO for database access
- Custom MVC structure (Controller, Model)
- No external libraries/frameworks

---

## 🧩 Folder Structure

mini-support-system/<br>
├── app/<br>
│ ├── Controllers/<br>
│ ├── Models/<br>
│ └── Middleware/<br>
├── config/<br>
│ └── database.php<br>
├── public/<br>
│ └── index.php<br>
├── storage/<br>
│ └── uploads/tickets/<br>
├── Tests/<br>
│ └── DepartmentControllerTest.php<br>
│ └── TicketControllerTest.php<br>
├── seed.php<br>
├── index.php<br>
├── .htaccess<br>
├── test.php<br>

## 📥 Installation

1. **Clone the repo**

git clone git@github.com:shantana1234/mini-support-system.git
cd mini-support-system

## 📥 Run basic tests
Basic tests provided for DepartmentController:

bash
Copy
Edit
php Tests/DepartmentControllerTest.php
Make sure you've seeded the DB and users are set up for tests.

## 📥 File uploads
Ticket attachments are saved in storage/uploads/tickets/

Only files with valid $_FILES['attachment'] input are stored

## 📌 Rate Limiting

Max 10 tickets per user per hour
Stored in storage/rate_limit.json
Custom logic in TicketController::rateLimitCheck

## ⚙️ API Endpoint and Database Schema

Endpoint details with JSON demo data and Database schema with other technical findings can be found at this link - 
https://docs.google.com/document/d/1wviNvNxDpRoDfL0u08Mtq_79K5slu_1oP4UuB5l8baY/edit?usp=sharing




