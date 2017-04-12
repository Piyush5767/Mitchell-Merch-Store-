<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id="order_detail_body">
<?php if (count($refundorders)) { ?>
	<?php $transaction_obj = json_decode($refundorders->refund_object,true); ?>
	<div class="row">
		<div class="col-md-9">
			<h4 id="orders_info"><?php echo lang('orders_admin_refund_transaction_title');?></h4>
			<div class="table-responsive">
				<table class="table table-hover">
					<tr>
						<th><?php echo lang('orders_admin_order_number_title');?></th>
						<td><a href="<?php echo base_url('/admin/orders/detail/'.$refundorders->order_id); ?>" target="_blank"><?php echo $refundorders->order_number; ?></a></td>
					</tr>
					<tr>
						<th><?php echo lang('orders_admin_order_status_title');?></th>
						<td><?php echo ucwords($order->shipping_status); ?></td>
					</tr>
					<tr>
						<th><?php echo lang('orders_admin_order_date_title');?></th>
						<td><strong><?php echo date("Y-m-d", strtotime($order->created_on)); ?></strong></td>
					</tr>
					<tr>
						<th><?php echo lang('orders_admin_refund_transaction_date');?></th>
						<td><?php echo date("Y-m-d", strtotime($refundorders->date)); ?></td>
					</tr>
					<tr>
						<th><?php echo lang('orders_admin_refund_transaction_id');?></th>
						<td><?php echo $refundorders->refund_id; ?></td>
					</tr>
					<tr>
						<th><?php echo lang('orders_admin_refund_paypal_correlation_id');?></th>
						<td><?php echo $transaction_obj['CORRELATIONID']; ?></td>
					</tr>
					<tr>
						<th><?php echo lang('orders_admin_refund_fee');?></th>
						<td><?php echo $refundorders->refund_fee; ?></td>
					</tr>
					<tr>
						<th><?php echo lang('orders_admin_netrefund_fee');?></th>
						<td><?php echo $refundorders->netrefund_amnt; ?></td>
					</tr>
					<tr>
						<th><?php echo lang('total');?></th>
						<td><?php echo $refundorders->refund_amount; ?></td>
					</tr>
					<tr>
						<th><?php echo lang('orders_admin_transaction_status');?></th>
						<td><?php echo ucwords($refundorders->status); ?></td>
					</tr>
				</table>				
				
				<?php if($refundorders->status == 'Failure'): ?>
					<h4 style="margin-top:40px;" id="orders_info"><?php echo lang('orders_admin_refund_error_transaction_title');?></h4>
					<table class="table table-hover">
					  <thead>
						<tr>
						  <th>#</th>
						  <th><?php echo lang('orders_admin_refund_error_transaction_title');?></th>
						  <th><?php echo lang('orders_admin_refund_error_transaction_title');?></th>
						</tr>
					  </thead>
					  <tbody>
						<?php if(array_key_exists('ERRORS',$transaction_obj)): ?>
							<?php $i= 1; 
							foreach($transaction_obj['ERRORS'] as $error_key=>$error_row): ?>
							<tr>
							  <th scope="row"><?php echo $i; ?></th>
							  <td><?php echo $error_row["L_ERRORCODE"]; ?></td>
							  <td><?php echo $error_row["L_LONGMESSAGE"]; ?></td>
							</tr>
							<?php endforeach; ?>
						<?php endif; ?>	
					  </tbody>
					</table>					
				<?php endif; ?>				
			</div>
		</div>	
	</div>		
<?php } else { ?>
	<div class="row">
		<div class="col-md-12">
			<?php echo lang('data_not_found'); ?>
		</div>
	</div>
<?php } ?>
</div>