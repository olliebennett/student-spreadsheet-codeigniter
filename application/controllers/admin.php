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


	function cleardemo() {

		if (ENVIRONMENT != 'demo' && ENVIRONMENT != 'development') {
			show_error("Cleaning is only available in <strong>DEMO</strong> or <strong>DEVELOPMENT</strong> mode!");
		}

		// TODO - enable this check!
		//if (strpos($this->db->database,'demo') === false) {
		//	show_error("It does not appear that the DEMO database is in use. Cleaning aborted.");
		//}

		if ($this->input->get('confirm') === FALSE) {
			show_error("Are you sure you want to wipe the database '" . $this->db->database . "'? <a href=\"" . site_url('admin/cleardemo?confirm') . "\">YES</a>.");
		}

		echo '<h3>1. Clearing demo database tables...</h3>';

		$this->db->truncate('comments'); 
		$this->db->truncate('houses'); 
		$this->db->truncate('link_houses_users'); 
		$this->db->truncate('link_notifications_users'); 
		$this->db->truncate('link_purchases_users'); 
		$this->db->truncate('purchases'); 
		$this->db->truncate('stsp_sessions'); 
		$this->db->truncate('users'); 
		
		echo '<p>OKAY</p>';

		echo '<h3>2. Registering house...</h3>';

		$housename = "Demo House";

		$housemates[] = array(
			'social_identifier_facebook' => '100004986727805',
			'social_displayName_facebook' => 'User One',
			'social_firstName' => 'Test',
			'social_lastName' => 'User'
		);

		$housemates[] = array(
			'social_identifier_facebook' => '100004978868645',
			'social_displayName_facebook' => 'User Two'
		);

		$housemates[] = array(
			'social_identifier_facebook' => '100004973678573',
			'social_displayName_facebook' => 'User Three'
		);

		$this->load->model('users_model');
		if ($this->users_model->createHouse($housename, $housemates)) {
			echo '<p>OKAY</p>';
		} else {
			show_error("Error occurred while creating house");
		}

		echo '<h3>3. Creating purchases...</h3>';

		$purchases[] = array(
			'user_id' => 1,
			'house_id' => 1,
			'data' => array(
				'description' => 'Sample Purchase',
				'payer' => 1,
				'purchase_date' => "2013/07/02",
				'split_type' => "custom",
				'payees' => array(
					0 => array(
						'user_id' => 1,
						'price' => 10					
					),
					1 => array(
						'user_id' => 2,
						'price' => 20
					)
				),
				'comment' => ''
			)
		);

		$purchases[] = array(
			'user_id' => 2,
			'house_id' => 1,
			'data' => array(
				'description' => 'Shopping Trip',
				'payer' => 3,
				'purchase_date' => "2013/07/24",
				'split_type' => "custom",
				'payees' => array(
					0 => array(
						'user_id' => 1,
						'price' => 3.33					
					),
					1 => array(
						'user_id' => 2,
						'price' => 4.44
					),
					2 => array(
						'user_id' => 3,
						'price' => 5.55
					)
				),
				'comment' => 'Beer, Honey, Garlic Bread, Milk. Delicious!'
			)
		);

		$purchases[] = array(
			'user_id' => 3,
			'house_id' => 1,
			'data' => array(
				'description' => 'Council Tax',
				'payer' => 3,
				'purchase_date' => "2013/08/07",
				'split_type' => "even",
				'payees' => array(
					0 => array(
						'user_id' => 1,
						'price' => 15					
					),
					1 => array(
						'user_id' => 2,
						'price' => 15
					),
					2 => array(
						'user_id' => 3,
						'price' => 15
					)
				),
				'comment' => 'Have confirmed our details over the phone, and set up a direct debit.'
			)
		);

		$this->load->model('purchases_model');
		if ($this->purchases_model->addPurchase($user_id, $house_id, $data)) {
			echo '<p>OKAY</p>';
		} else {
			show_error("Error occurred while adding purchase");
		}

	}

}

// EOF
