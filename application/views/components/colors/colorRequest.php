<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$lang = getLanguages();
?>
<h4>Search Color</h4>		
<div class="row">
	<div class="col-md-4"><!--<input class="search form-control" placeholder="Search" />-->
	<input type="text" class="form-control" placeholder="<?php echo lang('color_find_color_place')?>" onkeyup="dgUI.product.color.find('key', this)">
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
		<a class="box-color" href="javascript:void(0);" onclick="<?php echo $js; ?>">
			<span class="color-bg" style="background-color:#<?php echo $color->hex; ?>"></span>
			<?php echo $color->title; ?>
			<span class="colorclear"><?php echo $color->hex; ?></span>
		</a>
	</li>				
<?php } ?>			
</ul>
<?php } ?>	
<ul class="pagination" id="paginationmdl"></ul>