<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purchases_model extends CI_Model {

	// Fetch all purchases
	function get_purchases()
	{
				
		$this->db->select(); // defaults to all (*)
		$this->db->limit(2);
		//$this->db->order_by('entry_date','desc');
		$this->db->from('purchases');
		
		$query = $this->db->get();
 
		return $query->result_array();
	}
	

	
	function get_purchase($id)
	{
		


		
		$this->db->select(); // defaults to all (*)
		$this->db->where('purchase_id',$id);
		$this->db->limit(1);
		$this->db->from('purchases');
		
		$query = $this->db->get();
		
		// if($query->num_rows()!==0) return $query->result();
 		// else return FALSE;
		
		return $query->result_array();
	}
	
	function add_purchase($author,$name,$body,$categories)
	{
		$data = array(
		'author_id'		=> $author,
		'entry_name'	=> $name,
		'entry_body'	=> $body,
		);
		$this->db->insert('entry',$data);
		
		$object_id = (int) mysql_insert_id(); // get latest post id
		
		foreach($categories as $category)
		{
			$relationship = array(
			'object_id'		=> $object_id, // object id is post id
			'category_id'	=> $category,
			);
			$this->db->insert('entry_relationships',$relationship);
		}
	}
	
}
// EOF