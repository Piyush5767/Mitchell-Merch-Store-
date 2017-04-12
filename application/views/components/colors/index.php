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

$lang = getLanguages();
?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel"><?php echo language('select_color', $lang); ?></h4>
		</div>
		<div class="modal-body">
		<div id="clrmsg"></div>
		<form action="colors/addcolor" id="addclrfrm">					
			<div class="row">						
				<div class="col-md-4"><input type="text" name="colorTitle" class="form-control" placeholder="<?php echo language('color_title', $lang)?>" id="add-color-title" /></div>
				<div class="col-md-4"><input type="text" class="form-control color {pickerPosition:'botton'}" placeholder="<?php echo language('color_hex', $lang)?>" id="add-color-color" /></div>
				<div class="col-md-2"><button type="submit" name="addcolor" class="btn btn-primary"> Add Color</button></div>
				<div class="col-md-2"></div>
			</div>					
			<br />
		</form>
		<br/>
		<div class="clear-line"></div>
			<div id="colorBlck">
				<h4>Search Color</h4>		
				<div class="row">
					<div class="col-md-4">
					<input class="search form-control" placeholder="Search" />
					<!--<input type="text" class="form-control" placeholder="<?php echo lang('color_find_color_place')?>" onkeyup="dgUI.product.color.find('key', this)">-->
					</div>
					<div class="col-md-2"><button class="sort btn btn-primary" style="height: 32px;" data-sort="name">
						Sort by name
					</button></div>
					<div class="col-md-6"></div>
				</div>
				<br />
				<div class="clear-line"></div>			
				<?php if($content) { ?>
				<ul class="colors list">	
				<?php foreach($content as $color) { ?>				
					<li>
					<?php if($function == null) $function = "dgUI.product.addColor"; ?>
						<?php
							if(isset($id) && $id != null)
								$js = $function . "('".$color->title."', '".$color->hex."', '".$id."')";
							else
								$js = $function . "('".$color->title."', '".$color->hex."')";
						?>
						<div class="id" style="display:none;"><?php echo $color->hex; ?></div>
						<div class="singleobj"><div class="remove remove-item-btn" style="position: absolute;left: 20px;margin-left: 90px;margin-top: 10px;"><i class="fa fa-times" aria-hidden="true"></i></div>
						<a class="box-color" href="javascript:void(0);" onclick="<?php echo $js; ?>">
							<span class="color-bg" style="background-color:#<?php echo $color->hex; ?>"></span>
							<span class="name"><?php echo $color->title; ?></span>
							<span class="colorclear value"><?php echo $color->hex; ?></span>
						</a>
						</div>
					</li>				
				<?php } ?>			
				</ul>
				<ul class="pagination" id="paginationmdl" style="clear: both;width: 100%;margin-left: 10px;"></ul>
				<?php } ?>	
			</div>			
		</div>		
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo language('cancel_btn', $lang); ?></button>
		</div>
	</div>
</div>
<script type="text/javascript">
	jscolor.init();
	var options = {
	  valueNames: [ 'id','singleobj' ],
	  item: '<li><div class="id" style="display:none;"></div><div class="singleobj"></div></li>',
	  page: 20,
      plugins: [
		ListPagination({}),
      ]  
	};
	var userList = new List('colorBlck', options);
	//userList.search();
</script>