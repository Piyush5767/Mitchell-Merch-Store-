<link rel="stylesheet" type="text/css" href="<?php echo APPPATH .'modules/productslider/assets/owl.carousel.css'; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo APPPATH .'modules/productslider/assets/owl.transitions.css'; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo APPPATH .'modules/productslider/assets/productslider.css'; ?>">  
<script src="<?php echo APPPATH .'modules/productslider/assets/owl.carousel.min.js'; ?>" type="text/javascript"></script>
<script type="text/javascript">
jQuery(function(){
jQuery(".slider-owl").owlCarousel({
		// Most important owl features
			items : 4,
			itemsCustom : false,
			itemsDesktop : [1199,4],
			itemsDesktopSmall : [980,3],
			itemsTablet: [768,1],
			itemsTabletSmall: false,
			itemsMobile : [479,1],
			singleItem : false,
			itemsScaleUp : false,
			//Basic Speeds
			slideSpeed : 200,
			paginationSpeed : 800,
			rewindSpeed : 1000,
		 
			//Autoplay
			autoPlay : false,
			stopOnHover : false,
		 
			// Navigation
			navigation : true,
			navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
			rewindNav : true,
			scrollPerPage : false,
		 
			//Pagination
			pagination : false,
			paginationNumbers: false,
		 
			// Responsive 
			responsive: true,
			responsiveRefreshRate : 200,
			responsiveBaseWidth: window,
		 
			//Lazy load
			lazyLoad : false,
			lazyFollow : true,
			lazyEffect : "fade",
		 
			//Auto height
			autoHeight : false,
		 
			//JSON 
			jsonPath : false, 
			jsonSuccess : false,
		 
			//Mouse Events
			dragBeforeAnimFinish : true,
			mouseDrag : true,
			touchDrag : true,
		 
			//Transitions
			transitionStyle : false,
		 
			// Other
			addClassActive : false,
		}); 	
});
</script>
<section class="home-promotion-product home-product parten-bg">
	<div class="container">
		<div class="row">
			<div class="block-title-w">
				<h4 class="block-title text-center"><?php echo $getPrdObj->title; ?></h4> 
				<span class="text-center icon-title" style="width: 415px;">
					<span class="line">&nbsp;</span>
					<span class="fa fa-star" style="padding: 5px 0px 5px 5px;">&nbsp;</span>
				</span>
			</div>
			<ul class="slider-w slider-owl">
				<?php
				// get symbol
				if (!isset($setting->currency_symbol))
					$setting->currency_symbol = '$';
				$symbol = $setting->currency_symbol;	
				
				if($m_products->num_rows() >0 ){
					foreach($m_products->result() as $prds_key=>$prds_row){ ?>
						<li class="pro-item">
							<div class="product-image-action">
								<img src="<?php echo base_url($prds_row->image); ?>" alt="Grouper Business card">
								<div class="action">
									<a href="<?php echo base_url('product/'. $prds_row->id .'-'. $prds_row->slug); ?>" data-toggle="tooltip" data-placement="top" class="add-to-cart gbtn" title="Add to cart">
										<i class="fa fa-shopping-cart"></i> 
									</a>									
								</div>
								<?php if($prds_row->sale_price > 0): ?>
									<span class="product-icon sale-icon">sale!</span>
								<?php endif; ?>
							</div>
							<div class="product-info">
								<a href="<?php echo base_url('product/'. $prds_row->id .'-'. $prds_row->slug); ?>" title="<?php echo $prds_row->title; ?>" target="_blank" class="product-name"><?php echo $prds_row->title; ?></a> 
								<div class="price-box">
									<span class="normal-price"><?php echo $symbol.number_format($prds_row->price, 2, '.', ','); ?></span>
								</div>
							</div>
						</li>						
			<?php	}
				}?>				
			</ul>
		</div>
	</div>
</section>