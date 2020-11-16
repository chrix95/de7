## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

## Setup process
- Clone the project from the github repository using `git clone https://github.com/chrix95/de7.git`
- Run `composer install` to install all composer dependencies
- Run `npm install` to install all node modules and dependencies
- Rename the `.env.example` file to `.env`
- Update your database credentials
```
DB_DATABASE=YOUR_DB_NAME
DB_USERNAME=YOUR_DB_USERNAME
DB_PASSWORD=YOUR_DB_PASSWORD
```
- Run `php artisan migrate` to exceute the migrations created
- Run `php artisan db:seed` to seed the default user details
```
Admin credential:
username: admin@admin.com
password: secret

Driver credential:
username: driver@test.com
password: secret

User credential:
username: user@test.com
password: secret
```
NB: Registration page is defaulted to being a user
- Run `php artisan jwt:secret` to generate the JWT secret key on your .env file
- Update the .env file with your paystack test secret key and test public key 
- To test the setup, run `php artisan serve` (to serve laravel project locally) and `npm run watch` (to compile all assets)

## Contributor
- Christopher Okokon Ntuk
- Software developer
- engchris95@gmail.com