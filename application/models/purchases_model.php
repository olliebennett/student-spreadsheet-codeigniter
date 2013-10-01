<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchases_model extends CI_Model {

    /**
     * Get All Purchases
     * - affecting a given user and group.
     *
     * @param int $group_id ID of group
     * @param array $params    Query Parameters
     *              $params['user_id']
     *              $params['orderby']
     *
     * @return array of purchases
     */
    function getPurchases($house_id, $opt = array())
    {

        // Default options:
        $show = (!isset($opt['show']) || !in_array($opt['show'], array('ok','deleted','edited'))) ? 'ok' : $opt['show'];
        $orders[0]['order'] = (!isset($opt['order']) || !in_array($opt['order'], array('asc','desc'))) ? 'desc' : $opt['order'];
        $orders[0]['order_by'] = (!isset($opt['order_by']) || !in_array($opt['order_by'], array('added_time','date','purchase_id'))) ? 'p.added_time' : 'p.' . $opt['order_by'];

        // Define secondary ordering criteria
        switch ($orders[0]['order_by']) {
            case 'p.added_time':
                $orders[1]['order_by'] = 'p.purchase_id';
                $orders[1]['order'] = $orders[0]['order'];
                break;

            case 'p.date':
                $orders[1]['order_by'] = 'p.added_time';
                $orders[1]['order'] = $orders[0]['order'];
                break;
        }

        // Get all purchase details
        $this->db->select('*');
        $this->db->from('purchases p');
        $this->db->join('link_purchases_users lpu', 'lpu.purchase_id = p.purchase_id');
        $this->db->where('p.house_id', $house_id);
        $this->db->where('p.status', $show);
        for ($i = 0; $i < count($orders); $i++) {
            $this->db->order_by($orders[$i]['order_by'], $orders[$i]['order']);
        }
        $query = $this->db->get();

        return $this->purchasesArrayFromQuery($query);

    }

    /**
     * Get Purchase from given Purchase ID
     *
     * - also accepts an array of purchase IDs
     *
     * returns FALSE if purchase not found.
     *
     */
    function getPurchaseById($purchase_id)
    {

        log_message("debug", "Getting purchase with ID='$purchase_id'");

        $this->db->select('*'); // TODO - specify required fields?
        $this->db->from('purchases p');
        $this->db->join('link_purchases_users lpu', 'lpu.purchase_id = p.purchase_id');
        //$this->db->join('comments c', 'c.parent_id = p.purchase_id', 'left');
        if (is_array($purchase_id)) {
            $this->db->where_in('p.purchase_id', implode(',', $purchase_id));
        } else {
            $this->db->where('p.purchase_id', $purchase_id);
        }
        $this->db->order_by('p.added_time', 'desc');

        $query = $this->db->get();

        if($query->num_rows() === 0) {
          return FALSE;
        }

        return $this->purchasesArrayFromQuery($query);

    }


    function purchasesArrayFromQuery($query) {

        // TODO - this technique overwrites/updates the same array multiple times
        // - is there a more efficient method?
        $purchases = array();
        foreach ($query->result() as $row) {

            //d($row, 'row');

            // Standard Purchase details
            $purchases[$row->purchase_id]['description'] = $row->description;
            $purchases[$row->purchase_id]['added_by']    = $row->added_by;
            $purchases[$row->purchase_id]['added_time']  = $row->added_time;
            $purchases[$row->purchase_id]['status']      = $row->status;
            $purchases[$row->purchase_id]['payer']       = $row->payer;
            $purchases[$row->purchase_id]['date']        = $row->date;
            $purchases[$row->purchase_id]['house_id']    = $row->house_id;
            $purchases[$row->purchase_id]['split_type']  = $row->split_type;
            $purchases[$row->purchase_id]['edit_parent']  = $row->edit_parent;
            $purchases[$row->purchase_id]['edit_child']   = $row->edit_child;
            $purchases[$row->purchase_id]['deleted_by']   = $row->deleted_by;
            $purchases[$row->purchase_id]['deleted_time'] = $row->deleted_time;

            // Price this payee must contribute
            $purchases[$row->purchase_id]['payees'][$row->user_id] = $row->price;

        }

        // Update total price
        foreach ($purchases as $purchase_id => $purchase_details) {
            $purchases[$purchase_id]['total_price'] = array_sum(array_values($purchases[$purchase_id]['payees']));
        }

        return $purchases;
    }

    /**
     * Get Purchase Versions
     *
     * - Retrieve all purchases deriving from the given purchase_id
     * - This is effectively the "history" of that purchase
     */
    function getPurchaseVersions($purchase_id) {

        // Retrieve list of previous versions


        // For each version, grab the data

    }

    function addPurchase($user_id, $house_id, $data)
    {

        d_log($user_id, 'user_id');
        d_log($house_id, 'house_id');
        d_log($data, 'data');

        $p_data = array(
            'description' => $data['description'],
            'added_by'    => $user_id,
            'added_time'  => date("Y-m-d H:i:s"), // NOW()
            'payer'       => $data['payer'],
            'house_id'    => $house_id,
            'date'        => $data['purchase_date'],
            'split_type'  => $data['split_type']
        );

        // Specify edit details if necessary
        if (isset($data['edit_parent'])) {
            $p_data['edit_parent'] = $data['edit_parent'];
        }

        // Use a transaction to ensure purchase data is not partially committed.
        $this->db->trans_start(); // start transaction
        $this->db->insert('purchases', $p_data);
        $purchase_id = $this->db->insert_id(); // get created purchase id
        log_message('debug', "Created purchase with id '$purchase_id'.");

        // Insert links between purchases and payee users.
        $lpu_data = array();
        foreach ($data['payees'] as $payee) {
            $lpu_data[] = array(
                  'purchase_id' => $purchase_id,
                  'user_id'     => $payee['user_id'],
                  'price'       => $payee['price']
               );
        }
        //$this->db->set('purchase_id', $purchase_id);
        $this->db->insert_batch('link_purchases_users', $lpu_data);

        // Editing - modify "old version" if required.
        if (isset($data['edit_parent'])) {
            // Deactivate (i.e. "delete") old purchase.
            $p_old_data = array(
                'status' => 'edited',
                'edit_child' => $purchase_id
            );

            $this->db->where('purchase_id', $data['edit_parent']);
            $this->db->update('purchases', $p_old_data);
        }

        $this->db->trans_complete(); // commit transaction

        if ($this->db->trans_status() == true) {
            return $purchase_id;
        }
        log_message('error', 'Transaction failed!');
        return false;
    }

    function deletePurchase($purchase_id, $user_id) {

        $p_data = array(
               'status' => 'deleted',
               'deleted_by' => $user_id,
               'deleted_time' => date("Y-m-d H:i:s"), // NOW()
            );

        $this->db->where('purchase_id', $purchase_id);
        return $this->db->update('purchases', $p_data);

    }

    function restorePurchase($purchase_id) {

        $p_data = array(
               'status' => 'ok',
               'deleted_by' => NULL,
               'deleted_time' => NULL, // NOW()
            );

        $this->db->where('purchase_id', $purchase_id);
        return $this->db->update('purchases', $p_data);

    }

    function addComment($user_id, $purchase_id, $text, $type) {

        $c_data = array(
            'comment_added_by' => $user_id,
            'comment_text'     => $text,
            'comment_type'     => $type,
            'comment_added_time'       => date("Y-m-d H:i:s"), // NOW()
            'parent_id'        => $purchase_id
        );

        // Return success - TRUE or FALSE
        return $this->db->insert('comments', $c_data);

    }

}
// EOF
