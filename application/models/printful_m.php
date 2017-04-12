<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Printful_m extends MY_Model
{
	public $_table = 'printful_order_response';
	public function __construct(){
		parent::__construct();
	} 
	
	public function table($table){
		$this->_table = $table;
	}
	
	public function getwhere($where = array()){
		$this->db->where($where);
	}
	
	public function getobj($row = false){
		$get = $this->db->get($this->_table);
		
		if($row == false)
			return $get;	
		else
			return $get->row();  		
	}
	
	public function saveOrder($where,$data = array()){
		$this->db->where($where);
		if($this->db->update($this->_table,$data))
			return true;
		else
			return false;
	}
	
	public function getResponse($orderId){
		$get = $this->db->where('order_id',$orderId)->get('printful_order_response')->row();
		return $get;
	}
	
}