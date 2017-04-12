<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cart extends Frontend_Controller {
	
	public $keys;
	public function __construct()
	{
        parent::__construct();	
		//$this->lang->load('cart');
		
		$this->load->model('layout_m');
		
		if(!$this->load->is_loaded('cache')) 
			$this->load->driver('cache', array('adapter'=>'file')); 		
		
		if ($this->session->userdata('order_session_id'))
		{
			$this->session_id = $this->session->userdata('order_session_id');
		}
		else
		{
			$this->session_id = $this->session->userdata('session_id');
			$this->session->set_userdata('order_session_id', $this->session_id);
		}
		$this->keys = array('price','qty','teams','cliparts','cstmcliparts','uploadimages','texts','time','options','productsize','productclr','printfulCode');		
    }
	
	function index()
	{
		
		$this->data['designs'] 	= $this->cache->get('orders_designs'.$this->session_id);
		
		$this->data['items'] 	=  $this->cart->contents();
		$this->data['user'] 	= $this->session->userdata('user');		
		
		$content				= $this->load->view('components/cart/index', $this->data, true);
		
		$data = array();		
		$data['content']	= $content;		
		$data['subview'] 	= $this->load->view('layouts/cart/cart', array(), true);
		
		$lang = getLanguages();
		
		$data['breadcrumbs'] = array(
			0=>array(
				'title'=>language('cart', $lang),
				'href'=>'javascript:void(0)'
			)
		);
		$layout = $this->layout_m->getDesignLayout('cart/cart');
		
		if(!empty($layout)){
			$data['title']				=  $layout->title;		
			$data['meta_keywords']		=  $layout->meta_keywords;		
			$data['meta_description']	=  $layout->meta_description;			
		}	
		$data['meta_robots']		=  $layout->meta_robots;
		
		$this->theme($data, 'cart');
	}


	public function confirmShipping(){
		$user = $this->session->userdata('user');

		if (empty($user['loggedin']) || $user['loggedin'] == 0)
		{
			redirect('user/login');
		}

		$this->data['designs'] 	= $response = $this->cache->get('orders_designs'.$this->session_id);

		$this->data['items'] 	= $this->cart->contents();		
		$this->data['user'] 	= $user;	
		
		if ( count($this->data['items']) == 0)
			redirect('cart');

		// user info		
		$this->load->model('fields_m');
		$profiles	= $this->fields_m->getFiels('checkout', $user['id']);
		$this->data['profiles']	= $profiles;
			
		// get from
		$this->load->model('users_m');
		$this->data['forms'] = $this->users_m->getFormField('checkout');
		$this->load->helper('fields');
		$fields	= new Field();
		$this->data['fields']	= $fields;
		

		// Get Shipping if included in session
		
		$cart	  = $this->session->userdata('cart');
		$currCart = $this->cart->contents();
		if(!empty($currCart)){
			$curritem = array();
			$currsubtotal = 0;
			foreach($currCart as $k_cart=>$v_cart){
				$curritem[$v_cart['product_id']]    = $v_cart['qty'];
				$currsubtotal += $v_cart['subtotal'];
			}
		}		
		$shipping_list = '';
		if($cart != '' && $currsubtotal == $cart->cart->subtotal && empty(array_diff_assoc($cart->cart->item,$curritem))){
			$shipping_list = $this->session->userdata('session_shipping');
		}
		$this->data['shipping_lists'] = $shipping_list; 
		
		$content				= $this->load->view('components/cart/shippingconfirm', $this->data, true);
		
		
		$data = array();		
		$data['content']	= $content;		
		$data['subview'] 	= $this->load->view('layouts/cart/confirmShipping', array(), true);
		
		$lang = getLanguages();
		
		$data['breadcrumbs'] = array(
			0=>array(
				'title'=>language('cart', $lang),
				'href'=>site_url('cart')
			),
			1=>array(
				'title'=>'Shipping',
				'href'=>'javascript:void(0)'
			)
		);
		
		$layout = $this->layout_m->getDesignLayout('cart/confirmShipping');
		
		if(!empty($layout)){
			$data['title']				=  $layout->title;		
			$data['meta_keywords']		=  $layout->meta_keywords;		
			$data['meta_description']	=  $layout->meta_description;			
		}		
		$data['meta_robots']			=  $layout->meta_robots;
		
		$this->theme($data, 'cart');		
	}	
	
	
	public function checkout()
	{
		$user = $this->session->userdata('user');
		
		if (empty($user['loggedin']) || $user['loggedin'] == 0)
		{
			redirect('user/login');
		}

	
		$this->data['designs'] 	= $this->cache->get('orders_designs'.$this->session_id);

		
		$cart	  = $this->session->userdata('cart');
		$currCart = $this->cart->contents();
		if(!empty($currCart)){
			$curritem = array();
			$currsubtotal = 0;
			foreach($currCart as $k_cart=>$v_cart){
				$curritem[$v_cart['product_id']]    = $v_cart['qty'];
				$currsubtotal += $v_cart['subtotal'];
			}
		}
		

		if($cart == '' || ($currsubtotal != $cart->cart->subtotal && !empty(array_diff_assoc($cart->cart->item,$curritem)))){
			if($cart == ''){
				$this->session->set_flashdata('message', '<p class="text-danger">Please select your shipping method.</p>');				
			}else{
				$this->session->set_flashdata('message', '<p class="text-danger">There are changes occur in your order, So please update your shipping </p>');
			}
			redirect(base_url('cart/confirmShipping'));
		}
				
		$this->data['items'] 	= $this->cart->contents();		
		$this->data['user'] 	= $user;
		
		if ( count($this->data['items']) == 0)
			redirect('cart');

		// user info		
		$this->load->model('fields_m');
		$profiles	= $this->fields_m->getFiels('checkout', $user['id']);
		$this->data['profiles']	= $profiles;
			
		// get from
		$this->load->model('users_m');
		$this->data['forms'] = $userform = $this->users_m->getFormField('checkout');
		$this->load->helper('fields');
		$fields	= new Field();
		$this->data['fields']	= $fields;
		
		if(count($userform) > 0){
			foreach($userform as $k_r_userform=>$r_userform){
				if($r_userform->type == 'country'){
					$getCountryObj = $this->db->get_where('dg_country',array('id'=>$profiles[$r_userform->id]))->row();
					$valuedisp = $getCountryObj->name;
					$value = $profiles[$r_userform->id];
				}elseif($r_userform->type == 'state'){
					$getStateObj   = $this->db->get_where('dg_states',array('id'=>$profiles[$r_userform->id]))->row();						
					$valuedisp = $getStateObj->name;
					$value = $profiles[$r_userform->id];
				}else{					
					$valuedisp = $value = $profiles[$r_userform->id];
				}
				$userAddress[] = array('label'=>$r_userform->title,'valuedisplay'=>$valuedisp,'value'=>$value,'name'=>'fields['.$r_userform->name.']['.$r_userform->id.']');
			}						
		}
		
		$this->load->model('shipping_m');
		$this->shipping_m->db->where('published', 1);
		$shipping 					= $this->shipping_m->get();
		$this->data['shipping'] 	= $shipping;
		
		if ($this->session->userdata('shipping') === false){
		}
		
		// load payment method
		$this->load->model('payment_m');
		$this->payment_m->db->where('published', 1);
		$payments 					= $this->payment_m->get();
		$this->data['payments'] 	= $payments;
		$this->data['formfields'] 	= $userAddress;	
		$this->data['cartShipping'] = $cart->shipping;	
		
		$content			= $this->load->view('components/cart/checkout', $this->data, true);
		
		$data = array();		
		$data['content']	= $content;		
		$data['subview'] 	= $this->load->view('layouts/cart/checkout', array(), true);
		
		$lang = getLanguages();
		
		$data['breadcrumbs'] = array(
			0=>array(
				'title'=>language('cart', $lang),
				'href'=>site_url('cart/checkout')
			),
			1=>array(
				'title'=>language('checkout', $lang),
				'href'=>'javascript:void(0)'
			)
		);
		
		$layout = $this->layout_m->getDesignLayout('cart/checkout');
		
		if(!empty($layout)){
			$data['title']				=  $layout->title;		
			$data['meta_keywords']		=  $layout->meta_keywords;		
			$data['meta_description']	=  $layout->meta_description;			
		}	
		$data['meta_robots']		=  $layout->meta_robots;
		
		$this->theme($data, 'cart');
	}	
	
	// add to cart in designer
	public function addJs()
	{
		header('Content-Type: text/html; charset=UTF-8');
		$data = file_get_contents('php://input');
		$data = json_decode($data, true);
		$lang = getLanguages();
		
		// get data post
		$product_id		= $data['product_id'];
		$colors			= $data['colors'];
		$print			= $data['print'];		
		$quantity		= $data['quantity'];		
		
		// get attribute
		if ( isset( $data['attribute'] ) )
		{
			$attribute		= $data['attribute'];
		}
		else
		{
			$attribute		= false;
		}
				
		if ($quantity < 1 ) $quantity = 1;
		
		$time = strtotime("now");
		
		if (isset($data['attribute'])) $attribute = $data['attribute'];
		else $attribute = false;
		
		if (isset($data['cliparts'])) $cliparts = $data['cliparts'];
		else $cliparts = false;			
		
		$content = array();
		$content['error'] = 1;
		$this->load->model('product_m');
			
		// check product and user shop
		$options = array(
			'id' => $data['product_id']				
		);
		$product 		= $this->product_m->getProduct($options);
		if ($product == false)
		{
			$content['msg'] = language('data_not_found', $lang);
		}
		else
		{
			$product 		= $product[0];
			$content['error'] = 0;
			$this->load->helper('cart');
			$cart 		= new dgCart();
			
			$post 		= array(
				'colors' 		=> $colors,
				'print' 		=> $print,
				'attribute' 	=> $attribute,
				'quantity' 		=> $quantity,
				'product_id' 	=> $product_id,
			);
			
			// load setting
			$this->load->model('settings_m');
			$row 			= $this->settings_m->getSetting();			
			$setting		= json_decode($row->settings);
			$result 		= $cart->totalPrice($this->product_m, $product, $post, $setting);
			
			if($product->printfulprcdepend == 1){
				$getAttributes = $this->db->get_where('dg_sizeattributes',array('product_id'=>$product_id))->row();
				if(!empty($getAttributes)){
					$key = array_search($data['design']['size'],json_decode($getAttributes->size));
					$arrPrice = json_decode($getAttributes->price);
					if(!empty($arrPrice)){
						$result->price->base = $arrPrice[$key];
						$result->price->sale = $arrPrice[$key];							
					}
				}		
			}

			$result->product		= new stdClass();
			$result->product->name 	= addslashes($product->title);
			$result->product->sku 	= $product->sku;
			
			// get cliparts
			$clipartsPrice = array();			
			if ( isset($data['cliparts']) )
			{
				$this->load->model('art_m');
				
				$cliparts = $data['cliparts'];
				foreach($cliparts as $view => $arts)
				{
					if (count($arts))
					{
						$art = array();
						foreach($arts as $art_id)
						{
							// check admin shop and desginer
							$clipart 		= $this->art_m->getArt($art_id, 'system, add_price');
							
							if ( empty($clipart) ) continue;
							if ($clipart->add_price == 0) continue;
							
							$prices 		= $clipart->add_price;
							$art[$art_id] 	= $prices;							
						}
						$clipartsPrice[$view] = $art;
					}
				}
			}
			
			$result->cliparts = $clipartsPrice;							
				
			$total		 = new stdClass();

			$total->old  = $result->price->base + $result->price->colors;
			$total->sale = $result->price->sale + $result->price->colors;			

			if (count($result->cliparts))
			{
				foreach($result->cliparts as $view=>$art)
				{
					foreach($art as $id=>$amount)
					{
						$total->old 	= $total->old + $amount;
						$total->sale 	= $total->sale + $amount;
					}
				}
			}			

			// Get custom clipart price			
			$cstmclipartsPrice = array();			
			if (isset($data['Customcliparts'])){
				$this->load->model('designimage_m');				
				$customcliparts = $data['Customcliparts'];
				foreach($customcliparts as $view => $customarts)
				{					
					if (count($customarts))
					{
						$designart = array();
						foreach($customarts as $art_id)
						{
							// check admin shop and desginer
							$clipart 		= $this->designimage_m->getArt($art_id);							
							if ( empty($clipart->price) ) continue;
							if ($clipart->price == 0) continue;							
							$prices 				= $clipart->price;
							$designart[$art_id] 	= $prices;							
						}
						$cstmclipartsPrice[$view] = $designart;
					}
				}
			}	
			
			$result->cstmcliparts = $cstmclipartsPrice;			
			if (count($result->cstmcliparts))
			{
				foreach($result->cstmcliparts as $view=>$cstmart)
				{
					foreach($cstmart as $id=>$amount)
					{
						$total->old 	= $total->old + $amount;
						$total->sale 	= $total->sale + $amount;
					}
				}
			}
			
			//$settings = $this->settings_m->getSetting();
			//$settingObj = json_decode($settings->settings);
			$result->price->texts = 0;
			if(isset($data['texts'])){	
				foreach($data['texts'] as $texts){	
					foreach($texts as $text){
						$total->old = $total->old + $setting->text_price;
						$total->sale = $total->sale + $setting->text_price;	
						$result->price->texts = $result->price->texts + $setting->text_price;				
					}					
				}								
			}			
			
			$result->price->images = 0;
			$result->uploadimages = array();
			if (isset($data['images'])){
				foreach($data['images'] as $view => $images){	
					$uploadimg = array();
					foreach($images as $upld_key=>$image){
						$total->old 	= $total->old + $setting->upload_image_price;
						$total->sale 	= $total->sale + $setting->upload_image_price;
						$result->price->images = $result->price->images + $setting->upload_image_price;
						$uploadimg[$image] = $setting->upload_image_price;
					}
					$result->uploadimages[$view] =  $uploadimg;
				}
			}			
			
			$result->total 	= $total;
			
			// get symbol
			if (!isset($setting->currency_symbol))
				$setting->currency_symbol = '$';
			$result->symbol = $setting->currency_symbol;
			// save file image design
			$design = array();
			$getPrintfulId = $this->db->get_where('dg_products',array('id'=>$data['product_id']))->row();

			$get_products_design = $this->db->get_where('dg_products_design',array('product_id'=>$product_id))->row();
			if(!empty($get_products_design)){
				$clrArr = json_decode($get_products_design->color_hex);
				$clrkey = array_search($data['design']['prdcolor'],$clrArr);
			}	
			
			$nPrice = $result->total->sale; 
			
			if (isset($data['design']['images']['front'])){
				$design['images']['front'] 		= createFile($data['design']['images']['front'], 'front', $time);
				if($data['design']['images']['canvfrontExists'] == true || $getPrintfulId->printful_printfile == 'single'){
					$design['images']['canvfront'] 	= createFile($data['design']['images']['canvfront'], 'canvfront', $time);	
				}
				
				if($data['design']['images']['canvfrontExists'] == true){
					if(!empty($get_products_design) && !empty($get_products_design->frontprice)){
						$priceArr1 = json_decode($get_products_design->frontprice);
						$getPrice =  $priceArr1[$clrkey];
						$nPrice+= $getPrice;
					} 					
				}
			}
			
			if (isset($data['design']['images']['back'])){
				$design['images']['back'] 		= createFile($data['design']['images']['back'], 'back', $time);
				if($data['design']['images']['canvbackExists'] == true || $getPrintfulId->printful_printfile == 'single'){
					$design['images']['canvback']	= createFile($data['design']['images']['canvback'], 'canvback', $time);
				}

				if(($data['design']['images']['canvbackExists'] == true) && ($data['design']['images']['canvfrontExists'] == false)){
					if(!empty($get_products_design) && !empty($get_products_design->backprice)){
						$priceArr2 = json_decode($get_products_design->backprice);
						$getPrice =  $priceArr2[$clrkey];
						if($data['design']['images']['canvfrontExists'] == false){
							$nPrice+= $getPrice;
						}
					} 					
				}				
			}
			
			if (isset($data['design']['images']['left'])){
				$design['images']['left'] 		= createFile($data['design']['images']['left'], 'left', $time);
				if($data['design']['images']['canvleftExists'] == true){
					$design['images']['canvleft']	= createFile($data['design']['images']['canvleft'], 'canvleft', $time);					
				}					
				
				if($data['design']['images']['canvleftExists'] == true){
					if(!empty($get_products_design) && !empty($get_products_design->leftprice)){
						$priceArr3 = json_decode($get_products_design->leftprice);
						$getPrice =  $priceArr3[$clrkey];
						$nPrice+= $getPrice;
					} 					
				}				
			}
			
			if (isset($data['design']['images']['right'])){
				$design['images']['right']		= createFile($data['design']['images']['right'], 'right', $time);
				if($data['design']['images']['canvrightExists'] == true){
					$design['images']['canvright']	= createFile($data['design']['images']['canvright'], 'canvright', $time);
				}

				if($data['design']['images']['canvrightExists'] == true){
					if(!empty($get_products_design) && !empty($get_products_design->rightprice)){
						$priceArr4 = json_decode($get_products_design->rightprice);
						$getPrice =  $priceArr4[$clrkey];
						$nPrice+= $getPrice;
					} 					
				}				
			}  

			if (empty($result->options)) $result->options = array();
			
			if (isset($data['teams'])) $teams = $data['teams'];
			else $teams = '';

			$design_cache = array(
								'color' => $data['colors'][key($data['colors'])],
								'images' => $design['images'],
								'vector' => $data['design']['vectors'],
								'fonts' => $data['fonts'],
								'product_size'=>$data['design']['size'],
								'product_color'=>$data['design']['prdcolor'],
								'product_price'=>$nPrice,
								'qty'=> $data['quantity'],								
							);
							
			$user = $this->session->userdata('user');
			if(!empty($user)){
				$user_id = $user['id'];
			}else{
				$user_id = 0;
			}							
			$dataitem_cache = array(
								'color' => $data['colors'][key($data['colors'])],
								'vector' => $data['design']['vectors'],
								'oldPrice'=>$total->old,
								'salePrice'=>$total->sale,
								'quantity'=>$data['quantity'],
								'product_id'=>$data['product_id'],
								'fonts' => $data['fonts'],
								'product_size'=>$data['design']['size'],
								'product_color'=>$data['design']['prdcolor'],
								'product_price'=>$nPrice,
								'user_id'=>$user_id,
								'teams'=>$teams,
							);	
							

			$designs 		= $this->cache->get('orders_designs'.$this->session_id);
			$designItem 	= $this->cache->get('cart_items'.$this->session_id);

			$item 	= array(
				'id'      		=> $result->product->sku,
				'product_id'    => $data['product_id'],
				'price'   		=> $nPrice,
				'qty'     		=> $data['quantity'],
				'teams'     	=> $teams,				
				'prices'   		=> json_encode($result->price),
				'cliparts'   	=> json_encode($result->cliparts),
				'cstmcliparts' 	=> json_encode($result->cstmcliparts),	
				'uploadimages' =>  json_encode($result->uploadimages),
				'texts'         => $result->price->texts,
				'symbol'   		=> $result->symbol,
				'customPrice'   => $result->price->attribute,
				'name'    		=> $result->product->name,
				'time'    		=> $time,
				'options' 		=> json_decode(json_encode($result->options), true),
				'printfulId'    => $product->printful_productid,
				'productsize'   => $data['design']['size'],
				'productclr'    => $data['design']['prdcolor'], 
				'printfulCode'  => $product->printfulClrCode,
			);		


			if(($data['design_id'] != '') && (strpos($data['design_id'],'cart-id-') !== false)){
				
				$exp = explode('cart-id-',$data['design_id']);
				$item['rowid'] 	  = $exp[1];
				$this->load->library('cart');					
				$this->cart->product_name_rules = "\d\D";	
				$this->cart->update_all($item,$this->keys); 
				
				$designs[$exp[1]]	= $design_cache;
				$this->cache->save('orders_designs'.$this->session_id, $designs, 36000);

				$designItem[$exp[1]]	= $dataitem_cache;			
				$this->cache->save('cart_items'.$this->session_id, $designItem, 36000);					

				$cartRowId = $exp[1];				
				$msg = 'Item Successfully Inserted into cart';	
				
			}else{
				
				$this->cart->product_name_rules = "\d\D";				
				$cartRowId = $this->cart->insert($item);
				
				// add session design
				$rowid			= md5($result->product->sku . $time);				
				$designs[$rowid]	= $design_cache;				
				$this->cache->save('orders_designs'.$this->session_id, $designs, 36000);
	
				$designItem[$cartRowId]	= $dataitem_cache;			
				$this->cache->save('cart_items'.$this->session_id, $designItem, 36000);	
				$msg = 'Item Successfully Updated';
			}
			
			$content['product'] = array(
				'name'=> $result->product->name,
				'quantity'=> $data['quantity'],
				'image'=> base_url().$design['images']['front'],
				'cartItemId'=>$cartRowId,
				'cartmsg'=>$msg,
			);
		}
		
		echo json_encode($content);
	}
	
	public function prices()
	{
		$data 	= $this->input->post();
		
		// get data post
		$product_id		= $data['product_id'];
		$colors			= $data['colors'];
		$print			= $data['print'];		
		$quantity		= $data['quantity'];		
		
		// get attribute
		if ( isset( $data['attribute'] ) )
		{
			$attribute		= $data['attribute'];
		}
		else
		{
			$attribute		= false;
		}
				
		if ($quantity < 1 ) $quantity = 1;
		
		// load product
		$this->load->model('product_m');					
		$options = array(
			'id' => $product_id				
		);
		$product 		= $this->product_m->getProduct($options);
		
		$lang = getLanguages();
		
		if ($product == false)
		{
			echo json_encode( array('error' => language('data_not_found', $lang)) );
			exit;
		}
		else
		{
			$product 		= $product[0];
			// load cart
			$this->load->helper('cart');
			$cart 		= new dgCart();	
			$post 		= array(
				'colors' 		=> $colors,
				'print' 		=> $print,
				'attribute' 	=> $attribute,
				'quantity' 		=> $quantity,
				'product_id' 	=> $product_id					
			);
			
			// load setting
			$this->load->model('settings_m');
			$row 			= $this->settings_m->getSetting();			
			$setting		= json_decode($row->settings);
			$result 		= $cart->totalPrice($this->product_m, $product, $post, $setting);
			
			if($product->printfulprcdepend == 1){
				$getAttributes = $this->db->get_where('dg_sizeattributes',array('product_id'=>$product_id))->row();
				if(!empty($getAttributes)){
					$key = array_search($data['size'],json_decode($getAttributes->size));
					$arrPrice = json_decode($getAttributes->price);
					if(!empty($arrPrice)){
						$result->price->base = $arrPrice[$key];
						$result->price->sale = $arrPrice[$key];							
					}
				}		
			}			
			
			// get cliparts
			$clipartsPrice = array();			
			if (isset($data['cliparts'])){
				$this->load->model('art_m');				
				$cliparts = $data['cliparts'];
				foreach($cliparts as $view => $arts)
				{					
					if (count($arts))
					{
						$art = array();
						foreach($arts as $art_id)
						{
							// check admin shop and desginer
							$clipart 		= $this->art_m->getArt($art_id, 'system, add_price');							
							if ( empty($clipart->add_price) ) continue;
							if ($clipart->add_price == 0) continue;							
							$prices 		= $clipart->add_price;
							$art[][$art_id] 	= $prices;							
						}
						$clipartsPrice[$view] = $art;
					}
				}
			}			
			$result->cliparts = $clipartsPrice;
			$result->quantity = $quantity;			
			
			$total	= new stdClass();
			
			$total->old = $result->price->base + $result->price->colors;
			$total->sale = $result->price->sale + $result->price->colors;			
			
			if (count($result->cliparts))
			{
				foreach($result->cliparts as $view=>$art)
				{
					foreach($art as $id=>$amount)
					{
						$newamnt = array_shift(array_values($amount)); 
						$total->old 	= $total->old + $newamnt;
						$total->sale 	= $total->sale + $newamnt;
					}
				}
			}
			
			// Get custom clipart price			
			$cstmclipartsPrice = array();			
			if (isset($data['Customcliparts'])){
				$this->load->model('designimage_m');				
				$customcliparts = $data['Customcliparts'];
				foreach($customcliparts as $view => $customarts)
				{					
					if (count($customarts))
					{
						$designart = array();
						foreach($customarts as $art_id)
						{
							// check admin shop and desginer
							$clipart 		= $this->designimage_m->getArt($art_id);							
							if ( empty($clipart->price) ) continue;
							if ($clipart->price == 0) continue;							
							$prices 		= $clipart->price;
							$designart[][$art_id] 	= $prices;							
						}
						$cstmclipartsPrice[$view] = $designart;
					}
				}
			}			
			$result->cstmcliparts = $cstmclipartsPrice;
			
			if (count($result->cstmcliparts))
			{
				foreach($result->cstmcliparts as $view=>$cstmart)
				{
					foreach($cstmart as $id=>$amount)
					{
						$newamnt = array_shift(array_values($amount)); 
						$total->old 	= $total->old + $newamnt;
						$total->sale 	= $total->sale + $newamnt;
					}
				}
			}
			
			$this->load->model('settings_m');		
			$settings = $this->settings_m->getSetting();
			$settingObj = json_decode($settings->settings);
			if(isset($data['texts'])){	
				foreach($data['texts'] as $texts1){	
					foreach($texts1 as $text2){
						$total->old = $total->old + $settingObj->text_price;
						$total->sale = $total->sale + $settingObj->text_price;		
					}					
				}								
			}
			
			if (isset($data['images'])){
				foreach($data['images'] as $view => $images1){	
					foreach($images1 as $images2){
						$total->old 	= $total->old + $settingObj->upload_image_price;
						$total->sale 	= $total->sale + $settingObj->upload_image_price;
					}
				}
			}			
			
			
			$total->old 	= ($total->old * $quantity);
			$total->sale 	= ($total->sale * $quantity);
			
			
			$total->old 	= number_format($total->old, 2, '.', ',');
			$total->sale 	= number_format($total->sale, 2, '.', ',');
			
			echo json_encode($total);
			exit;
		}	  
	}
	
	public function designPrice(){	
		$Designid = $this->input->post('designId');
		$data 	= $this->input->post();
		$total	= new stdClass();
		if(strpos($Designid,'cart-id-') !== false){			
			$exp = explode('cart-id-',$Designid);
			$cartObj = $this->cache->get('cart_items'.$this->session_id);	
			if(!empty($cartObj) && array_key_exists($exp[1],$cartObj)){			
				$cart = $cartObj[$exp[1]];	
				$total->old 					= $cart['oldPrice'];
				$total->sale 					= $cart['salePrice'];			
				$total->selected 				= $cart['product_size'];
				$total->color 				    = $cart['color'];			
				if($cart['quantity'] < 0){
					$total->quantity 				= $quantity = 1 ;					
				}else{
					$total->quantity 				= $quantity = $cart['quantity'] ;				
				}	
			}else{
				$total->old 					= '';
				$total->sale 					= '';			
				$total->selected 				= '';
				$total->color 				    = '';	
				$total->quantity 	            = 1;
			}			
		}else{		
			$this->load->model('design_m');			
			$options = array(
				'design_id'=> $Designid
			);			
			$design = $this->design_m->getDesign($options);	
			if(!empty($design)){
				$total	= new stdClass();
				$total->old 					= $design->oldPrice;
				$total->sale 					= $design->salePrice;			
				$total->selected 				= $design->productsize;
				$total->color 				    = $design->product_options;				
				if($design->quantity < 0){
					$total->quantity 				= $quantity = 1 ;					
				}else{
					$total->quantity 				= $quantity = $design->quantity;				
				}				
			}else{
				$total->old 					= '';
				$total->sale 					= '';			
				$total->selected 				= '';
				$total->color 				    = '';	
				$total->quantity 	            = 1;				
			}				
		}
		
		// get data post
		$product_id		= $data['product_id'];
		$colors			= $data['colors'];
		$print			= $data['print'];	
		if(isset($data['quantity']) && $data['quantity'] > 0){
			$quantity		= $data['quantity'];			
		}	
		
		// get attribute
		if ( isset( $data['attribute'] ) )
		{
			$attribute		= $data['attribute'];
		}
		else
		{
			$attribute		= false;
		}
				
		//if ($quantity < 1 ) $quantity = 1;
		
		// load product
		$this->load->model('product_m');					
		$options = array(
			'id' => $product_id				
		);
		$product 		= $this->product_m->getProduct($options);
		
		$lang = getLanguages();
		
		if ($product == false)
		{
			echo json_encode( array('error' => language('data_not_found', $lang)) );
			exit;
		}
		else
		{
			$product 		= $product[0];
			// load cart
			$this->load->helper('cart');
			$cart 		= new dgCart();	
			$post 		= array(
				'colors' 		=> $colors,
				'print' 		=> $print,
				'attribute' 	=> $attribute,
				'quantity' 		=> $quantity,
				'product_id' 	=> $product_id					
			);
			
			// load setting
			$this->load->model('settings_m');
			$row 		= $this->settings_m->getSetting();			
			$setting	= json_decode($row->settings);
			$result 	= $cart->totalPrice($this->product_m, $product, $post, $setting);
			
			if(($data['request'] > 0) && array_key_exists('size',$data) && $product->printfulprcdepend == 1){				
				$getAttributes = $this->db->get_where('dg_sizeattributes',array('product_id'=>$product_id))->row();
				if(!empty($getAttributes)){
					$key = array_search($data['size'],json_decode($getAttributes->size));
					$arrPrice = json_decode($getAttributes->price);
					if(!empty($arrPrice)){
						$total->old = $arrPrice[$key];
						$total->sale = $arrPrice[$key];							
					}  
				}		
			}			
			  
			// get cliparts
			$clipartsPrice = array();			
			if (isset($data['cliparts'])){
				$this->load->model('art_m');				
				$cliparts = $data['cliparts'];
				foreach($cliparts as $view => $arts)
				{					
					if (count($arts))
					{
						$art = array();
						foreach($arts as $art_id)
						{
							// check admin shop and desginer
							$clipart 		= $this->art_m->getArt($art_id, 'system, add_price');							
							if ( empty($clipart->add_price) ) continue;
							if ($clipart->add_price == 0) continue;							
							$prices 		= $clipart->add_price;
							$art[][$art_id] 	= $prices;							
						}
						$clipartsPrice[$view] = $art;
					}
				}
			}			
			$result->cliparts = $clipartsPrice;
			$result->quantity = $quantity;			
			
		
			if (count($result->cliparts))
			{
				foreach($result->cliparts as $view=>$art)
				{
					foreach($art as $id=>$amount)
					{
						$newamnt = array_shift(array_values($amount)); 
						$total->old 	= $total->old + $newamnt;
						$total->sale 	= $total->sale + $newamnt;
					}
				}
			}

			// Get custom clipart price			
			$cstmclipartsPrice = array();			
			if (isset($data['Customcliparts'])){
				$this->load->model('designimage_m');				
				$customcliparts = $data['Customcliparts'];
				foreach($customcliparts as $view => $customarts)
				{					
					if (count($customarts))
					{
						$designart = array();
						foreach($customarts as $art_id)
						{
							// check admin shop and desginer
							$clipart 		= $this->designimage_m->getArt($art_id);							
							if ( empty($clipart->price) ) continue;
							if ($clipart->price == 0) continue;							
							$prices 		= $clipart->price;
							$designart[][$art_id] 	= $prices;							
						}
						$cstmclipartsPrice[$view] = $designart;
					}
				}
			}			
			$result->cstmcliparts = $cstmclipartsPrice;
			
			if (count($result->cstmcliparts))
			{
				foreach($result->cstmcliparts as $view=>$cstmart)
				{
					foreach($cstmart as $id=>$amount)
					{
						$newamnt = array_shift(array_values($amount)); 
						$total->old 	= $total->old + $newamnt;
						$total->sale 	= $total->sale + $newamnt;
					}
				}
			}
			
			
			$this->load->model('settings_m');		
			$settings = $this->settings_m->getSetting();
			$settingObj = json_decode($settings->settings);
			if(isset($data['texts'])){	
				foreach($data['texts'] as $texts1){	
					foreach($texts1 as $text2){
						$total->old = $total->old + $settingObj->text_price;
						$total->sale = $total->sale + $settingObj->text_price;		
					}					
				}								
			}
			
			if (isset($data['images'])){
				foreach($data['images'] as $view => $images1){	
					foreach($images1 as $images2){
						$total->old 	= $total->old + $settingObj->upload_image_price;
						$total->sale 	= $total->sale + $settingObj->upload_image_price;
					}
				}
			}			
						
			$total->old 	= ($total->old * $quantity);
			$total->sale 	= ($total->sale * $quantity);			
			
			$total->old 		= number_format($total->old, 2, '.', ',');
			$total->sale 		= number_format($total->sale, 2, '.', ',');

			echo json_encode($total);
			exit;
		}		
	}
	
	public function shipping($id = '')
	{
		$id	= (int) $id;
		
		$this->load->model('shipping_m');		
		$shipping 					= $this->shipping_m->get($id, true);		
		
		if ($this->session->userdata('cart') === false)
		{
			$cart 					= new stdClass();
			$cart->shipping			= new stdClass();
			
		}
		else
		{
			$cart	= $this->session->userdata('cart');
			if (empty($cart->shipping))
				$cart->shipping		= new stdClass();
		}
		$cart->shipping->id 	= $id;
		$cart->shipping->price 	= $shipping->price;
		$this->session->set_userdata('cart', $cart);
				
		$this->data['designs'] 	= $this->cache->get('orders_designs'.$this->session_id);
		$this->data['items'] 	= $this->cart->contents();
		
		$this->load->view('components/cart/items', $this->data);
	}
	
	// get coupon
	public function coupon($code = '')
	{		
		$this->load->model('coupon_m');
		$this->coupon_m->db->where('code', $code);
		$this->coupon_m->db->where('publish', 1);		
		$this->coupon_m->db->where('end_date > Now()');
		
		$coupon 				= $this->coupon_m->get();
		if ($this->session->userdata('cart') === false)
		{
			$cart 					= new stdClass();
			$cart->discount			= new stdClass();
		}
		else
		{
			$cart	= $this->session->userdata('cart');
			if (empty($cart->discount))
				$cart->discount		= new stdClass();
		}			
		
		$discount		= true;
		if ( count($coupon) == 0)
		{
			$discount	= false;
		}
		else
		{
			if ($coupon[0]->coupon_type == 'g' && $coupon[0]->count != 0)
			{
				$discount	= false;
			}
			
			// check min total discount
			$total 	= $this->cart->total();
			if ($coupon[0]->minimum > $total)
			{
				$discount	= false;
			}
		}
		
		if ($discount === true)
		{
			$cart->discount->id 			= $coupon[0]->id;
			$cart->discount->type 			= $coupon[0]->coupon_type;
			$cart->discount->discount_type 	= $coupon[0]->discount_type;
			$cart->discount->value 			= $coupon[0]->value;
			$cart->discount->code 			= $coupon[0]->code;
		}
		else
		{
			$cart->discount		= new stdClass();
		}
		$this->session->set_userdata('cart', $cart);
				
		$this->data['designs'] 	= $this->cache->get('orders_designs'.$this->session_id);
		$this->data['items'] 	= $this->cart->contents();
		
		$this->load->view('components/cart/items', $this->data);
	}
	
	function update(){
		$data = $this->input->post();
		$return = false;
		if(!empty($data)){
			$cart = array();
			foreach($data['cart'] as $key=>$cart_row){
				$cart[] = array('rowid'=>$key,'qty'=>$cart_row['qty']);
			}
			if(!empty($cart) && ($this->cart->update($cart))){
				$return =  true;
			}		
		}   
		if($return){
			$this->session->set_flashdata('message', '<p class="text-success">Shopping Cart Updated Successfully</p>');				
		}else{
			$this->session->set_flashdata('message', '<p class="text-danger">Error: Please check items in your shopping cart</p>');
		}			
		echo $return;
	}
	
	function remove($rowid = '')
	{
		if ($rowid != '')
		{
			$data = array(
				'rowid' => $rowid,
				'qty' => '0'
			);
			$this->cart->update($data);
			
			$designs 		= $this->cache->get('orders_designs'.$this->session_id);
			unset($designs[$rowid]);
			$this->cache->save('orders_designs'.$this->session_id, $designs, 86400);
		}
	}
	
	function destroy()
	{
		$this->cart->destroy();
		if ($this->cache->get('orders_designs'.$this->session_id))
			$this->cache->delete('orders_designs'.$this->session_id);
		
		if ($this->cache->get('cart_items'.$this->session_id))
			$this->cache->delete('cart_items'.$this->session_id);	

		$this->session->set_flashdata('message', '<p class="text-success">Shopping Cart Empty Successfully</p>');				
	}
}

?>