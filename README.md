## Figured Code Challenge

## Setup

### Laravel
Clone this repository and run the following commands below:
```bash
cd ./figured-challenge-main

# Install Laravel dependencies and packages
composer install

# Copy ENV example file to run your own local setup
cp .env.example .env

# Generate your local encryption key
php artisan key:generate

# Make sure to create your local database named `figured` before running the migration command.
# This will seed the database with initial inventory data
php artisan migrate:fresh --seed

# You will need to open two terminals to serve both frontend and backend
php artisan serve

```

### Vue3
The project uses Vue3 with Vite, Inertia and TailWindCSS. Run the following commands on a separate terminal to set up.
```bash

# install vue packages
npm install

# This runs the Vite development server that provides Hot Module Replacement is located at localhost:3000.
npm run dev
```

## UI/UX
You may now visit the site in the browser in http://127.0.0.1:8000/

You may register for a new account or use the test credentials below
```
Email: test@example.com
Password:  password
```

## Testing
On the terminal, run `php artisan test`.

## Developed by
- [Arnel Jan Sarmiento](https://github.com/ArJSarmiento)
