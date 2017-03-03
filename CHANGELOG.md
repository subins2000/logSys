# Changelog

[Shamil Kashmeri](https://plus.google.com/u/0/105291845791114608759) asked whether he should upgrade from 0.1 to 0.3 and I thought it would be a good idea for making a changelog instead of explaining the new features everytime someone asks.

Each version section also has instructions on how to modify your existing logSys calls to work on the new version.

## 0.9

* 2 Step Login
		* 2 Step Login process is done now with exceptions
		* Added `Fr\LS\TwoStepLogin` exception class
		* Added try limit for entering token. For this `config` -> `two_step_login` -> `token_tries` has been added. Default : 3
		* Revoking the device which is used by user will force a logout
* Requesting tokens more than **5** times will cause account to be blocked
	This can be changed with `config` -> `brute_force` -> `max_tokens`
* All result from database is regarded [as strings or null](https://phpdelusions.net/pdo#returntypes)
* Fixed `Fr\LS::joinedSince()` bug
* Added `Fr\LS::getDeviceID()`
* Added `Fr\LS::userIDExists()`
* Added `Fr\LS::removeToken()`

## 0.8

* logSys is now an object class. Static methods has been replaced
* logSys object is passed by reference as first parameter to all callbacks

### Object

Instead of this :

```
\Fr\LS::config($config);
```

This is the new way :

```
$LS = new \Fr\LS($config);
```

All the functions can be called just like before but on `$LS` object :

```
$LS->login();
$LS->register();
$LS->forgotPassword();
```

### Callbacks

The first parameter of all callbacks will be the logSys object passed by reference. So, if you had a callback like this before :

```
"send_callback" => function($userID, $token){
	...
}
```

You should replace that with this :

```
"send_callback" => function(&$LS, $userID, $token){
	...
}
```

## 0.7

* Added SQLite support
* Fixed bugs
* Added custom message callback

## 0.6.2

* [New! #16](https://github.com/subins2000/logSys/issues/16)

## 0.6.1

* [Fixed bug #15](https://github.com/subins2000/logSys/issues/16)

## 0.6

- Fixed bugs
- Removed SHA256 and instead use Bcrypt

## 0.5

- Two Step Login
- Fixed Bugs
- Manage Devices
- More Examples
- Improved Examples
- `config` -> `info` is now `config` -> `basic`
- Added Email Callback so that developer can change the mechanism of sending email
	Previously, developer had to change the contents of \Fr\LS::sendMail() function
	Callback can be added in `config` -> `basic` -> `email_callback`

## 0.4

- Updates to existing features
- logSys is now part of the Francium Project
- logSys is a static class and not an object class
- "class.loginsys.php" is now "class.logsys.php"
- \Fr\LS::changePassword() is merely a function and does not anymore prints the form for changing the password
- Configuration is done by \Fr\LS::$config and Default Config in \Fr\LS::$default_config
- \Fr\LS::timeSinceJoin() is now \Fr\LS::joinedSince()
- More detailed comments
- Tidied up parts of the code
- Updates to the examples
- Bugs reported has been fixed

## 0.3

- Updates to existing features
- Added feature for blocking brute force attacks with settings variables

## 0.2

- Updates to existing features
- Remember Me option on login
- Improved $LS->init()
- Added $LS->getUser() for easy retrieval of user information
- Added $LS->updateUser() to easily update user information
- Added functionality for getting time since the user joined
- Added $LS->sendMail() for easily updating the preferred way of sending mails

## 0.1

- Login, Register and basic functions of a login system
- Forgot Password feature
- $LS->init()
