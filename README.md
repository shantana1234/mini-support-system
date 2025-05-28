# 🛠️ Mini Support System

A simple PHP-based support ticket system built without a framework. This project demonstrates MVC structure, custom routing, role-based access control, rate-limiting, and file uploads — suitable for junior dev interviews and small internal tools.

---

## 🚀 Features

 ✅ User authentication via middleware (Admin/User)
 🧾 Ticket creation with file uploads
 🧑‍💼 Department management (admin only)
 📌 Ticket update, view, delete (based on ownership or admin access)
 🧃 Rate-limiting for ticket creation (10 per hour)
 📂 Ticket notes support (if implemented)
 🌐 Clean API responses with JSON
 🧪 Includes simple unit testing script

---

## 🛠️ Tech Stack

- PHP 8+
- MySQL / MariaDB
- PDO for database access
- Custom MVC structure (Controller, Model)
- No external libraries/frameworks

---

## 🧩 Folder Structure

mini-support-system/
├── app/
│ ├── Controllers/
│ ├── Models/
│ └── Middleware/
├── config/
│ └── database.php
├── public/
│ └── index.php
├── storage/
│ └── uploads/tickets/
├── Tests/
│ └── DepartmentControllerTest.php
│ └── TicketControllerTest.php
├── seed.php
├── index.php
├── .htaccess
├── test.php

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




