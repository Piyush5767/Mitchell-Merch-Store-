<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if($arts)
{
	foreach($arts as $art)
	{							
		$images = imageCustomArt($art);
?>
		<div class="col-sm-3 col-md-2 box-art">
			<a class="box-image" data-toggle="modal" href="javascript:void(0)" title="<?php echo $art->name; ?>">
				<img src="<?php echo $images->thumb; ?>" alt="" class="img-responsive">
			</a>
			<span class="box-publish">
				<input type="checkbox" class="checkb" name="ids[]" value="<?php echo $art->id; ?>">
			</span>
			
			<?php if ($art->price > 0 ){ ?>
				<div class="box-detail-price">$<?php echo $art->price; ?></div>
			<?php } ?>
		</div>
<?php 
	}
?>
	<!-- begin pagination -->
	<div class="clear-line clear-line-head col-md-12"></div>
	<div id="arts-pagination" class="pull-right col-md-12 text-right">
		<?php echo $this->pagination->create_links(); ?>
	</div>
	<!-- end pagination -->
<?php 
}else{
	echo '<div class="col-md-2 col-sm-3 box-art">' . lang('data_not_found') .'</div>';
}
?>	