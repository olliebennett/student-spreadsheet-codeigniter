<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model {

	function getUser($forceLogin = TRUE, $forceRegister = TRUE, $forceFacebookRefresh = FALSE) {

    // Log in automatically if in DEMO mode
		if ($this->config->item('stsp_demo')) {
      return $this->getUserByFacebookId('504850777'); // TODO - use a sample user.
    }
    
    // Override when offline
		//return $this->getUserByFacebookId(504850777);

		$this->load->library('HybridAuthLib');

		// Check for user in session
		$user_id_facebook = $this->session->userdata('user_id_facebook');
		//d($user_id_facebook, 'session user_id_facebook');
		//die();
		//if ($user_id_facebook !== FALSE)


		if (!$user_id_facebook || $forceFacebookRefresh) {

			if ($forceLogin) {
				$this->session->set_flashdata('error', 'You must login first.');
				redirect('auth');				
			} else {
				return NULL;
			}

			// Check whether user is connected with Facebook
			// This will force login (if not already logged in) so will redirect to Facebook silently.
			$isUserConnected = $this->hybridauthlib->authenticate('Facebook')->isUserConnected();

			//var_dump($isUserConnected);
			if (!$isUserConnected && $forceRegister) {
				//$this->session->set_flashdata('error', 'You must login first.');
				//redirect('auth');
				die('problem when logging in.');
			}

			// Force user to authenticate
			$user_fb = $this->getUserFacebookInfo();

			$user_id_facebook = $user_fb->identifier;
			$user_name_facebook = $user_fb->displayName;

			// Update user session (for next time)
			$this->session->set_userdata('user_id_facebook', $user_id_facebook);
			$this->session->set_userdata('user_name_facebook', $user_name_facebook);

		}

		$user = $this->getUserByFacebookId($user_id_facebook);
		if ($user == FALSE) {
			// User not yet registered
			if ($forceRegister) {
				$this->session->set_flashdata('error', 'Facebook account <!--"' . $user_id_facebook . '"--> not recognised. Please register first.');
				redirect('register');
			} else {
				// If available, compare Facebook details
				// with stored user details and update as necessary
				if (isset($user_fb)) {
					$this->updateUserFacebookInfo($user_fb, $user);
				}
				return FALSE;
			}

		}

		return $user;
	}

	function getUserFacebookInfo () {
		return $this->hybridauthlib->authenticate('Facebook')->getUserProfile();
		// identifier,webSiteURL,profileURL,photoURL,displayName,description,firstName,lastName,gender,language,age,birthDay,birthMonth,birthYear,email,emailVerified,phone,address,country,region,city,zip
	}

	function updateUserFacebookInfo($user_facebook, $user_database) {

		log_message('info', 'users_model: BEFORE Updating user #' . $user_database['user_id']);
		log_message('debug', 'users_model: database->user_name_facebook: ' . $user_database['user_name_facebook']);
		log_message('debug', 'users_model: database->user_email_facebook: ' . $user_database['user_email_facebook']);
		log_message('debug', 'users_model: database->user_name_first: ' . $user_database['user_name_first']);
		log_message('debug', 'users_model: database->user_name_last: ' . $user_database['user_name_last']);

		log_message('debug', 'users_model: facebook->displayName: ' . $user_facebook->displayName);
		log_message('debug', 'users_model: facebook->email: ' . $user_facebook->email);
		log_message('debug', 'users_model: facebook->firstName: ' . $user_facebook->firstName);
		log_message('debug', 'users_model: facebook->lastName: ' . $user_facebook->lastName);

		// If Facebook details are out of date, update them in database
		if (
				$user_facebook->displayName != $user_database['user_name_facebook'] ||
				$user_facebook->email != $user_database['user_email_facebook'] ||
				$user_facebook->firstName != $user_database['user_name_first'] ||
				$user_facebook->lastName != $user_database['user_name_last']
			) {

			$user_database['user_name_facebook'] = $user_facebook->displayName;
			$user_database['user_email_facebook'] = $user_facebook->email;
			$user_database['user_name_first'] = $user_facebook->firstName;
			$user_database['user_name_last'] = $user_facebook->lastName;

			// Store the updated values
			$data = array(
				'user_name_facebook'  => $user_facebook->displayName,
				'user_email_facebook' => $user_facebook->email,
				'user_name_first'     => $user_facebook->firstName,
				'user_name_last'      => $user_facebook->lastName
			);

			$this->db->where('user_id', $user_database['user_id']);
			$this->db->update('users', $data);
			log_message('info', 'users_model: Updated user #' . $user_database['user_id']);
		}

		return $user_database;

	}

	function getUserByFacebookId($facebook_id) {

		$this->db->select();
		$this->db->where('user_id_facebook', $facebook_id);
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
	 *			$housemates[x]['user_id_facebook']
	 *			$housemates[x]['user_name_facebook']
	 *
	 * - the current user is $housemates[0]
	 * - even this user may not have registered yet (so may not have user_id)
	 */
	function createHouse($housename, $housemates) {

		// Find those users who already have accounts
		$this->db->select('user_id, user_id_facebook, house_id');
        foreach ($housemates as $housemate) {
        	// TODO - it is probably nicer to use a subquery here.
        	$this->db->or_where('user_id_facebook', $housemate['user_id_facebook']);
        }
        $query = $this->db->get('users');
		foreach ($query->result() as $row) {
		    for ($i = 0; $i < count($housemates); $i++) {
		    	if ($housemates[$i]['user_id_facebook'] == $row->user_id_facebook) {
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
        		$this->db->insert('users', array(
        			'user_id_facebook' => $housemates[$i]['user_id_facebook'],
        			'user_name_facebook' => $housemates[$i]['user_name_facebook'],
        			'user_name' => $housemates[$i]['user_name_facebook'] // Update locally stored username too
        		));
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
			// TODO - use WHERE user_id IN x,y,z instead?
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
