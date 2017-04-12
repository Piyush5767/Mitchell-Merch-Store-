<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ini_set('max_execution_time', 600);
class Payment extends Frontend_Controller
{
	public $_session_id;
	public function __construct(){
		parent::__construct();
		$this->langs = getLanguages();	
		$this->_session_id 	= $this->session->userdata('order_session_id');	
		if(!$this->load->is_loaded('cache')) 
			$this->load->driver('cache', array('adapter'=>'file'));				
	}
	
	// check form
	function index()
	{	
		$this->user 	= $this->session->userdata('user');
		$this->items 	= $this->cart->contents();
		
		$lang = getLanguages();
		
		if (count($this->items) == 0 || count($this->user) == 0)
			redirect('cart');
		
		
		if($this->input->post('payment'))
		{
			$shipping_obj = '';
			$data = $this->input->post();			
			
			// add payment to session
			if ($this->session->userdata('cart'))
			{
				$cart = $this->session->userdata('cart');
				$shipping_obj = json_encode($cart->shipping);
			}
			else
			{
				$cart = new stdClass();
			}
			$cart->payment = $data['payment'];				
			
			// update user profile
			$fields = $data['fields'];
			if (count($fields) == 0)
				redirect('cart/checkout');
							
			$user_profile	= array();
			foreach($fields as $key => $value)
			{
				$id 	= key($value);
				$user_profile[] = array(
					'field_id'=>$id,
					'form_field'=>'checkout',
					'value'=>$value[$id],
					'object'=>$this->user['id'],
				);				
			}
			$this->load->model('fields_m');
			if ( count($user_profile) > 0 )
			{
				$this->fields_m->add($user_profile);
			}			
			
			// get design option
			$designs 		= $this->cache->get('orders_designs'.$this->_session_id);
	
			$print_parent = array();
			
			foreach($this->items as $key_items=>$value_items){
				$getPrintfulId = $this->db->get_where('dg_products',array('id'=>$value_items['product_id']))->row();				
				$row_images = $mockup = '';			
				$items_printful	= array();			
				$items_printful['rowid'] = $value_items['rowid'];
				$items_printful['productSku']=  $value_items['id'];
				$items_printful['productId'] = $value_items['product_id'];
				$items_printful['PrintfulproductId'] = $getPrintfulId->printful_productid;	
				$items_printful['productFiletype']   = $getPrintfulId->printful_printfile;	
				$items_printful['printfulClrCode']   = $getPrintfulId->printfulClrCode;					
				$items_printful['qty'] =  $value_items['qty'];				
				$items_printful['subtotal'] = $value_items['subtotal'];
				$items_printful['prices'] =  $value_items['prices'];		
				$items_printful['productName']= $value_items['name'];
				$items_printful['time']	=  $value_items['time'];		
				$items_printful['product_size']	=  $designs[$value_items['rowid']]['product_size'];	
				$items_printful['product_color']	=  $designs[$value_items['rowid']]['product_color'];						
				$row_images =  $designs[$value_items['rowid']];	
				if(!empty(array_filter($row_images['images']))){
					$sha1 = sha1($items_printful['time'] * $this->user['id']);
					
					if(array_key_exists('canvfront',$row_images['images'])){
						$info1 = getimagesize('/home/swissmal/public_html/developer/'.$row_images['images']['canvfront']);
						$extension1 = image_type_to_extension($info1[2]);
						$frontimagepath = $this->resampleImage('/home/swissmal/public_html/developer/'.$row_images['images']['canvfront'],'printfulfront_'.$sha1.$extension1);
						$items_printful['image']['mockup'] = $row_images['images']['front'];	
						$items_printful['image']['frontImage']  = $frontimagepath;						
					}
					
					if(array_key_exists('canvback',$row_images['images'])){
						$info2 = getimagesize('/home/swissmal/public_html/developer/'.$row_images['images']['canvback']);
						$extension2 = image_type_to_extension($info2[2]);						
						$backimagepath = $this->resampleImage('/home/swissmal/public_html/developer/'.$row_images['images']['canvback'],'printfulback_'.$sha1.$extension2);
						$items_printful['image']['mockup']= $row_images['images']['back'];	
						$items_printful['image']['backImage']  = $backimagepath;						
					}		
					
					if(array_key_exists('canvleft',$row_images['images'])){
						$info3 = getimagesize('/home/swissmal/public_html/developer/'.$row_images['images']['canvleft']);
						$extension3 = image_type_to_extension($info3[2]);						
						$leftimagepath = $this->resampleImage('/home/swissmal/public_html/developer/'.$row_images['images']['canvleft'],'printfulleft_'.$sha1.$extension3);
						$items_printful['image']['leftMockup'] = $row_images['images']['left'];	
						$items_printful['image']['backImage'] = $leftimagepath;						
					}
					
					if(array_key_exists('canvright',$row_images['images'])){
						$info4 = getimagesize('/home/swissmal/public_html/developer/'.$row_images['images']['canvright']);
						$extension4 = image_type_to_extension($info4[2]);						
						$rightimagepath = $this->resampleImage('/home/swissmal/public_html/developer/'.$row_images['images']['canvright'],'printfulright_'.$sha1.$extension4);
						$items_printful['image']['rightMockup'] = $row_images['images']['right'];	
						$items_printful['image']['backImage'] = $rightimagepath;					
					}
					
					if(array_key_exists('canvfront',$row_images['images'])  && array_key_exists('canvback',$row_images['images'])){
						$items_printful['image']['mockup']	=  $this->imageMerge($row_images['images']['front'],$row_images['images']['back'],'printfulmockup_'.$sha1,'horizontal');						
						if($getPrintfulId->printful_printfile == 'single'){
							$imageMerge =  $this->imageMerge($row_images['images']['canvfront'],$row_images['images']['canvback'],'printfulcustom_'.$sha1,'horizontal');													
							$items_printful['image']['printfulcustom'] = $this->resampleImage('/home/swissmal/public_html/developer/'.$imageMerge,'printfulcustom_'.$sha1.'.png');	
						}
					}     		 		
				}
				$print_parent[] = $items_printful;
			}			
			
			$items	= array();
			$i 			= 0;
			$total 		= 0;
			$subtotal 	= 0;				
			foreach($this->items as $key => $item)
			{ 			
				$subtotal  = $subtotal + $item['subtotal'] + $item['customPrice'];
				$items['design'][$i] = $designs[$key];
				$items['cart'][$i]	= $item;
				$items['cart'][$i]['teams']	    = json_encode($items['cart'][$i]['teams']);
				$items['cart'][$i]['options']	= json_encode($items['cart'][$i]['options']);
				$i++;
									
			}
			$items['user'] 				= $this->user;
			$items['metod'] 			= $cart;				
			$items['metod']->subtotal 	= $subtotal;
			
			// save design
			if(!$this->load->is_loaded('order_m')) 
				$this->load->model('order_m');
			
			$design_ids = array();
			if (count($items['design']))
			{
				$this->load->model('design_m');
				foreach($items['design'] as $i=>$design)
				{
					$design_id 				= $this->order_m->creteOrderNumber(15);
					$design_ids[$i]			= $design_id;
					$insert = array(
						'title'				=> '', 
						'description'		=> '', 
						'design_id'			=> $design_id,						
						'modified'			=> '',
						'fonts'				=> $design['fonts'],
						'system_id'			=> 0,
						'user_id'			=> $this->user['id'], 
						'product_id'		=> $items['cart'][$i]['product_id'], 
						'product_options'	=> $design['color'], 
						'vectors'			=> $design['vector'], 
						'teams'				=> json_encode($items['cart'][$i]['teams']), 
						'image' 			=> $design['images']['front'],						
						'created' 			=> date("Y-m-d H:i:s")
					);
					
					$this->design_m->save($insert, null);					
				}
			}
			
			
			// save order
			$order 					= $this->order_m->addNew('order');
			$order['order_number']	= $this->order_m->creteOrderNumber();
			$order['order_pass']	= $this->order_m->creteOrderNumber();
			$order['user_id']		= $this->user['id'];			
			$order['payment_id']	= $items['metod']->payment;
			$order['shipping_id']	= $items['metod']->shipping->id;
			$order['shipping_obj']  = $shipping_obj;
			
			if ( isset($items['metod']->discount) && isset($items['metod']->discount->id) )
			{
				// get discount
				$order['discount_id']	= $items['metod']->discount->id;
				if ( $items['metod']->discount->discount_type == 't' )
				{
					$order['discount']	=  $items['metod']->discount->value;
				}
				else
				{
					$order['discount']	=  ($items['metod']->subtotal * $items['metod']->discount->value)/100;
				}
				
				// update coupon
				$this->load->model('coupon_m');
				if ( $items['metod']->discount->type == 'g' )
				{
					$coupon 	= array(
						'count'	=> 1
					);
				}
				else
				{
					$row 		= $this->coupon_m->get($items['metod']->discount->id, true);
					$coupon 	= array(
						'count'	=> $row->count + 1
					);
				}
				$this->coupon_m->save($coupon, $items['metod']->discount->id);
			}
			$order['shipping_id']	= $items['metod']->shipping->id;
			$order['shipping_price']= $items['metod']->shipping->price;
			$order['sub_total']		= $items['metod']->subtotal;
			$order['total']			= $order['sub_total'] + $order['shipping_price'] - $order['discount'];			
			$order['status']		= 'pending';
			$order_id 				= $this->order_m->save($order, null);
			
			// Insert Cart Cache Id
			$cache_cart = 'cart_'.$order_id.'_'.$this->_session_id;
			$cart_cache['cart_cache_id']  = $cache_cart;
			$this->order_m->save($cart_cache, $order_id);

			$this->session->set_userdata(array('cart_cache_id'=>$cache_cart));
			
			// Insert Printful Object				
			$this->order_m->printful_order($this->user['id'],$order_id,json_encode($print_parent));
			
			// Save Order Json Obj For Customer Email
			$orderObj = $this->order_m->Ordercartcontent($order_id,serialize($this->items),serialize($items['cart']));
			
			// save order items
			$order_item				= $this->order_m->addNew('item');
			$order_item['order_id'] = $order_id;
			
			// get setting
			$this->load->model('settings_m');
			$row 	= $this->settings_m->getSetting();
			$setting = json_decode($row->settings);
			
			// get shipping method
			$this->load->model('shipping_m');
			$shipping	= $this->shipping_m->get($items['metod']->shipping->id, true);
			
			// get payment method
			$this->load->model('payment_m');
			$payment	= $this->payment_m->get($items['metod']->payment, true);
			
			// get discount
			if (isset($items['metod']->discount->id))
			{
				$this->load->model('coupon_m');
				$discount	= $this->coupon_m->get($items['metod']->discount->id, true);
			}
			else
			{
				$discount	= array();
			}
			$this->data['discount'] = $discount;
			// html email.
			$total = 0;
			$count = 1;
			$shipping_price = $items['metod']->shipping->price;
			$payment_price = 0.0;
			
			foreach($items['cart'] as $i=>$item)
			{
				$clipart_total = $price_clipart	= $price_customclipart = 0;

				$cliparts						= json_decode($item['cliparts']);
				$customcliparts                 = json_decode($item['cstmcliparts']); 
				$uploadimages    				= json_decode($item['uploadimages'],true);  
				if (count($cliparts))
				{	
					// save order cliparts
					$arts 	= array();
					$ij = 0;
					foreach($cliparts as $view=>$art)
					{
						if (count($art))
						{
							foreach($art as $art_id=>$price)
							{
								if ($art_id > 0)
								{
									$price_clipart 	= $price_clipart + $price;
									$arts[$ij]		= array(										
										'clipart_id'=> $art_id,
										'order_id'	=> $order_id,
										'created'	=> date("Y-m-d H:i:s")
									);
									$ij++;
								}
							}
						}
					}
					if (count($arts))
						$this->db->insert_batch('order_cliparts', $arts);
				}				

				if (count($customcliparts))
				{	
					// save order cliparts
					$customarts 	= array();
					$ij = 0;
					foreach($customcliparts as $view=>$customart)
					{
						if (count($customart))
						{
							foreach($customart as $art_id=>$price)
							{
								if ($art_id > 0)
								{
									$price_customclipart 	= $price_customclipart + $price;
									$customarts[$ij]		= array(										
										'clipart_id'=> $art_id,
										'order_id'	=> $order_id,
										'created'	=> date("Y-m-d H:i:s"),
									);
									$ij++;
								}
							}
						}
					}
					if (count($customarts))
						$this->db->insert_batch('order_customcliparts', $customarts);
				}
				
				$uploadimgprice = 0;
				if(count($uploadimages))
				{					
					$date 	= new DateTime();
					$year	= $date->format('Y');
					$now    = 1 * $date->getTimestamp();
					$root 	= ROOTPATH .DS. 'media' .DS. 'assets' .DS. 'uploaded' .DS. 'order'.DS. $year;
					if (!file_exists($root))
						mkdir($root, 0755);
					
					$month 	= $date->format('m');
					$root 	= $root .DS. $month .DS;
					if (!file_exists($root))
						mkdir($root, 0755);		
					
					$thumbpth 	= $root.'thumb'.DS;
					if (!file_exists($thumbpth))
						mkdir($thumbpth, 0755);
					$this->load->library('image_lib');
					foreach($uploadimages as $view=>$imgobj){
						if(count($imgobj)){
							foreach($imgobj as $img_id=>$price)
							{
								$getimgobj = $this->db->get_where('dg_user_upload_image',array('id'=>$img_id))->row();
								if(!empty($getimgobj)){									
									$info = new SplFileInfo(ROOTPATH.DS.$getimgobj->imgfull);
									$basename = $info->getBasename('.'.$info->getExtension());
									$img = sha1($basename.'_'.$now).'.'.$info->getExtension();
									$fullimg     = $root.$img;
									$thumbimg    = $thumbpth.$img;	
									$config['image_library']  = 'gd2';
									$config['source_image']	  = ROOTPATH.DS.$getimgobj->imgfull;
									$config['new_image']	  = $fullimg;
									$config['create_thumb']   = FALSE;
									$config['maintain_ratio'] = FALSE;	
									$this->image_lib->initialize($config);								
									$this->image_lib->resize();
									$this->image_lib->clear();										
												
									// Image Thumb
									$config['image_library']  = 'gd2';
									$config['source_image']	  = ROOTPATH.DS.$getimgobj->imgfull;
									$config['new_image']	  = $thumbimg;
									$config['create_thumb']   = TRUE;
									$config['maintain_ratio'] = TRUE;
									$config['width']		  = 300;
									$config['height']	      = 300;	
									$this->image_lib->initialize($config);																				
									$this->image_lib->resize();
									$this->image_lib->clear();	
									$imgpth           = 'media/assets/uploaded/order/'. $year .'/'. $month .'/';
									$this->db->insert('dg_order_uploadimage',array('clipart_id'=>$img_id,'order_id'=>$order_id,'full'=>$imgpth.$img,'thumb'=>$imgpth.'thumb/'.$img,'created'=>date("Y-m-d H:i:s")));
								}								
								$uploadimgprice	= $uploadimgprice + $price;
							}
						}
					}
				}				
				
				$clipart_total = $price_clipart + $price_customclipart + $uploadimgprice + $item['texts'];
				
				$prices							= json_decode($item['prices']);
				$order_item['design_id'] 		= $design_ids[$i];
				$order_item['product_id'] 		= $item['product_id'];				
				$order_item['product_name'] 	= $item['name'];				
				$order_item['product_sku'] 		= $item['id'];				
				$order_item['product_price'] 	= $prices->sale + $prices->colors;				
				$order_item['price_print'] 		= $prices->prints;				
				$order_item['price_clipart'] 	= $clipart_total;				
				$order_item['price_attributes'] = $item['customPrice'];				
				$order_item['quantity'] 		= $item['qty'];				
				$order_item['poduct_status'] 	= 'pending';				
				$order_item['attributes'] 		= json_encode($item['options']);				
				
				$this->order_m->save($order_item, null);
				
			}
			
			// save user address shipping
			$order_info				= $this->order_m->addNew('info');
			$order_info['order_id'] = $order_id;
			$order_info['user_id'] 	= $this->user['id'];
			$profiles				= array();
			foreach($fields as $key => $value)
			{
				$id 	= key($value);
				$field	= $this->fields_m->getField($id);
				
				if ($field != '')
				{
					if ($field->type == 'country')
					{
						$profiles[$field->title]	= $this->fields_m->getCountry($value[$id]);
					}
					elseif ($field->type == 'state')
					{
						$profiles[$field->title]	= $this->fields_m->getState($value[$id]);
					}
					else
					{
						$profiles[$field->title]	= $value[$id];
					}
				}
			}
			$order_info['address'] 	= json_encode($profiles);
			$this->order_m->save($order_info, null);
			
			// Payment
			$this->load->model('payment_m');
			$row	= $this->payment_m->get($cart->payment, true);
			if (count($row) == 0)
			{
				redirect('cart/checkout');
			}
			$payment_method	= $row->type;
			$file = ROOTPATH .DS. 'application' .DS. 'payments' .DS. $payment_method .DS. $payment_method.'.php';
						
			// get currency
			$this->load->model('settings_m');
			$currency	= $this->settings_m->getCurrency();
			$product = array(
				'item_name'=> $order['order_number'],
				'item_number'=> $order['order_number'],
				'amount'=> ($subtotal - $order['discount']),
				'shipping'=> $items['metod']->shipping->price,
				'qty'=> 1,
				'currency_code'=> $currency->currency_code
			);
		
			if(file_exists($file))
			{
				include_once($file);
				$options	= json_decode($row->configs, true);				
				$pay = new $payment_method( $options );
				$pay->action($product, $data, $row->id);
			}else{
				redirect('cart/checkout');
			}
		}
		else
		{
			die('eror');
			redirect('index.php');
		}	
	}
	
