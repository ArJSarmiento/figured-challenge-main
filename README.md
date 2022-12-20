# Figured Code Challenge

## Setup

### Laravel
Clone this repository and run the following commands:
```bash
cd ./figured-challenge-main

# Install Laravel dependencies and packages
composer install

# Copy ENV example file to run your own local setup
cp .env.example .env

# Generate your local encryption key
php artisan key:generate

# Make sure the development server is on
# Makes the database migrations
# Choose `yes` when prompted to create database
php artisan migrate

# Seeds the database with the supplied CSV file
php artisan db:seed

# Runs the backend
php artisan serve

```

### Vue3
The project uses Vue3 with Vite, Inertia and TailWindCSS. Run the following commands on a separate terminal to set up.
```bash
# make sure you are using Node v16 or up
# if not change version with nvm 
nvm use 16

# install vue packages
npm install

# This runs the Vite development server that provides Hot Module Replacement located at localhost:3000.
npm run dev
```

## Application
You may now visit the site in the browser in http://127.0.0.1:8000/

You may register for a new account or use the seeded test credentials below
```
Email: test@example.com
Password:  password
```
## Demo
<a href="https://www.youtube.com/watch?v=XrOBDaQPo8Q" target="_blank"><img src="https://img.youtube.com/vi/XrOBDaQPo8Q/hqdefault.jpg" ></a>

## Testing
On the terminal, run `php artisan test`.

## Developed by
- [Arnel Jan Sarmiento](https://github.com/ArJSarmiento)
