<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Design extends Frontend_Controller {
	
	public function __construct(){
        parent::__construct();	
		$this->langs = getLanguages();
    }
	
	// get layout of designer
	public function index($id = 0, $color = 0, $design_id = '')
	{		
		$id 					= (int) $id;
		
		$data	= array();
		$data['color'] 			= (string) $color;
		$data['design_id'] 		= $design_id;
		
		$this->load->model('settings_m');
		$setting				= $this->settings_m->getSetting();
		
		$data['setting']		= json_decode($setting->settings);
		$data['lang'] 			= $this->langs;
			
		// get product default or product id
		if ($id > 0)
		{
			$fields 			= array('id'=>$id, 'published'=>1);
		}
		else
		{
			$fields 			= array('published'=>1);
		}
		$this->load->model('product_m');
		$rows 	= $this->product_m->getProduct($fields);
		
		
		if ($rows != false)
		{
			$product	= $rows[0];
			
			// get product design
			$design 	= $this->product_m->getProductDesign($product->id);
			if ($design == false)
			{
				$product = false;
			}
			else
			{
				$this->load->helper('product');
				$help_design 			= new helperProduct();
				$product->design		= $help_design->getDesign($design);
				
				// attribute
				$attribute 				= $this->product_m->getAttribute($product->id);
				$sizeAttr 				= $this->product_m->getSizeAttribute($product->id);				
				if (count($attribute)) 
				{					
					$product->attribute = $help_design->displayAttributes($attribute);
				}
				else
				{
					$product->attribute = '';
				}						
				$product->attribute 	= $help_design->quantity($product->min_order) . $product->attribute;					
				
				if (count($sizeAttr)) 
				{					
					$product->sizeAttribute = $help_design->displaySizeAttributes($sizeAttr,$product->printfulprcdepend,$product->printfulsize_differ);
				}
				else
				{
					$product->sizeAttribute = '';
				}					
				
				$this->load->model('categories_m');
				$product->categories	= $this->categories_m->getCategories('product');		
				
			}			
			$data['product']			= $product;		

		}
		else
		{
			$data['product']		= false;			
		}		
		
		$data['user']	= $this->session->userdata('user');
		
		// check user admin
		$is_admin		= true;
		if ( empty($data['user']['id']) )
		{
			$is_admin		= false;
		}
		else
		{
			//$this->load->model('users_m');
			//$is_admin	= $this->users_m->userPermission('art');
		}
		$data['is_admin']	= $is_admin;
		
		$designer 		= $this->load->view('components/design/designer', $data, true);
		
		$this->data['meta']	= '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1, minimum-scale=0.5, maximum-scale=1.0"/>';		
		
		$this->data['content']	= $designer;		
		$this->data['subview']	= $this->load->view('layouts/design/default', array(), true);
		
		$this->data['breadcrumbs'] = array(
			0=>array(
				'title'=>language('design', $this->langs),
				'href'=>'javascript:void(0)'
			)
		);
		
		$this->load->model('layout_m');
		$layout = $this->layout_m->getDesignLayout('design/default');
		
		$this->data['title']				=  $layout->title;		
		$this->data['meta_keywords']		=  $layout->meta_keywords;		
		$this->data['meta_description']		=  $layout->meta_description;
		$this->data['meta_robots']			=  $layout->meta_robots;
		
		$this->theme($this->data, 'design');
	}
	
	function colors()
	{
		if ( $this->session->userdata('colors') )
		{			
			$colors = $this->session->userdata('colors');
		}
		else
		{
			$colors = $this->help_products->getColor();
			$this->session->set_userdata('colors', $colors);
		}
		
		$data = array();
		
		if ($colors === false){
			$data['status'] = 0;
			$data['error'] = language('sys_try_again', $this->langs);
		}
		else{
			$data['status'] = 1;
		}
		
		$data['colors'] = $colors;
		
		echo json_encode($data);
		exit();	
	}
	
	function fonts()
	{
		$fonts = $this->help_products->getFonts();
		$this->session->set_userdata('fonts', $fonts);
		if ( $this->session->userdata('fonts') )
		{		
			$fonts = $this->session->userdata('fonts');
		}
		else
		{
			$fonts = $this->help_products->getFonts();
			$this->session->set_userdata('fonts', $fonts);
		}
				
		$data = array();
		
		if ($fonts === false){
			$data['status'] = 0;
			$data['error'] = language('sys_try_again', $this->langs);
		}
		else{
			$data['status'] = 1;
		}
		
		$data['fonts']		= $fonts;
		
		//echo '<pre>';print_r($data);echo '<pre>';exit;
		
		echo json_encode($data);
		exit();	
	}
}