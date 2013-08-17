<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model {

	function getUser($forceLogin = TRUE, $forceRegister = TRUE, $forceFacebookRefresh = FALSE) {

		// Log in automatically if in DEMO mode
		if (ENVIRONMENT === 'demo') {

			// 100004986727805
			
			$demo_user['user_id'] = 1;
			$demo_user['facebook_id'] = '100004986727805'; //'504850777';
			$demo_user['facebook_displayName'] = 'Sample User';
			$demo_user['facebook_firstName'] = 'Sample';
			$demo_user['facebook_lastName'] = 'User';

			$this->session->set_userdata('social_identifier_facebook', $demo_user['facebook_id']);
			$this->session->set_userdata('social_displayName', $demo_user['facebook_displayName']);
			$this->session->set_userdata('social_firstName', $demo_user['facebook_firstName']);
			$this->session->set_userdata('social_lastName', $demo_user['facebook_lastName']);

			$fake_user = $this->getUserByFacebookId($demo_user['facebook_id']); // TODO - use a sample user.
			if ($fake_user === FALSE) {
				show_error('An error occurred while logging you in as a demo user. Test user does not exist!');
			}
		}

		// Improve page load performance by preventing a request if possible:
		// Check locally if user is logged in (has session)
		$social_identifier_facebook = $this->session->userdata('social_identifier_facebook');
		if ($social_identifier_facebook !== FALSE) {
			
			// check if user exists in db
			$user = $this->getUserByFacebookId($social_identifier_facebook);
			if ($user !== FALSE) {
				return $user;
			}
		}

		// User not found in local session - try the HybridAuth "slow way" ...

		$this->load->library('HybridAuthLib');

		// Check whether user is connected with Facebook
		// This will NOT force login (at this stage).
		$isConnectedWithFacebook = $this->hybridauthlib->isConnectedWith('Facebook');

		// Return null if user is not connected and we're not forcing them to login.
		if (!$isConnectedWithFacebook && !$forceLogin && !$forceRegister) {
			return NULL;
		}

		// Redirect to log-in page if required
		if (!$isConnectedWithFacebook && ($forceLogin || $forceRegister)) {
			$this->session->set_flashdata('info', 'Please log in before viewing that page.');
			$this->session->set_userdata('next_page', $this->uri->uri_string());
			redirect('auth');
		}

		// We want remaining users to log in - get their details!
		// (this will force a log-in if they're not already...)
		$user_fb = $this->hybridauthlib->authenticate('Facebook')->getUserProfile();

		//d($user_fb,'user_fb');

		// Update local session, to speed up next time
		$this->session->set_userdata('social_identifier_facebook', $user_fb->identifier);
		// This is also required if user is registering.
		$this->session->set_userdata('social_displayName', $user_fb->displayName);
		$this->session->set_userdata('social_firstName', $user_fb->firstName);
		$this->session->set_userdata('social_lastName', $user_fb->lastName);

		//log_message('info', 'user session data set from Facebook:');
		//d_log($user_fb->firstName, '[users_model] fb firstName');

		// Check whether user has registered
		$user = $this->getUserByFacebookId($user_fb->identifier);
		if ($user === FALSE) {
			
			// User not yet registered

			// Shall we force them to register?
			if ($forceRegister) {
				$this->session->set_flashdata('info', 'You are logged in. All you need to do is create a house.');
				redirect('register');
			} else {
				return NULL;
			}

		}

		// User is logged in and registered!

		// Update facebook info if required.
		if ($forceFacebookRefresh) {
			$this->updateUserFacebookInfo($user_fb, $user);
		}

		return $user;
	}

	/*
	function getUserFacebookInfo () {
		$this->load->library('HybridAuthLib');
		return $this->hybridauthlib->authenticate('Facebook')->getUserProfile();
		// identifier,webSiteURL,profileURL,photoURL,displayName,description,firstName,lastName,gender,language,age,birthDay,birthMonth,birthYear,email,emailVerified,phone,address,country,region,city,zip
	}
	*/


	function updateUserFacebookInfo($user_facebook, $user_database) {

		log_message('info', 'users_model: BEFORE Updating user #' . $user_database['user_id']);
		log_message('debug', 'users_model: database->social_displayName: ' . $user_database['social_displayName']);
		log_message('debug', 'users_model: database->user_email_facebook: ' . $user_database['user_email_facebook']);
		log_message('debug', 'users_model: database->social_firstName: ' . $user_database['social_firstName']);
		log_message('debug', 'users_model: database->social_lastName: ' . $user_database['social_lastName']);

		log_message('debug', 'users_model: facebook->displayName: ' . $user_facebook->displayName);
		log_message('debug', 'users_model: facebook->email: ' . $user_facebook->email);
		log_message('debug', 'users_model: facebook->firstName: ' . $user_facebook->firstName);
		log_message('debug', 'users_model: facebook->lastName: ' . $user_facebook->lastName);

		// If Facebook details are out of date, update them in database
		if (
				$user_facebook->displayName != $user_database['social_displayName'] ||
				$user_facebook->email != $user_database['user_email_facebook'] ||
				$user_facebook->firstName != $user_database['social_firstName'] ||
				$user_facebook->lastName != $user_database['social_lastName']
			) {

			$user_database['social_displayName'] = $user_facebook->displayName;
			$user_database['user_email_facebook'] = $user_facebook->email;
			$user_database['social_firstName'] = $user_facebook->firstName;
			$user_database['social_lastName'] = $user_facebook->lastName;

			// Store the updated values
			$data = array(
				'social_displayName'  => $user_facebook->displayName,
				'user_email_facebook' => $user_facebook->email,
				'social_firstName'	 => $user_facebook->firstName,
				'social_lastName'	  => $user_facebook->lastName
			);

			$this->db->where('user_id', $user_database['user_id']);
			$this->db->update('users', $data);
			log_message('info', 'users_model: Updated user #' . $user_database['user_id']);
		}

		return $user_database;

	}

	function getUserByFacebookId($facebook_id) {

		$this->db->select();
		$this->db->where('social_identifier_facebook', $facebook_id);
		$this->db->limit(1);
		$this->db->from('users');

		$query = $this->db->get();

		$fbusers =  $query->result_array();

		//var_dump($arr);

		if (count($fbusers) !== 1) {
			return FALSE;
		} else { // Only 1 entry found
			// Move configuration items into sub-array
			foreach ($fbusers[0] as $k => $v) {
				if (substr($k, 0, 5) === 'conf_') {
					$fbuser['conf'][substr($k,5)] = $v;
				} else {
					$fbuser[$k] = $v;
				}
			}
			return $fbuser;
		}
	}

	/**
	 * Get User's Facebook Friends
	 * - store in session for faster access
	 */
	function getUserFacebookFriends() {

		if (ENVIRONMENT == 'demo') {
			return array(
				2 => "User Two",
				3 => "User Three",
				4 => "User Four",
				5 => "User Five",
				6 => "User Six",
				7 => "User Seven"
			);
		}

		// Retrieve from session, if available
		$friends_session = $this->session->userdata('fb_friends');

		if ($friends_session == FALSE) {

			$this->load->library('HybridAuthLib');

			// Grab friends from Facebook API
			$friends_obj = $this->hybridauthlib->authenticate('Facebook')->getUserContacts();

			// Clean and sort friends list
			$friends_arr = array();
			foreach ($friends_obj as $friend) {
				$friends_arr[$friend->identifier] = $friend->displayName;
			}
			asort($friends_arr);

			$this->session->set_userdata('fb_friends', $friends_arr);

		} else {

			$friends_arr = $friends_session;

		}

		return $friends_arr;

	}

	/**
	 * Clear User Friends from Session
	 * - to save space and processing in session database
	 */
	function clearUserFriends() {
		$this->session->unset_userdata('fb_friends');
	}

	/**
	 * Create House
	 * @param $housename - house name
	 * @param $housemates - array of housemate details:
	 *			$housemates[x]['social_identifier_facebook']
	 *			$housemates[x]['social_displayName']
	 *
	 * - the current user is $housemates[0]
	 * - even this user may not have registered yet (so may not have user_id)
	 */
	function createHouse($housename, $housemates) {

		d_log($housemates, 'housemates');

		// Find those users who already have accounts
		$this->db->select('user_id, social_identifier_facebook, house_id');
		foreach ($housemates as $housemate) {
			// TODO - it is probably nicer to use a subquery here.
			$this->db->or_where('social_identifier_facebook', $housemate['social_identifier_facebook']);
		}
		$query = $this->db->get('users');
		foreach ($query->result() as $row) {
			for ($i = 0; $i < count($housemates); $i++) {
				if ($housemates[$i]['social_identifier_facebook'] == $row->social_identifier_facebook) {
					$housemates[$i]['user_id'] = $row->user_id;
					$housemates[$i]['house_id'] = $row->house_id;
					break; // end "for" loop
				}
			}
		}

		// Create those users that don't already exist
		for ($i = 0; $i < count($housemates); $i++) {
			if (!isset($housemates[$i]['user_id'])) {
				// user not in database
				$arr_insert['social_identifier_facebook'] = $housemates[$i]['social_identifier_facebook'];
				$arr_insert['social_displayName_facebook'] = $housemates[$i]['social_displayName_facebook'];
				// Update locally stored username too
				$arr_insert['user_name'] = $housemates[$i]['social_displayName_facebook'];
				// Add first, last names if we know them (i.e. for current user)
				if (isset($housemates[$i]['social_firstName']) && isset($housemates[$i]['social_lastName'])) {
					$arr_insert['user_name_first'] = $housemates[$i]['social_firstName'];
					$arr_insert['user_name_last'] = $housemates[$i]['social_lastName'];
				}
				$this->db->insert('users', $arr_insert);
				unset($arr_insert);
				// TODO - These inserts could be "batched", but that adds complexity
				$housemates[$i]['user_id'] = $this->db->insert_id();
			}
		}

		// Create house
		$this->db->trans_start(); // start transaction
		$this->db->insert('houses', array(
			'house_name' => $housename,
			'house_created_by' => $housemates[0]['user_id'],
			'house_joined' => date("Y-m-d H:i:s") // NOW()
			));
		$house_id = $this->db->insert_id(); // get created house id

		// Insert links between house and users
		// i.e. associate each housemate with this house
	   	$lhu_data = array();
		foreach ($housemates as $housemate) {
			$lhu_data[] = array(
				'house_id' => $house_id,
				'user_id' => $housemate['user_id']
			);
		}
		$this->db->insert_batch('link_houses_users', $lhu_data);

		// TODO - Notify the existing users that they're in a new house!

		// Update users to use new house.
		// Note: only update current user, and other new users
		$this->db->set('house_id', $house_id);
		$in = array();
		for ($i = 0; $i < count($housemates); $i++) {
			if (!isset($housemates[$i]['house_id']) || $i == 0) {
				$in[] = $housemates[$i]['user_id'];
			}
		}
		$this->db->where_in('user_id', $in);
		$this->db->update('users');

		$this->db->trans_complete(); // commit transaction

		// Verify that everything worked okay
		if ($this->db->trans_status() == true) {
			//return $house_id;
			return TRUE;
		}
		log_message('error', 'Transaction failed!');
		return FALSE;
	}

	/**
	 * Get User's Selected House
	 *
	 * @param $user_id
	 *
	 * @return id of user's "primary" or "selected" house
	 */
	function getUserHouseSelected($user_id) {

		// Get user's current/selected house
		$this->db->select('house_id');
		$this->db->from('users');
		$this->db->where('user_id', $user_id);

		// Workaround: use tmp variable for PHP <= 5.3
		$tmp = $this->db->get()->result_array();
		return $tmp[0]['house_id'];

	}

	function getUserHouses($user_id) {

		$this->db->select('h.house_id, h.house_name, h.house_created_by, h.house_currency, h.house_joined');
		$this->db->from('link_houses_users lhu');
		$this->db->join('houses h', 'h.house_id = lhu.house_id');
		$this->db->where('lhu.user_id', $user_id);
		$houses_tmp = $this->db->get()->result_array();

		foreach ($houses_tmp as $house_tmp) {
			$houses[$house_tmp['house_id']] = $house_tmp;
		}

		return $houses;

	}

	function getHousemates($house_id) {

		// Get all users from the house
		$this->db->select('user_id');
		$this->db->from('link_houses_users');
		$this->db->where('house_id', $house_id);
		$house_users = $this->db->get()->result_array();

		// Get details of those users
		$this->db->select('*');
		$this->db->from('users');
		$in = array();
		foreach ($house_users as $house_user) {
			$in[] = $house_user['user_id'];
		}
		$this->db->where_in('user_id', $in);
		$hms = $this->db->get()->result_array();

		foreach ($hms as $hm) {
			$housemates[$hm['user_id']] = $hm;
		}

		return $housemates;

	}
}
// EOF
