<?php if (!defined('BASEPATH')) exit('No direct script access allowed');class Register extends CI_Controller {	// Constructor	function __construct() {		parent::__construct();		// Get User info, but do not force registration (!)		$this->load->model('users_model');		$this->user = $this->users_model->getUser(TRUE, FALSE); // Force login, but not registration		//d($this->user, '$this->user');		// Get list of user's friends		$this->fb_friends = $this->users_model->getUserFacebookFriends();	    if (ENVIRONMENT == 'development') {	      $this->output->enable_profiler(true);	    }	}	function index() {		$this->load->helper('form');		$data['title'] = "Create House";		// Check whether user is already registered		// (they are allowed to register again...)		if ($this->user) {			// User logged in, but not yet registered			$data['user'] = $this->user;		}		$data['fb_friends'] = $this->fb_friends;		//var_dump($data['fb_friends']);		// Check for POST		if ($this->input->post()) {			// Check valid parameters			$register_housemates = $this->input->post('register');			$register_housename = htmlspecialchars($this->input->post('housename'));			// Add current user to list of housemates			$housemates[0]['user_id_facebook'] = $this->session->userdata('user_id_facebook');			$housemates[0]['user_name_facebook'] = $this->session->userdata('user_name_facebook');			// Verify proposed housemates are friends and save names			$friend_ids = array_keys($this->fb_friends);			foreach ($register_housemates as $housemate_input) {				if (in_array($housemate_input, $friend_ids)) {					$housemates[] = array(						'user_id_facebook' => $housemate_input,						'user_name_facebook' => $this->fb_friends[$housemate_input]						);				} else {					$this->session->set_flashdata('error', 'One or more selected housemates are not friends!');					log_message('warn', "Housemates not friends!");					redirect('register');				}			}			//var_dump($housemates);			$result = $this->users_model->createHouse($register_housename, $housemates);			if ($result === FALSE) {				// Registration failure!				$this->session->set_flashdata('error', 'Sorry - Registration failed due to a server error!');				log_message('error', "Registration failed - some server error?");				redirect('register');			} else {				// Registration success!				$this->users_model->clearUserFriends();				$this->session->set_flashdata('success', 'You\'re registered! Review your settings below.');				redirect('settings/refresh');			}		} else {			// No POST data entered			$data['view'] = 'register/form';		}		$this->load->view('template', $data);	}}// EOF