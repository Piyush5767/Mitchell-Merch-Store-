<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Newsletter extends Frontend_Controller{ 

	public function __construct(){ 
		parent::__construct();	
		$this->lang->load('newsletter');
		$this->load->helper('url');
		$this->newsletter = $this->load->model('newsletter/m_newsletter_m');
	} 
	
	public function default_index(){

	}
	
	public function index($id = ''){
	
		$this->data['id'] =	$id;
		$this->data['newsletter'] =	$this->newsletter->getCustomPar($id);
		$this->load->view('index',$this->data);
	}
	
	public function subscribe($token){
		$data['checkTkn'] = $this->newsletter->checkTkn($token);
		$data['subview'] 	= $this->load->view('newsletter', $data, true);
		$this->theme($data);
	}
	
	public function checkemail(){
		$email 	  = $this->input->post('email');
		$response = $this->newsletter->checkEmail($email);
		echo $response;
	}

	public function addSubscriber(){
		$email 	  = $this->input->post('email');
		$response = $this->newsletter->addEmail($email);
		if($response){
			echo '<p class="text-success">'.lang('subscribed').'</div>';
		}else{
			echo '<p class="text-danger">'.lang('subscribed_error').'</div>';			
		}
	}	
}