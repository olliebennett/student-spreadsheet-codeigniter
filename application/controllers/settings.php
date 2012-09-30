<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Settings extends CI_Controller {	private $slug = 'settings';		private $is_logged_in;		private $facebook_uid;	// Constructor	function __construct() {		parent::__construct();				$this->auth->require_login();		$this->is_logged_in = TRUE;		$this->facebook_uid = $this->session->userdata('facebook_uid');
				$this->load->model('settings_model');				$this->output->enable_profiler(TRUE);
	}
	// Index
	function index() {					$data['slug'] = 'settings';				// Send to view		$data['slug'] = $this->slug;		$data['is_logged_in'] = $this->is_logged_in;		$data['facebook_id'] = $this->facebook_uid;		$this->load->view('template',$data);
	}	
}// EOF