# Fintech Demo

## Installation and set-up
* Laravel v9.52.5 
* PHP v8.1.5
* Clone the repo and run composer install
* Rename .env.example to .env
* Run php artisan key:generate
* Run php artisan serve to start up the application

## How to run the script
One you start the Laravel instance a web app will be accessible normal at http://127.0.0.1:8000/. Upload the demo csv file and submit the form. CSV file is located under: resources/demo folder for your convenience.

## Application config
Application config variables are located under the config/bank.php file.

## Testing
php artisan test
