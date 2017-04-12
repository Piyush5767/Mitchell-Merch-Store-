<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$cart	= $this->session->userdata('cart');

$lang = getLanguages();
?>

<link   href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" type="text/css" rel="stylesheet" />
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/printfulRequest.js'); ?>"></script>	
<div class="page-cart">
<form id="calculateShipping" method="POST" action="<?php echo site_url('payment'); ?>">
	<!-- cart head -->
	<div class="row">
		<div class="col-sm-6 text-left">
			<h3>Calculate & Update Your Shipping </h3>
		</div>
		<div class="col-sm-6 text-right">
			<a class="btn btn-primary" href="<?php echo site_url(); ?>" style="margin-top: 20px;"><?php echo language('cart_continue_shopping_btn', $lang);?></a>
		</div>
	</div>
	<hr />	
	<div class="row">		
		<div class="col-xs-12 col-sm-7 col-md-12 col-lg-12 pull-left">
			<!-- BEGIN:: user info -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo language('cart_address_and_shipping', $lang);?></h3>
				</div>
				<div class="panel-body">
					<div class="msg"><?php echo $this->session->flashdata('message');?></div>
					<div class="row">
						<div class="form-horizontal col-sm-12">
							<div class="loading2" style="display:none;"></div>
							<?php if ( count($forms) > 0 ) { ?>
								<?php foreach($forms as $field) { ?>
								<div class="form-group">
									<label class="col-sm-4 col-md-2 control-label"><?php echo $field->title; ?></label>
									<div class="col-sm-8 col-md-6">
										<?php 
										if ( isset($profiles[$field->id]) )
										{
											echo $fields->display($field, $profiles[$field->id]);
										}
										else
										{
											echo $fields->display($field);
										}
										?>
									</div>
								</div>
								<?php } ?>		
							<?php } ?>	
						</div>
					</div>
					<div class="row">
						<div class="col-md-8">
							<div class="shipprates" style="margin:0px 20px; display:<?php echo ($shipping_lists !='')? 'block': 'none'; ?>">
								<hr/><h3> Shipping Rates </h3><hr/>
								<table id="shippRtes" class="cell-border" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Name</th>
											<th>Price</th>
											<th>Select</th>
										</tr>
									</thead>
									<tfoot>
										<tr>									
											<th>Name</th>
											<th>Price</th>
											<th>Select</th>
										</tr>
									</tfoot>
									<tbody>
										<?php echo $shipping_lists; ?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-4"></div>						
					</div>
					<div class="row"> 
						<div class="col-sm-4 col-md-2 text-right" style="margin-top:40px;">
							<button type="button" id="calculateShipping" onclick="printful.calculateShipping('calculateShipping')" class="btn btn-primary">Calculate & Select Shipping</button>
						</div>
						<div class="col-sm-4 col-md-2 text-right" style="margin-top:40px;">
							<button type="button" id="checkout" onclick="printful.checkout('calculateShipping')" class="btn btn-primary">Checkout</button>
						</div>					
					</div>					
				</div>
			</div>
			<!-- END:: user info -->
		</div>
	</div>
</form>
</div>
<script>
	var baseURL	= '<?php echo base_url(); ?>';
	if (jQuery('#field-country').length > 0){
		var state_id	= jQuery('#field-state').data('id');
		if (state_id != null)

			apps.state(document.getElementById('field-country'), state_id);

		else

			apps.state(document.getElementById('field-country'));

	}
	jQuery(function(){
		jQuery('#shippRtes').DataTable({
			"order": [[ 1, "asc" ]],
			"columnDefs": [{
							"targets": 2,
							"orderable": false
							}],
			});
	});
</script>