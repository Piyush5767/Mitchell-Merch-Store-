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

class helperProduct
{
	function sortDesign($design, $orderby = 'ordering'){	
	
		$rows 	= array();			
		
		foreach( $design as $key => $value ){
			if(is_array($value))
			{				
				for( $i=0; $i<count($value); $i++ ){	
					$rows[$i][$key]	= $value[$i];
				}
			}		
		}					
		
		$sortArray = array(); 
		
		foreach($rows as $row){	
			foreach($row as $key=>$value){
				if(!isset($sortArray[$key])){ 
					$sortArray[$key] = array();
				}				
				$sortArray[$key][] = $value;
			} 		
		}
		
		if(count($sortArray)){		
			array_multisort($sortArray[$orderby], SORT_ASC, $rows);	
		}	
		
		return $rows;
	
	}		
	
	function json($design){		
	$rows = new stdClass();		
	foreach($design as $key => $value){			
	$rows->$key = json_decode($value);		
	}				
	return $rows;	
	}
	
	function getImgage($str)
	{
		$data = str_replace("'", '"', $str);
		$data = json_decode($data);
		
		if( count($data) > 0 )
		{
			foreach($data as $vector)
			{
				if( isset($vector->img) && $vector->img != '' )
				{
					$img = $vector->img;
					return base_url($img);
				}
			}
		}
		
		return '';
	}
	
	public function getDesign($data)
	{
		$design = new stdClass();
		
		if (strlen($data->front) > 10)
		{
			$design->front		= json_decode($data->front);
		}
		else
		{
			$design->front		= false;
		}
		
		if (strlen($data->back) > 10)
		{
			$design->back		= json_decode($data->back);
		}
		else
		{
			$design->back		= false;
		}
		
		if (strlen($data->left) > 10)
		{
			$design->left		= json_decode($data->left);
		}
		else
		{
			$design->left		= false;
		}
		
		if (strlen($data->right) > 10)
		{
			$design->right		= json_decode($data->right);
		}
		else
		{
			$design->right		= false;
		}		
		$design->area		= json_decode($data->area);
		$design->params		= json_decode($data->params);
		$design->color_hex	= json_decode($data->color_hex);
		$design->color_title= json_decode($data->color_title);
		
		return $design;
	}
	public function displaySizeAttributes($attribute,$prdpricedep=1,$printfulsizediffer=0){		
		if (isset($attribute->dpi) && $attribute->dpi != ''){	
			$attrs = new stdClass();					
			$attrs->dpi 			= json_decode($attribute->dpi);			
			$attrs->size 			= json_decode($attribute->size);			
			$attrs->height 			= json_decode($attribute->height);			
			$attrs->width 			= json_decode($attribute->width);
			$attrs->totheig			= json_decode($attribute->totalheight);			
			$attrs->totwidth		= json_decode($attribute->totalwidth);		
			$attrs->back_height 	= json_decode($attribute->back_height,true);			
			$attrs->back_width 		= json_decode($attribute->back_width,true);
			$attrs->back_totalheight= json_decode($attribute->back_totalheight,true);			
			$attrs->back_totalwidth	= json_decode($attribute->back_totalwidth,true);				
			$attrs->price 			= json_decode($attribute->price);

			$html = '';			
			$html.= '<select class="form-control input-sm" id="productSize" data-printfulsizediffer="'.$printfulsizediffer.'" data-prdtpricedep="'.$prdpricedep.'" name="productSize">';			
			for ($i=0; $i<count($attrs->size); $i++){	
				$row = $i+1;
				$back_height = $back_width = $back_totalheight = $back_totalwidth = '';
				$back_height = (!empty($attrs->back_height))? (array_key_exists($row,$attrs->back_height) && $attrs->back_height[$row] != '') ? $attrs->back_height[$row] : 0 : 0;
				$back_width  = (!empty($attrs->back_width))? (array_key_exists($row,$attrs->back_width) && $attrs->back_width[$row] != '') ? $attrs->back_width[$row] : 0 : 0;
				$back_totalheight = (!empty($attrs->back_totalheight))? (array_key_exists($row,$attrs->back_totalheight) && $attrs->back_totalheight[$row] != '') ? $attrs->back_totalheight[$row] : 0 : 0;
				$back_totalwidth =  (!empty($attrs->back_totalwidth))? (array_key_exists($row,$attrs->back_totalwidth) && $attrs->back_totalwidth[$row] != '') ? $attrs->back_totalwidth[$row] : 0 : 0;				
				$html.= '<option data-height="'.$attrs->height[$i].'" data-totalheight="'.$attrs->totheig[$i].'" data-price="'.$attrs->price[$i].'" data-width="'.$attrs->width[$i].'" data-totalwidth="'.$attrs->totwidth[$i].'" data-dpi="'.$attrs->dpi[$i].'" data-back_width="'.$back_width.'" data-back_height="'.$back_height.'" data-back_totalheight="'.$back_totalheight.'" data-back_totalwidth="'.$back_totalwidth.'" value="'.$attrs->size[$i].'">'.$attrs->size[$i].'</option>';							
			}	
			$html .= '</select>';							
			return $html;		
		}else{			
			return '';		
		}	
	}	
	public function displayAttributes($attribute)
	{
		if (isset($attribute->name) && $attribute->name != '')
		{
			$attrs = new stdClass();
		
			$attrs->name 		= json_decode($attribute->name);
			$attrs->titles 		= json_decode($attribute->titles);
			$attrs->prices 		= json_decode($attribute->prices);
			$attrs->type 		= json_decode($attribute->type);
			
			$html 				= '';
			for ($i=0; $i<count($attrs->name); $i++)
			{
				$html 	.= '<div class="form-group product-fields">';
				$html 	.= 		'<label for="fields">'.$attrs->name[$i].'</label>';
				
				$id 	 = 'attribute['.$attribute->id.']['.$i.']';
				$html 	.= 		$this->field($attrs->name[$i], $attrs->titles[$i], $attrs->prices[$i], $attrs->type[$i], $id);
				
				$html 	.= '</div>';
			}
			return $html;
		}
		else
		{
			return '';
		}
	}
	
