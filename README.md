# About
Basic file management platform with user authentication & email confirmation. <br>
Upload multiple files, edit and view text files, view images and videos.

# Requirements
Symfony 6.0.0+ <br>
Php 8.0.9+ <br>
NodeJs 14.19.1+

# Installation

Change your DATABASE_URL in .env.local file if needed.

1. Clone the repository to your computer
2. Go to the project directory
3. Run `composer install`
4. Run `npm install`
5. Run `bin/console doctrine:database:create --if-not-exists` to create database
6. Run `bin/console doctrine:migrations:diff` to create migration file
7. Run `bin/console doctrine:migrations:migrate` to execute migration
8. Run `npm run build`

Start your local server: `symfony serve` and go to localhost:8000 or configure a web server like Apache

# Data Fixtures

Run `bin/console doctrine:fixtures:load` to load a test user

Log in with the following credentials: <br>
Email: TestUser@test.com <br>
Password: TestPassword