	function Orderemail($id){		
		$this->load->model(array('order_m','users_m','settings_m','shipping_m','payment_m','coupon_m'));
		$order_id  		= $this->order_m->Getcartid($id);
		$order 			= $this->order_m->getOrder($order_id);
		$shipping_obj   = json_decode($order->shipping_obj);

		$dbCartorder 	= $this->order_m->GetOrdercartcontent($order_id);
		$cart_content 	= unserialize($dbCartorder->cart_content);
		$cart_items 	= unserialize($dbCartorder->cart_items);
		$lang = getLanguages();
		$user = $this->users_m->getUser($order->user_id);		
		
		// get setting
		$row 	= $this->settings_m->getSetting();
		$setting = json_decode($row->settings);	
		
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
			
			$prices							= json_decode($item['prices']);
	
			// html email.
			$html .= '<tr>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$item['name'].'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$item['id'].'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$setting->currency_symbol.number_format($prices->sale + $prices->colors, 2).'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$setting->currency_symbol.number_format($clipart_total, 2).'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$setting->currency_symbol.number_format($item['customPrice'], 2).'</td>';
			$html .= '<td style="border: 1px solid #ccc; padding: 5px;">'.$item['qty'].'</td>';
			$total_row = $item['qty']*($prices->sale + $prices->colors+$prices->prints+$clipart_total)+$item['customPrice'];
			$html .= '<td style="border: 1px solid #ccc; text-align: right;">'.$setting->currency_symbol.number_format($total_row, 2).'</td>
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
			<td style="border: 1px solid #ccc; text-align: right; padding: 5px;">'.$setting->currency_symbol.number_format($shipping_price, 2).'</td>
		</tr>';
		
		$html.='<tr>
			<td  colspan="6" style="border: 1px solid #ccc; text-align: right; padding: 5px;" >
				'.language("payment_fee", $lang).':';
				if (count($payment)) {							
					$html .= '<br><small>'.language("cart_payment_method", $lang).': <a href="'.site_url().'"><strong>'.$payment->title.'</strong></a></small>
					<br><small>'.$payment->description.'</small>';
				}
			$html .= '</td>
			<td style="border: 1px solid #ccc; text-align: right; padding: 5px;">'.$setting->currency_symbol.number_format($payment_price, 2).'</td>
		</tr>';
		
		$html.='<tr>
			<td colspan="6" style="border: 1px solid #ccc; text-align: right; padding: 5px;">
				'.language("cart_discount", $lang);
				if (count($discount)) {							
					$html .= '<br><small>'.$discount->name.': <a href="'.site_url().'"><strong>'.$discount->code.'</strong></a></small>';							
				}
			$html .= '</td>
			<td style="border: 1px solid #ccc; text-align: right; padding: 5px;">'.$setting->currency_symbol.number_format($order->discount, 2).'</td>
		</tr>
		<tr>';
		
		$total = $order->total;
		$html .= '<td colspan="6" style="border: 1px solid #ccc; text-align: right;">'.language("total", $lang).':</td>
			<td style="border: 1px solid #ccc; text-align: right; padding: 5px;" colspan="7"><strong>'.$setting->currency_symbol.number_format($total, 2).'<strong></td>
		</tr></table>';

		// send email.
		$params = array(
			'username'=>$user->username,
			'date'=>date('Y-m-d H:i:s'),
			'total'=>$setting->currency_symbol.number_format($total, 2),
			'order_number'=>$order->order_number,
			'shipping_method'=>$shipping_obj->name,
			'shipping_price'=>'$'.$shipping_obj->price,
			'table'=>$html,
		);

		//config email.
		$config = array(
			'mailtype' => 'html',
		);

		$subject = configEmail('sub_order_detai', $params);
		$message = configEmail('order_detai', $params);
		$this->load->library('email', $config);
		$this->email->from(getEmail(config_item('admin_email')), getSiteName(config_item('site_name')));
		$this->email->to($user->email);    
		$this->email->subject($subject);
		$this->email->message($message);   
		$this->email->send();		
	}	
	
