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

class Error404 extends Frontend_Controller 
{
    function __construct()
    {
        parent::__construct();
    }
	
	public function index()
	{
		$data = array();
		
		$data['subview'] = $this->load->view('layouts/404/404', array(), true);	
		
		$data['breadcrumbs'] = array(
			0=>array(
				'title'=>'404',
				'href'=>'javascript:void(0)'
			)
		);
		$this->load->model('layout_m');		$layout = $this->layout_m->getDesignLayout('404/404');				$data['title']				=  $layout->title;				$data['meta_keywords']		=  $layout->meta_keywords;				$data['meta_description']	=  $layout->meta_description;				$data['meta_robots']		=  $layout->meta_robots;	
		$this->theme($data);
	}
}
?>