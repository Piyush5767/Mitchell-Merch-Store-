<?php
/**
 * @author tshirtecommerce - www.tshirtecommerce.com
 * @date: 2015-01-10
 * 
 * payment with paypal
 * 
 * @copyright  Copyright (C) 2015 tshirtecommerce.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paypal
{
	function __construct($options)
	{
		$this->ini = $options;		
	}
	
	function action($data = array(), $post = array(), $id)
	{		
		$post_data = array(
			'notify_url'	 => site_url('payment/paymentipn/paypal/'.$id),
			'return'		 => site_url('payment/confirm'),
			'cancel_return'	 => site_url(),
			'tax'			 => 0,
			'no_note'		 => 1,
			'cmd'		 => '_xclick',
		);
		
		if(isset($this->ini['email']))
		{
			$post_data['business'] = $this->ini['email'];
		}
		
		if(isset($data['currency_code']))
		{
			$post_data['currency_code'] = $data['currency_code'];
		}
		
		if(isset($data['item_name']))
		{
			$post_data['item_name'] = $data['item_name'];
		}
		
		if(isset($data['item_number']))
		{
			$post_data['item_number'] = $data['item_number'];
		}
		
		if(isset($data['qty']))
		{
			$post_data['quantity'] = $data['qty'];
		}
		
		if(isset($data['amount']))
		{
			$post_data['amount'] = number_format($data['amount'], 2);
		}
		
		if(isset($data['shipping']))
		{
			$post_data['shipping'] = $data['shipping'];
		}
		
		if(isset($this->ini['sandbox']))
		{
			if($this->ini['sandbox'] == 1)
			{
				header('Location: https://www.sandbox.paypal.com/cgi-bin/webscr?' . http_build_query($post_data));
			}
			else
			{
				header('Location: https://www.paypal.com/cgi-bin/webscr?' . http_build_query($post_data));
			}
			
			$ci = & get_instance();
			$ci->load->library('session');
			
			if(isset($this->ini['message']))
				$ci->session->set_flashdata('message', $this->ini['message']);
			

		}
		else
		{
			redirect(site_url('payment'));
		}
	}
	
	function ipn($data = array())
	{
		if(isset($this->ini['sandbox']) && isset($this->ini['api_username']) && isset($this->ini['password']) && isset($this->ini['signature']))
		{
			$config = array( 
						'Sandbox' => $this->ini['sandbox'],
						'APIUsername' => $this->ini['api_username'],
						'APIPassword' => $this->ini['password'],
						'APISignature' => $this->ini['signature'], 
						'PrintHeaders' => false, 
						'LogResults' => false,
						'LogPath' => site_url('/payment'),
			); //config paypal get transition.
			
			if(isset($data['txn_id']) && isset($data['item_number']))
			{	
				$ci = & get_instance();
				$ci->load->library('getpaypal');
				$paypal = new getPaypal($config);
			
				$trans = $paypal->getTransaction($data['txn_id']);
				if(!isset($trans['AMT']))
					exit();
					
				$money = $paypal->getMoney($data['txn_id']);
				
				$ci->load->model('order_m');
				$order = $ci->order_m->getOrderNumber($data['item_number']);
					
				if(isset($order->total) && $money == $order->total)
				{
					$receiver_payment = array(
						'payer_email'=>$data['payer_email'],
						'verify_sign'=>$data['verify_sign'],
						'txn_id'=>$data['txn_id'],
						'currency_type'=>$data['mc_currency'],
						'payer_id'=>$data['payer_id'],
						'tax'=>$data['tax'],
						'payment_date'=>$data['payment_date'],
						'payment_fee'=>$data['payment_fee'],
						'receiver_id'=>$data['receiver_id'],
						'handling_amount'=>$data['handling_amount'],
						'shipping'=>$data['shipping'],
						'auth'=>$data['auth'],						
					);	
					//$ci->session->set_flashdata('msg', 'Thanks you for payment!');
					$update['status'] = 'completed';
					$update['payment_obj'] = json_encode($receiver_payment);
					$updatehis['label'] = 'order_status';
					$updatehis['content'] = json_encode(array($order->order_number=>'completed'));
					$updatehis['date'] = date('Y-m-d H:i:s');
					$updatehis['order_id'] = $order->id;					
					if($ci->order_m->save($update, $order->id))
					{
						$ci->order_m->_table_name = 'orders_histories';
						$ci->order_m->save($updatehis);						
					}
				}
			}
		}
	}
	
	function refundTransaction($data = array()){
		if(isset($this->ini['sandbox']) && isset($this->ini['api_username']) && isset($this->ini['password']) && isset($this->ini['signature']))
		{
			$refund = $insert = false;
			$config = array( 
						'Sandbox' => $this->ini['sandbox'],
						'APIUsername' => $this->ini['api_username'],
						'APIPassword' => $this->ini['password'],
						'APISignature' => $this->ini['signature'], 
						'PrintHeaders' => false, 
						'LogResults' => false,
						'LogPath' => site_url('/payment'),
			); //config paypal get transition.
					
			$ci = & get_instance();
			$ci->load->library('getpaypal');
			$paypal = new getPaypal($config);			
			$RTFields = array(
				'transactionid' => $data['txn_id'],
				'payerid' => $data['payer_id'],
				'invoiceid' => $data['order_number'],	
				'refundtype' => 'Full',	
				'note' => 'Refund related to cancelation of product',         
			);			
			$transRefund = $paypal->refundOrderAmount($RTFields);			

			$ci->load->model(array('order_m','payment_m','refund_m'));
			$order = $ci->order_m->getOrder($data['order_id'],true);				
			$refundObj = array(
							'order_id'=>$data['order_id'],
							'order_number'=>$data['order_number'],
							'payment_id'=>$order->payment_id,
							'date'=>date('Y-m-d H:i:s'),
							'status'=>$transRefund['ACK'],
							'refund_object'=>json_encode($transRefund),
						);							
			if($transRefund['ACK'] == 'Success'){
				$refundObj['refund_id'] = $transRefund['REFUNDTRANSACTIONID'];							
				$refundObj['refund_fee'] = $transRefund['FEEREFUNDAMT'];
				$refundObj['netrefund_amnt'] = $transRefund['NETREFUNDAMT'];
				$refundObj['refund_amount'] = $transRefund['TOTALREFUNDEDAMOUNT'];
				$update['status'] = 'refunded';
				$ci->order_m->save($update, $order->id);
				$insert = true; 
			}elseif($transRefund['ACK'] == 'Failure'){
				$checkRefund = $ci->db->where(array('refund_error'=>$transRefund['L_ERRORCODE0'],'order_id'=>$data['order_id']))->get('refund_transaction')->row();
				if(empty($checkRefund)){
					$refundObj['refund_id']   =  ucwords($transRefund['L_SHORTMESSAGE0']);	
					$refundObj['refund_error'] = $transRefund['L_ERRORCODE0'];
					$insert = true;
				}
			}
			if($insert == true){
				$ci->payment_m->_table_name = 'refund_transaction';
				$ci->payment_m->save($refundObj);
				if($transRefund['ACK'] == 'Success'){					
					$refund =  $ci->db->insert_id();
				}				
			}	
			return $refund;			
		}		
	}  
}
?>