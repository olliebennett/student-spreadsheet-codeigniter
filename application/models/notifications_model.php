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

    function sendNotification($action, $user, $housemates, $d) {

        //d_log($action, 'action');
        //d_log($user, 'user');
        //d_log($housemates, 'housemates');
        //d_log($d, 'notification data');

        log_message('debug',"sending '$action' notification");

        // Confirm valid action
        if (!in_array($action, array_keys($this->config->item('notification_options')))) {
            log_message("error", "Invalid action when sending notification. Must be one of '" . implode("','", array_keys($this->config->item('notification_options'))) . "'");
            return;
        }

        foreach ($housemates as $housemate) {
            
            if (strpos($housemate['conf_n_'.$action],'email') != -1
                && $housemate['user_email'] != NULL) {
                
                log_message("debug", "Sending email to a housemate!");

                $message = "";
                $subject = "Notification";

                switch ($action) {

                    case 'purchase_add':
                        $message .= "<p>A purchase was just added by " . $user['user_name'] . ".</p>";
                        $message .= "<p>" . $d['description'] . "</p>";
                        $message .= "<p>Paid by: " . $housemates[$d['payer']]['user_name'] . " on " . $d['purchase_date'] . "</p>";
                        $message .= '<p>Click <a href="' . site_url('purchases/view/' . hashids_encrypt($d['purchase_id'])) . '">here</a> to view purchase details, or to leave a comment or dispute.</p>';
                        $subject = "New Purchase";
                        break;
                    default:
                        log_message("error", "Unhandled action: '" + $action + "'");

                }

                $this->load->model('email_model');
                $this->email_model->send_email($housemate['user_email'], $subject, $message);

                if ($housemate['user_id'] == $user['user_id']) {

                }
            }

        }

    }

}
// EOF