	function paymentIpn($payment = '', $id = '')
	{
		if($this->input->post()){
			$itemObj = $this->input->post();
			$get_orderid = $this->db->get_where('dg_orders',array('order_number'=>$itemObj['item_number']))->row();
			if(!empty($get_orderid) && $itemObj['payment_status'] == 'Completed'){			
				$checkPrintful = $this->db->get_where('dg_printfulorder',array('order_id'=>$get_orderid->id))->row();					
				if($checkPrintful->order_status != 'received'){					
					//Sending email to customer
					$this->Orderemail($itemObj['item_number']);
					
					$this->db->where('order_id',$get_orderid->id);
					$this->db->update('dg_printfulorder',array('order_status'=>'received'));
					
					$this->db->where('order_id',$get_orderid->id);
					$this->db->update('dg_order_items',array('poduct_status'=>'completed'));						
					$file = ROOTPATH.DS.'application'.DS.'payments'.DS.$payment.DS.$payment.'.php';			
					if(file_exists($file))
					{
						include_once($file);
						$this->load->model('payment_m');
						$row = $this->payment_m->getData($id);
						if(count($row) > 0)
						{
							$options = json_decode($row->configs, true);	
							$pay = new $payment($options);
							$pay->ipn($this->input->post());
							$this->session->set_flashdata('msg','Payment successfully deducted from your account');	
						}
					}					
				}				
			}			
		}else{
			redirect(site_url());
		}
	}
	
