<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notifications_model extends CI_Model {

    function getNotifications($user_id)
    {

        // Get all purchase details
        $this->db->select('*');
        $this->db->from('notifications n');
        $this->db->join('link_notifications_users lnu', 'n.notification_id = lnu.notification_id', 'left');
        //$this->db->where('lnu.user_id', $user_id);
        //$this->db->where('n.house_id', $house_id);
        if ($user_id !== 'all') {
            $this->db->where('lnu.active', '1');
        }
        $this->db->order_by('lnu.time', 'desc');
        $query = $this->db->get();

        $notifications = array();
        foreach ($query->result() as $n) {
            $notifications[$n->notification_id] = $n;
        }

        //d($notifications, 'notifications');

        return $notifications;

    }

}
// EOF
