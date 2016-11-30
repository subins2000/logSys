logSys
======

PHP Advanced Login System as part of the [Francium Project](http://subinsb.com/the-francium-project)

See this [Blog Post](http://subinsb.com/php-logsys) for more details

Features
========

logSys includes but not limited to :

* Basic Login/Register
* Secure - Uses PDO, Bcrypt & Protection against CSRF
* Password Reset (Forgot Password) functionality
* 2 Step Login (2 Step Verification by Mobile SMS/E-Mail)
* Email Functionality
* Add & use custom user data
* **[Admin Panel](http://subinsb.com/logsys-admin)**
* Device Manager to know the devices that are currently logged in
* Debugging/Logging made simpler
* Simple Examples to get you started
* Lightweight (**42 KB of Awesomeness...**)
* A very active project

Installation
============

Simply download [`LS.php`](https://github.com/subins2000/logSys/blob/master/src/LS.php) file and include it in PHP :

```php
<?php
require_once "LS.php";
```

or use [Composer](http://getcomposer.org) :

```bash
composer require francium/logsys
```

Instructions
============

The **[Blog Post](http://subinsb.com/php-logsys)** contains the entire information on how to install and use logSys.

In case of GitHub repo, the following folders contain examples of usage
* example-basic
* example-two-step-login

PHP's mail() function is used to send emails. Most likely, emails sent through it will reach the SPAM folder. To avoid this, add an email function in `config` -> `basic` -> `email_callback`.

I recommend to use [PHPMailer](https://github.com/PHPMailer/PHPMailer/) (SMTP) or [Mailgun API](https://mailgun.com) to send emails.

Versions
========

## 0.6

**Requires PHP 5.5** - If you want to use it in an older PHP version, get the **password.php** file from [here](https://github.com/ircmaxell/password_compat/blob/master/lib/password.php) and include it before loading `LS.php` file.

This version changes the algorithm used to hash passwords. If you're using an old version of logSys, you **cannoot** upgrade without resetting the existing passwords in your database.

### Upgrade

* (For upgrading from old versions) Remove all existing values in `password` column in your table
* Remove `password_salt` column from your users' table and set the length of the `password` column to `255`.
* Update "LS.php" file. If it's class.logsys.php, rename it to LS.php

Testing
=======

Run `phpunit`.

## Config

Edit `phpunit.xml` and change `DB_TYPE` value to either `mysql` or `sqlite` :

```xml
<var name="DB_TYPE" value="sqlite" />
```