	function confirm()
	{
		$user = $this->session->userdata('user');
		if((isset($user['id']) && $user['id'] != ''))
		{
			if($this->input->post()){
				$paymentObj = $this->input->post();
				$get_orderid = $this->db->get_where('dg_orders',array('order_number'=>$paymentObj['item_number']))->row();
				if(!empty($get_orderid) && $paymentObj['payment_status'] == 'Completed'){
					$checkPrintful = $this->db->get_where('dg_printfulorder',array('order_id'=>$get_orderid->id))->row();					
					if($checkPrintful->order_status != 'received'){						
						//Sending email to customer
						$this->Orderemail($paymentObj['item_number']);
					
						$this->db->where('order_id',$get_orderid->id);
						$this->db->update('dg_printfulorder',array('order_status'=>'received'));
						
						$this->db->where('order_id',$get_orderid->id);
						$this->db->update('dg_order_items',array('poduct_status'=>'completed'));						
						$id = $get_orderid->payment_id;
						// Payment
						$this->load->model('payment_m');
						$row	= $this->payment_m->getData($id);
						$payment_method	= $row->type;
						$file = ROOTPATH .DS. 'application' .DS. 'payments' .DS. $payment_method .DS. $payment_method.'.php';		
						if(file_exists($file))
						{
							include_once($file);
							if(count($row) > 0)
							{
								$options = json_decode($row->configs, true);	
								$pay = new $payment_method($options);
								$pay->ipn($this->input->post());
							}
						}				
					}
					$this->data['msg'] = '<div class="alert alert-success">Payment successfully deducted from your account</div>';	
					$this->session->set_userdata('user'.$user['id'].'tmpsessorder', array('orderid'=>$get_orderid->id,'paymentobj'=>$paymentObj));					
				}
			}else{
					$this->data['msg'] = '<div class="alert alert-danger">You haven\'t purchase any order yet</div>';
			}
			$orderObj	= $this->session->userdata('user'.$user['id'].'tmpsessorder');
			if(isset($orderObj)){
				$this->orderDetail($orderObj['orderid']);				
			}else{
				redirect(site_url());				
			}
		}else{
			redirect(site_url());
		}

	}

