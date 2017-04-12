<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class designClipart extends Admin_Controller {

	public function __construct ()
	{
		parent::__construct();
		$this->lang->load('user');		
		$this->user = $this->session->userdata('user');
		$this->langs = getLanguages();
		$this->load->model('designimage_m');
	}
	
	public function index ($layout = 'default', $cateID = 0, $limit = 0)
	{
		$this->data['meta_title'] = lang('dashboard_design_title');
		$this->data['breadcrumb'] = lang('dashboard_admin_design_breadcrumb');
		$this->data['sub_title']  =  lang('dashboard_admin_sub_title');
		$this->data['lang'] 	  = $this->langs;
		$this->load->model(array('settings_m','designimage_m'));
		$setting				= $this->settings_m->getSetting();
		
		$config['base_url'] 		= site_url(). '/admin/designClipart/index/default/'.$cateID;
		
		$config['per_page'] 		= 24;
		$config['uri_segment'] 		= 6;
		$config['prev_link'] 		= '&larr;';
		$config['next_link'] 		= '&rarr;';
		$config['first_link']		= '&laquo;';
		$config['last_link'] 		= '&raquo;';
		
		$this->data['setting']		= json_decode($setting->settings);

		if(file_exists($this->session->flashdata('path_file_update')))
		{
			unlink($this->session->flashdata('path_file_update'));
		}
		
		$count = $this->uri->segment_array();	
		
		if($this->input->post('search'))
			$this->session->set_userdata('keyword', $this->input->post('keyword'));
			
		if ( count($count) == $config['uri_segment']  )
		{
			$limit 					= (int) $this->uri->segment($config['uri_segment']);
		}else{
			$limit 					= 0;
		}		
		
		if ($cateID != null)
		{
			$config['total_rows']		= $this->designimage_m->getDesigns($cateID, true, 0, $this->session->userdata('keyword'));
			$arts						= $this->designimage_m->getDesigns($cateID, false, $limit, $this->session->userdata('keyword'));
		}
		else
		{
			$config['total_rows']		= $this->designimage_m->getDesigns('', true, 0, $this->session->userdata('keyword'));
			$arts						= $this->designimage_m->getDesigns('', false, $limit,$this->session->userdata('keyword'));
		}
		$this->data['arts'] 			= $arts;
		
		$this->load->library('pagination');
		$this->pagination->initialize($config);
		
		$this->data['subview'] 			= 'admin/designClipart/index';
		if ($layout == 'ajax')
			$this->load->view('admin/designClipart/ajax', $this->data);
		else
			$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit(){
		$this->data['meta_title'] = lang('dashboard_design_title');
		$this->data['breadcrumb'] = lang('dashboard_admin_design_breadcrumb');
		$this->data['sub_title']  =  lang('dashboard_admin_sub_title');
		$this->data['lang'] 	  = $this->langs;
		$this->load->model('settings_m');
		$setting				= $this->settings_m->getSetting();
		
		$this->data['setting']		= json_decode($setting->settings);

		if(file_exists($this->session->flashdata('path_file_update')))
		{
			unlink($this->session->flashdata('path_file_update'));
		}
		
		$this->load->model('categories_m');		
		$this->data['designCategories'] = $this->categories_m->getCategories('design',false,false,'','','');
		$this->data['subview'] 			= 'admin/designClipart/design';
    	$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function saveClipart(){
		header('Content-Type: text/html; charset=UTF-8');
		$data 		 				= file_get_contents('php://input');
		$data 						= json_decode($data, true);
		$designImage 				= designimage($data['designArt'],'design');
		$root 						= ROOTPATH .DS. 'media' .DS. 'designimage' .DS. 'thumb';	

		$config['image_library'] 	= 'gd2';
		$config['source_image']		= ROOTPATH .DS. 'media' .DS. 'designimage' .DS.$designImage;
		$config['new_image']		= $root.DS.$designImage;;		
		$config['create_thumb'] 	= TRUE;
		$config['file_permissions'] = '0644';		
		$config['maintain_ratio'] 	= TRUE;
		$config['width']			= 100;
		$config['height']			= 100;	
		$this->load->library('image_lib', $config);
		$this->image_lib->resize();
		$thumb 	= str_replace($this->image_lib->dest_folder, '', $this->image_lib->full_dst_path);		

		$design  			 = array();
		$design['name']  	 = $data['title'];
		$design['price']	 = $data['price'];		
		$design['catid']	 = $data['category'];
		$design['slug']  	 = url_title($data['slug'],'-',true);
		$design['thumb'] 	 = $thumb;
		$design['full']  	 = $designImage;	
		$design['datetime']  = date('Y-m-d H:i:s');			
		$designsave = $this->designimage_m->savedesign($design);
		if($designsave){
			$this->session->set_flashdata('message', '<p class="text-danger">Design Saved Successfully.</p>');					
		}
		echo $designsave;
	}

	public function savecategory(){
		$data = $this->input->post();
		$cat = array();
		$cat['type']  	   		=  'design';
		$cat['title'] 	   		= ucwords(strtolower($data['catname']));
		$cat['slug']  	   		= url_title($data['catslug'],'-',true);
		$cat['description']	    = $data['description'];		
		$cat['level']  	   		= 1; 
		$cat['parent_id']  		= 0; 
		$cat['published'] 		= 1;		
		$cat['created']    		= date('Y-m-d H:i:s');		
	
		$insrt = $this->designimage_m->savecategory($cat);
		if($insrt){
			$this->session->set_flashdata('message', '<p class="text-danger">Category Inserted Successfully.</p>');					
		}else{
			$this->session->set_flashdata('message', '<p class="text-danger">Error: Please try again later</p>');					
		}
		redirect(base_url('/admin/designClipart'));
	}
	
	
	function remove()
	{
		$ids 	= $this->input->post('ids');
		if($ids > 0)
		{
			foreach($ids as $id)
			{
				$art = $this->designimage_m->getArt($id);
				if($this->designimage_m->remove($id))
				{
					if(count($art))
					{
						$thumb = ROOTPATH .DS. 'media' .DS. 'designimage'.DS. 'thumb'.DS.$art->thumb;
						$full = ROOTPATH .DS. 'media' .DS. 'designimage'.DS. $art->full;
						
						if(file_exists($thumb))
							unlink($thumb);
						if(file_exists($full))
							unlink($full);
					}
				}
			}
			$this->session->set_flashdata('success', lang('art_admin_clipart_remove_success_msg'));
		}		
		redirect('admin/designClipart');	
	}	
}