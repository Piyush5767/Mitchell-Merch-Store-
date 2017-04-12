<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Newsletter extends Admin_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->lang->load('user');
		$this->lang->load('newsletter');
		$this->load->model('newsletter_m');		
		$this->load->library('session');
		$this->user = $this->session->userdata('user');
		$this->group_id = '';
	}
	
	public function index()
	{		
		// check user permission	
		$this->data['breadcrumb'] = lang('newsletter_admin_breadcrumb');
        $this->data['meta_title'] = lang('newsletter_admin_meta_title');
        $this->data['sub_title']  = lang('newsletter_admin_sub_title');
		
		// pagination
        $this->load->library('pagination'); 
        $this->load->helper('url');
        $config['base_url'] 	= base_url('admin/newsletter'); 		
        $config['total_rows'] = $this->newsletter_m->getUsers(true);
		
		if($this->session->userdata('per_page') != '')
			$config['per_page'] = $this->session->userdata('per_page');
		else
			$config['per_page'] 	= 20;
			
        $config['uri_segment'] = 3; 
        $config['next_link'] = lang('next'); 
        $config['prev_link'] = lang('prev'); 
        $config['first_link'] = lang('first'); 
        $config['last_link'] = lang('last'); 
        $config['num_links']	= 2;                 
        $this->pagination->initialize($config); 
        $this->data['links'] = $this->pagination->create_links();
		$this->data['per_page'] = $config['per_page'];
		$this->data['search'] = $this->input->post('search');
		$this->data['option'] = $this->input->post('option');
		
		// Fetch all users
		$this->data['users'] = $this->newsletter_m->getUsers(false,$this->data['search'],$this->data['option'],$config['per_page'], $this->uri->segment(3));
		
		// Load view
		$this->data['subview'] = 'admin/newsletter/index';
		$this->load->view('admin/_layout_main', $this->data);		
	}
	
	public function action($type='', $id = null){
		switch($type) {
			case 'Subscribe':
				 $data['subscribe'] = '1';
				 break;
			case 'unSubscribe':
				 $data['subscribe'] = '0';
				 break;
			case 'verify':
				 $data['verified'] = '1';
				 break;
			case 'unverify':
				 $data['verified'] = '0';
				 break;			
		}
		if($id != null){
			$this->newsletter_m->_table_name = 'newsletter';
			$this->newsletter_m->save($data, $id);
			redirect(site_url().'admin/newsletter');
		}else{
			if($this->input->post('checkb') != ''){
				foreach($this->input->post('checkb') as $id){
					$this->newsletter_m->_table_name = 'newsletter';
					$this->newsletter_m->save($data, $id);
				}
			}
			redirect(site_url().'admin/newsletter');
		}		
	}	

	public function edit ($id = '')
	{
		if($id !=''){
			$data = $this->input->post('data');
			if(array_key_exists('email',$data)){
				if(!array_key_exists('subscribe',$data)){
					$data['subscribe'] = '0';
				}
				if(!array_key_exists('verified',$data)){
					$data['verified'] = '0';
				}				
				$this->newsletter_m->_table_name = 'newsletter';
				if($this->newsletter_m->save($data)){					
					$this->session->set_flashdata('msg', '<p class="text-success">'.lang('newsletter_add_user_successfully').'</p>');
				}else{
					$this->session->set_flashdata('msg', '<p class="text-danger">'.lang('network_error').'</p>');					
				}				
			}else{
				$this->session->set_flashdata('msg', '<p class="text-danger">'.lang('newsletter_user_exists').'</p>');
			}	
			redirect(site_url().'admin/newsletter/edit');				
		}else{
			$this->data['breadcrumb'] = lang('newsletter_add_user');
			$this->data['meta_title'] = lang('newsletter_add_user');
			$this->data['sub_title'] = '';
			
			// Load the view
			$this->data['subview'] = 'admin/newsletter/edit';
			$this->load->view('admin/_layout_main', $this->data);			
		}
	}	
	public function delete($id = null)
	{
		$this->users_m->_table_name = 'newsletter';
		if($id != null){
			if($this->users_m->delete($id))
				$this->session->set_flashdata('msg', lang('user_msg_delete_success'));
			else
				$this->session->set_flashdata('error', lang('user_error_delete'));

			redirect(site_url().'admin/newsletter');
		}else{
			if($this->input->post('checkb') != ''){
				foreach($this->input->post('checkb') as $id){
					if($this->user['id'] != $id)
						$this->users_m->delete($id);
				}
				$this->session->set_flashdata('msg', lang('user_msg_delete_success'));
				redirect(site_url().'admin/newsletter');
			}else{
				$this->session->set_flashdata('error', lang('user_error_delete'));
				redirect(site_url().'admin/newsletter');
			}
		}
	}	
}