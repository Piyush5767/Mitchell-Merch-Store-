<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class cron extends Frontend_Controller{
	
	private $_printclient;
	private $_setting;
	function __construct(){
		parent::__construct();
		$this->load->model('settings_m');		

		// get setting
		$this->lang->load('custom');
		$row 	= $this->settings_m->getSetting();	
		if(count($row) > 0)
			$this->_setting	= json_decode($row->settings);
		else
			$this->_setting	= $this->settings_m->setNew();
		
	}
	
	function newsletter(){
		$emailStatus = $this->db->get_where('dg_config_emails',array('label'=>'newsletter_status'))->row();
		if($emailStatus->message == 1){
			$newsletterUsrs = $this->db->get_where('dg_newsletter',array('subscribe'=>'1','verified'=>'1'));
			if($newsletterUsrs->num_rows()>0){
				foreach($newsletterUsrs->result() as $k_news=>$row){
					$this->db->save_query = true;
					$getProducts = $this->db->get_where('dg_products',array('created >='=>date( "Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - 48 * 3600 ),'created <='=>date( "Y-m-d H:i:s")));
					if($getProducts->num_rows()>0){
						$html = '<table style="border-collapse:collapse;">';
						$html .= '<tr>';
						$html .= '<td style="border: 1px solid #ccc; padding: 5px;width:43%;">'.lang("name").'</td>';
						$html .= '<td style="border: 1px solid #ccc; padding: 5px;width:40%;">'.lang("product_image").'</td>';				
						$html .= '<td style="border: 1px solid #ccc; padding: 5px;width:17%;">'.lang("price").'</td>';
						$html .= '</tr>';					
						foreach($getProducts->result() as $k_prds=>$row_prds){
							$html .= '<tr>';
							$html .= '<td style="border: 1px solid #ccc; padding: 5px;"><a href="'.base_url('product/'.$row_prds->id.'-'.$row_prds->slug).'" target="_blank">'.$row_prds->title.'</a></td>';
							$html .= '<td style="border: 1px solid #ccc; padding: 5px;"><img src="'.base_url($row_prds->image).'" style="width:200px;height:200px;"></td>';	
							$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$this->_setting->currency_symbol.number_format($row_prds->price,2).'</td>';
							$html .= '</tr>';						
						}
						$html .= '</table>';
						
						$getDesigns = $this->db->get_where('dg_designimage',array('datetime >='=>date( "Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - 48 * 3600 ),'datetime <='=>date( "Y-m-d H:i:s")));
						$design = '<table style="border-collapse:collapse;">';
						$design .= '<tr>';
						$design .= '<td style="border: 1px solid #ccc; padding: 5px;width:43%;">'.lang("name").'</td>';
						$design .= '<td style="border: 1px solid #ccc; padding: 5px;width:40%;">'.lang("design_image").'</td>';				
						$design .= '<td style="border: 1px solid #ccc; padding: 5px;width:17%;">'.lang("price").'</td>';
						$design .= '</tr>';	
						if($getDesigns->num_rows()>0){
							foreach($getDesigns->result() as $k_dsgns=>$row_dsgns){
								$design .= '<tr>';
								$design .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$row_dsgns->name.'</td>';
								$design .= '<td style="border: 1px solid #ccc; padding: 5px;"><img src="'.base_url('media/designimage/'.$row_dsgns->full).'" style="width:200px;height:200px;"></td>';	
								$design .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$this->_setting->currency_symbol.number_format($row_dsgns->price,2).'</td>';
								$design .= '</tr>';						
							}						
						}
						$design .= '</table>';
						
						// send email.
						$params = array(
							'email'=>$row->email,
							'date'=>date('Y-m-d H:i:s'),
							'products'=>$html,
							'designs'=>$design,
						);
						
						//config email.
						$config = array(
							'mailtype' => 'html',
						);
						$subject = configEmail('newsletter_subject', $params);
						$message = configEmail('newsletter_msg', $params);
						
						$this->load->library('email', $config);
						$this->email->from(getEmail(config_item('admin_email')), getSiteName(config_item('site_name')));
						$this->email->to($row->email);    
						$this->email->subject($subject);
						$this->email->message($message);   
						$this->email->send();			
						$this->email->clear();						
					}			
				}			
			}				
		}		
	}
	
	function refund()
	{	
		$this->load->model(array('order_m','payment_m'));		
		$order = $this->order_m->getOrderByCondition(array('status'=>'completed','shipping_status'=>'canceled'),false);
		if(!empty($order) && ($order->num_rows()>0))
		{
			foreach($order->result() as $order_key=>$order_row)
			{
				$order_row = $this->order_m->getOrder($order_row->id,true);
				$transobj = json_decode($order_row->payment_obj,true);
				$transobj['order_id'] = $order_row->id;
				$transobj['order_number'] = $order_row->order_number;		
				// Payment
				$row	= $this->payment_m->getData($order_row->payment_id);
				$payment_method	= $row->type;
				$file = ROOTPATH .DS. 'application' .DS. 'payments' .DS. $payment_method .DS. $payment_method.'.php';		
				if(file_exists($file))
				{
					include_once($file);
					if(count($row) > 0)
					{
						$options = json_decode($row->configs, true);	
						$payment = new $payment_method($options);
						$refund = $payment->refundTransaction($transobj);
						if($refund != false){
							$this->refundEmail($refund);
						}
					}
				}					
			}		
		}		
	}

	public function refundEmail($id)
	{
		$this->load->model(array('refund_m','settings_m','payment_m','order_m','users_m'));
		$refund = $this->refund_m->getRefund($id);
		if(!empty($refund))
		{
			$order  = $this->order_m->getOrder($refund->order_id,true);		
			$user   = $this->users_m->getUser($order->user_id);
			$payment = $this->payment_m->getData($refund->payment_id);
			
			// get setting
			$row 	= $this->settings_m->getSetting();
			$setting = json_decode($row->settings);	
			
			// send email.
			$params = array(
				'username'=>$user->username,
				'date'=>date('Y-m-d H:i:s'),
				'email'=>$user->email,
				'order_number'=>$order->order_number,
				'payment_refund_id'=>$refund->refund_id,
				'payment_type'=>ucwords($payment->title),
				'payment_refund_fee'=>$setting->currency_symbol.number_format($refund->refund_fee, 2),
				'payment_refund_amount'=>$setting->currency_symbol.number_format($refund->refund_amount, 2),
			);
	  
			//config email.
			$config = array(
				'mailtype' => 'html',
			);
			
			$subject = configEmail('refundorder_subject', $params);
			$message = configEmail('refundorder_msg', $params);
			$this->load->library('email', $config);
			$this->email->from(getEmail(config_item('admin_email')), getSiteName(config_item('site_name')));
			$this->email->to($user->email);    
			$this->email->subject($subject);
			$this->email->message($message);   
			$this->email->send();				
		}	
	}	
}