	function orderDetail($id = '')
	{
		$this->load->model('users_m');
		$this->user = $this->session->userdata('user');	
		
		// get order detail
		if(!$this->load->is_loaded('order_m')) 
			$this->load->model('order_m');
		
		$order 	= $this->order_m->getOrder($id, false);
		if(count($order) == 0)
			redirect('user/orderhistory');	
		
		// get items
		$data['order'] = $order;
		$items = $this->order_m->getItems($id);
		$data['items'] = $items;
		$data['status_accepted'] = array('draft','pending','failed','onhold','partial');
		// get histories
		$data['histories'] = $this->order_m->getHistory($id);
		
		// get user info
		$userInfo	= $this->order_m->getUserInfo($id);
		if ($userInfo !== false)
		{
			$address	= json_decode($userInfo->address);
		}
		else
		{
			$address	= false;
		}
		$data['address'] = $address;
		
		
		// get shipping method
		$this->load->model('shipping_m');
		$shipping	= $this->shipping_m->get($order->shipping_id, true);
		$data['shipping'] = $shipping;
		
		// get payment method
		$this->load->model('payment_m');
		$payment	= $this->payment_m->get($order->payment_id, true);
		$data['payment'] = $payment;
		
		// get discount
		if ($order->discount_id > 0)
		{
			$this->load->model('coupon_m');
			$discount	= $this->coupon_m->get($order->discount_id, true);
		}
		else
		{
			$discount	= array();
		}
		$data['discount'] = $discount;
		
		$content				= $this->load->view('components/users/order_detail', $data, true);
		
		$this->data['content']	= $content;		
		$this->data['subview'] 	= $this->load->view('layouts/user/profile', array(), true);
		
		$this->data['breadcrumbs'] = array(
			0=>array(
				'title'=>language('my_account', $this->langs),
				'href'=>site_url('user/myaccount')
			),
			1=>array(
				'title'=>language('user_order_history', $this->langs),
				'href'=>site_url('user/orderhistory')
			),
			2=>array(
				'title'=>language('user_order_detail', $this->langs),
				'href'=>'javascript:void(0)'
			)
		);
		
		$this->theme($this->data, 'user');
	}