	function field($name, $title, $price, $type, $id)
	{
		$html = '<div class="dg-poduct-fields">';
		switch($type)
		{
			case 'checkbox':
				for ($i=0; $i<count($title); $i++)
				{
					$html .= '<label class="checkbox-inline">';
					$html .= 	'<input type="checkbox" name="'.$id.'['.$i.']" value="'.$i.'"> '.$title[$i];
					$html .= '</label>';
				}
			break;
			
			case 'selectbox':
				$html .= '<select class="form-control input-sm" name="'.$id.'">';
				
				for ($i=0; $i<count($title); $i++)
				{
					$html .= '<option value="'.$i.'">'.$title[$i].'</option>';
				}
				
				$html .= '</select>';
			break;
			
			case 'radio':
				for ($i=0; $i<count($title); $i++)
				{
					$html .= '<label class="radio-inline">';
					$html .= 	'<input type="radio" name="'.$id.'" value="'.$i.'"> '.$title[$i];
					$html .= '</label>';
				}
			break;
			
			case 'textlist':
				$html 		.= '<style>.product-quantity{display:none;}</style><ul class="p-color-sizes list-number col-md-12">';
				for ($i=0; $i<count($title); $i++)
				{
					$html .= '<li>';
					$html .= 	'<label>'.$title[$i].'</label>';
					$html .= 	'<input type="text" class="form-control input-sm size-number" name="'.$id.'['.$i.']">';					
					$html .= '</li>';
				}
				$html 		.= '</ul>';
			break;
		}
		$html	.= '</div>';
		
		return $html;
	}
	
	public function quantity($min = 1, $name = 'Quantity', $name2 = 'minimum quantity: '){
		
		$html = '<div class="form-group product-fields product-quantity">';
		$html .= 	'<label class="col-sm-4">'.$name.'</label>';
		$html .= 	'<div class="col-sm-6">';
		$html .= 		'<input type="number" class="form-control input-sm" min="1" value="1" name="quantity" id="quantity">';
		$html .= 	'</div>';
		$html .= 	'<span class="help-block"><small>'.$name2.$min.'</small></span>';
		$html .= '</div>';
		
		return $html;
	}
}
?>