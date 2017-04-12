<?php
/**
 * @author tshirtecommerce - www.tshirtecommerce.com
 * @date: 2015-01-10
 * 
 * order
 * 
 * @copyright  Copyright (C) 2015 tshirtecommerce.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_m extends MY_Model
{
	
	public $_table_name = 'orders';
	public $_order_by 	= 'id';
	public $_primary_key = 'id';
	public $_designimgtble = 'dg_printfulorder';
	function __construct ()
	{
		parent::__construct();
	}
	
	// convert object to array
	public function fields($fields)
	{
		$this->load->helper('security');
		
		$data = array();
		if (count($fields))
		{
			foreach($fields as $key=>$value)
			{
				$data[$key]	= xss_clean(strip_tags($value));
			}
		}
		
		return $data;
	}
	
	// set new order
	public function addNew($type)
	{		
	
		switch($type)
		{
			case 'order':
				$this->_table_name 			= 'orders';
				$data = array(
					'order_number' 			=> '',
					'user_id' 				=> 0,					
					'order_pass' 			=> '',
					'payment_id' 			=> 0,
					'payment_price' 		=> 0,
					'shipping_id' 			=> 0,
					'shipping_price' 		=> '',
					'sub_total' 			=> 0,
					'total' 				=> 0,
					'discount_id' 			=> 0,
					'discount' 				=> 0,
					'tax' 					=> '',
					'status' 				=> '',
					'created_on' 			=> date('Y-m-d h:i:s'),
					'modified_on' 			=> date('Y-m-d h:i:s'),
					'client_note' 			=> ''
				);
			break;
			
			case 'item':
				$this->_table_name 			= 'order_items';
				$data = array(
					'order_id' 				=> 0,
					'design_id' 			=> 0,
					'product_id' 			=> 0,
					'product_name' 			=> '',
					'product_sku' 			=> '',
					'product_price' 		=> 0,					
					'price_print' 			=> 0,
					'price_clipart' 		=> 0,
					'price_attributes' 		=> 0,
					'quantity' 				=> 0,
					'poduct_status' 		=> '',
					'attributes' 			=> '',
					'created_on' 			=> date('Y-m-d h:i:s'),
					'modified_on' 			=> date('Y-m-d h:i:s')					
				);
			break;
			
			case 'info':
				$this->_table_name 			= 'orders_userinfo';
				$data = array(
					'order_id' 				=> 0,
					'user_id' 				=> 0,					
					'address' 				=> '',					
					'created_on' 			=> date('Y-m-d h:i:s'),
					'modified_on' 			=> date('Y-m-d h:i:s')					
				);
			break;
			
			case 'histories':
				$this->_table_name 			= 'orders_histories';
				$data = array(
					'order_id' 				=> 0,
					'lable' 				=> '',					
					'content' 				=> '',					
					'date' 					=> date('Y-m-d h:i:s')
				);
			break;
		}
		
		return $data;
	}
	
	public function printful_order($userid,$order_id,$order_obj){		
		$this->db->insert($this->_designimgtble,array('user_id'=>$userid,'order_id'=>$order_id,'order_object'=>$order_obj));
	}	
	
	// get all orders
	public function getOrders($count = false, $number = 5, $offset = 1, $search='', $option='', $admin = true)
	{
		$this->db->select('orders.*, name');
				
		$this->db->join('users', 'orders.user_id = users.id');
		
		if($admin == false)
			$this->db->where('orders.user_id', $this->user['id']);
		
		if($option == 'order_number' && $search != '')
		{
			$this->db->like('orders.order_number', $search);
		}
		elseif( $option == 'customer' && $search != '' && $admin == true)
		{			
			$this->db->like('users.username', $search);
		}
		elseif($option == 'date' && $search != '')
		{
			$this->db->like('orders.created_on', $search);
		}
		
		$this->db->order_by("id", "DESC"); 
		
		if ( $count == true )
		{
			$query = $this->db->get('orders');			
			return count($query->result());
		}
		else 
		{
			$query = $this->db->get('orders', $number, $offset);			
			return $query->result();
		}
	}
	
	// get order detail
	function getOrder($id, $admin = true)
	{
		$this->db->select('orders.*, name, username, email');
		
		$this->db->where('orders.id', $id);
		
		$this->db->join('users', 'orders.user_id=users.id');
		
		if($admin == false)
		{
			$this->db->where('users.id', $this->user['id']);
		}
		
		$query = $this->db->get('orders');
		
		return $query->row();
	}

	// get order detail by item number
	function getOrderByItemNumber($id, $admin = true)
	{
		$this->db->select('orders.*, name, username, email');
		
		$this->db->where('orders.order_number', $id);
		
		$this->db->join('users', 'orders.user_id=users.id');
		
		if($admin == false)
		{
			$this->db->where('users.id', $this->user['id']);
		}
		
		$query = $this->db->get('orders');
		
		return $query->row();
	}
	
	//Get Order Number from cart id
	function Getcartid($id){
		$crtobj = $this->db->get_where('dg_orders',array('order_number'=>$id))->row();		
		return $crtobj->id;
	}	
	
	
	//Save cart content object	
	function Ordercartcontent($id,$cartobj,$cartitems){
		$this->db->insert('dg_ordercartobject',array('order_id'=>$id,'cart_content'=>$cartobj,'cart_items'=>$cartitems));		
	}
	
	// Get Cartcontent object
	function GetOrdercartcontent($id){
		$crtobj = $this->db->get_where('dg_ordercartobject',array('order_id'=>$id))->row();		
		return $crtobj;
	}
	
	// get all items of order
	function getItems($order_id)
	{
		$this->db->where('order_id', $order_id);
		$query = $this->db->get('order_items');
		return $query->result();
	}
	
	// get all item of order
	function getItem($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('order_items');
		return $query->row();
	}
	
	// Get Orders By Multiple Conditions
	function getOrderByCondition($where,$row = false)
	{
		$this->db->where($where);
		$query = $this->db->get('orders');
		
		if($row == false)
			return $query;	
		else
			return $query->row(); 		
	}	
	
	// get cliparts
	function getCliparts($order_id)
	{		
		$this->db->select('cliparts.title, cliparts.add_price, cliparts.fle_url, cliparts.clipart_id, cliparts.file_name, cliparts.cate_id');
		$this->db->join('cliparts', 'cliparts.clipart_id = order_cliparts.clipart_id');		
		
		$this->db->where('order_cliparts.order_id', $order_id);		
		$query = $this->db->get('order_cliparts');
				
		return $query->result();
	}


	function getCliparts_tr($order_id)
	{		
		$result = '';
		
		$this->db->select('cliparts.title, cliparts.add_price, cliparts.fle_url, cliparts.clipart_id, cliparts.file_name, cliparts.cate_id');
		$this->db->join('cliparts', 'cliparts.clipart_id = order_cliparts.clipart_id');		
		
		$this->db->where('order_cliparts.order_id', $order_id);		
		$query = $this->db->get('order_cliparts');
				
		$obj = $query->result();
		foreach($obj as $k=>$clip_r){
			$result.='<tr><td>'.$clip_r->title.'</td><td class="center"><img  style="width:100px;height:100px;" src="/media/cliparts/'.$clip_r->cate_id.'/thumbs/'.md5($clip_r->clipart_id).'.png" /></td><td class="text-center">$'.$clip_r->add_price.'</td></tr>';			
		}
		
		$this->db->select('designimage.id,designimage.name, designimage.price,designimage.slug, designimage.thumb, designimage.full,designimage.catid');
		$this->db->join('designimage', 'designimage.id = order_customcliparts.clipart_id');		
		
		$this->db->where('order_customcliparts.order_id',$order_id);		
		$query_2 = $this->db->get('order_customcliparts');
				
		$obj2 = $query_2->result();
		if(!empty($obj2)){
			foreach($obj2 as $k=>$clip_r){			
				$result.='<tr><td>'.$clip_r->name.'</td><td class="center"><img  style="width:100px;height:100px;" src="/media/designimage/thumb/'.$clip_r->thumb.'" /></td><td class="text-center">$'.$clip_r->price.'</td></tr>';			
			}				
		}
			
		$this->load->model('settings_m');
		$settings = $this->settings_m->getSetting();
		$settingObj = json_decode($settings->settings);
		$obj_3 = $this->db->get_where('order_uploadimage',array('order_id'=>$order_id));
		if(!empty($obj_3)){
			foreach($obj_3->result() as $k=>$clip_r){			
				$result.='<tr><td>Uploaded Image</td><td class="center"><img  style="width:100px;height:100px;" src="'.base_url().$clip_r->thumb.'" /></td><td class="text-center">$'.$settingObj->upload_image_price.'</td></tr>';			
			}				
		}				
		
		
		// Get Texts
		$this->db->select('users_designs.*');
		$this->db->join('users_designs', 'users_designs.design_id = order_items.design_id');
		$this->db->where('order_items.order_id', $order_id);
		$obj_4  = $this->db->get('order_items');

		if(!empty($obj_4)){
			$design_obj = $obj_4->result();
			foreach($design_obj as $k=>$r_design){
				if(!empty($r_design->vectors)){
					$vectors = json_decode($r_design->vectors); 
					foreach($vectors as $k_v=>$r_vec){
						foreach($r_vec as $vecobj){
							if($vecobj->type == 'text'){
								$result.='<tr><td>Text</td><td class="center"><span style="color:'.$vecobj->color.'; font-size:25px;">'.$vecobj->text.'</span></td><td class="text-center">$'.$settingObj->text_price.'</td></tr>';			
							}
						}						
					}						
				}			
			} 
		}	
		return $result;
		
	}
	
	// get History
	function getHistory($id)
	{
		$this->db->where('order_id', $id);
		$query = $this->db->get('orders_histories');
		return $query->result();
	}
	
	// get user shipping info
	public function getUserInfo($order_id)
	{
		$this->_table_name	= 'orders_userinfo';
		$this->db->where('order_id', $order_id);
		$row = $this->get();
		
		if ( count($row) > 0 )
			return $row[0];
		else
			return false;
	}
	
	// get design of item
	function getDesign($id)
	{
		$this->db->select('users_designs.*,order_items.order_id');
		$this->db->join('users_designs', 'users_designs.design_id = order_items.design_id');
		$this->db->where('order_items.id', $id);
		$query = $this->db->get('order_items');
		return $query->row();
	}
	
	function getDesignDetail($id)
	{
		$this->db->select('users_designs.*');
		$this->db->where('id', $id);
		$query = $this->db->get('users_designs');
		return $query->row();
	}
	
	// check order or item status
	public function checkStatus($id, $status, $order = false)
	{
		$this->db->where('id', $id);
		if($order == true)
		{
			$this->db->where('status', $status);
			$query = $this->db->get('orders');
		}
		else
		{
			$this->db->where('poduct_status', $status);
			$query = $this->db->get('order_items');
		}
		if($query->num_rows() > 0)
			return true;
		else
			return false;
	}
	
	// get user of order
	public function getUser($order_id)
	{
		$this->db->select('users.email, users.username, orders.total, orders.order_number');
		$this->db->join('users', 'orders.user_id = users.id');
		
		$this->db->where('orders.id', $order_id);
		$query = $this->db->get('orders');
		return $query->row();
	}
	
	// get order number
	public function creteOrderNumber($length = 7)
	{
		$rand 			= strtoupper(uniqid(sha1(time())));
		$orderNumber 	= substr($rand, -1 * $length);	
		
		return $orderNumber;
	}
	
	// get design.
	public function getDesigns($id = '')
	{
		$this->db->select('users_designs.*,order_items.order_id');
		$this->db->join('users_designs', 'users_designs.design_id = order_items.design_id');
		$this->db->where('order_items.order_id', $id);
		$query = $this->db->get('order_items');
		return $query->result();
	}
	
	function updateOrder($where, $data)
	{
		$this->db->where($where);
		if($this->db->update($this->_table_name, $data))
			return true;
		else
			return false;
	}
	
	function deleteOrder($where)
	{
		$this->db->where($where);
		if($this->db->delete($this->_table_name))
			return true;
		else
			return false;
	}
	
	function listHistory($id)
	{
		$this->db->where('order_id', $id);
		$query = $this->db->get('orders_histories');
		return $query->result();
	}
	
	function getOrderNumber($order_number)
	{
		$this->db->where('order_number', $order_number);
		$query = $this->db->get('orders');
		return $query->row();
	}
	
	function getOrderByCustom($where){
		$this->db->where($where);
		$query = $this->db->get('orders');
		return $query->row();		
	}
}