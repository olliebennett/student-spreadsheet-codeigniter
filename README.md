The Student Spreadsheet
=======================

<http://studentspreadsheet.com>

## Requirements

* __Apache__ with `mod_rewrite`
* __MySQL__ or other [supported](http://ellislab.com/codeigniter/user-guide/general/requirements.html) database
* __PHP__ with `curl` and `openssl`
* [__CodeIgniter__](http://ellislab.com/codeigniter) __v2.x__
* [__Composer__](http://getcomposer.org/) Dependency Manager

## Installation / Configuration

Set up your own domain (or make one up and modify your `hosts` file with `127.0.0.1 <your_fake_domain>`). `localhost` is not directly supported by Facebook.

Create a [Facebook app](https://developers.facebook.com/apps), pointing to your domain.

Update the dependencies using Composer (see [these instructions](http://getcomposer.org/doc/00-intro.md) to install)

* `cd student-spreadsheet-codeigniter`
* `composer update`

Update your settings in the `application/config` folder. Specifically:

### `config.php`

* `$config['base_url']` (the domain name you used earlier)
* `$config['encryption_key']`
	
### `database.php`

* `$db['default']['username']`
* `$db['default']['password']`
* `$db['default']['database']`

### `hashids.php`

* `$config['hashids_salt']`

### `email.php`

* `$config['smtp_user']`
* `$config['smtp_pass']`

## Running and Testing

Tests are written for [Selenium](http://docs.seleniumhq.org/). Load the test suite from `test/selenium/StudentSpreadsheet.html` into the Selenium IDE and export/convert to other formats if required.

The tests use the base url `http://offline-studentspreadsheet-codeigniter.com/`, which should be changed to match your DEV system's `$config['base_url']`.

Before running tests, ensure you're registered for your site using Facebook, as the test suite does not store your credentials. You'll also need at least one friend, but I can't help you with that!
