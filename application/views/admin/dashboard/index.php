<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php if ($this->session->flashdata('error') !== false) { ?>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('error'); ?></div>
	</div>
</div>
<?php } ?>
<div class="row">
	<div class="col-md-12">
		<div class="row dashboard-manager-admin">
			<div class="col-md-2 col-sm-3">
				<div class="statistics-box">
					<div class="statistics-icon">
						<i class="fa fa-user fa-2x"></i>
					</div>
					<a class="statistics-info" href="<?php echo site_url('admin/users'); ?>">
						<span class="number"><?php echo $count_users; ?></span><span><?php echo lang('dashboard_user')?></span>
					</a>
				</div>
			</div>
			<div class="col-md-2  col-sm-3">
				<div class="statistics-box">
					<div class="statistics-icon">
						<i class="fa fa-picture-o fa-2x"></i>
					</div>
					<a class="statistics-info" href="<?php echo site_url('admin/art'); ?>">
						<span class="number"><?php echo $count_cliparts;?></span><span><?php echo lang('dashboard_clipart')?></span>
					</a>
				</div>
			</div>
			<div class="col-md-2 col-sm-3">
				<div class="statistics-box">
					<div class="statistics-icon">
						<i class="clip-t-shirt fa-2x"></i>
					</div>
					<a class="statistics-info" href="<?php echo site_url('admin/products'); ?>">
						<span class="number"><?php echo $count_products;?></span><span><?php echo lang('dashboard_product')?></span>
					</a>
				</div>
			</div>
			<div class="col-md-2 col-sm-3">
				<div class="statistics-box">
					<div class="statistics-icon">
						<i class="fa fa-shopping-cart fa-2x"></i>
					</div>
					<a class="statistics-info" href="<?php echo site_url('admin/orders'); ?>">
						<span class="number"><?php echo $count_orders;?></span><span><?php echo lang('dashboard_order')?></span>
					</a>
				</div>
			</div>
		</div>
		<div class="control-panel-box control-panel-box1-admin">
			<div class="control-panel-title">
				<span><?php echo lang('dashboard_control_panel')?></span>
			</div>
			<div class="row">
				<div class="col-md-8">
					<div class="col-lg-2 col-md-3 col-sm-2">
						<a class="control-panel-icon" href="<?php echo site_url('admin/users'); ?>">
							<i class="fa fa-users fa-4x"></i><span><?php echo lang('dashboard_users')?></span>
						</a>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-2">
						<a class="control-panel-icon" href="<?php echo site_url('admin/art'); ?>">
							<i class="fa fa-picture-o fa-4x"></i><span><?php echo lang('dashboard_arts')?></span>
						</a>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-2">
						<a class="control-panel-icon" href="<?php echo site_url('admin/design-clipart'); ?>">
							<i class="fa fa-picture-o fa-4x"></i><span><?php echo lang('dashboard_design_clipart')?></span>
						</a>
					</div>  				
					<div class="col-lg-2 col-md-3 col-sm-2">
						<a class="control-panel-icon" href="<?php echo site_url('admin/products'); ?>">
							<i class="clip-t-shirt fa-4x"></i><span><?php echo lang('dashboard_products')?></span>
						</a>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-2">
						<a class="control-panel-icon" href="<?php echo site_url('admin/products/edit'); ?>">
							<i class="fa fa-plus-square fa-4x"></i><span><?php echo lang('dashboard_add_product')?></span>
						</a>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-2">
						<a class="control-panel-icon" href="<?php echo site_url('admin/orders'); ?>">
							<i class="fa fa-shopping-cart fa-4x"></i><span><?php echo lang('dashboard_orders')?></span>
						</a>
					</div>
					<div class="col-lg-2 col-md-3 col-sm-2">
						<a class="control-panel-icon" href="<?php echo site_url('admin/settings'); ?>">
							<i class="fa fa-gear fa-4x"></i><span><?php echo lang('dashboard_settings')?></span>
						</a>
					</div>
				</div>
			</div>
		</div>		
		<div class="control-panel-box google-analystic-admin">
			<div class="control-panel-title">
				<span><?php echo lang('dashboard_control_analystic')?></span>
			</div>
			<div class="row google-analystic-block">
				<div class="col-md-6">
					<div class="dashboard_graph">
						<div class="row x_title">
							<div class="col-md-12" style="padding-top: 25px;">
								<p><strong>Site Number Of Page Views Between<small><?php echo date('M j, Y',strtotime('-30 day')).' - '.date('M j, Y'); ?></small></strong></p>
							</div>
						</div>			
						<div id="chart1" class="chartscstm"></div>
					</div>				
				</div>
				<div class="col-md-6">
					<div class="dashboard_graph">
						<div class="row x_title">
							<div class="col-md-12" style="padding-top: 25px;">
								<p><strong>Site Number of Unique Session Per Day Between<small><?php echo date('M j, Y',strtotime('-30 day')).' - '.date('M j, Y'); ?></small></strong></p>
							</div>
						</div>			
						<div id="chart2" class="chartscstm"></div>
					</div>				
				</div>					
			</div>
		</div>	
	</div>
	<div class="col-md-4"></div>
</div>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>		
<script type="text/javascript">
if((jQuery('#chart1').length > 0) && jQuery('#chart2').length > 0){
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart1);
	google.setOnLoadCallback(drawChart2);
  function drawChart1(){
	var data = new google.visualization.DataTable();
	<!-- Create the data table -->
	data.addColumn('string', 'Day');
	data.addColumn('number', 'Pageviews');	
	data.addRows([
	  <?php
	  foreach($results as $result) {
		  echo '["'.date('M j',strtotime($result->getDate())).'",'.$result->getPageviews().'],';
	  }
	  ?>
	]);	
	var chart = new google.visualization.AreaChart(document.getElementById('chart1'));
	chart.draw(data, {width: 450, height: 250, title: '',
					  colors:['#058dc7','#e6f4fa'],
					  areaOpacity: 0.1,
					  hAxis: {textPosition: 'in', showTextEvery: 5, slantedText: false, textStyle: { color: '#058dc7', fontSize: 10 } },
					  pointSize: 5,
					  legend: 'none',
					  chartArea:{left:0,top:30,width:"100%",height:"100%"}
	});		
  }
  function drawChart2(){
	var data = new google.visualization.DataTable();
	<!-- Create the data table -->
	data.addColumn('string', 'Day');
	data.addColumn('number', 'Sessions');	
	data.addRows([
	  <?php
	  foreach($results as $result) {
		  echo '["'.date('M j',strtotime($result->getDate())).'",'.$result->getVisits().'],';
	  }
	  ?>
	]);	
	var chart = new google.visualization.AreaChart(document.getElementById('chart2'));
	chart.draw(data, {width: 450, height: 250, title: '',
					  colors:['#f00','#000'],
					  areaOpacity: 0.1,
					  hAxis: {textPosition: 'in', showTextEvery: 5, slantedText: false, textStyle: { color: '#000', fontSize: 10 } },
					  pointSize: 5,
					  legend: 'none',
					  chartArea:{left:0,top:30,width:"100%",height:"100%"}
	});			  
  }	  
} 
</script>