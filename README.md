The Student Spreadsheet
=======================

<http://studentspreadsheet.com>


## Requirements

* Apache `mod_rewrite`
* PHP `curl` extension

## Installation / Configuration

The following settings probably require modification, or you can overwrite them. See [this](http://ellislab.com/codeigniter/user-guide/general/environments.html).

### `/config/config.php`

	$config['base_url']
	$config['encryption_key']
	
### `/config/database.php`

	$db['default']['username']
	$db['default']['password']
	$db['default']['database']

### `/config/hashids.php`

	$config['hashids_salt'] = 'YOUR_HASHID_SALT';
	
See [hashids.org](http://www.hashids.org/php/).

