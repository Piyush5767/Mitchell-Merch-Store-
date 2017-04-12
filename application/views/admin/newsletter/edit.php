<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script src="<?php echo base_url('assets/plugins/validate/validate.js'); ?>"></script>
<form id="fr-edit" class="form-horizontal" method="post" action="<?php echo site_url().'admin/newsletter/edit/1';?>">
<div class="row">
	<div class="col-sm-12">
		<p class="pull-right">
			<button type="submit" class="btn btn-primary" ><?php echo lang('save'); ?></button>
			<a href="<?php echo site_url().'admin/newsletter'?>" class="btn btn-danger" ><?php echo lang('cancel'); ?></a>
		</p>
	</div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
		<i class="fa fa-external-link-square icon-external-link-sign"></i>
		<?php echo lang('newsletter_add_user'); ?>
	</div>
	<div class="modal-body" style="display: table; width: 100%;">
		<?php echo $this->session->flashdata('msg'); ?>		
		<div class="col-sm-6">		
			<div class="form-group">
				<label class="control-label col-md-4 text-left"><?php echo lang('user_email');?><span class="symbol required"></span></label>
				<div class="col-md-8">
					<input autocomplete="off" class="form-control validate required" type="text" data-msg="<?php echo lang('user_edit_msg_validate_email');?>" data-type="email" placeholder="<?php echo lang('user_edit_email_place');?>" name="data[email]">
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-md-4 text-left"><?php echo lang('newsletter_click_unblock'); ?></label>
				<div class="col-md-1">
				<input type="checkbox" name="data[subscribe]" value="1">
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-md-4 text-left"><?php echo lang('user_verified'); ?></label>
				<div class="col-md-1">
				<input type="checkbox" name="data[verified]" value="1">
				</div>
			</div>			

		</div>	
		<div class="col-sm-6"></div>
	</div>
</div>
</form>
<script type="text/javascript">
	jQuery('#fr-edit').validate();
</script>