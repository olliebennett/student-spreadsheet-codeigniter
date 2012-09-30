<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stspauth extends CI_Controller {

	// Constructor
	function __construct() {
		parent::__construct();
				
		$this->output->enable_profiler(TRUE);
		
	}
	
	function index() {
		redirect(site_url('stspauth/login'), 'location');
	}
	
	function login() {
		
		$this->load->model('fizzlebizzle');
		$result = $this->fizzlebizzle->get_user();
		
		if ($result['is_true']) {
			$this->session->set_userdata(array('facebook_uid' => $result['facebook_uid'], 'is_logged_in' => TRUE));
			
			// Build the url to redirect to,
			// eg /stspauth?redirect=items/add -> /items/add
			$redirect_uri = $this->input->get('redirect', TRUE);
			
			redirect(site_url($redirect_uri), 'refresh');
			
		} else {
			$data['slug'] = 'stspauth';
			$data['title'] = 'Login';
			$this->load->view('template', $data);
		}
	}
	
	function logout() {
		$this->auth->logout();
	}
}