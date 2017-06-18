logSys
======

[![Build Status](https://travis-ci.org/subins2000/logSys.svg?branch=master)](https://travis-ci.org/subins2000/logSys)

PHP Advanced Login System as part of the [Francium Project](http://subinsb.com/the-francium-project)

See this [Blog Post](http://subinsb.com/php-logsys) for complete documentation.

[Features](http://subinsb.com/php-logsys#Features)

Installation
============

Simply download [`LS.php`](https://github.com/subins2000/logSys/blob/master/src/Fr/LS.php) file and include it in PHP :

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

The following folders contain examples of usage
* example-basic
* example-two-step-login

PHP's mail() function is used to send emails. Most likely, emails sent through it will reach the SPAM folder. To avoid this, add an email function in `config` -> `basic` -> `email_callback`.

I recommend to use [PHPMailer](https://github.com/PHPMailer/PHPMailer/) (SMTP) or [Mailgun API](https://mailgun.com) to send emails.

Versions & Upgrading
====================

See [CHANGELOG](https://github.com/subins2000/logSys/blob/master/CHANGELOG.md)

Contributing
============

* Follow [PSR standards](http://www.php-fig.org/psr)
* Write or modify unit tests for changes you make (if applicable)
* Run unit tests before pull request.

## Security Bugs

Please report security bugs directly to me via [email](https://subinsb.com/contact).

## Testing

First of all do a `composer update` in the main folder. This will install phpunit.

Edit the database configuration in the XML files located in `testing` folder and run :

```
vendor/bin/phpunit -c testing/phpunit.mysql.xml && vendor/bin/phpunit -c testing/phpunit.postgresql.xml && vendor/bin/phpunit -c testing/phpunit.sqlite.xml
```
