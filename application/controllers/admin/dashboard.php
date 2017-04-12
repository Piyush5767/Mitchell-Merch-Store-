<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	public function __construct ()
	{
		parent::__construct();
		$this->lang->load('user');		
		$this->user = $this->session->userdata('user');
	}
	
	public function index()
	{
		$this->data['meta_title'] = lang('dashboard_admin_meta_title');
		$this->data['breadcrumb'] = lang('dashboard_admin_breadcrumb');
		$this->data['sub_title'] =  lang('dashboard_admin_sub_title');
		
		if(file_exists($this->session->flashdata('path_file_update')))
		{
			unlink($this->session->flashdata('path_file_update'));
		}

		$this->load->model('settings_m');
		
		$Msettings = $this->settings_m->getSetting();
		
		if(count($Msettings) > 0)
			$setting	= json_decode($Msettings->settings);
		else
			$setting	= $this->settings_m->setNew();
		
		$this->load->model('users_m');
		require_once(APPPATH.'libraries/gapi.php');		
		
		/* Define variables */
		$ga_email       = $setting->google_client_email;
		$ga_profile_id  = $setting->google_profile_id;
		/* Create a new Google Analytics request and pull the results */
		$ga = new gapi($ga_email,APPPATH.'libraries/login-with-google-fe59dff29ca3.p12');
		$ga->requestReportData($ga_profile_id, array('date'),array('pageviews', 'uniquePageviews', 'exitRate', 'avgTimeOnPage', 'entranceBounceRate', 'newVisits','visits'), 'date',null,date('Y-m-d', strtotime("-30 days")),null);  
		
		$this->data['results'] = $ga->getResults();			
		$this->data['count_users'] = $this->users_m->getCountUsers();
		$this->data['count_cliparts'] = $this->users_m->getCountCliparts();
		$this->data['count_products'] = $this->users_m->getCountProducts();
		$this->data['count_orders'] = $this->users_m->getCountOrders();		
		$this->data['subview'] = 'admin/dashboard/index';
    	$this->load->view('admin/_layout_main', $this->data);
	}
}