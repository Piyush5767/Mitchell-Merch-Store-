<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Refund_m extends MY_Model
{
	public $_table_name = 'refund_transaction';
	public $_primary_key = 'id';
	public $_timestamps = False;	
	
   function getRefund($id){
	   $refund = $this->db->where('id',$id)->get('refund_transaction')->row();
	   return $refund;
   }
   
   function getRefunds($count = false, $number = 5, $offset = 1, $search='', $option='')
   {
		$this->db->order_by("id", "DESC"); 
		
		if($option == 'order_number' && $search != '')
		{
			$this->db->like('order_number', $search);
		}
		elseif($option == 'date' && $search != '')
		{
			$this->db->like('date', $search);
		}
		
		if ( $count == true )
		{
			$query = $this->db->get('refund_transaction');			
			return count($query->result());
		}
		else 
		{
			$query = $this->db->get('refund_transaction', $number, $offset);			
			return $query->result();
		}	   
   }  


	function deleteTransaction($where){
		$this->db->where($where);
		if($this->db->delete($this->_table_name))
			return true;
		else
			return false;
	}
}