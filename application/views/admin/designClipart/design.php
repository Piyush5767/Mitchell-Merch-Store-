<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>
<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/admin/css/admin-style.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('assets/js/add-ons.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/jquery.ui.rotatable.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/language.js'); ?>"></script>	
<script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/canvg.js'); ?>"></script>
<?php if ($this->session->flashdata('error') !== false) { ?>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger" role="alert"><?php echo $this->session->flashdata('error'); ?></div>
	</div>
</div>
<?php } ?>

<!-- Begin clipart -->
<div class="modal fade" id="dg-cliparts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="overflow: hidden;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<div class="col-xs-4 col-md-3">
					<h4 class="modal-title"><?php echo language('designer_art_select', $lang); ?></h4>
				</div>
				<div class="col-xs-7 col-md-4">
					<div class="input-group">
					  <input type="text" id="art-keyword" autocomplete="off" class="form-control input-sm" placeholder="<?php echo language('search_btn', $lang); ?>">
					  <span class="input-group-btn">
						<button class="btn btn-default btn-sm" onclick="design.designer.art.arts(0)" type="button"><?php echo language('search_btn', $lang); ?></button>
					  </span>
					</div>
				</div>
			</div>
			<div class="modal-body">
				<div class="row align-center">
					<div id="dag-art-panel">
						<a href="javascript:void(0)" title="Click to show categories">
							<?php echo language('designer_clipart_shop_library', $lang); ?> <span class="caret"></span>
						</a>
						<a href="javascript:void(0)" title="Click to show categories">
							<?php echo language('designer_clipart_store_design', $lang); ?> <span class="caret"></span>
						</a>
					</div>
				</div>						
				
				<div class="row">
					<div id="dag-art-categories" class="row col-xs-4 col-md-3"></div>
					<div class="col-xs-8 col-md-9">
						<div id="dag-list-arts"></div>
						<div id="dag-art-detail">
							<button type="button" class="btn btn-danger btn-xs"><?php echo language('close_btn', $lang); ?></button>
						</div>
					</div>								
				</div>
			</div>
			
			<div class="modal-footer">
				<div class="align-right" id="arts-pagination" style="display:none">
					<ul class="pagination">
						<li><a href="javascript:void(0)">&laquo;</a></li>
						<li class="active"><a href="javascript:void(0)">1</a></li>
						<li><a href="javascript:void(0)">2</a></li>
						<li><a href="javascript:void(0)">3</a></li>
						<li><a href="javascript:void(0)">4</a></li>
						<li><a href="javascript:void(0)">5</a></li>
						<li><a href="javascript:void(0)">&raquo;</a></li>
					</ul>
					<input type="hidden" value="0" autocomplete="off" id="art-number-page">
				</div>
				<div class="align-right" id="arts-add" style="display:none">
					<div class="art-detail-price"></div>
					<button type="button" class="btn btn-primary"><?php echo language('add_design_btn', $lang); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End clipart -->

<!-- Begin Upload -->
<div class="modal fade" id="dg-myclipart" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>				
				<ul role="tablist" id="upload-tabs">
					<li class="active"><a href="#upload-conputer" role="tab" data-toggle="tab"><?php echo language('designer_upload_upload_photo', $lang); ?></a></li>
					<li><a href="#uploaded-art" role="tab" data-toggle="tab"><?php echo language('designer_upload_photo_uploaded', $lang); ?></a></li>
				</ul>
			</div>
			<div class="modal-body">
				<div class="tab-content">
					<div class="tab-pane active" id="upload-conputer">
						<div class="row">
							<div class="col-xs-6 col-md-6">
								<div class="form-group">
									<label><?php echo language('designer_upload_choose_a_file_upload', $lang); ?></label>
									<input type="file" id="files-upload" autocomplete="off"/>											
								</div>
								
								<div class="checkbox" style="display:none;">
									<label>
									  <input type="checkbox" autocomplete="off" id="remove-bg"> <span class="help-block"><?php echo language('designer_upload_remove_white_background', $lang); ?></span>
									</label>
								</div>
							</div>
							
							<div class="col-xs-6 col-md-6">
								<div class="form-group">
									<label><strong><?php echo language('designer_upload_accepted_file_types', $lang); ?></strong> <small>(<?php echo language('designer_upload_max_file_size', $lang); ?>: <?php echo settingValue($setting, 'site_upload_max', '0.5'); ?>MB)</small></label>
									<p><?php echo language('designer_upload_accept_the_following', $lang); ?>: <strong>PNG, JPG, GIF</strong></p>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="checkbox">
									<label>
									  <input type="checkbox" autocomplete="off" id="upload-copyright"> <span class="help-block"><?php echo language('designer_upload_please_read', $lang); ?> <a href="<?php echo settingValue($setting, 'site_upload_terms', '#'); ?>" target="_blank"><?php echo language('designer_upload_copyright_terms', $lang); ?></a>. <?php echo language('designer_upload_if_you_do_not_have_the_complete', $lang); ?></span>
									</label>
								</div>
								<div class="form-group">
									<button type="button" class="btn btn-primary" id="action-upload"><?php echo language('upload_btn', $lang); ?></button>
								</div>
							</div>
						</div>
					</div>
					
					<div class="tab-pane" id="upload-facebook">
						<?php echo language('designer_upload_facebook', $lang); ?>
					</div>
					<div class="tab-pane" id="uploaded-art">
						<div class="row" id="dag-files-images">
						</div>
						
						<div id="drop-area"></div>
						<div class="row col-md-12">
							<span class="help-block"><?php echo language('designer_upload_click_image_to_add_design', $lang); ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> 
