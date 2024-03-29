<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Register extends CI_Controller {

	// Constructor
	function __construct() {

		parent::__construct();

		/*
		if (ENVIRONMENT == 'demo') {
			$this->session->set_flashdata('warning', 'Creating new houses is not supported in DEMO mode.');
			redirect('/settings');
		}
		*/

		// Get User info, but do not force registration (!)
		$this->load->model('users_model');
		$this->user = $this->users_model->getUser(TRUE, FALSE); // Force login, but not registration

		// Get list of user's friends
		$this->fb_friends = $this->users_model->getUserFacebookFriends();

	    if (ENVIRONMENT == 'development') {
	      $this->output->enable_profiler(true);
	    }
	}

	function index() {

		$this->load->helper('form');

		$data['title'] = "Create House";

		// Check whether user is already registered
		// (they are allowed to register again...)
		if ($this->user) {
			// User logged in, but not yet registered
			$data['user'] = $this->user;
		}

		$data['fb_friends'] = $this->fb_friends;
		//var_dump($data['fb_friends']);

		// Check for POST
		if ($this->input->post()) {

			// Check valid parameters
			$register_housemates = $this->input->post('register');
			$register_housename = htmlspecialchars($this->input->post('housename'));

			// Validate input
			if (strlen($register_housename) < 3) {
				$this->session->set_flashdata('warning', 'The house name must be at least 3 characters long.');
				redirect('register');
			}
			if (!is_array($register_housemates) || count($register_housemates) === 0) {
				$this->session->set_flashdata('warning', 'Select at least one housemate.');
				redirect('register');				
			}

			// Add current user to list of housemates
			$housemates[0]['social_identifier_facebook'] = $this->session->userdata('social_identifier_facebook');
			$housemates[0]['social_displayName_facebook'] = $this->session->userdata('social_displayName');
			$housemates[0]['social_firstName'] = $this->session->userdata('social_firstName');
			$housemates[0]['social_lastName'] = $this->session->userdata('social_lastName');

			// Verify proposed housemates are friends and save names
			$friend_ids = array_keys($this->fb_friends);
			foreach ($register_housemates as $housemate_input) {
				if (in_array($housemate_input, $friend_ids)) {
					$housemates[] = array(
						'social_identifier_facebook' => $housemate_input,
						'social_displayName_facebook' => $this->fb_friends[$housemate_input]
						);
				} else {
					$this->session->set_flashdata('error', 'One or more selected housemates are not friends!');
					log_message('warn', "Housemates not friends!");
					redirect('register');
				}
			}

			//var_dump($housemates);

			$result = $this->users_model->createHouse($register_housename, $housemates);

			if ($result === FALSE) {

				// Registration failure!
				$this->session->set_flashdata('error', 'Sorry - Registration failed due to a server error!');
				log_message('error', "Registration failed - some server error?");
				redirect('register');

			} else {

				// Registration success!

				$this->users_model->clearUserFriends();

				$this->session->set_flashdata('success', 'You\'re registered! Review your settings below.');
				redirect('settings/refresh');

			}

		} else {

			// No POST data entered
			$data['view'] = 'register/form';

		}

		$this->load->view('template', $data);

	}

}

// EOF
