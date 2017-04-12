<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Newsletter_m extends MY_Model
{
	
	public $_table_name = 'newsletter';
	protected $_order_by = 'id';
	public $_primary_key 	= 'id';

	function __construct ()
	{
		parent::__construct();
	}
	
	// users shop.
	public function getUsers($count = false,$search='', $o_search='',$number = '', $segment = '')
	{
		if($search != ''){
			$this->db->where($o_search,$search);
		}
		if($count == true)
		{
			$query = $this->db->get('newsletter');
			return count($query->result());
		}else
		{
			$query = $this->db->get('newsletter', $number, $segment);
			return $query->result();
		}
	}
}