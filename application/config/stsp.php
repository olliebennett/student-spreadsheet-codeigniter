<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Student Spreadsheet Settings

// Version
$config['stsp_version'] = '4.0';

// Admins - user IDs of users with admin access
$config['stsp_admins'] = array(1);

// User Settings
$config['purchases_per_page'] = array(5,10,25,50,100,500);

// Email unsubscribe secret
$config['email_unsubscribe_secret'] = 'YOUR_UNSUBSCRIBE_SECRET';

$config['notification_options'] = array(
  'purchase_add' => 'Purchase <em>added</em> by a housemate',
  //'purchase_dispute' => 'Purchase <em>disputed</em> by a housemate',
  //'purchase_delete' => 'Purchase <em>deleted</em> by a housemate',
  //'purchase_comment' => 'Purchase <em>commented on</em> by a housemate',
  'news' => 'General Student Spreadsheet news'
);
$config['notification_methods'] = array(
	// key | icon-key | name
	//array('mobile','mobile-phone', 'SMS'),
	//array('web', 'globe', 'on-site'),
	array('email', 'envelope', 'email')
);