<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Home extends CI_Controller {		private $slug = 'home';	private $is_logged_in;		private $facebook_uid;	// Constructor	function __construct() {		parent::__construct();				// Do not require login		$this->is_logged_in = $this->auth->is_logged_in();		$this->facebook_uid = $this->session->userdata('facebook_uid');
				$this->output->enable_profiler(TRUE);			}
	// Index
	function index() {					// set page title and slug (for current menu highlight)		$data['title'] = 'Home';		$data['slug'] = 'home';				$data['content'] = 'This is the homepage.';								// Send to view		$data['slug'] = $this->slug;		$this->load->view('template',$data);
	}	
}// EOF