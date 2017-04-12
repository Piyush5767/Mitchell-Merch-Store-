<?php
/**
 * @author tshirtecommerce - www.tshirtecommerce.com
 * @date: 2015-01-10
 * 
 * @copyright  Copyright (C) 2015 tshirtecommerce.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Colors extends Frontend_Controller
{

	public function __construct ()
	{
		parent::__construct();
		
	}
	
	function index($f = null, $id = null)
	{	
		$this->db->where('published', 1);
		$query = $this->db->get('colors');		
		$data['content'] = $query->result();
		
		$clrcatquery = $this->db->get('dg_colorCategory');
		$data['colorCat'] = $clrcatquery->result();		
		$data['function'] = $f;
		$data['id'] = $id;
		
		$this->load->view('components/colors/index', $data);
	}
	
	function getColor($f = null, $id = null){		
		$this->db->where('published', 1);		
		$query 	= $this->db->get('colors');
		
		$data['content'] = $query->result();
		
		$clrcatquery = $this->db->get('dg_colorCategory');
		$data['colorCat'] = $clrcatquery->result();	
		$data['function'] = $f;
		$data['id'] = $id;
		
		$this->load->view('components/colors/index', $data);
	}		
	
	function saveColor(){
		$data22 = $this->input->post();	
		$checkclr = $this->db->get_where('dg_colors',array('hex'=>$data22['colorcode']))->row();
		if(!empty($checkclr)){
			echo json_encode(array('result'=>0,'res'=>'<p class="text-danger"> Color with code '.$data22['colorcode'].' already exists </p>'));
		}else{
			$insertClr = $this->db->insert('dg_colors',array('hex'=>$data22['colorcode'],'title'=>$data22['colorTitle'],'lang_code'=>'en','published'=>1));
			if($insertClr){		
				$query 	= $this->db->get('colors');				
				$data['content'] = $query->result();
				$data['function'] = null;
				$data['id'] = null;	
				echo json_encode(array('result'=>1,'res'=>'<p class="text-success">Color with code '.$data22['colorcode'].' Insert successfully </p>','colorTitle'=>trim($data22['colorTitle']),'colorCode'=>trim($data22['colorcode']),'bgcolorspan'=>'<span class="color-bg" style="background-color:#'.trim($data22['colorcode']).'"></span>'));
			}else{
				echo json_encode(array('result'=>0,'res'=>'<p class="text-danger">Error: Try again later</p>'));
			}			
		}
	}	
	
	
	function removeclr(){
		$data = $this->input->post();
		$checkclr = $this->db->get_where('dg_colors',array('hex'=>$data['clrid']))->row();		
		if(!empty($checkclr)){
			$rmvclr = $this->db->delete('dg_colors',array('hex'=>$data['clrid']));	
			if($rmvclr){
				echo json_encode(array('result'=>1,'res'=>'<p class="text-success">Color successfully removed </p>'));				
			}else{
				echo json_encode(array('result'=>1,'res'=>'<p class="text-danger">Error: Please try again later</p>'));						
			}
		}		
	}
	
	function colorCategory(){
		$data = $this->input->post();	
		$slug = url_title($data['colorCat'],'-', TRUE);	
		$checkCat = $this->db->get_where('dg_colorCategory',array('slug'=>$slug));	
		if($checkCat->num_rows()>0){
			echo json_encode(array('result'=>0,'res'=>'<p class="text-danger"> Category Already Exists</p>'));
		}else{	
			$insertCat = $this->db->insert('dg_colorCategory',array('name'=>$data['colorCat'],'slug'=>$slug));
			if($insertCat){
				$option = '';
				$clrcatquery = $this->db->get('dg_colorCategory');
				foreach($clrcatquery->result() as $clr_key=>$clr_val){
					$option.='<option value='.$clr_val->slug.'>'.$clr_val->name.'</option>';
				}
				echo json_encode(array('result'=>1,'res'=>'<p class="text-success">Category inserted successfully</p>','options'=>$option));					
			}else{
				echo json_encode(array('result'=>0,'res'=>'<p class="text-danger">Error: Try again later</p>'));					
			}		
		}	
	}
}