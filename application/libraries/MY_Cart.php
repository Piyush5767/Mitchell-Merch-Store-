<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Cart extends CI_Cart{
	
  public $CI;
  public $keys;
  function __construct($params = array())
  {
    parent::__construct($params);
    $this->CI =& get_instance();
  } 
	
	public function update_all($items = array(),$keys = array()){		
        // Was any cart data passed?
        if ( ! is_array($items) OR count($items) == 0)
        {
            return false;
        }
		
		$this->keys = $keys;

        if (isset($items['rowid']))
        {
			if ($this->_update_item($items) == TRUE)
			{
				$save_cart = TRUE;
			}
        }
        else
        {
            foreach($items as $item)
            {                
				if (is_array($item) AND isset($item['rowid']) AND isset($item['qty']))
				{
					if ($this->_update_item($item) == TRUE)
					{
						$save_cart = TRUE;
					}
				}				
            }
        }

		if ($save_cart == TRUE)
		{
			$this->_save_cart();
			return TRUE;
		}
		
		return FALSE;	
	}


	public function _update_item($items = array()){
		// Without these array indexes there is nothing we can do
		if ( ! isset($items['qty']) OR ! isset($items['rowid']) OR ! isset($this->_cart_contents[$items['rowid']]))
		{
			return FALSE;
		}

		// Prep the quantity
		$items['qty'] = preg_replace('/([^0-9])/i', '', $items['qty']);

		// Is the quantity a number?
		if ( ! is_numeric($items['qty']))
		{
			return FALSE;
		}

				
		// Is the quantity zero?  If so we will remove the item from the cart.
		// If the quantity is greater than zero we are updating
		if ($items['qty'] == 0)
		{
			unset($this->_cart_contents[$items['rowid']]);
		}
		else
		{
			if(!empty($this->keys)){
				foreach($this->keys as $crt_k=>$crt_row){
					if(array_key_exists($crt_row,$this->_cart_contents[$items['rowid']])){
						$this->_cart_contents[$items['rowid']][$crt_row]   = $items[$crt_row];						
					}
				}
			}else{
				$this->_cart_contents[$items['rowid']]['qty']   = $items['qty'];
				$this->_cart_contents[$items['rowid']]['price'] = $items['price'];				
			}		
		}
		
		return TRUE;
	}	
}