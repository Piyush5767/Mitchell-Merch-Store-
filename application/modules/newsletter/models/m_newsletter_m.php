<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class M_Newsletter_m extends MY_Model
{
	public $_table_name = 'modules';
	public $_primary_key = 'id';
	
	function __construct ()
	{
		parent::__construct();
		$this->db = $this->load->database('', true);
	}

	public function getCustomsPar($count = false, $number = '', $segment = '')
	{
		$this->db->where('type', 'newsletter');
		$this->db->order_by('title', 'ASC');
		if($count == true)
		{
			$query = $this->db->get('modules');
			return count($query->result());
		}else
		{
			$query = $this->db->get('modules', $number, $segment);
			return $query->result();
		}
	}
	
	public function getCustomPar($id = '')
	{
		$this->db->where('id', $id);
		$this->db->where('type', 'newsletter');
		$query = $this->db->get('modules')->row();
		$newsletter = json_decode($query->content);
		$custom = new stdClass();
		$custom->title = $query->title;
		$custom->placeholder = $newsletter->newsletter_placeholder;
		$custom->buttontxt = $newsletter->newsletter_button;
		$custom->buttonclass = $newsletter->newsletter_button_class;
		$custom->options = $query->options;
		$custom->params = $query->params;
		$custom->key = $query->key;		
		return $custom;		
	}
	
	public function checkNewsletter($id = '')
	{
		$this->db->where('id', $id);
		$this->db->where('type', 'newsletter');
		$query = $this->db->get('modules');
		return $query->row();
	}
	
	public function getNew()
	{
		$custom = new stdClass();
		$custom->title = '';
		$custom->placeholder = '';
		$custom->buttontxt = '';
		$custom->buttonclass = '';
		$custom->options = '[]';
		$custom->params = '[]';
		return $custom;
	}
	
	public function delete($id = '')
	{
		$this->db->where('id', $id);
		$this->db->where('type', 'newsletter');
		if($this->db->delete('modules'))
			return true;
		else
			return false;
	}	
	
	public function checkEmail($email){
		$this->db->save_query = true;
		$qry = $this->db->get_where('dg_newsletter',array('email'=>$email))->row();
		if(!empty($qry)){
			if($qry->verified == '0'){
				$this->db->get_where('dg_validate_token',array('type'=>'newsletter','email'=>$email,'expired <'=>date( "Y-m-d H:i:s")));
				$id = $qry->id;
				$config = array(
					'mailtype' => 'html',
					'protocol' => 'sendmail',
					'mailpath'=>  '/usr/sbin/sendmail',
					'charset'=>   'iso-8859-1',
					'wordwrap'=>  TRUE,
					'validate'=>  TRUE,				
				);
				$date_obj = new DateTime();
				$timestamp = $date_obj->getTimestamp();
				$token = sha1($id*$timestamp);
				$this->db->insert('dg_validate_token',array('type'=>'newsletter','token'=>$token,'email'=>$email,'data'=>json_encode(array()),'created'=>date("Y-m-d H:i:s"),'expired'=>date( "Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + 12 * 3600 )));
				$message=	'<table width="100%"cellspacing="1" cellpadding="1">
							<tr><td width="483" height="50"><font color="#CC3333">Below is email verification link.Please click on the Link to verify your email address. If any case Link didn\'t work then Re-send a Request or Contact our Support. This link would be expire after 12 hours.</font></td></tr>
							<tr><td width="483" height="50">'.base_url().'newsletter/subscribe/'.$token.'</td></tr></table>';
				$this->load->library('email', $config);
				$this->email->from(getEmail(config_item('admin_email')), getSiteName(config_item('site_name')));
				$this->email->to($email);    
				$this->email->subject('Email Verification');
				$this->email->message($message);   
				$this->email->send();				
			}
			return 'false';				
		}else{
			return 'true';				
		}
	}
	
	public function addEmail($email){		
		$isrtNewsletter = $this->db->insert('dg_newsletter',array('email'=>$email,'timestamp'=>date("Y-m-d H:i:s")));	
		if($isrtNewsletter){
			$insert_id = $this->db->insert_id();
			$config = array(
				'mailtype' => 'html',
				'protocol' => 'sendmail',
				'mailpath'=>  '/usr/sbin/sendmail',
				'charset'=>   'iso-8859-1',
				'wordwrap'=>  TRUE,
				'validate'=>  TRUE,				
			);
			$date_obj = new DateTime();
			$timestamp = $date_obj->getTimestamp();
			$token = sha1($insert_id*$timestamp);
			$this->db->insert('dg_validate_token',array('type'=>'newsletter','token'=>$token,'email'=>$email,'data'=>json_encode(array()),'created'=>date("Y-m-d H:i:s"),'expired'=>date( "Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) + 12 * 3600 )));
			$message=	'<table width="100%"cellspacing="1" cellpadding="1">
						<tr><td width="483" height="50"><font color="#CC3333">Thanks for subscribe to our newsletter.Please click on the Link to verify your email address. If any case Link didn\'t work then Re-send a Request or Contact our Support</font></td></tr>
						<tr><td width="483" height="50">'.base_url().'newsletter/subscribe/'.$token.'</td></tr></table>';
			$this->load->library('email', $config);
			$this->email->from(getEmail(config_item('admin_email')), getSiteName(config_item('site_name')));
			$this->email->to($email);    
			$this->email->subject('Subscribed Successfully');
			$this->email->message($message);   
			$this->email->send();				
			return true;
		}else{
			return false;
		}		
	}

	public function checkTkn($token){
		$checkTkn = $this->db->get_where('dg_validate_token',array('token'=>$token,'expired >='=>date("Y-m-d H:i:s"),'used'=>'0'))->row();			
		 if(!empty($checkTkn)){
			$this->db->where('token',$token);
			$this->db->update('dg_validate_token',array('used'=>'1'));
			$checkSubscribe = $this->db->get_where('dg_newsletter',array('email'=>$checkTkn->email,'verified'=>'0'))->row();
			if(!empty($checkSubscribe)){
				$this->db->where('email',$checkTkn->email);
				$this->db->update('dg_newsletter',array('verified'=>'1'));	
				$config = array(
					'mailtype' => 'html',
					'protocol' => 'sendmail',
					'mailpath'=>  '/usr/sbin/sendmail',
					'charset'=>   'iso-8859-1',
					'wordwrap'=>  TRUE,
					'validate'=>  TRUE,				
				);
				$message=	'<table width="100%"cellspacing="1" cellpadding="1">
							<tr><td width="483" height="50"><font color="#CC3333">Congratulation You have been successfully subscribe to our newsletter. In mean time you will receive alerts regarding new products, designs add to website. </font></td></tr>
							</table>';
				$this->load->library('email', $config);
				$this->email->from(getEmail(config_item('admin_email')), getSiteName(config_item('site_name')));
				$this->email->to($checkTkn->email);    
				$this->email->subject('Email Verification');
				$this->email->message($message);   
				$this->email->send();				
				return array('response'=>true,'msg'=>'<p class="text-success">Email Verified Successfully</p>');				
			}else{
				return array('response'=>false,'msg'=>'<p class="text-danger">Either email does not exists or its already verified successfully</p>');					 
			}
		 }else{
				return array('response'=>false,'msg'=>'<p class="text-danger">Either token expired or it does not exists</p>');				 
		 }
	}	
}