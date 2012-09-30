<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends CI_Model {

	function get_houses() {
		
		$query = $this->db->get('houses');
		return $query->result_array();
		
	}
	
	function get_users() {
		
		$query = $this->db->get('users');
		return $query->result_array();
		
	}
	
	function get_log() {
		
		$query = $this->db->get('log');
		return $query->result_array();
		
	}
	
}
// EOF