<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model {

	function send_email($recipient = null, $subject = null, $message = null) {

		if ($subject == null || $subject == "") {
			log_message('error', 'Email subject not specified.');
			return false;
		}

		if ($message == null || $message == "") {
			log_message('error', 'Email html not specified.');
			return false;
		}

		if ($recipient == null || $recipient == "") {
			log_message('error', 'Email recipient not specified.');
			return false;
		}

$body = '<html>
<head>
<title>'.$subject.'</title>
</head>
<body>
	<h1>'.$subject.'</h1>
	'.$message.'
	
	<p>This message was sent by <a href="http://studentspreadsheet.com/">The Student Spreadsheet</a>.</p>
	<p>To stop receiving these emails, click <a href="' . $this->_getUnsubscribeUrl($recipient) . '">Unsubscribe</a>.</p>
	<p style="color: gray; font-size:small;">&copy; The Student Spreadsheet v'. $this->config->item('stsp_version') .'.</p>
</body>
</html>';
		//load email library
		$this->load->library('email');

		//set email information and content
		$this->email->from('studentspreadsheet@gmail.com', 'StudentSpreadsheet');
		$this->email->to($recipient);

		$this->email->subject($subject);
		$this->email->message($body);

		log_message("debug", "Sending message:\r\nSubject: $subject\r\n\r\nBody: $body");

		if($this->email->send()) {
			log_message('debug', 'Email message sent.');
		} else {
			show_error($this->email->print_debugger());
		}

	}

	function unsubscribe($email) {

		$data = array(
			'conf_n_purchase_dispute' => NULL,
			'conf_n_purchase_comment' => NULL,
			'conf_n_purchase_add' => NULL,
			'conf_n_news' => NULL
		);

		$this->db->where('user_email', $email);
		$this->db->update('users', $data);

		return TRUE;

	}

	function _getUnsubscribeUrl($email) {

		return site_url( 'settings/unsubscribe/' . urlencode($email) . '/' . $this->_getUnsubscribeHash($email) );

	}
	
	function _validateUnsubscribeUrl($email, $hash) {

		return ($hash === $this->_getUnsubscribeHash($email));

	}

	function _getUnsubscribeHash($email) {

		$secret = $this->config->item('email_unsubscribe_secret');

		return sha1($email . $secret);

	}

}
// EOF