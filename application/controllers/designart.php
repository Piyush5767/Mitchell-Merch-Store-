<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DesignArt extends Frontend_Controller {
	
	public function __construct(){
        parent::__construct();	
		$this->user = $this->session->userdata('user');		
		$this->load->model('designimage_m');
    }
	
	public function categories($system = 0)
	{
		$this->load->model('categories_m');
		$data	= $this->categories_m->getTreeCategories('design');
		
		$lang = getLanguages();
		$all 				= array();
		$all[0]				= new stdClass();
		$all[0]->id 		= 0;
		$all[0]->title 		= language('all_art', $lang);
		$all[0]->children 	= array();
		$all[0]->parent_id 	= 0;
			
		$categories = array_merge($all, $data);
				
		echo json_encode($categories);	
		exit;
	}

	// get all art
	public function arts($cate_id = 0)
	{
		$page		= $this->input->post('page');
		$keyword	= $this->input->post('keyword');
		$number		= 24;
		
		$count 		= $this->designimage_m->getDesigns($cate_id, true, 0, $this->session->userdata('keyword'));
		$arts		= $this->designimage_m->getDesigns($cate_id, false, $page * $number, $this->session->userdata('keyword'));
		
		$clips = array();
		
		if (count($arts)> 0)
		{
			$i = 0;
			$url 	= site_url('media/designimage') .'/';
			foreach ($arts as $art)
			{
		
				$clips[$i] = new stdClass();
				$clips[$i]->clipart_id 		= $art->id;
				$clips[$i]->title 			= $art->name;
				$clips[$i]->file_type 		= 'png';
				$clips[$i]->price 			= $art->price;				
				// thumb
				
				$clips[$i]->path 			= $url;
				$clips[$i]->url 			= $art->full;
				$clips[$i]->thumb 			= 'thumb/'.$art->thumb;				
				$i++;
			}
		}
		
		$data 			= array();
		
		if (($count % $number) == 0)
			$data['count']	= $count/$number;
		else
			$data['count']	= (int) ($count/$number) + 1;
		$data['arts']		= $clips;
		
		echo json_encode($data);
		exit;
	}

	// get art detail
	public function detail($id = 0)
	{
		$data = new stdClass();
		if ($id > 0)
		{			
			$art	= $this->designimage_m->getArt($id);
			
			if (count($art) > 0)
			{
				$this->load->model('settings_m');
				$currency			= $this->settings_m->getCurrency();
				
				$price				= new stdClass();
				$price->currency_symbol 	= $currency->currency_symbol;
				$price->amount 				= $art->price;
				
				$info				= new stdClass();
				$info->title 		= $art->name;
				$info->description 	= $art->description;
				$data->error 		= 0;
				$data->info 		= $info;
				$data->price 		= $price;
			}
			else
			{				
				$data->error = 1;
			}
		}
		else
		{
			$data->error = 1;
			
		}
		
		echo json_encode($data);
		return;
	}	
	
	public function designupload(){
		$page  		= $this->input->post('page');
		$usrid 		= $this->user;
		$number		= 12;
		
		$info  = new stdClass();
		if(!empty($usrid)){
			$uploads 		= $this->designimage_m->getUploaded($usrid['id']);
			$info->imageobj = $uploads->result();
			$count 			= $uploads->num_rows();
			
			$data 			= array();		
			
			if (($count % $number) == 0)
				$info->counts	= $count/$number;
			else
				$info->counts	= (int)($count/$number) + 1;
			
			echo json_encode($info);
			return;	
		}		
	}
		
	function remove()
	{
		$ids 	= $this->input->post('ids');
		if(count($ids) > 0)
		{
			foreach($ids as $id)
			{
				$art = $this->designimage_m->getUploadedImage($id);
				if($this->designimage_m->removeImage($id))
				{
					if(count($art))
					{
						$thumb = ROOTPATH .DS. $art->imgthumb;
						$full = ROOTPATH .DS. $art->imgfull;
						
						if(file_exists($thumb))
							unlink($thumb);
						if(file_exists($full))
							unlink($full);
					}
				}
			}
			echo true;
		}else{
			echo false;
		}				
	}
}