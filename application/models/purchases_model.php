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
    function getPurchases($house_id)
    {

        // Get all purchase details
        $this->db->select('*');
        $this->db->from('purchases p');
        $this->db->join('link_purchases_users lpu', 'lpu.purchase_id = p.purchase_id');
        $this->db->where('p.house_id', $house_id);
        $this->db->where('p.status', 'ok');
        $this->db->order_by('p.added_time', 'desc');
        $query = $this->db->get();


        // Return this query result if exporting data
        //if ($this->uri->segment(2) == 'export') {
        //    return $query;
        //}

        return $this->purchasesArrayFromQuery($query);

        /*
        $sql = "
            SELECT      *,
                        COUNT(*) IN (
                            SELECT  c.comment_id
                            FROM    comments c
                            ) as comment_count
            FROM        purchases p
            LEFT JOIN   comments c
            ON          c.parent_id = p.purchase_id
            JOIN        link_purchases_users lpu
            ON          p.purchase_id = lpu.purchase_id
            GROUP BY    p.purchase_id
        ";
        $query = $this->db->query($sql);
        return $query->result_array();

        */


        // $this->db->select(); // defaults to all (*)
        // $this->db->order_by('date', 'desc');
        // $this->db->from('purchases');
        // $this->db->limit(4);
        // $query = $this->db->get();

        // return $query->result_array();
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
            $purchases[$row->purchase_id]['edit_child']  = $row->edit_child;


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
                //'deleted_time' => date("Y-m-d H:i:s"), // NOW()
                //'deleted_by' => $user_id,
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
