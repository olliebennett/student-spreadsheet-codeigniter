<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Settings extends CI_Controller {

  // Constructor
  function __construct() {
    parent::__construct();
        
    $this->load->model('users_model');
    $this->user = $this->users_model->getUser();
    $this->houses = $this->users_model->getUserHouses($this->user['user_id']);
    // Get housemates for each house
    foreach ($this->houses as $house) {
      $this->housemates_all[$house['house_id']] = $this->users_model->getHousemates($house['house_id']);
    }

    if (ENVIRONMENT == 'development') {
      $this->output->enable_profiler(true);
    }
  }

  // Index
  function index() {

    // Save settings, if submitted
    if ($this->input->post()) {
      $data = $this->_validateSettings();
    }

    // 
    if (isset($data['settings']) && !isset($data['error'])) {

      log_message("info", "Updating settings...");

      d($this->input->post(), 'raw post data');
      d($data['settings'], 'validated settings');

      // Get updated purchases
      //$new_settings = array_diff_assoc($data['settings'], $this->user['conf']);

      //d($data['settings'],'settings');
      //d($this->user['conf'],'user conf');
      //d($new_settings,'new_settings');

      foreach ($data['settings'] as $setting_key => $setting_val) {
        switch ($setting_key) {
          case 'house_id':
            $update['house_id'] = $setting_val;
            $this->user['house_id'] = $setting_val;
            break;
          case 'user_email':
            $update['user_email'] = $setting_val;
            $this->user['user_email'] = $setting_val;
            break;
          case 'user_mobile':
            $update['user_mobile'] = $setting_val;
            $this->user['user_mobile'] = $setting_val;
            break;
          case 'purchases_order':
            $update['conf_purchases_order'] = $setting_val;
            $this->user['conf']['purchases_order'] = $setting_val;
            break;
          case 'purchases_order_by':
            $update['conf_purchases_order_by'] = $setting_val;
            $this->user['conf']['purchases_order_by'] = $setting_val;
            break;
          case 'purchases_per_page':
            $update['conf_purchases_per_page'] = $setting_val;
            $this->user['conf']['purchases_per_page'] = $setting_val;
            break;
          default:
            // process notification option arrays
            if (substr($setting_key, 0, 7) == 'conf_n_') {
              $update[$setting_key] = $setting_val;
            }
            log_message("warn", "Unrecognised setting parameter: '$setting_key'");
        }
      }
      
      // Record that user has saved their settings!
      $update['conf_seensettings'] = 1;

      //d($update, 'array for updating database...');
      //die();

      // Save to database
      $this->db->where('user_id', $this->user['user_id']);
      $this->db->update('users', $update);

      // Save succeeded
      $this->session->set_flashdata('success', 'Your settings have been updated!');
      redirect('settings');

    }
    
    // Get user details
    $data['user'] = $this->user;
    $data['houses'] = $this->houses;
    $data['housemates_all'] = $this->housemates_all;

    // Send to view
    $this->load->helper('form');
    $this->load->view('template', $data);

  }

  function refresh() {

    // Not available in demo mode!
    if (ENVIRONMENT == 'demo') {
      $this->session->set_flashdata('warning', 'Facebook info cannot be updated in DEMO version.');
      redirect('settings');
    }

    // Save any changed Facebook details to database
    //$this->user_fb = $this->users_model->getUserFacebookInfo();
    //$this->user = $this->users_model->updateUserFacebookInfo($this->user_fb, $this->user);

    // Get the user, and force login+register+refresh
    $this->user = $this->users_model->getUser(TRUE, TRUE, TRUE);
    $this->session->set_flashdata('info', 'Your details were updated from Facebook.');
    redirect('settings');

  }

  function unsubscribe($email = null, $hash = null) {

    if (!$email || !$hash) {
      show_error("There was a problem with the request.");
    }

    $email = urldecode($email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      show_error("An invalid email address was supplied");
    }

    $this->load->model('email_model');
    if (!$this->email_model->_validateUnsubscribeUrl($email, $hash)) {
      show_error("There was a problem validating the email and hash provided.");
    }

    $data['email'] = "olliebennett@gmail.com";

    if ($this->input->post('confirm')) {
      
      // Unsubscribe any instance of this email address in the database
      $this->email_model->unsubscribe($email);

      // Send user home.
      $this->session->set_flashdata('success', "You have successfully unsubscribed <b>$email</b> from all notifications.");
      redirect(); 

    }

    $data['view'] = 'settings/unsubscribe_confirm';
    
    // Get user details
    $data['user'] = $this->user;
    
    $data['title'] = "Unsubscribe";
    $this->load->helper('form');
    $this->load->view('template', $data);

  }

  // Save
  function _validateSettings() {

    // Return results in an array
    $ret = array();
  
    // Validate: "house"
    $repop['house_id'] = $this->input->post('house_id');
    if ($repop['house_id'] !== FALSE) {
      if (!in_array($repop['house_id'], array_keys($this->housemates_all))) {
        $error['house_id'] = 'You are not a member of the selected house.';
      } else {
        $settings['house_id'] = $repop['house_id'];
      }
    }

    // Validate: "purchases_order_by"
    $repop['purchases_order_by'] = strtolower($this->input->post('purchases_order_by'));
    if ($repop['purchases_order_by'] !== FALSE) {
      if (!in_array($repop['purchases_order_by'], array('added_time', 'date'))) {
        $error['purchases_order_by'] = 'An invalid purchase sort type was specified.';
      } else {
        $settings['purchases_order_by'] = $repop['purchases_order_by'];
      }
    }

    // Validate: "purchases_order"
    $repop['purchases_order'] = strtolower($this->input->post('purchases_order'));
    if ($repop['purchases_order'] !== FALSE) {
      if (!in_array($repop['purchases_order'], array('asc', 'desc'))) {
        $error['purchases_order'] = 'An invalid purchase sort order was specified.';
      } else {
        $settings['purchases_order'] = $repop['purchases_order'];
      }
    }

    // Validate: "purchases_per_page"
    $repop['purchases_per_page'] = $this->input->post('purchases_per_page');
    if ($repop['purchases_per_page'] !== FALSE) {
      if (!in_array($repop['purchases_per_page'], $this->config->item('purchases_per_page'))) {
        $error['purchases_per_page'] = 'An invalid number of purchases to view per page was specified.';
      } else {
        $settings['purchases_per_page'] = $repop['purchases_per_page'];
      }
    }

    // Validate: "user_email"
    $repop['user_email'] = $this->input->post('user_email');
    if ($repop['user_email'] !== FALSE) {
      if ($repop['user_email'] === '') { 
        $settings['user_email'] = ''; // user may have deleted his email address
      } elseif (!filter_var($repop['user_email'], FILTER_VALIDATE_EMAIL)) {
        $error['user_email'] = 'The entered email address does not appear to be valid';
      } else {
        $settings['user_email'] = $repop['user_email'];
      }
    }

    // Validate: "user_mobile"
    $repop['user_mobile'] = $this->input->post('user_mobile');
    if ($repop['user_mobile'] !== FALSE) {
      $valid_phone = validate_mobile($repop['user_mobile']);
      if ($repop['user_mobile'] === '') {
        $settings['user_mobile'] = ''; // user may have deleted his phone number
      } elseif (FALSE === $valid_phone) {
        $error['user_mobile'] = 'The entered phone number was not a valid UK mobile number.';
      } else {
        $settings['user_mobile'] = $valid_phone;
      }
    }

    // Validate: "notification" settings
    //$repop['notification'] = $this->input->post('notification');

    // We do this whether it's defined or not, 
    // ... because if user has unticked all checkboxes, "notification" is not sent
    foreach (array_keys($this->config->item('notification_options')) as $nkey) {
      // Check if this key was specified
      $repop["conf_n_$nkey"] = $this->input->post("conf_n_$nkey");
      if (FALSE !== $repop["conf_n_$nkey"]) {
        // If so, user ticked (at least) one method (email/sms/etc).
        foreach ($this->config->item('notification_methods') as $nmet) {
          if (isset($repop["conf_n_$nkey"][$nmet[0]])) {
            // Build a comma-separated list of the methods (for MySQL 'SET' field)
            if (isset($settings["conf_n_$nkey"])) {
              $settings["conf_n_$nkey"] .= ',' . $nmet[0];
            } else {
              $settings["conf_n_$nkey"] = $nmet[0];
            }
          }
        }
      } else {
        $settings["conf_n_$nkey"] = null;
      }
    }


    $ret['repop'] = $repop;
    if (isset($error)) {
      $ret['error'] = $error;
    }
    $ret['settings'] = (isset($settings) ? $settings : array());

    return $ret;

  }
  
}

// EOF
