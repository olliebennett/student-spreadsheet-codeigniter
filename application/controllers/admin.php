<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	
	private $subnav = array('houses'=>'Houses','users'=>'Users','notifications'=>'Notifications');
									
	// Constructor
	function __construct() {
		parent::__construct();

		// Check user is admin
		$admin_users = $this->config->item('stsp_admins');
		/*
		if (!in_array($this->facebook_uid, $this->config->item('stsp_admins'))) {
			$this->session->set_flashdata('error', 'You do not have access to the admin area.');
			redirect('/');
		}
		*/

		$this->load->model('users_model');
		$this->user = $this->users_model->getUser();

		//d($this->user['user_id'], 'user_id');

		if (!in_array($this->user['user_id'], $admin_users)) {
			$this->session->set_flashdata('error', 'Access denied to admin area.');
			redirect('/');
		}
		
		$this->load->model('admin_model');
		
		if (ENVIRONMENT == 'development') {
			$this->output->enable_profiler(true);
		}
	}

	// Index
	function index() {

		// Send to view
	$data['user'] = $this->user;
		$data['subnav'] = $this->subnav;
		$this->load->view('template', $data);
		
	}
	
	// Show house details
	function houses($house_id = NULL) {
	
		if ($house_id === NULL) {
			
			$data['title'] = 'All Houses';
			
			$data['houses'] = $this->admin_model->get_houses();
			
			// Fetch extra house info
			foreach ($data['houses'] as $key => $house) {
				$data['houses'][$key]['housemates'] = $this->users_model->getHousemates($house['house_id']);
			}
			
			$data['view'] = 'admin/houses';

		} else {

			$data['housemates'] = $this->users_model->getHousemates($house_id);

			$data['view'] = 'admin/houses/details';

		}

		$data['user'] = $this->user;
		$data['subnav'] = $this->subnav;
		$this->load->view('template', $data);
	
	}
	
	// Show user details
	function users($user_id = NULL) {
	
		$data['users'] = $this->admin_model->get_users();

		if ($user_id === NULL) {

			$data['title'] = 'All Users';
			
			$data['user'] = $this->user;

		} else {

			$data['user_houses'] = $this->users_model->getUserHouses($user_id);

			$data['view'] = 'admin/users/details';

		}
				
		// Send to view
		$data['subnav'] = $this->subnav;
		$data['view'] = 'admin/users';
		$this->load->view('template', $data);
	
	}


	function notifications() {
	
		$this->load->model('notifications_model');

		$data['title'] = 'All (Global) Notifications';
		
		$data['user'] = $this->user;
		
		$data['notifications'] = $this->notifications_model->getNotifications('all');
				
		// Send to view
		$data['subnav'] = $this->subnav;
		$data['view'] = 'admin/notifications';
		$this->load->view('template', $data);
	
	}

}

// EOF
