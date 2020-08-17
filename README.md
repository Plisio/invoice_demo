#### PHP example

This is a simple example of making crypto payments with Plisio.


###### Installation

To control payments you will need to have MySQL like database.

+ Create a database and add an user:
```
CREATE DATABASE `demo` DEFAULT CHARACTER SET = `utf8` DEFAULT COLLATE = `utf8_general_ci`;
CREATE USER 'demo'@'%' IDENTIFIED BY 'demoPass';
GRANT DELETE, UPDATE, SHOW VIEW, INSERT, LOCK TABLES, ALTER, SELECT, CREATE ON `demo`.* TO 'demo'@'%';
FLUSH PRIVILEGES;
```

+ Create a table to store invoices data from [SQL](https://github.com/Plisio/invoice_demo/blob/master/plisio.sql)

+ Install dependencies with composer
```
composer install
```
+ [Setup your store](https://plisio.net/faq/how-to-connect-the-api) and get "Secret key". 

To handle invoices on your side, please do not forget to enable "White-label payment processing".

+ Put your "Secret key" and database connection settings into settings.php 
```
'secret_key' => 'Your secret key',
```

+ Start the server that will be accessible here: http://localhost:8000
```
php -S localhost:8000
```

This is simple example that should help you to create your custom solutions! 

Please do not forget to validate user input and securely save data into the database.