<!-- End Upload -->
<!-- Begin fonts -->
<div class="modal fade" id="dg-fonts" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>						
				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<?php echo language('designer_fonts_font_categories', $lang); ?> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu font-categories" role="menu"></ul>
				</div>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 list-fonts"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End fonts -->
	
<div class="popover right" id="dg-popover">
	<div class="arrow"></div>
	<h3 class="popover-title"><span><?php echo language('designer_clipart_edit_size_position', $lang); ?></span> <a href="javascript:void(0)" class="popover-close"><i class="glyphicons remove_2 glyphicons-12 pull-right"></i></a></h3>
	<div class="popover-content">		
		<!-- BEGIN clipart edit options -->
		<div id="options-add_item_clipart" class="dg-options">
			<div class="dg-options-toolbar">
				<div aria-label="First group" role="group" class="btn-group btn-group-lg">						
					<button class="btn btn-default btn-action-edit" type="button" data-type="edit">
						<i class="glyphicon glyphicon-tint"></i> <small class="clearfix"><?php echo language('edit', $lang); ?></small>
					</button>
					<button class="btn btn-default btn-action-colors" type="button" data-type="colors">
						<i class="glyphicon glyphicon-tint"></i> <small class="clearfix"><?php echo language('colors', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="size">
						<i class="fa fa-text-height"></i> <small class="clearfix"><?php echo language('size', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="rotate">
						<i class="fa fa-rotate-right"></i> <small class="clearfix"><?php echo language('rotate', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="functions">
						<i class="fa fa-cogs"></i> <small class="clearfix"><?php echo language('designer_functions', $lang); ?></small>
					</button>
				</div>
			</div>
			
			<div class="dg-options-content">
				<div class="row toolbar-action-edit">					
					<div id="item-print-colors">
					</div>
				</div>
				<div class="row toolbar-action-size">
					<div class="col-xs-3 col-lg-3 align-center">
						<div class="form-group">
							<small><?php echo language('width', $lang); ?></small>
							<input type="text" size="2" id="clipart-width" readonly disabled>
						</div>
					</div>
					<div class="col-xs-3 col-lg-3 align-center">
						<div class="form-group">
							<small><?php echo language('height', $lang); ?></small>
							<input type="text" size="2" id="clipart-height" readonly disabled>
						</div>
					</div>
					<div class="col-xs-6 col-lg-6 align-left">
						<div class="form-group">
							<small><?php echo language('designer_clipart_edit_unlock_proportion', $lang); ?></small><br />
							<input type="checkbox" class="ui-lock" id="clipart-lock" />
						</div>
					</div>
				</div>
				
				<div class="row toolbar-action-rotate">					
					<div class="form-group col-lg-12">
						<div class="row">
							<div class="col-xs-6 col-lg-6">
								<small><?php echo language('rotate', $lang); ?></small>
							</div>
							<div class="col-xs-6 col-lg-6 align-right">
								<span class="rotate-values"><input type="text" value="0" class="input-small rotate-value" id="clipart-rotate-value" />&deg;</span>
								<span class="rotate-refresh glyphicons refresh"></span>
							</div>
						</div>						
					</div>
				</div>
				
				<div class="row toolbar-action-colors">
					<div id="clipart-colors">
						<div class="form-group col-lg-12 text-left position-static">
							<small><?php echo language('designer_clipart_edit_choose_your_color', $lang); ?></small>
							<div id="list-clipart-colors" class="list-colors"></div>
						</div>
					</div>
				</div>
				
				<div class="row toolbar-action-functions">	
					<div class="col-lg-12 form-group">
						<span class="btn btn-default btn-xs" onclick="design.item.flip('x')">
							<i class="glyphicons transfer glyphicons-12"></i>
							 <?php echo language('designer_clipart_edit_flip', $lang); ?>
						</span>							
						<span class="btn btn-default btn-xs" onclick="design.item.center()">
							<i class="glyphicons align_center glyphicons-12"></i>
							 <?php echo language('designer_clipart_edit_center', $lang); ?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<!-- END clipart edit options -->			
		<!-- BEGIN Text edit options -->
		<div id="options-add_item_text" class="dg-options">
			<div class="dg-options-toolbar">
				<div aria-label="First group" role="group" class="btn-group btn-group-lg">
					<button class="btn btn-default" type="button" data-type="text">
						<i class="fa fa-pencil"></i> <small class="clearfix"><?php echo language('designer_text', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="fonts">
						<i class="fa fa-font"></i> <small class="clearfix"><?php echo language('designer_fonts', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="style">
						<i class="fa fa-align-justify"></i> <small class="clearfix"><?php echo language('designer_style', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="outline">
						<i class="fa fa-crop"></i> <small class="clearfix"><?php echo language('outline', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="size">
						<i class="fa fa-text-height"></i> <small class="clearfix"><?php echo language('size', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="rotate">
						<i class="fa fa-rotate-right"></i> <small class="clearfix"><?php echo language('rotate', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="functions">
						<i class="fa fa-cogs"></i> <small class="clearfix"><?php echo language('designer_functions', $lang); ?></small>
					</button>
				</div>
			</div>				
			<div class="dg-options-content">
				<!-- edit text -->
				<div class="row toolbar-action-text">
					<div class="col-xs-12">
						<textarea class="form-control text-update" data-event="keyup" data-label="text" id="enter-text"></textarea>
					</div>
				</div>			
				<div class="row toolbar-action-fonts">
					<div class="col-xs-8">
						<div class="form-group">
							<small><?php echo language('choose_a_font', $lang); ?></small>
							<div class="dropdown" data-target="#dg-fonts" data-toggle="modal">
								<a id="txt-fontfamily" class="pull-left" href="javascript:void(0)">
								<?php echo language('designer_clipart_edit_arial', $lang); ?>
								</a>
								<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s pull-right"></span>
							</div>
						</div>
					</div>
					<div class="col-xs-4">
						<div class="form-group">
							<small><?php echo language('designer_clipart_edit_text_color', $lang); ?></small>
							<div class="list-colors">
								<a class="dropdown-color" id="txt-color" title="Click to change color" href="javascript:void(0)" data-color="black" data-label="color" style="background-color:black">
									<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="clear-line"></div>
				<div class="clear"></div>			
				<div class="row toolbar-action-style">
					<div class="col-xs-6">
						<small><?php echo language('designer_clipart_edit_text_style', $lang); ?></small>
						<div id="text-style">
							<span id="text-style-i" class="text-update btn btn-default btn-xs glyphicons italic glyphicons-12" data-event="click" data-label="styleI"></span>
							<span id="text-style-b" class="text-update btn btn-default btn-xs glyphicons bold glyphicons-12" data-event="click" data-label="styleB"></span>							
							<span id="text-style-u" class="text-update btn btn-default btn-xs glyphicons text_underline glyphicons-12" data-event="click" data-label="styleU"></span>
						</div>
					</div>
					<div class="col-xs-6">
						<small><?php echo language('designer_clipart_edit_text_align', $lang); ?></small>
						<div id="text-align">
							<span id="text-align-left" class="text-update btn btn-default btn-xs glyphicons align_left glyphicons-12" data-event="click" data-label="alignL"></span>
							<span id="text-align-center" class="text-update btn btn-default btn-xs glyphicons align_center glyphicons-12" data-event="click" data-label="alignC"></span>
							<span id="text-align-right" class="text-update btn btn-default btn-xs glyphicons align_right glyphicons-12" data-event="click" data-label="alignR"></span>
						</div>
					</div>
				</div>			
				<div class="clear"></div>					
				<div class="row toolbar-action-outline">
					<div class="col-xs-6">
						<small><?php echo language('outline', $lang); ?></small>
						<div class="option-outline">							
							<div class="list-colors">
								<a class="dropdown-color bg-none" data-label="outline" data-placement="top" data-original-title="<?php echo language('designer_click_to_change_color', $lang); ?>" href="javascript:void(0)" data-color="none">
									<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>
								</a>
							</div>
							<div class="dropdown-outline">
								<a data-toggle="dropdown" class="dg-outline-value" href="javascript:void(0)"><span class="outline-value pull-left">0</span> <span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s pull-right"></span></a>
								<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
									<li><div id="dg-outline-width"></div></li>
								</ul>
							</div>
						</div>
					</div>
				</div>			
				<div class="row" style="display:none;">
					<div class="col-lg-12">
						<small><?php echo language('designer_clipart_edit_adjust_shape', $lang); ?></small>
						<div id="dg-shape-width"></div>
					</div>
				</div>							
				<div class="clear"></div>			
				<div class="row toolbar-action-size">
					<div class="col-xs-3 col-lg-3 align-center">
						<div class="form-group">
							<small><?php echo language('width', $lang); ?></small>
							<input type="text" size="2" id="text-width" readonly disabled>
						</div>
					</div>
					<div class="col-xs-3 col-lg-3 align-center">
						<div class="form-group">
							<small><?php echo language('height', $lang); ?></small>
							<input type="text" size="2" id="text-height" readonly disabled>
						</div>
					</div>
					<div class="col-xs-6 col-lg-6 align-left">
						<div class="form-group">
							<small><?php echo language('designer_clipart_edit_unlock_proportion', $lang); ?></small><br />
							<input type="checkbox" class="ui-lock" id="text-lock" />
						</div>
					</div>
				</div>			
				<div class="row toolbar-action-rotate">					
					<div class="form-group col-lg-12">
						<div class="row">
							<div class="col-xs-6 col-lg-6">
								<small><?php echo language('rotate', $lang); ?></small>
							</div>
							<div class="col-xs-6 col-lg-6 align-right">
								<span class="rotate-values"><input type="text" value="0" class="input-small rotate-value" id="text-rotate-value" />&deg;</span>
								<span class="rotate-refresh glyphicons refresh"></span>
							</div>
						</div>						
					</div>
				</div>			
				<div class="row toolbar-action-functions">	
					<div class="col-lg-12">
						<span class="btn btn-default btn-xs" onclick="design.item.flip('x')">
							<i class="glyphicons transfer glyphicons-12"></i>
							<?php echo language('designer_clipart_edit_flip', $lang); ?>
						</span>
						<span class="btn btn-default btn-xs" onclick="design.item.center()">
							<i class="glyphicons align_center glyphicons-12"></i>
							<?php echo language('designer_clipart_edit_center', $lang); ?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<!-- END clipart edit options -->			
		<!-- BEGIN team edit options -->
		<div id="options-add_item_team" class="dg-options">
			<div class="dg-options-toolbar">
				<div aria-label="First group" role="group" class="btn-group btn-group-lg">
					<button class="btn btn-default" type="button" data-type="name-number">
						<i class="glyphicons soccer_ball glyphicons-small"></i> <small class="clearfix"><?php echo language('add_name', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="teams">
						<i class="fa fa-users"></i> <small class="clearfix"><?php echo language('designer_teams', $lang); ?></small>
					</button>
					<button class="btn btn-default" type="button" data-type="add-list">
						<i class="fa fa-user"></i> <small class="clearfix"><?php echo language('add_team', $lang); ?></small>
					</button>						
				</div>
			</div>			
			<div class="dg-options-content">
				<input type="hidden" id="team-height" value="">
				<input type="hidden" id="team-width" value="">
				<input type="hidden" id="team-rotate-value" value="0">
				<div class="row toolbar-action-name-number">
					<div class="col-md-12 position-static">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="team_add_name" onclick="design.team.addName(this)" autocomplete="off"> <strong><?php echo language('add_name', $lang); ?></strong>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" id="team_add_number" onclick="design.team.addNumber(this)" autocomplete="off"> <strong><?php echo language('designer_clipart_edit_add_number', $lang); ?></strong>
							</label>
						</div>						
						<div class="form-group row">
							<div class="col-xs-3 col-md-3 position-static">
								<div class="list-colors">
									<a class="dropdown-color" id="team-name-color" data-placement="right" title="<?php echo language('designer_click_to_change_color', $lang); ?>" href="javascript:void(0)" data-color="000000" data-label="colorT" style="background-color:black">
										<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>
									</a>
								</div>
							</div>
							<div class="col-xs-9 col-md-9">
								<div data-toggle="modal" data-target="#dg-fonts" class="dropdown">
									<a href="javascript:void(0)" class="pull-left" id="txt-team-fontfamly"><?php echo language('designer_clipart_edit_arial', $lang); ?></a>
									<span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s pull-right"></span>
								</div>
							</div>
						</div>
					</div>
				</div>				
				<div class="row toolbar-action-teams">
					<div class="col-md-12">
						<span class="help-block">
							<?php echo language('designer_clipart_edit_enter_your_full_list', $lang); ?>
						</span>
					</div>					
					<div class="col-md-12">
						<div class="clear-line"></div><br>
					</div>					
					<div class="col-md-12 div-box-team-list">
						<table id="item_team_list" class="table table-bordered">
							<thead>
								<tr>
									<td width="70%"><strong><?php echo language('name', $lang); ?></strong></td>
									<td width="10%"><strong><?php echo language('number', $lang); ?></strong></td>
									<td width="20%"><strong><?php echo language('size', $lang); ?></strong></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td align="left"> </td>
									<td align="center"> </td>
									<td align="center"> </td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="clear-line"></div><br>
				<div class="row toolbar-action-add-list">
					<div class="col-md-12">
						<center><button class="btn btn-primary input-sm" data-target="#dg-item_team_list" data-toggle="modal" type="button"><?php echo language('designer_clipart_edit_add_list_name', $lang); ?></button><center>
					</div>
				</div>
			</div>
		</div>
		<!-- END team edit options -->
	</div>
</div>

<!-- BEGIN colors system -->
<div class="o-colors" style="display:none;">		
	<div class="other-colors"></div>
</div>
<!-- END colors system -->

<div id="cacheText"></div>	

<!-- Add Category -->
<div class="modal fade" id="dg-category" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Add Category</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-top:-22px">&times;</button>						
			</div>
			<div class="modal-body add-category" style="margin:0px 30px;">
				<form class="form-horizontal" id="add-category" method="post" action="<?php echo base_url('/admin/designClipart/savecategory'); ?>">
					<div class="row">
						<div class="form-group row">
						  <label for="example-text-input" class="col-xs-4 col-form-label">Category Name</label>
						  <div class="col-xs-8">
							<input class="form-control" type="text" name="catname">
						  </div>
						</div>
						<div class="form-group row">
						  <label for="example-search-input" class="col-xs-4 col-form-label">Category Slug</label>
						  <div class="col-xs-8">
							<input class="form-control" type="text" name="catslug">
						  </div>
						</div>	
						<div class="form-group">
							<label class="col-sm-4 col-form-label"><?php echo lang('description'); ?></label>
							<div class="col-sm-8">
								<textarea class="form-control textarea-tinymce" name="description"></textarea>
							</div>
						</div>						
					</div>
					<div class="row"><button type="submit" class="btn btn-primary">Save Category</button></div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- Add Category -->

<div class="row admindesign" id="dg-designer">
	<div id="dg-mask" class="loading"></div>
	<div id="frmmsg"></div>
	<div class="col-md-4">
		<div style="margin-top: 25px;" class="imageinfo">
			<div class="form-group row">
			  <label for="example-text-input" class="col-xs-4 col-form-label">Title* </label>
			  <div class="col-xs-8">
				<input class="form-control required" type="text" placeholder="Enter Image Title " name="title" id="title">
			  </div>
			</div>
			<div class="form-group row">
			  <label for="example-text-input" class="col-xs-4 col-form-label">Slug* </label>
			  <div class="col-xs-8">
				<input class="form-control required" type="text" placeholder="Enter Slug Title" id="slug" id="slug">
			  </div>
			</div>
			<div class="form-group row">
			  <label for="example-text-input" class="col-xs-4 col-form-label">Price* </label>
			  <div class="col-xs-8">			
				<div class="form-group">
					<label class="sr-only" for="amount">Amount (in dollars)</label>
					<div class="input-group">
					  <div class="input-group-addon">$</div>
					  <input type="text" class="form-control required" name="price" id="price" placeholder="Amount">
					  <div class="input-group-addon">.00</div>
					</div>
				  </div>
			  </div>
			</div>  
			<div class="form-group row">
			  <label for="example-text-input" class="col-xs-4 col-form-label">Select Category</label>
			  <div class="col-xs-8">		  
				<select class="form-control" name="imagecat" id="imagecat">
				<option value="0">Select Category</option>
				 <?php
				 //print_r($designCategories);
					if(!empty($designCategories)){
						foreach($designCategories as $val){
						  echo '<option value="'.$val->id.'">'.$val->title.'</option>';
						}									
					}							  
				 ?>  
				</select>	
			  </div>			
			</div>			
		</div>		
		<div id="dg-left" class="width-100">
			<div class="dg-box width-100">
				<ul class="menu-left">
					<li>
						<a href="javascript:void(0)" class="add_item_text" title="">
							<i class="glyphicons text_bigger"></i> <?php echo language('add_text', $lang); ?>
						</a>
					</li>					
					<li>
						<a href="javascript:void(0)" class="add_item_clipart" title="" data-toggle="modal" data-target="#dg-cliparts">
							<i class="glyphicons picture"></i> <?php echo language('add_art', $lang); ?>
						</a>
					</li>
					<li>
						<a href="javascript:void(0)" title="" data-toggle="modal" data-target="#dg-myclipart">
							<i class="glyphicons cloud-upload"></i> <?php echo language('designer_menu_upload_image', $lang); ?>
						</a>
					</li>					
				</ul>
			</div>
			
			<div class="dg-box width-100 div-layers no-active">
				<div class="layers-toolbar">
					<button type="button" class="btn btn-default">
						<i class="fa fa-long-arrow-down"></i>
						<i class="fa fa-long-arrow-up"></i>
					</button>
					<button type="button" class="btn btn-default btn-sm">
						<i class="fa fa-angle-right"></i>						
					</button>
				</div>					
				<div class="accordion">
					<h3><?php echo language('designer_menu_login_layers', $lang); ?></h3>
					<div id="dg-layers">
						<ul id="layers">									
						</ul>
					</div>
				</div>
			</div>
		</div>		
		<button type="button" class="btn btn-primary" onclick="design.ajax.addJs(this)">Save Design</button>
		<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#dg-category">Add Category</button>
	</div>
	<div class="col-md-8">
		<!-- Begin sidebar -->
		<div id="test"></div>
		<!-- design area -->
		<div id="design-area" class="div-design-area">
			<div id="app-wrap" class="div-design-area">
				<!-- begin front design -->					
				<div id="view-front" class="labView active">
					<div class="product-design"><img class="modelImage" id="front-img-images-0" src="/assets/transparent.png" style="width: 300px; height: 300px;z-index: auto;"></div>
					<div class="design-area" style="height: 300px; width: 300px; border-radius: 0px; z-index: 200;"><div class="content-inner"></div></div>
				</div>				
				<!-- end front design -->				
			</div>
		</div>	
	</div>
</div>
<script type="text/javascript">
	var baseURL = '<?php echo base_url(); ?>';
	var siteURL = '<?php echo site_url(); ?>';
	var urlCase = '<?php echo base_url('image-tool/thumbs.php'); ?>';
	var edit_text_title = '<?php echo language('edit_text', $lang);?>';
	var team_number_title = '<?php echo language('team_number', $lang);?>';
	var confirm_reset_msg = '<?php echo language('confirm_reset_msg', $lang);?>';
	var add_qty_or_size_msg = '<?php echo language('add_qty_or_size_msg', $lang);?>';
	var minimum_qty_msg = '<?php echo language('minimum_qty_msg', $lang);?>';
	var please_add_qty_or_size_msg = '<?php echo language('please_add_qty_or_size_msg', $lang);?>';
	var please_try_again_msg = '<?php echo language('please_try_again_msg', $lang);?>';
	var select_a_color_msg = '<?php echo language('select_a_color_msg', $lang);?>';
	var tick_the_checkbox_msg = '<?php echo language('tick_the_checkbox_msg', $lang);?>';
	var choose_a_file_upload_msg = '<?php echo language('choose_a_file_upload_msg', $lang);?>';
	var myAccount = '';
	var temp = 0;
	var logOut = '';
	var uploadSize = [];
	uploadSize['max']  = '<?php echo settingValue($setting, 'site_upload_max', '10'); ?>';
	uploadSize['min']  = '<?php echo settingValue($setting, 'site_upload_min', '0.5'); ?>';
</script>
<script src="<?php echo base_url('assets/admin/js/designAdmin.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/design_upload.js"></script>