<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchases extends CI_Controller {

  // Constructor
  function __construct() {

    parent::__construct();

    // Get user details
    $this->load->model('users_model');
    $this->user = $this->users_model->getUser();
    $this->housemates = $this->users_model->getHousemates($this->user['house_id']);
    $this->houses = $this->users_model->getUserHouses($this->user['user_id']);

    $this->load->model('purchases_model');
    $this->load->model('comments_model');

    if (ENVIRONMENT == 'development') {
      $this->output->enable_profiler(true);
    }

  }

  // Index: All Purchases
  function index() {

    // Options
    // by default, use GET parameters,
    // otherwise use user's setting or sys default.
    $opt = array();
    $opt_user = $this->user['conf'];
    // show - deleted or all purchases
    $opt['show'] = ($this->input->get('show') == 'deleted') ? 'deleted' : 'ok';
    // user purchase display options
    $opt['order'] = ($this->input->get('order')) ? $this->input->get('order') : $opt_user['purchases_order'];
    $opt['order_by'] = ($this->input->get('order_by')) ? $this->input->get('order_by') : $opt_user['purchases_order_by'];

    // Get all purchases
    $data['purchases'] = $this->purchases_model->getPurchases(
      $this->user['house_id'],
      $opt
    );

    // Add purchase permissions
    foreach ($data['purchases'] as $pid => $p) {
      $data['purchases'][$pid]['perm_userCanModify'] = $this->_userCanModify($p);
    }

    // Calculate housemate balances
    $data['balances'] = $this->_getBalances($data['purchases']);

    // Send to view
    $data['user'] = $this->user;
    $data['housemates'] = $this->housemates;
    $data['houses'] = $this->houses;
    $data['options'] = $opt;
    $this->load->view('template', $data);

  }

  function _verifyPurchasePermissions($purchase_id, $user_id) {
    
    //$perms['view'] = false;
    //$perms['edit'] = false;
    //$perms['delete'] = false;

    // Verify purchase with edit_id exists

    // Verify it is owned by (current) user.

    // TODO - validate the edit id!
    // return FALSE; // not valid!

    // Valid!
    return $purchase_id;

  }

  /**
   * Add Purchase
   */
  function add() {

    $this->load->helper('form');

    // CSRF token has already been checked by this stage

    if ($this->input->post()) {

      $data = $this->_validatePurchase();

      if (!isset($data['error'])) {

        // Use "edit" parent, if present
        if ($this->input->post('edit_id')) {
          $edit_id = $this->_verifyPurchasePermissions($this->input->post('edit_id'), $this->user['user_id']);
          if ($edit_id === FALSE) {
            $this->session->set_flashdata('error', 'Edit failed. You do not have permissions to edit this purchase (or it was not found).');
            redirect('purchases');
          } else {
            // Add edit_id to purchase_details
            $data['purchase_details']['edit_parent'] = $edit_id;
          }
        }

        // Fetch user's house
        $house_id = $this->users_model->getUserHouseSelected($this->user['user_id']);

        // Add purchase to database
        $purchase_id = $this->purchases_model->addPurchase($this->user['user_id'], $house_id, $data['purchase_details']);
        // Check for failure adding purchase
        if ($purchase_id != FALSE) {

          // Add comment to database (if any)
          //d($data['purchase_details']['comment'], 'data-comment');
          if ($data['purchase_details']['comment'] != '') {
            // TODO - what happens when adding comment fails?
            $this->comments_model->addComment($purchase_id, $data['purchase_details']['comment'], $this->user['user_id']);
          }
          
          // Forward user depending on whether it's a new purchase, or an edit
          if ($this->input->post('edit_id') && ($edit_id !== FALSE)) {
            // Edited Purchase
            $this->session->set_flashdata('success', 'Purchase edited. ');
            redirect("purchases/view/$purchase_id");
          } else {
            // New Purchase
            // Provide link to view purchase, but redirect to add purchase page.
            //var_dump('added purchase #'.$success.' to database');
            $this->session->set_flashdata('success', 'Purchase added. View it <a href="'.site_url("purchases/view/$purchase_id").'">here</a> (or see <a href="'.site_url('purchases').'">all purchases</a>).');
            redirect('purchases/add');            
          }


        }

      }

    }

    $data['title'] = "Add Purchase";
    $data['user'] = $this->user;
    $data['housemates'] = $this->housemates;
    $data['view'] = 'purchases/add_purchase';
    $this->load->view('template', $data);

  }

  /**
   * Edit Purchase
   * - this page also renders the 'add_purchase' view, but with
   *   existing values updated, and with the ID defined in the form.
   */
  function edit($purchase_id) {

    // Ensure a valid purchase id was given
    if (!is_numeric($purchase_id)) {
      $this->session->set_flashdata('error', 'Invalid purchase ID was specified for editing.');
      redirect('purchases');
    }

    // Get purchase details
    $p = $this->purchases_model->getPurchaseById($purchase_id);

    //d($p, 'p');

    // Check that (exactly) one purchase was found
    if (count($p) == 1) {
      $data['edit'] = $p[$purchase_id];
    } else {
      $this->session->set_flashdata('error', 'No purchase found with this ID.');
      redirect('purchases');
    }

    
    if (FALSE == $this->_userCanView($p[$purchase_id])) {
      $this->session->set_flashdata('error', 'Purchase does not exist, or you are not allowed to view it.');
      redirect('purchases');
    }
    if (FALSE == $this->_userCanModify(reset($p))) {
      $this->session->set_flashdata('error', 'Only the creator or payer can edit a purchase.');
      redirect("purchases/view/$purchase_id");
    }
    
    // Do not allow editing of deleted purchases or old versions
    if ($p[$purchase_id]['status'] == 'deleted') {
      $this->session->set_flashdata('error', 'Deleted purchases cannot be edited.');
      redirect("purchases/view/$purchase_id");
    } else if ($p[$purchase_id]['status'] == 'edited') {
      $this->session->set_flashdata('error', 'Previous purchase versions cannot be edited.');
      redirect("purchases/view/$purchase_id");
    }

    $this->load->helper('form');

    // Update unfilled $repop elements with previous purchase details (to be edited).
    foreach ($data['edit'] as $edit_key => $edit_val) {
      //d($edit_val, $edit_key);
      // Don't overwrite if repop already exists
      if (!isset($data['repop'][$edit_key]) || $data['repop'][$edit_key] == '') {
        $data['repop'][$edit_key] = $edit_val;
      }
    }

    $data['title'] = 'Edit Purchase';
    $data['user'] = $this->user;
    $data['purchase_id'] = $purchase_id;
    $data['housemates'] = $this->housemates;
    $data['view'] = 'purchases/add_purchase';
    $this->load->view('template', $data);

  }

  /*
   * View Purhase
   */
  function view($purchase_id = NULL) {
    
    $this->load->helper('form');

    if ($purchase_id == NULL) {
      $this->session->set_flashdata('error', 'No purchase ID specified.');
      redirect('purchases');
    }

    // Ensure a valid (numeric) purchase id was given
    if (!is_numeric($purchase_id)) {
      $this->session->set_flashdata('error', 'Invalid purchase ID was specified when viewing details.');
      redirect('purchases');
    }

    $data['purchase_id'] = $purchase_id;
    $next_purchase_id = $purchase_id;
    $old_purchase_id = NULL;

    // Collect any old (edited) versions of the purchase too.
    do {
            
      // Get purchase details
      $next_purchase = $this->purchases_model->getPurchaseById($next_purchase_id);
      
      //d($next_purchase, 'next purchase');
      
      // Was purchase found, and has user got permission to view it?
      if ($next_purchase === FALSE || FALSE == $this->_userCanView($next_purchase[$next_purchase_id])) {
        $this->session->set_flashdata('error', 'Purchase does not exist, or you are not allowed to view it.');
        redirect('purchases');
      }
      
      // If there is a child of the main purchase, redirect user to the latest version.
      if (NULL == $old_purchase_id && NULL != $next_purchase[$next_purchase_id]['edit_child']) {
        $this->session->set_flashdata('info', 'You were redirected to the latest version of this purchase.');
        redirect('purchases/view/' . $next_purchase[$next_purchase_id]['edit_child']);
      }
      
      // Add to purchases array.
      $data['purchases'][$next_purchase_id] = $next_purchase[$next_purchase_id];

      // Get purchase comments
      $comments = $this->comments_model->getComments($next_purchase_id);
      //d($comments, 'comments');
      if (count($comments) > 0) {
        foreach ($comments as $comment) {
          $data['purchases'][$comment['parent_id']]['comments'][$comment['comment_id']]['text'] = $comment['comment_text'];
          $data['purchases'][$comment['parent_id']]['comments'][$comment['comment_id']]['added_by'] = $comment['comment_added_by'];
          $data['purchases'][$comment['parent_id']]['comments'][$comment['comment_id']]['added_time'] = $comment['comment_added_time'];
          $data['purchases'][$comment['parent_id']]['comments'][$comment['comment_id']]['type'] = $comment['comment_type'];
        }
      }
      
      // Get any changes made to the purchase (apart from on the first purchase!)
      if ($old_purchase_id != NULL) {
        $data['purchases'][$old_purchase_id]['edit_changes'] = $this->_getPurchaseDifferences($data['purchases'][$next_purchase_id], $data['purchases'][$old_purchase_id], $this->housemates);
      }
      
      $old_purchase_id = $next_purchase_id;
      $next_purchase_id = $data['purchases'][$next_purchase_id]['edit_parent'];
            
    } while ($next_purchase_id != NULL);

    // Check that (at least) one purchase was found
    if (count($data['purchases']) == 0) {
      $this->session->set_flashdata('error', 'No purchase found with this ID.');
      redirect('purchases');
    }
    
    // Sort the array by its keys
    ksort($data['purchases']);

    // Add permissions information
    foreach ($data['purchases'] as $pid => $p) {
      $data['purchases'][$pid]['perm_userCanModify'] = $this->_userCanModify($p);
    }

    // Detect required format, and send to view
    if (strtolower($this->input->get('format')) === 'json') {
      $this->load->helper('json_helper');
      output_json($data);
    } else {
      $data['title'] = 'Purchase Details';
      $data['view'] = 'purchases/view_purchase';
      $data['user'] = $this->user;
      $data['housemates'] = $this->housemates;
      $this->load->view('template', $data);
    }

  }

  function restore($purchase_id = NULL) {

    if ($purchase_id == NULL) {
      $this->session->set_flashdata('error', 'No purchase specified to be restored.');
      redirect("purchases");
    }

    // Get Purchase info
    $purchase = $this->purchases_model->getPurchaseById($purchase_id);

    // Verify purchase exists and user has permission.
    if ($purchase == FALSE || !$this->_userCanView($purchase[$purchase_id])) {
      $this->session->set_flashdata('error', 'Purchase does not exist, or you are not allowed to view it.');
      redirect("purchases");
    }

    // Verify purchase exists and user has permission.
    if (!$this->_userCanModify(reset($purchase))) {
      $this->session->set_flashdata('error', 'Only the creator or payer can restore a purchase.');
      redirect("purchases/view/$purchase_id");
    }

    if ($purchase[$purchase_id]['status'] != 'deleted') {
      $this->session->set_flashdata('info', 'This purchase has not been deleted.');
      redirect("purchases/view/$purchase_id");
    }

    if ($this->purchases_model->restorePurchase($purchase_id, $this->user['user_id'])) {
      $this->session->set_flashdata('success', 'Purchase restored.');
      redirect("purchases/view/$purchase_id");
    } else {
      $this->session->set_flashdata('error', 'Purchase could not be restored. Please try again later.');
      redirect("purchases/view/$purchase_id");
    }

  }

  function delete($purchase_id = NULL) {

    if ($purchase_id == NULL) {
      $this->session->set_flashdata('error', 'No purchase specified for deletion.');
      redirect("purchases");
    }

    // Get Purchase info
    $purchase = $this->purchases_model->getPurchaseById($purchase_id);

    // Verify purchase exists and user has permission.
    if ($purchase == FALSE || !$this->_userCanView($purchase[$purchase_id])) {
      $this->session->set_flashdata('error', 'Purchase does not exist, or you are not allowed to view it.');
      redirect("purchases");
    }

    // Verify purchase exists and user has permission.
    if (!$this->_userCanModify($purchase[$purchase_id])) {
      $this->session->set_flashdata('error', 'Only the creator or payer can delete a purchase.');
      redirect("purchases/view/$purchase_id");
    }

    if ($purchase[$purchase_id]['status'] == 'deleted') {
      $this->session->set_flashdata('info', 'This purchase is not been deleted, so cannot be restored.');
      redirect("purchases/view/$purchase_id");
    }

    if ($this->purchases_model->deletePurchase($purchase_id, $this->user['user_id'])) {
      $this->session->set_flashdata('success', 'Purchase deleted. View it <a href="' . site_url("purchases/view/$purchase_id") . '">here</a> or <a href="' . site_url("purchases/restore/$purchase_id") . '">restore it</a> now.');
      redirect("purchases");
    } else {
      $this->session->set_flashdata('error', 'Purchase could not be deleted. Please try again later.');
      redirect("purchases/view/$purchase_id");
    }

  }

  /*
   * Comment on Purhase
   */
  function addcomment($purchase_id = NULL) {

    if ($purchase_id == NULL) {
      $this->session->set_flashdata('error', 'No comment ID specified to comment on.');
      redirect("purchases");
    }

    // Get Purchase info
    $purchase = $this->purchases_model->getPurchaseById($purchase_id);

    // Verify purchase exists and user has permission.
    if ($purchase == FALSE || !$this->_userCanView($purchase[$purchase_id])) {
      $this->session->set_flashdata('error', 'Purchase does not exist, or you are not allowed to view it.');
      redirect("purchases");
    }

    if ($text = htmlspecialchars(trim($this->input->post('commenttext')))) {

      // Is it a comment or a dispute?
      // note - $_POST['button_dispute'] will be '' if dispute was pressed.
      $type = (($this->input->post('button_dispute') === '') ? 'dispute' : 'comment');

      if ($this->purchases_model->addComment($this->user['user_id'], $purchase_id, $text, $type)) {
        // it worked
        $this->session->set_flashdata('success', "Your $type was added successfully.");
        redirect("purchases/view/$purchase_id");
      } else {
        // comment save failed
        $this->session->set_flashdata('error', "There was an error saving your $type. Please try again later.");
        redirect("purchases/view/$purchase_id");
      }
    } else {
      $this->session->set_flashdata('error', 'No comment data received.');
      redirect("purchases");
    }
  }

  /*
   * Consolidate Purchases
   */
  function consolidate() {

    // Get all purchases
    $data['purchases'] = $this->purchases_model->getPurchases(
      $this->user['house_id']
    );

    // Calculate housemate balances
    $balances = $this->_getBalances($data['purchases']);

    // work on a copy of original balances
    $balances_after = $balances;

    $max = 1;
    $min = -1;
    $i = 1; // safety to avoid infinite loop on error
    $diff = 2;
    while ($i <= 25) {

      // detect highest and lowest balances
      $max = max($balances_after);
      //d($max, 'max');
      $min = min($balances_after);
      //d($min, 'min');

      if ($max < 0.005) {
        break;
      }

      //d($diff, 'diff1');
      $diff = min(abs($max), abs($min));
      //d($diff, 'diff');

      //d($balances_after, 'balances_after');

      // Subtract from max ...
      $max_id = array_search ($max, $balances_after);
      //d($max_id, 'max_id');
      $balances_after[$max_id] -= $diff;
      // ... and add to min.
      $min_id = array_search ($min, $balances_after);
      //d($min_id, 'min_id');
      $balances_after[$min_id] += $diff;

      $payments[] = array(
        'payer' => $min_id,
        'payee' => $max_id,
        'price' => $diff
      );

      $i++;
    }
    // Show a warning if things have got out of hand!
    if ($i == 25) {
      $payments[] = '... limit of 25 transactions reached.';
    }

    // Send to view
    $data['title'] = 'Consolidate Purchases';
    $data['user'] = $this->user;
    $data['housemates'] = $this->housemates;
    $data['houses'] = $this->houses;
    $data['payments'] = $payments;
    $data['balances'] = $balances;
    $data['balances_after'] = $balances_after;
    $data['view'] = 'purchases/consolidate';
    $this->load->view('template', $data);

  }

  /*
   * Export Purchases
   */
  function export($filetype = null) {

    if (in_array($filetype, array('csv', 'xml'))) {

      // List of (escaped) housemate names
      foreach ($this->housemates as $housemate) {
        $housemate_names[$housemate['user_id']] = str_replace(',','_',$housemate['user_name']);
      }

      // Get all purchases
      $purchases = $this->purchases_model->getPurchases(
        $this->user['house_id']
      );

      if ($filetype == 'csv') {

        // CSV Headings
        $filedata = 'Purchase,Description,Added By,Added Time,Payer,Date,House ID,';
        $filedata .= implode(',',$housemate_names);
        $filedata .= "\n";

        // Build CSV Data
        foreach ($purchases as $purchase_id => $purchase) {
          $filedata .= $purchase_id . ','
                          . filterCsv($purchase['description']) . ','
                          . filterCsv($housemate_names[$purchase['added_by']]) . ','
                          . $purchase['added_time'] . ','
                          . filterCsv($housemate_names[$purchase['payer']]) . ','
                          . $purchase['date']. ','
                          . $purchase['house_id'].',';
          foreach ($this->housemates as $housemate) {
            $filedata .= (isset($purchase['payees'][$housemate['user_id']]) ? filterCsv($purchase['payees'][$housemate['user_id']]) : '0') . ',';
          }
          $filedata .= "\n";
        }
      } elseif ($filetype == 'xml') {

        // $xml = new SimpleXMLElement('<purchases/>');
        // array_walk_recursive($purchases, array ($xml, 'addChild'));
        // $filedata = $xml->asXML();

        // TODO - Yeah, yeah, use an XML parser. Maybe.

        // Begin XML
        $filedata = '<?xml version="1.0"?>';
        $filedata .= "\n<purchases>";

        // Build XML Data
        foreach ($purchases as $purchase_id => $purchase) {
          $filedata .= "\n\t<purchase>";
          $filedata .= "\n\t\t<id>{$purchase_id}</id>";
          $filedata .= "\n\t\t<description>".filterXml($purchase['description'])."</description>";
          $filedata .= "\n\t\t<added_by>".filterXml($housemate_names[$purchase['added_by']])."</added_by>";
          $filedata .= "\n\t\t<added_time>".$purchase['added_time']."</added_time>";
          $filedata .= "\n\t\t<payer>".filterXml($housemate_names[$purchase['payer']])."</payer>";
          $filedata .= "\n\t\t<date>".$purchase['date']."</date>";
          $filedata .= "\n\t\t<house>".$purchase['house_id']."</house>";
          $filedata .= "\n\t\t<payees>";
          foreach ($purchase['payees'] as $housemate_id => $price) {
            $filedata .= "\n\t\t\t<payee>";
            $filedata .= "\n\t\t\t\t<name>".$this->housemates[$housemate_id]['user_name']."</name>";
            if (isset($purchase['payees'][$housemate_id])) {
              $filedata .= "\n\t\t\t\t<price>".$purchase['payees'][$housemate_id]."</price>";
            } else {
              $filedata .= "\n\t\t\t\t<price>0</price>";
            }
            $filedata .= "\n\t\t\t</payee>";
          }
          $filedata .= "\n\t\t</payees>";
          $filedata .= "\n\t</purchase>";
        }

        // End XML
        $filedata .= "\n</purchases>";

      }

      $this->load->helper('download');

      // Name and download CSV
      $filename = 'stsp_export_'.date('YmdHis').'.'.$filetype;
      force_download($filename, $filedata);

    } else {

      // Send to view
      $data['title'] = 'Export Purchase Data';
      $data['view'] = 'purchases/export';
      $this->load->view('template', $data);

    }
  }

  /**
   * Validate POST Data
   *
   * - create repopulation array for refilling form elements
   * - create error array containing validation problems
   * - create purchase_details array with valid/safe information only
   */
  function _validatePurchase() {

    // Return results in an array
    $ret = array();

    // Validate: "description"
    $repop['description'] = htmlspecialchars(trim($this->input->post('description')));
    if (!$repop['description']) {
      $error['description'] = 'Description cannot be blank.';
    } elseif (count($repop['description']) > 50) {
      $error['description'] = 'Description cannot exceed 50 characters.';
    } else {
      $purchase_details['description'] = $repop['description'];
    }

    // Validate: "payer"
    $repop['payer'] = $this->input->post('payer');
    if (!$repop['payer']) {
      $error['payer'] = 'No user was selected as the "Payer" of this purchase.';
    } else {
      $valid = false;
      foreach ($this->housemates as $housemate) {
        if ($housemate['user_id'] == $repop['payer']) {
          $valid = true; break;
        }
      }
      if ($valid) {
        $purchase_details['payer'] = $repop['payer'];
      } else {
        $error['payer'] = 'The user selected as purchase "Payer" is not your housemate.';
      }
    }

    // Validate: "purchase_date"
    $repop['purchase_date'] = htmlspecialchars(trim($this->input->post('purchase_date_submit')));
    if (!$repop['purchase_date']) {
      $error['purchase_date'] = 'You must specify a purchase date.';
    } elseif (FALSE) {
      // TODO - verify specified date is a valid format, and exists!
    } else {
      $purchase_details['purchase_date'] = $repop['purchase_date'];
    }

    // Validate: "split_type"
    $repop['split_type'] = $this->input->post('split_type');
    $repop['total_price'] = trim($this->input->post('total_price'));
    $repop['payees'] = $this->input->post('payees');
    $repop['price_custom'] = $this->input->post('price_custom');
    if ($repop['split_type'] == 'even') {
      $purchase_details['split_type'] = 'even';

      // Validate: "total_price"
      if (!$repop['total_price']) {
        $error['total_price'] = 'Total Price must be specified if choosing an "even" split.';
      } elseif (!is_numeric($repop['total_price'])) {
        $error['total_price'] = 'Total Price must be numeric.';
      } else {
        $purchase_details['total_price'] = $repop['total_price'];

        // Validate: "payees"
        if (!is_array($repop['payees']) || count($repop['payees']) == 0) {
          $error['payees'] = 'One or more users must be selected to share the total price.';
        } else {
          $tick_count = 0; // count 'ticked' housemates
          $tmp = array_values($repop['payees']);
          foreach ($this->housemates as $housemate) {
            if (in_array($housemate['user_id'], $tmp)) {
              $tick_count++;
            }
          }
          if ($tick_count == 0) {
            $error['payees'] = 'One or more selected users are not housemates.';
          } else {
            // divide the total price between ticked housemates
            foreach ($this->housemates as $housemate) {
              if (in_array($housemate['user_id'], $tmp)) {
                $purchase_details['payees'][] = array(
                  'user_id' => $housemate['user_id'],
                  'price' => ($purchase_details['total_price']/$tick_count)
                );
              }
            }
          }
        }
      }
    } elseif ($repop['split_type'] == 'custom') {
      $purchase_details['split_type'] = 'custom';

      // Validate: "price_custom"
      if (!$repop['price_custom'] || !is_array($repop['price_custom']) || (count($repop['price_custom']) == 0)) {
        $error['price_custom'] = 'One or more housemates must have prices specified when using a "custom" split.';
      } else {
        $user_prices_count = 0; // count number of defined housemate prices
        foreach ($this->housemates as $housemate) {
          if (isset($repop['price_custom'][$housemate['user_id']]) && ($repop['price_custom'][$housemate['user_id']] != '')) {
            if (!is_numeric($repop['price_custom'][$housemate['user_id']])) {
              $error['price_custom'] = 'One or more custom prices were not numeric.';
              $user_prices_count = -1;
              break;
            }
            // Include this user's price in the purchase details
            $purchase_details['payees'][] = array(
                'user_id' => $housemate['user_id'],
                'price' => $repop['price_custom'][$housemate['user_id']]
              );
            $user_prices_count++;
          }
        }
        if ($user_prices_count == 0) {
          $error['price_custom'] = 'One or more housemates must have prices specified when using a "custom" split.';
          //$error['price_custom'] = 'One or more specified users are not housemates.';
        }
      }
    } else {
      $error['split_type'] = 'Purchase splitting is not defined. Select either "even" or "custom" splitting.';
    }

    // Validate: comment
    $repop['comment'] = htmlspecialchars(trim($this->input->post('comment')));
    if (count($repop['comment']) > 250) {
      $error['comment'] = 'Comment cannot exceed 250 characters';
    } else {
      $purchase_details['comment'] = $repop['comment'];
    }

    $ret['repop'] = $repop;
    if (isset($error)) {
      $ret['error'] = $error;
    }
    $ret['purchase_details'] = $purchase_details;

    return $ret;

  }
  
  function _userCanModify($purchase) {
    
    if ($this->user['user_id'] == $purchase['added_by']) {
      return TRUE;
    }
    
    if ($this->user['user_id'] == $purchase['payer']) {
      return TRUE;
    }
    
    return FALSE;
    
  }
  
  function _userCanView($purchase) {
    
    if ($this->_userCanModify($purchase)) {
      return TRUE;
    }
    
    if (in_array($this->user['user_id'], array_keys($purchase['payees']))) {
      return TRUE;
    }

    // Did a housemate add it?
    if (in_array($purchase['added_by'], array_keys($this->housemates))) {
      return TRUE;
    }
    
    return FALSE;
    
  }

  /**
   * Compare Purchases
   * - Determine what has changed between two purchase versions, i.e. from $a to $b.
   * - $users is specified to extract usernames into string.
   * - returns an array of change explanations, eg.
   *   > "changed the purchase type from X to Y".
   *   > "made no changes".
   *   > "increased the purchase price from X to Y".
   *   > "split the purchase with an extra user".
   *   > ... etc.
   */
  function _getPurchaseDifferences($a, $b, $users) {
    
    //d($a,'a');
    //d($b,'b');
    //d($users, 'users');

    $diffs = array();
            
    // Description
    if ($a['description'] != $b['description']) {
      $diffs[] = "changed description from '<b>" . $a['description'] . "</b>' to '<b>" . $b['description'] . "</b>'.";
    }
    
    // Date
    if ($a['date'] != $b['date']) {
      $diffs[] = "changed purchase date from '<b>" . $a['date'] . "</b>' to '<b>" . $b['date'] . "</b>'.";
    }
    
    // Payer
    if ($a['payer'] != $b['payer']) {
      $diffs[] = "changed payer from '<b>" . $users[$a['payer']]['user_name'] . "</b>' to '<b>" . $users[$b['payer']]['user_name'] . "</b>'.";
    }
    
    // Split Type
    if ($a['split_type'] != $b['split_type']) {
      $diffs[] = "changed split type from '<b>" . $a['split_type'] . "</b>' to '<b>" . $b['split_type'] . "</b>'.";
    }

    // Payees
    foreach (array_unique(array_merge(array_keys($a['payees']),array_keys($b['payees']))) as $payee_id) {
      if (isset($a['payees'][$payee_id])) {
        if (isset($b['payees'][$payee_id])) {
          // in BOTH - check if amounts have changed
          if ($a['payees'][$payee_id] != $b['payees'][$payee_id]) {
            if ($a['payees'][$payee_id] > $b['payees'][$payee_id]) {
              $diff = "decreased";
            } else {
              $diff = "increased";
            }            
            $diffs[] = $diff . " amount '<b>" . $users[$payee_id]['user_name'] . "</b>' owes from <b>" . render_price($a['payees'][$payee_id]) . "</b> to <b>" . render_price($b['payees'][$payee_id]) . "</b>.";
          }
        } else {
          // in A only
          $diffs[] = "removed '<b>" . $users[$payee_id]['user_name'] . "</b>' from the list of payees.";
        }
      } else {
        // in B only
        $diffs[] = "added '<b>" . $users[$payee_id]['user_name'] . "</b>' to the list of payees, for <b>" . render_price($b['payees'][$payee_id]) . "</b>.";
      }
    }

    if (count($diffs) == 0) {
      $diffs[] = 'No changes were made to the purchase.';
    }

    
    return $diffs;
    
  }

  function _getBalances($purchases) {

    $balances = array();

    // Initialise balances as zero
    foreach (array_keys($this->housemates) as $housemate_id) {
      $balances[$housemate_id] = 0;
    }

    //d($data['purchases'], 'purchases');
    foreach ($purchases as $purchase) {
      foreach ($purchase['payees'] as $payee_id => $payee_price) {

      // Add to payer's balance
      $balances[$purchase['payer']] += $payee_price;

      // Subtract from payee's balance
      $balances[$payee_id] -= $payee_price;

      }
    }

    return $balances;
  }

  /**
   * Search
   * - performs search for purchases, returning results in JSON format
   */
  function search() {
    // ...
  }

}

// EOF
