The Student Spreadsheet
=======================

<http://studentspreadsheet.com>


## Requirements

* __Apache__ with `mod_rewrite`
* __PHP__ with `curl` and `openssl`
* [__CodeIgniter__](http://ellislab.com/codeigniter) __v2.x__

## Installation / Configuration

Settings need updating in the `application/config` folder. Specifically:

### `config.php`

* `$config['base_url']`
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
