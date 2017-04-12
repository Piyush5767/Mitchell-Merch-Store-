<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script type="text/javascript">
    jQuery(document).on('click change', 'input[name="check_all"]', function() {
        var checkboxes = $(this).closest('table').find(':checkbox').not($(this));
        if ($(this).prop('checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });
</script>
<div class="row">
<?php if($this->session->flashdata('msg') != ''){?> 
	<div class="col-md-12">
		<div class="alert alert-success">
			<button class="close" data-dismiss="alert"> <i class="fa fa-times" aria-hidden="true"></i> </button>
			<i class="fa fa-check-circle"></i>
			<?php echo $this->session->flashdata('msg');?>
		</div>
	</div>
<?php }?>
<?php if($this->session->flashdata('error') != ''){?> 
	<div class="col-md-12">
		<div class="alert alert-danger">
			<button class="close" data-dismiss="alert"> <i class="fa fa-times" aria-hidden="true"></i> </button>
			<i class="fa fa-times-circle"></i>
			<?php echo $this->session->flashdata('error');?>
		</div>
	</div>
<?php }?>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<i class="fa fa-external-link-square icon-external-link-sign"></i>
		<?php echo lang('orders_admin_transaction_status'); ?>           
	</div>
	<?php
	$attribute = array('class' => 'form-orders', 'id' => 'form-orders');		
	echo form_open(site_url('admin/orders/refundorders'), $attribute);
	?>
	<div class="panel-body" id="panelbody">
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-2">
						<?php $option_s = array(''=>  lang('all'), '5'=>5, '10'=>10, '15'=>15, '20'=>20, '25'=>25, '100'=>100); ?>
						<?php echo form_dropdown('per_page', $option_s, $per_page, 'class="form-control" id="per_page"'); ?>
					</div>
					<div class="col-md-4">
						<?php 
							$search = array('name' => 'search', 'id' => 'search', 'class' => 'form-control datepicker', 'placeholder' => lang('orders_admin_search_place'), 'autocomplete'=>'off', 'value'=>$search);
							echo form_input($search);
						?>
					</div>
					<div class="col-md-4">
						<?php 
							$option_s = array('order_number' => lang('orders_admin_search_order_number'), 'date' => lang('orders_admin_search_date'));
							echo form_dropdown('option', $option_s, $option, 'class="form-control" id="option_s"'); 
						?>
					</div>
					<div class="col-md-2">
						<button type="submit" class="btn btn-primary"><?php echo lang('search');?></button>
					</div>
				</div>
			</div>
			
			<div class="col-md-6">
				<p style="text-align:right;">
					<a id="btn-delete" class="btn btn-bricky tooltips" title="<?php echo lang('delete'); ?>" href="javascript:void(0);" > 
						<i class="fa fa-trash-o"></i>
					</a>
				</p>
			</div>
		</div>
		<table id="sample-table-1" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th class="center">
						<div class="checkbox-table">
							<label>
								<input id="select_all" type="checkbox" name='check_all' />
							</label>
						</div>
					</th>
					<th class="center"><?php echo lang('orders_admin_order_number_title'); ?></th>
					<th class="center"><?php echo lang('orders_admin_order_payment_type'); ?></th>					
					<th class="center"><?php echo lang('orders_admin_order_payment_status'); ?></th>
					<th class="center"><?php echo lang('orders_admin_refund_transaction_id'); ?></th>
					<th class="center"><?php echo lang('orders_admin_refund_fee'); ?></th>
					<th class="center"><?php echo lang('total'); ?></th>										
					<th class="center"><?php echo lang('date'); ?></th>
					<th class="center"><?php echo lang('action'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if (count($orders)) { ?>			
				<?php foreach($orders as $order) { ?>
				<tr>
					<td class="center">
						<div class="checkbox-table">
							<label>
								<input type="checkbox" name="checkb[]" class="checkb" name="check" value="<?php echo $order->id; ?>">
							</label>
						</div>
					</td>
					<td class="center">
						<a target="_blank" href="<?php echo site_url('admin/orders/detail/'.$order->order_id); ?>"><?php echo $order->order_number; ?></a>
					</td>
					<td class="center"><?php echo ucwords($payments[$order->payment_id]); ?></td>
					<td class="center">
						<?php /* if($order->status == 'Failure'): */ ?>
							<a target="_blank" href="<?php echo site_url('admin/orders/refundfailure/'.$order->id); ?>">
								<?php echo ucwords($order->status); ?>
							</a>
						<?php /* else: ?>
							<?php echo ucwords($order->status); ?>
						<?php /* endif; */ ?>
					</td>
					<td class="center"><?php echo $order->refund_id; ?></td>
					<td class="center"><?php echo settingValue($setting, 'currency_symbol', '$'); ?><?php echo number_format($order->refund_fee, 2); ?></td>
					<td class="center"><?php echo settingValue($setting, 'currency_symbol', '$'); ?><?php echo number_format($order->refund_amount, 2); ?></td>
					<td class="center"><?php echo date("Y-m-d", strtotime($order->date)); ?></td>
					<td class="center">
						<a class="remove btn btn-bricky tooltips" onclick="return confirm('<?php echo lang('orders_admin_confirm_delete');?>');" href="<?php echo site_url('admin/orders/transacationDelete/'.$order->id); ?>" data-original-title="<?php echo lang('remove');?>" data-placement="top">
							<i class="fa fa-trash-o"></i>
						</a>
					</td>
				</tr>
				<?php } ?>				
			<?php } ?>
			</tbody>
		</table>
		<div class="pull-right">
			<?php echo $links; ?>
		</div>
	</div>
	<?php echo form_close(); ?>        
</div>   
<script type="text/javascript">
	jQuery('.pagination').css('margin', '0px');
	jQuery('.tooltips').tooltip();
	
	if(jQuery('#option_s').val() == 'date')
	{
		jQuery('.datepicker').datepicker({
			setDate: '2015-02-07',
			dateFormat: 'yy-mm-dd'
		});		
	}
		
	jQuery('#option_s').change(function(){
		var check = jQuery(this).val();
		if(check == 'date')
		{
			jQuery('.datepicker').datepicker();
			jQuery('.datepicker').datepicker("option", "dateFormat", "yy-mm-dd");
		}else
		{
			jQuery('.datepicker').datepicker('destroy');
		}
	});
	
	jQuery('#btn-delete').click(function(){
		if(jQuery('.checkb').is(':checked')){
			var cf = confirm("<?php echo lang('orders_admin_confirm_delete');?>");
			if(cf)
				jQuery('#form-orders').attr('action', '<?php echo site_url('admin/orders/transacationDelete');?>').submit();
		}else{
			alert('<?php echo lang('orders_admin_error_not_checbox_msg');?>');
		}
	});
	
	jQuery('#per_page').change(function(){
		jQuery('#form-orders').submit();
	});
	
</script>