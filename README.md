Campus Coin - Smart Student Finance Management 
System 
2.Project Overview: 
Campus Coin is a student-focused financial management platform built to help university students 
track income, monitor expenses, set monthly budgets, and improve their saving habits. The system is 
designed using Laravel and modern web interface components to provide a clean and user-friendly 
experience. 
3.Key Features 
Transaction Tracking:** Add, edit, and view income and expense records. 
Budget Management:** Set monthly budget limits by category and compare planned vs actual    
spending. 
Spending Insights:** Get financial recommendations and trend-based reports based on past 
transactions. 
Saving Tips:** Personalized saving guidance and educational finance content. 
Notifications & Alerts:** Receive alerts for overspending and important budget thresholds. 
Category Management:** Organize transactions into categories like food, transport, study, 
shopping, and more. 
Admin Dashboard:** Monitor system-wide transaction activity, top categories, and recent records. 
Student Dashboard:** View summary cards, charts, and quick actions for money management. 
## Main Modules 
* **Student Dashboard:** Overview of current financial health, monthly summary, and quick 
actions. 
* **Transactions:** Add, modify, and review expense/income history. 
* **Budgets:** Create budgets for categories and compare actual spending. 
* **Reports & Insights:** Analyze spending patterns and receive smart recommendations. 
* **Saving Tips:** Access savings advice and bookmark favourite tips. 
* **Notifications:** Alerts for finance activities, budget warnings, and campus notices. 
* **Admin Console:** Admin panel for monitoring users, transactions, categories, and analytics. 
## Project Structure 
``` 
compus_coin/ 
├── app/                  
├── bootstrap/            
├── config/               
├── database/            
├── public/                
├── resources/            
├── routes/                
├── storage/               
├── tests/                 
├── .env.example           
├── artisan                
├── composer.json         
├── package.json           
├── vite.config.js         
# Laravel application logic, models, services, controllers 
# Application bootstrap files 
# Project configuration files 
 # Migrations and seeders 
# Public assets and entry point 
 # Views, CSS, JS, and frontend assets 
# Web routes 
# Application storage and logs 
# Test suite 
# Environment example file 
# Laravel command-line tool 
 # PHP dependencies 
# Node dependencies 
# Vite configuration 
├── tailwind.config.js     
├── phpunit.xml            
└── README.md              
``` 
## Technology Stack 
# Tailwind configuration 
# PHPUnit configuration 
# Project documentation 
* **Backend:** PHP, Laravel 
* **Frontend:** Blade Templates, Tailwind CSS, JavaScript 
* **Database:** MySQL / Laravel Database System 
* **Build Tools:** Vite, Node.js 
* **Testing:** PHPUnit / Pest 
## Installation Instructions 
```bash 
git clone <repository-url> 
cd compus_coin 
composer install 
cp .env.example .env 
php artisan key:generate 
php artisan migrate 
npm install 
npm run dev 
``` 
## Run Project 
```bash 
php artisan serve 
``` 
Then open the application in the browser: 
```text 
http://127.0.0.1:8000 
``` 
## Admin Access 
The system includes an administrator panel for managing users, transactions, budgets, 
announcements, and analytics. Admin functionality can be accessed using the configured admin 
login route. 
## Project Goals 
* Help students take control of personal finances. 
* Encourage better spending decisions through awareness and insights. 
* Simplify budget planning and saving habits. 
* Provide a campus-focused financial dashboard for student life. 
## Future Enhancements 
* Advanced AI-driven financial recommendations 
* Multi-user role expansion 
* Better analytics and export reports 
* Savings goal tracking and reminders 
* Mobile-first responsive improvements 
## Team 
Campus Coin Project 
## Developed By 
Student Development Team 