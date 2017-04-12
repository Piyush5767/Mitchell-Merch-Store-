<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class PrintfulRequest extends Frontend_Controller{
	
	public $_request;
	function __construct(){
		parent::__construct();
		require_once(APPPATH.'libraries/PrintfulClient.php');
		
		$this->load->model('settings_m');	
		// get setting
		$this->lang->load('custom');		
		$row = $this->settings_m->getSetting();		
		if(count($row) > 0)
			$this->_setting	= json_decode($row->settings);
		else
			$this->_setting	= $this->settings_m->setNew();
		
		$this->_printclient = new PrintfulClient($this->_setting->printful_client);
	}
	
	public function PrintfulshippingReq($data=array()){
		$rates = $this->_printclient->post('shipping/rates',array(
			'recipient' =>$data['addressArr'],
			'items'=> $data['items'],
		));
		if($data['request'] == 'rates'){
			return $rates;
		}elseif($data['request']=='list'){
			$rate = '';
			if(!empty($rates)){
				foreach($rates as $k_rate=>$row_rate){
					$checked = '';
					if($k_rate==0){ $checked = 'checked="checked"'; }
					$rate.='<tr><td>'.$row_rate['name'].'</td><td class="text-center">$'.$row_rate['rate'].'</td><td class="text-center"><input type="radio" name="shippingrate" '.$checked .' value="'.$row_rate['id'].'"></td></tr>';
				}				
			}
			$this->session->set_userdata('session_shipping', $rate);
			echo $rate;
		}
	}
	
	
	public function shippingRates(){		
		$data = $this->input->post();
		$this->user = $this->session->userdata('user');
		$cartobj =  $this->cart->contents();
		$items = array();
		$cart	 = $this->session->userdata('cart');
		if(empty($cart)){
			$cart = new stdClass();
			$cart->cart = new stdClass();
			$cart->cart->item = array();
			$cart->cart->subtotal = 0;
		}else{
			$cart->cart->item = array();
			$cart->cart->subtotal = 0;			
		}
		if(!empty($cartobj)){
			foreach($cartobj as $ke_cart=>$ro_cart){
				$cart->cart->item[$ro_cart['product_id']]  = $ro_cart['qty']; 
				$cart->cart->subtotal += $ro_cart['subtotal'];				
				$printobj = $this->_printclient->get('products/'.$ro_cart['printfulId']);
				$prodVariants = $printobj['variants'];
				$variantId = '';
				foreach($prodVariants as $k_variant=>$v_variant){
					if($ro_cart['printfulCode'] == 1){
						$clrcde = $v_variant['color_code'];
						$productClrCde = null;
					}else{							
						$clrcde = str_replace('#','',$v_variant['color_code']);
						$productClrCde = strtolower($ro_cart['productclr']);					
					}
					if((utf8_decode($v_variant['size']) == utf8_decode($ro_cart['productsize'])) && ($clrcde == $productClrCde)){
						$variantId = $v_variant['id'];
						break;
					}							
				}
				$items[] = array('variant_id'=>$variantId,'quantity'=>$ro_cart['qty']); 	
			}
			$this->session->set_userdata('cart', $cart);
		}		
		if($data['type'] == 'checkoutShipping'){
		   $rate = '';
		   $getCountryObj = $this->db->get_where('dg_country',array('id'=>$data['fields']['country'][19]))->row();
		   $getStateObj   = $this->db->get_where('dg_states',array('id'=>$data['fields']['state'][20]))->row();		   
		   $addressArr =  array(
							"address1"=> $data['fields']['address'][21],
							"city"=> $data['fields']['57962cad48bb6'][22],
							'country_code'=>$getCountryObj->code_2,
							'state_code' =>$getStateObj->code,
							"zip"=>$data['fields']['zipcode'][17]
						  );
			$this->session->set_userdata('shipment_address', $addressArr);			
			$rate = $this->PrintfulshippingReq(array('request'=>'list','addressArr'=>$addressArr,'items'=>$items));
			echo $rate;
		}elseif($data['type'] == 'addShipping'){
			$user 	= $this->session->userdata('user');	
			$sessionaddress	= $this->session->userdata('shipment_address');			
		    $getCountryObj = $this->db->get_where('dg_country',array('id'=>$data['fields']['country'][19]))->row();
		    $getStateObj   = $this->db->get_where('dg_states',array('id'=>$data['fields']['state'][20]))->row();				
			$addressArr =  array(
								"address1"=> $data['fields']['address'][21],
								"city"=> $data['fields']['57962cad48bb6'][22],
								'country_code'=>$getCountryObj->code_2,
								'state_code' =>$getStateObj->code,
								"zip"=>$data['fields']['zipcode'][17]
						  );
			$checkdiff = array_diff_assoc($sessionaddress,$addressArr);
			if(!empty($checkdiff)){
				$this->session->set_userdata('shipment_address', $addressArr);
				$rate = $this->PrintfulshippingReq(array('request'=>'list','addressArr'=>$addressArr,'items'=>$items));
				echo json_encode(array('response'=>false,'updateshipping'=>$rate,'msg'=>'<p class="text-danger">There is change in address, please update your shipping'));
			}else{				
				// update user profile
				$fields = $data['fields'];						
				$user_profile	= array();
				foreach($fields as $key => $value)
				{
					$id = key($value);
					$user_profile[] = array(
						'field_id'=>$id,
						'form_field'=>'checkout',
						'value'=>$value[$id],
						'object'=>$this->user['id'],
					);				
				}

				$this->load->model('fields_m');
				if (count($user_profile) > 0){
					$this->fields_m->add($user_profile);
				}				
				
				// Updating shipping Price
				$rates = $this->PrintfulshippingReq(array('request'=>'rates','addressArr'=>$addressArr,'items'=>$items));
				$shipping = $price = '';
				foreach($rates as $k_rate=>$v_rate){
					if($v_rate['id'] == $data['shippingrate']){
						$shipping = $v_rate['name'];
						$price = $v_rate['rate'];						
						break;
					}
				}
				//$cart	 = $this->session->userdata('cart');
				if(empty($cart->shipping)){
					$cart->shipping		= new stdClass();
				}
				$cart->shipping->id 	= $data['shippingrate'];
				$cart->shipping->price 	= $price;
				$cart->shipping->name   = $shipping;
				$this->session->set_userdata('cart', $cart);		
				echo json_encode(array('response'=>true));
			}
		}
	}
	
	function printfulOrderRequest()
	{
		$getOrders = $this->db->get_where('dg_printfulorder',array('order_status'=>'received','order_request'=>'pending'));
		if($getOrders->num_rows()>0)
		{
			$items = array();
			foreach($getOrders->result() as $k_row=>$v_row)
			{
				$getUserinfo  = $this->db->get_where('dg_orders_userinfo',array('order_id'=>$v_row->order_id))->row();
				$webapp_order = $this->db->get_where('dg_orders',array('id'=>$v_row->order_id))->row();
				$userobj 	  = json_decode($getUserinfo->address);			
				$printfulusr  = (Array)$userobj; 
				$productItems = json_decode($v_row->order_object);
				foreach($productItems as $k_item=>$v_item)
				{
					$variantId = '';
					$productId = $v_item->PrintfulproductId;
					$prdctFiletype = $v_item->productFiletype;
					$printfulClrCode = $v_item->printfulClrCode; 
					$printobj = $this->_printclient->get('products/'.$productId);
					$prodVariants = $printobj['variants'];

					foreach($prodVariants as $k_variant=>$v_variant){
						if($printfulClrCode == 1){
							$clrcde = $v_variant['color_code'];
							$productClrCde = null;
						}else{							
							$clrcde = str_replace('#','',$v_variant['color_code']);
							$productClrCde = strtolower($v_item->product_color);
						
						}
						if((utf8_decode($v_variant['size']) == utf8_decode($v_item->product_size)) && ($clrcde == $productClrCde)){
							$variantId = $v_variant['id'];
							break;
						}							
					}
					$prodPrice = json_decode($v_item->prices);
					$images = $v_item->image;

					if($prdctFiletype == 'single'){
						if(property_exists($images,"printfulcustom")){
							$files[] = array(
											'type'=> 'default',
											'url' => base_url($images->printfulcustom),
										);
						}else{
							if(property_exists($images,"frontImage")){								
								$files[] = array(
												'type'=> 'default',
												'url' => base_url($images->frontImage),
											);
							}							
						}										
					}else{
						if(property_exists($images,"frontImage")){
							
							$files[] = array(
											'type'=> 'default',
											'url' => base_url($images->frontImage),
										);
						}
						
						if(property_exists($images,"backImage")){
							$files[] = array(
											'type'=> 'back',
											'url' => base_url($images->backImage),
										);
						}						
					}
					if(property_exists($images,"mockup")){
						$files[] = array(
									'type'=> 'preview',
									'url' => base_url($images->mockup),
								);
					}	

					$items[] = array(
								'variant_id'=>$variantId,
								'quantity'=>$v_item->qty,
								'name' => $v_item->productName,
								'retail_price' =>$v_item->subtotal,	
								'files' => $files,	
								'options' => array(
												 array(
													'id' => 'remove_labels',
													'value' => true
													)
											),								
								);					
				}   
				$country_isoTwo = $state_isoTwo = '';
				$getCountryObj = $this->db->get_where('dg_country',array('name'=>array_values($printfulusr)[4]))->row();
				if(!empty($getCountryObj))
				{
					$country_isoTwo = $getCountryObj->code_2;
					$getStateObj = $this->db->get_where('dg_states',array('name'=>array_values($printfulusr)[6],'country_id'=>$getCountryObj->id))->row();
					$state_isoTwo = $getStateObj->code;
					$shipping = json_decode($webapp_order->shipping_obj);
					$data = array(
								'recipient' => array(
									'name' => array_values($printfulusr)[0].' '.array_values($printfulusr)[1],
									'address1' => array_values($printfulusr)[3],
									'city' => array_values($printfulusr)[5],
									'state_code' => $state_isoTwo,
									'country_code' => $country_isoTwo,
									'zip' => array_values($printfulusr)[8]
								),
								'items' =>$items,
								'external_id'=>$webapp_order->order_number,
								'shipping'=>$shipping->id,
							);
					$this->printful($v_row->order_row,$data);						
				}
			}
		}		
	}
	
	function printful($id,$order){
		$checkOrder = $this->db->get_where('dg_printfulorder',array('order_row'=>$id))->row();
		if($checkOrder->order_request != 'sent'){	
			$orderobj = $this->_printclient->post('orders',$order,array('confirm'=>1));
			

			$this->db->where('order_row',$id);
			$this->db->update('dg_printfulorder',array('order_request'=>'sent','printful_respstatus'=>$orderobj['status']));

			$this->db->where('order_id',$checkOrder->order_id);
			$this->db->update('dg_orders_histories',array('order_dealer_status'=>$orderobj['status']));

			$this->db->where('id',$orderobj['id']);
			$this->db->update('dg_orders',array('shipping_status'=>$orderobj['status']));
			
			$printful_response = array(
									'order_id'=>$checkOrder->order_id,
									'printful_orderid'=>$orderobj['id'],
									'external_id'=>$orderobj['external_id'],
									'status'=>$orderobj['status'],
									'shipping'=>$orderobj['shipping'],
									'created'=>date('Y-m-d H:i:s',$orderobj['created']),
									'updated'=>date('Y-m-d H:i:s',$orderobj['updated']),
									'recipient'=>json_encode($orderobj['recipient']),
									'items'=>json_encode($orderobj['items']),
									'costs'=>json_encode($orderobj['costs']),
									'retail_costs'=>json_encode($orderobj['retail_costs']),
									'shipments'=>json_encode($orderobj['shipments']),
									'gift'=>$orderobj['gift'],
									'packing_slip'=>$orderobj['packing_slip'],								
								  );
			$this->db->insert('dg_printful_order_response',$printful_response);			
		}		
	}	

	function PrintfulTracking(){
		$checkOrder = $this->db->get_where('dg_printful_order_response',array('`status` !='=>'fulfilled','`status` !='=>'canceled'));
		if(!empty($checkOrder)){
			foreach($checkOrder->result() as $k_order=>$row_order){
				$order = $this->_printclient->get('orders/'.$row_order->printful_orderid);
				if(!empty($order)){
					if($row_order->status != $order['status']){
						$update_array = array(
							'status'=>$order['status'],
							'shipping'=>$order['shipping'],							
							'shipments'=>json_encode($order['shipments']),
							'updated'=>date('Y-m-d H:i:s',$order['updated']),
						);						
						$this->db->where('id',$row_order->id);
						$this->db->update('dg_printful_order_response',$update_array);

						$this->db->where('id',$row_order->id);
						$this->db->update('dg_orders"',array('shipping_status'=>$order['status']));

						$this->db->where('order_id',$row_order->id);
						$this->db->update('dg_printfulorder"',array('printful_respstatus'=>$order['status']));
						
						if($order['status'] == 'failed' || $order['status'] == 'fulfilled'){
							$this->Orderemail($row_order->order_id,$order['status']);
						}
					}
				}
			}
		}		
	}
	
	function Orderemail($order_id,$type){
		$this->load->model(array('order_m','users_m','shipping_m','payment_m','printful_m'));
		$order 			= $this->order_m->getOrder($order_id);
		$shipping_obj   = json_decode($order->shipping_obj);
		$printful_obj   = $this->printful_m->getResponse($order_id);
		$dbCartorder 	= $this->order_m->GetOrdercartcontent($order_id);
		$cart_content 	= unserialize($dbCartorder->cart_content);
		$cart_items 	= unserialize($dbCartorder->cart_items);
		
		$lang = getLanguages();
		$user = $this->users_m->getUser($order->user_id);		
		
		$shipping_price = $order->shipping_price;
		
		// get shipping method
		$shipping	= $this->shipping_m->get($order->shipping_id, true);

		// get payment method
		$payment	= $this->payment_m->get($order->payment_id, true);

		// get discount
		if ($order->discount_id != 0){
			$discount	= $this->coupon_m->get($order->discount_id, true);
		}else{
			$discount	= array();
		}
			
		$total = 0;
		$count = 1;		
		$payment_price = 0.0;
		$items	= array();
		$i 			= 0;
		$total 		= 0;
		$subtotal 	= 0;				

		$html = '<table style="border-collapse:collapse;">';
		$html .= '<tr>';
		$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("name", $lang).'</td>';
		$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("sku", $lang).'</td>';
		$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("product_price", $lang).'</td>';
		$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("clipart_price", $lang).'</td>';
		$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("product_attributes", $lang).'</td>';
		$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("quantity", $lang).'</td>';
		$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("total", $lang).'</td>';
		$html .= '</tr>';

		foreach($cart_items as $i=>$item)
		{
			$clipart_total = $price_clipart	= $price_customclipart = 0;
			$cliparts						= json_decode($item['cliparts']);
			$customcliparts					= json_decode($item['cstmcliparts']);		
			$uploadimages    				= json_decode($item['uploadimages'],true);  
			// Clipart Price
			if (count($cliparts))
			{	
				foreach($cliparts as $view=>$art)
				{
					if (count($art))
					{
						foreach($art as $art_id=>$price)
						{
							$price_clipart 	= $price_clipart + $price;
						}
					}
				}				
			}
			
			// Custom Clipart Price
			if (count($customcliparts))
			{	
				foreach($customcliparts as $view=>$customart)
				{
					if (count($customart))
					{
						foreach($customart as $art_id=>$price)
						{
							$price_customclipart 	= $price_customclipart + $price;
						}
					}
				}
			}
			
			$uploadimgprice = 0;
			if(count($uploadimages))
			{						
				foreach($uploadimages as $view=>$imgobj){
					if(count($imgobj)){
						foreach($imgobj as $img_id=>$price)
						{							
							$uploadimgprice	= $uploadimgprice + $price;
						}
					}
				}
			}	
			
			$clipart_total = $price_clipart	+ $price_customclipart + $uploadimgprice + $item['texts'];			
			
			$prices		   = json_decode($item['prices']);
	
			// html email.
			$html .= '<tr>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$item['name'].'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$item['id'].'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$this->_setting->currency_symbol.number_format($prices->sale + $prices->colors, 2).'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$this->_setting->currency_symbol.number_format($clipart_total, 2).'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$this->_setting->currency_symbol.number_format($item['customPrice'], 2).'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$item['qty'].'</td>';
			$total_row = $item['qty']*($prices->sale + $prices->colors+$prices->prints+$clipart_total)+$item['customPrice'];
			$html .= '<td style="border: 1px solid #ccc; text-align: right;">'.$this->_setting->currency_symbol.number_format($total_row, 2).'</td>
			</tr>';
		}
			
		// html email.
		$html .= '<tr>
			<td  colspan="6" style="border: 1px solid #ccc; text-align: right; padding: 5px;" >
				'.language("shipment_fee", $lang).':';
				if (count($shipping)){							
					$html .= '<br><small>'.language("cart_shipping_method", $lang).': <a href="'.site_url().'"><strong>'.$shipping->title.'</strong></a></small>
					<br><small>'.$shipping->description.'</small>';
				}
				
			$html .= '</td>
			<td style="border: 1px solid #ccc; text-align: right; padding: 5px;">'.$this->_setting->currency_symbol.number_format($shipping_price, 2).'</td>
		</tr>';
		
		$html.='<tr>
			<td  colspan="6" style="border: 1px solid #ccc; text-align: right; padding: 5px;" >
				'.language("payment_fee", $lang).':';
				if (count($payment)) {							
					$html .= '<br><small>'.language("cart_payment_method", $lang).': <a href="'.site_url().'"><strong>'.$payment->title.'</strong></a></small>
					<br><small>'.$payment->description.'</small>';
				}
			$html .= '</td>
			<td style="border: 1px solid #ccc; text-align: right; padding: 5px;">'.$this->_setting->currency_symbol.number_format($payment_price, 2).'</td>
		</tr>';
		
		$html.='<tr>
			<td colspan="6" style="border: 1px solid #ccc; text-align: right; padding: 5px;">
				'.language("cart_discount", $lang);
				if (count($discount)) {							
					$html .= '<br><small>'.$discount->name.': <a href="'.site_url().'"><strong>'.$discount->code.'</strong></a></small>';							
				}
			$html .= '</td>
			<td style="border: 1px solid #ccc; text-align: right; padding: 5px;">'.$this->_setting->currency_symbol.number_format($order->discount, 2).'</td>
		</tr>
		<tr>';
		
		$total = $order->total;
		$html .= '<td colspan="6" style="border: 1px solid #ccc; text-align: right;">'.language("total", $lang).':</td>
			<td style="border: 1px solid #ccc; text-align: right; padding: 5px;" colspan="7"><strong>'.$this->_setting->currency_symbol.number_format($total, 2).'<strong></td>
		</tr></table>';

		// send email.
		$params = array(
			'username'=>$user->username,
			'date'=>date('Y-m-d H:i:s'),
			'email'=>$order->email,
			'total'=>$this->_setting->currency_symbol.number_format($total, 2),
			'order_number'=>$order->order_number,
			'shipping_method'=>$shipping_obj->name,
			'shipping_price'=>'$'.$shipping_obj->price,
			'table'=>$html,
		);

		//config email.
		$config = array(
			'mailtype' => 'html',
		);
		
		$this->load->library('email', $config);
		$this->email->from(getEmail(config_item('admin_email')), getSiteName(config_item('site_name')));
		$this->email->to($user->email);
		//$this->email->bcc($this->_setting->admin_email);

		if($type == 'fulfilled'){
			$tracking  = '<table style="border-collapse:collapse;">';
			$tracking .= '<tr>';
			$tracking .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("id", $lang).'</td>';
			$tracking .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("shipping_carrier_name", $lang).'</td>';
			$tracking .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("shipping_service_name", $lang).'</td>';
			$tracking .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("shipping_tracking_number", $lang).'</td>';
			$tracking .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("shipping_tracking_url", $lang).'</td>';
			$tracking .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("shipping_service_time", $lang).'</td>';
			$tracking .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("shipping_tracking_date", $lang).'</td>';
			$tracking .= '<td style="border: 1px solid #ccc; padding: 5px;">'.language("shipping_tracking_reshipment", $lang).'</td>';			
			$tracking .= '</tr>';			
			if(!empty($printful_obj['shipments']) && !empty(json_decode($printful_obj['shipments']))){
				$tracking_obj = json_decode($printful_obj['shipments'],true);
				$tracking .= '<tr><td>'.$tracking_obj['id'].'</td><td>'.$tracking_obj['carrier'].'</td><td>'.$tracking_obj['service'].'</td><td>'.$tracking_obj['tracking_number'].'</td><td>'.$tracking_obj['tracking_url'].'</td><td>'.$tracking_obj['created'].'</td><td>'.$tracking_obj['ship_date'].'</td><td>'.ucfirst($tracking_obj['reshipment']).'</td></tr>';	
			}else{
				$tracking .= '<tr><td class="text-center" colspan="8">No Shipment Details We Will Update It Shortly</td></tr>';
			}
			$tracking .= '</table>';
			$params['tracking_information'] = $tracking;
			$subject = configEmail('fulfilled_subject', $params);
			$message = configEmail('fulfilled_subject', $params);			
		}else{
			$subject = configEmail('failedorder_subject', $params);
			$message = configEmail('failedorder_msg', $params);
		}		
    
		$this->email->subject($subject);
		$this->email->message($message);   
		$this->email->send();		
	}	
}