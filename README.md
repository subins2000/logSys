logSys
======

PHP Advanced Login System as part of the [Francium Project](http://subinsb.com/the-francium-project)

See this [Blog Post](http://subinsb.com/php-logsys) for more details

Features
========

logSys includes but not limited to :

1. Basic Login/Register Function (Secured by PDO, SHA256).
   Hashes passwords with random salt + site salt + password
2. Password Reset (Forgot Password) functionality
3. 2 Step Login (2 Step Verification by Mobile SMS/E-Mail)
4. Custom fields
5. Device Manager to know the devices that are currently logged in
6. Debugging/Logging made simpler
7. Simple Examples to get you started
8. Lightweight (**42 KB of Awesomeness...**)
9. Maintained frequently
10. Email Functionality

Instructions
============

The [Blog Post](http://subinsb.com/php-logsys) contains entire information on how to install and use logSys

In case of GitHub repo, the following folders contain examples of usage
* example-basic
* example-two-step-login

PHP's mail() function is used to send emails. Most likely, emails sent through it will reach the SPAM folder. To avoid this, add an email function in `config` -> `basic` -> `email_callback`.

I recommend to use [PHPMailer](https://github.com/PHPMailer/PHPMailer/) (SMTP) or [Mailgun API](https://mailgun.com) to send emails.
