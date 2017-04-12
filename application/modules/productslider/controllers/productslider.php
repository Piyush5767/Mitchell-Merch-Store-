<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ProductSlider extends Frontend_Controller{ 

	public function __construct(){ 
		parent::__construct();		
		$this->load->helper('url');
		$this->load->model(array('productslider_m','settings_m'));
		$this->productslider_m = $this->load->model('productslider/productslider_m');			
	} 
	
	public function index($id = ''){
		$this->data['getPrdObj']  = $getPrdObj = $this->productslider_m->getM_product($id);
		$row 					  = $this->settings_m->getSetting();			
		$this->data['setting']	  = json_decode($row->settings);		
		$this->data['m_products'] = $this->productslider_m->display_Products($getPrdObj); 
		$this->load->view('index',$this->data);
	}
}