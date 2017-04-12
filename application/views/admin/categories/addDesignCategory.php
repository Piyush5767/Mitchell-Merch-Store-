<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title"><?php echo lang('category_add'); ?></h4>
</div>
<form class="form-horizontal" id="add-category" method="post" action="<?php echo base_url('/admin/designClipart/savecategory'); ?>">
<div class="modal-body">	
	<ul id="nav-tabs-lang" class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#"><?php echo lang('art_category_info'); ?></a></li>		
	</ul>
	<div class="tab-content" id="tab-content-lang">
		<div id="en" class="tab-pane active">			
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo lang('title'); ?></label>
				<div class="col-sm-6">
					<input class="form-control" type="text" name="catname">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo lang('slug'); ?></label>
				<div class="col-sm-6">
					<input class="form-control" type="text" name="catslug">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo lang('description'); ?></label>
				<div class="col-sm-10">
					<textarea class="form-control textarea-tinymce" name="description"></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer">
	<button type="button" data-dismiss="modal" class="btn modal-close"><?php echo lang('close'); ?></button>
	<button type="submit" class="btn btn-primary">Save Category</button>
</div>
<?php echo form_close(); ?>