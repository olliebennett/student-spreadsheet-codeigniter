<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comments_model extends CI_Model {

    /**
     * Get Comments
     * - Retrieve all comments for a specified purchase
     *
     * @param int $purchase_id ID of purchase(s) for which to get comments
     *
     * @return array of comments
     */
    function getComments($purchase_id)
    {
        
        $this->db->select('*');
        $this->db->from('comments');
        if (is_array($purchase_id)) {
            $this->db->where_in('parent_id', implode(',', $purchase_id));
        } else {
            $this->db->where('parent_id', $purchase_id);
        }
        $query = $this->db->get();

        return $query->result_array();

    }

    function addComment($purchase_id, $comment_string, $user_id, $comment_type = 'dispute') {
        
        $c_data = array(
            'parent_id'          => $purchase_id,
            'comment_text'       => $comment_string,
            'comment_added_by'   => $user_id,
            'comment_added_time' => date("Y-m-d H:i:s") // NOW()
        );

        d($c_data, 'c_data');

        $this->db->insert('comments', $c_data);

        // TODO - handle failure when adding comment
        return TRUE;
    }

}
// EOF
