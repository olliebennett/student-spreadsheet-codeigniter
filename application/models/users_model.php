<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model {

	function get_user_by_facebook_id($facebook_id) {
					
		$this->db->select();
		$this->db->where('facebook_id',$facebook_id);
		$this->db->limit(1);
		$this->db->from('users');
		
		$query = $this->db->get();
				
		$arr =  $query->result_array();
		
		//var_dump($arr);
		
		if (size($arr)!==1) {
			$this->session->set_flashdata('error', 'User with Facebook ID of "'.$facebook_id.'" was not found.');
			redirect('/');
		} else {
			return $arr[0];
		}
	}
	
}
// EOF