<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
  *  Email Config Variables
  *
  *  See: http://ellislab.com/codeigniter/user-guide/libraries/email.html
  *
  */

$config['useragent']        = 'CodeIgniter';
$config['protocol']         = 'smtp';
$config['mailpath']         = '/usr/sbin/sendmail';
$config['smtp_host']        = 'ssl://smtp.googlemail.com';
$config['smtp_user']        = 'YOUR_GMAIL_USERNAME'; // '@gmail.com' not required 
$config['smtp_pass']        = 'YOUR_GMAIL_PASSWORD';
$config['smtp_port']        = 465;
$config['smtp_timeout']     = 5;
$config['wordwrap']         = TRUE;
$config['wrapchars']        = 76;
$config['mailtype']         = 'text';
$config['charset']          = 'utf-8';
$config['validate']         = FALSE;
$config['priority']         = 3;
$config['crlf']             = "\r\n";
$config['newline']          = "\r\n";
$config['bcc_batch_mode']   = FALSE;
$config['bcc_batch_size']   = 200;

/* End of file email.php */
/* Location: ./system/application/config/email.php */ 