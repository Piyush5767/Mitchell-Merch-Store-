<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Designimage_m extends MY_Model
{
	public $_table_name 	= 'designimage';
	
	public function savedesign($data){
		$savedes = $this->db->insert($this->_table_name,$data);
		return $savedes;
	}
	
	public function designCat(){
		$result = '';
		$cate = $this->db->get('dg_designcat');
		return $cate;
	}
	
	public function savecategory($data){
		$catInsrt = $this->db->insert('categories',$data);
		return $catInsrt;
	}
	
	public function getDesigns($cate_id = 0, $count = false, $limit = 0, $search = '')
	{
		$this->db->select('designimage.*');		
				
		if($search != '')
		{			
			$this->db->where('(`name` LIKE \'%'.$search.'%\' OR `description` LIKE \'%'.$search.'%\')');
		}
				
		if ($cate_id > 0)
			$this->db->where('catid', $cate_id);
			
		$this->db->order_by('datetime', 'DESC');
		
		if ($count == true)
		{
			$query 	= $this->db->get('designimage');
			$art 	= count($query->result());
		}
		else
		{
			$this->db->limit(24, $limit);
			$query 	= $this->db->get('designimage');
			$art 	= $query->result();			
		}		
		return $art;
	}

	public function getArt($id){
		$clipart = $this->db->get_where('designimage',array('id'=>$id))->row();
		return $clipart;
	}

	public function getUploadedImage($id){
		$uploadimg = $this->db->get_where('user_upload_image',array('id'=>$id))->row();
		return $uploadimg;
	}
	
	public function remove($id){
		$clipartRmv = $this->db->delete('designimage',array('id'=>$id));
		return $clipartRmv;
	}	

	public function removeImage($id){
		$clipartRmv = $this->db->delete('user_upload_image',array('id'=>$id));
		return $clipartRmv;
	}	
	
	public function getUploaded($id){
		$uploadedimgs = $this->db->get_where('user_upload_image',array('user_id'=>$id));
		return $uploadedimgs;		
	}
}   