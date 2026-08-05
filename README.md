# NutriTrack Pro - Complete Full-Stack Web Application

## 🎯 Project Overview
NutriTrack Pro is a comprehensive health and diet management system built with **HTML, CSS, JavaScript (Frontend)**, **PHP (Backend)**, and **MySQL (Database)**.

## 👥 Three User Roles
1. **User** - Track food, water, get diet plans, connect with dietitians
2. **Dietitian** - Manage clients, create diet plans, messaging
3. **Admin** - Manage users, dietitians, food database, approve registrations

## 🔐 Admin Credentials (Hardcoded)
```
Email: adminalvi@nutritrack.com
Password: Admin@123

Email: adminfaiza@nutritrack.com
Password: Admin@123
```

## 📁 Project Structure
```
nutritrack-pro/
├── index.php (Landing Page)
├── README.md
├── assets/
│   ├── css/
│   │   ├── main.css (Main stylesheet)
│   │   └── landing.css (Landing page styles)
│   ├── js/
│   │   ├── main.js (Utility functions)
│   │   └── auth.js (Authentication logic)
│   └── images/ (Profile pictures, icons)
├── backend/
│   ├── config/
│   │   ├── database.php (Database connection)
│   │   └── session.php (Session management)
│   ├── controllers/
│   │   └── AuthController.php (Authentication logic)
│   └── api/
│       ├── login.php
│       ├── signup_user.php
│       ├── signup_dietitian.php
│       └── logout.php
├── pages/
│   ├── user/ (User dashboard and pages)
│   ├── dietitian/ (Dietitian dashboard and pages)
│   └── admin/ (Admin dashboard and pages)
├── database/
│   └── schema.sql (Complete database structure)
└── includes/ (Reusable components)
```

## 🚀 Installation Instructions

### Prerequisites
- XAMPP / WAMP / LAMP (Apache + MySQL + PHP)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Step 1: Setup Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `nutritrack_pro`
3. Import the SQL file: `database/schema.sql`
4. Or run the SQL commands directly in phpMyAdmin SQL tab

### Step 2: Configure Database Connection
1. Open `backend/config/database.php`
2. Update database credentials if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nutritrack_pro');
```

### Step 3: Deploy Application
1. Copy the entire `nutritrack-pro` folder to your web server root directory:
   - XAMPP: `C:\xampp\htdocs\`
   - WAMP: `C:\wamp64\www\`
   - LAMP: `/var/www/html/`

### Step 4: Access the Application
1. Start Apache and MySQL services
2. Open your browser and navigate to:
   - `http://localhost/nutritrack-pro-fixed/`

## 📝 Default Sample Data
The database includes sample data:
- 4 Sample Dietitians (already approved)
- 20 Food items in food database
- Sample credentials for testing

## 🎨 Features Implemented

### User Features
- ✅ User Dashboard with calorie tracking
- ✅ Food Tracking (Breakfast, Lunch, Dinner, Snacks)
- ✅ Water Intake Tracking (8 glasses)
- ✅ Smart Pantry with recipe suggestions
- ✅ Find & Subscribe to Dietitians
- ✅ Personalized Diet Plan (from dietitian)
- ✅ Messaging System with dietitian
- ✅ Alerts System (calorie limit, missed meals)
- ✅ Profile Management with BMI calculator
- ✅ Weight Tracking (weekly updates only)
- ✅ Analytics (Daily/Weekly/Monthly)

### Dietitian Features
- ✅ Dietitian Dashboard
- ✅ Client Management
- ✅ Create Diet Plans for clients
- ✅ Client Progress Tracking
- ✅ Messaging with clients
- ✅ Send Alerts to clients
- ✅ Editable Profile & About section
- ✅ Client Analytics View

### Admin Features
- ✅ Admin Dashboard with statistics
- ✅ User Management (View/Delete/Search)
- ✅ Dietitian Management (View/Delete/Search)
- ✅ Food Database Management (Add/Edit/Delete)
- ✅ Pending Dietitian Approvals
- ✅ Hardcoded admin credentials

## 🔒 Security Features
- Password hashing using PHP password_hash()
- SQL injection prevention using prepared statements
- Session-based authentication
- Role-based access control
- Input validation on both client and server side

## 💡 Key Technologies
- **Frontend**: Pure HTML5, CSS3, Vanilla JavaScript (No frameworks)
- **Backend**: PHP 7.4+ (No Laravel or frameworks)
- **Database**: MySQL (No MongoDB or NoSQL)
- **Architecture**: MVC-inspired structure
- **Authentication**: Session-based
- **API**: RESTful JSON endpoints

## 📊 Database Schema
The application uses 15 tables:
1. users
2. dietitians
3. pending_dietitians
4. food_items
5. food_logs
6. water_logs
7. pantry_items
8. recipes
9. subscriptions
10. diet_plans
11. diet_plan_days
12. messages
13. alerts
14. progress_tracker
15. weight_logs

## 🎯 Business Logic

### BMI Calculation
BMI = weight (kg) / (height (m))²

### Daily Calories (Mifflin-St Jeor Equation)
**Men**: BMR = 10 × weight + 6.25 × height - 5 × age + 5
**Women**: BMR = 10 × weight + 6.25 × height - 5 × age - 161

Daily Calories = BMR × Activity Multiplier

### Macros Distribution
- Protein: 30% of calories (÷ 4 calories/gram)
- Carbs: 40% of calories (÷ 4 calories/gram)
- Fats: 30% of calories (÷ 9 calories/gram)

### Auto Alerts
1. **Calorie Limit Exceeded**: Triggers when daily calories > target calories
2. **Missed Meal Logging**: Triggers if no meals logged by certain time

## 🌐 Browser Support
- Chrome (recommended)
- Firefox
- Safari
- Edge
- Opera

## 📱 Responsive Design
Fully responsive layout that works on:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (< 768px)

## ⚙️ Configuration Files
- `backend/config/database.php` - Database settings
- `backend/config/session.php` - Session management
- `backend/controllers/AuthController.php` - Authentication logic

## 🐛 Troubleshooting

### Database Connection Error
- Check if MySQL service is running
- Verify database credentials in `backend/config/database.php`
- Ensure database `nutritrack_pro` exists

### Login Not Working
- Clear browser cookies and cache
- Check if sessions are enabled in PHP
- Verify credentials are correct

### Pages Not Loading
- Check Apache service is running
- Verify .htaccess file permissions
- Check PHP error logs

## 📞 Support
For any issues or questions, refer to the inline code comments or modify according to your needs.

## 📄 License
This is a course project for educational purposes.

## 👨‍💻 Development Notes
- All pages include "Back" button as required
- No external frameworks used (except essential PHP functions)
- Follows the exact specifications provided
- Fully functional and ready to deploy

## 🎓 Course Project Completion
This project fulfills all requirements for the web programming course including:
- ✅ Multi-role authentication system
- ✅ Complete CRUD operations
- ✅ Complex business logic
- ✅ Responsive UI
- ✅ Database relationships
- ✅ Session management
- ✅ Form validation
- ✅ Dynamic content rendering
Contributor: Faiza