	function resampleImage($imagePath,$returnfile) {
		$im = new Imagick();
		$im->readImage($imagePath);
		$im->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
		$im->setImageResolution(150,150);
		$im->setImageFormat("png");
		if($im->writeImage('/home/swissmal/public_html/developer/media/assets/system/2016/printful/07/'.$returnfile)){
			return 'media/assets/system/2016/printful/07/'.$returnfile;
		}else{
			return '';
		}
	}
	  
	public function imageMerge($resOriginalImage1,$resOriginalImage2,$image,$position)
	{
		$image_details1 = getimagesize($resOriginalImage1);
		$Original_W1 = $image_details1[0];
		$Original_H1 = $image_details1[1];
		$type1 = $image_details1[2];
		$mime1 = $image_details1['mime'];

		switch ($type1){
			case 1:
				//	GIF
				$source1 = imagecreatefromgif($resOriginalImage1);
				break;
				
			case 2:
				//	JPG
				$source1 = imagecreatefromjpeg($resOriginalImage1);
				break;
				
			case 3:
				//	PNG
				$source1 = imagecreatefrompng($resOriginalImage1);
				break;
		}

		$image_details2 = getimagesize($resOriginalImage2);
		$Original_W2 = $image_details2[0];
		$Original_H2 = $image_details2[1];
		$type2 = $image_details2[2];
		$mime2 = $image_details2['mime'];
		
		switch ($type2){
			case 1:
				//	GIF
				$source2 = imagecreatefromgif($resOriginalImage2);
				break;
				
			case 2:
				//	JPG
				$source2 = imagecreatefromjpeg($resOriginalImage2);
				break;
				
			case 3:
				//	PNG
				$source2 = imagecreatefrompng($resOriginalImage2);
				break;
		}
		
		if($position == 'horizontal'){
			//Set new Image Dimensions
			$newWidth = $Original_W1 + $Original_W2;
			$newHeight = $Original_H1;	
			$dst_x = $Original_W1;
			$dst_y = 0;	
		}elseif($position == 'verticle'){
			$source2 = imagerotate($source2, 180, 0);
			//Set new Image Dimensions
			$newWidth = $Original_W1;
			$newHeight = $Original_H1+$Original_H2;	
			$dst_x = 0;
			$dst_y = $Original_H1;				
		}

		//create holder for new image
		$resResizedImage = imagecreatetruecolor($newWidth, $newHeight);
		imagealphablending($resResizedImage, false);
		imagesavealpha($resResizedImage, true);

		 
		//Copy both images into the new image
		imagecopyresampled($resResizedImage, $source1, 0, 0, 0, 0, $Original_W1, $Original_H1, $Original_W1, $Original_H1);
		imagecopyresampled($resResizedImage, $source2, $dst_x, $dst_y, 0, 0, $Original_W2, $Original_H2, $Original_W2, $Original_H2);

		//Save the New PNG File.
		imagepng($resResizedImage,"/home/swissmal/public_html/developer/media/assets/system/2016/printful/07/$image.png");
		return "media/assets/system/2016/printful/07/$image.png";
	}	
}
?>