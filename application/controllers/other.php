<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Other extends CI_Controller {
										
	// Constructor
	function __construct() {
		parent::__construct();
	}

	// Index
	function index() {

		show_error('What did you expect this page to do?');
		
	}

	function cleardemo() {

		if (ENVIRONMENT != 'demo' && ENVIRONMENT != 'development') {
			show_error("Cleaning is only available in <strong>DEMO</strong> or <strong>DEVELOPMENT</strong> mode!");
		}

		// Check that the database name contains 'demo' before wiping!
		if (strpos($this->db->database,'demo') === false) {
			show_error("It does not appear that the DEMO database is in use. Cleaning aborted.");
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
			'social_identifier_facebook' => '100004978868645',
			'social_displayName_facebook' => 'Alice'
		);

		$housemates[] = array(
			'social_identifier_facebook' => '100004986727805',
			'social_displayName_facebook' => 'Bob',
			'social_firstName' => 'Bob',
			'social_lastName' => 'TestUser'
		);

		$housemates[] = array(
			'social_identifier_facebook' => '100004973678573',
			'social_displayName_facebook' => 'Charlie'
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

		// Add all defined purchases
		foreach ($purchases as $i => $p) {
			if ($this->purchases_model->addPurchase($p['user_id'], $p['house_id'], $p['data'])) {
				echo '<p>Purchase #' . $i . ' OKAY</p>';
			} else {
				show_error('Error occurred while adding purchase #' . $i);
			}			
		}

		echo '<p>All <strong>DONE</strong>!</p>';

	}
}