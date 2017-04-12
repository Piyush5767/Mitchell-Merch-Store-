<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="newsletter-module">
	<h4><?php echo lang('newsletter_subscribe');?></h4>
	<div class="msg"></div>
	<form class="subnewsletter" id="subscribe-<?php echo $id;?>" action="" >
		<input type="text" name="placeholder" id="subscribeEmail-<?php echo $id;?>" placeholder="<?php echo $newsletter->placeholder; ?>" class="validate required" />
		<button type="submit" class="<?php echo $newsletter->buttonclass; ?>"><?php echo $newsletter->buttontxt; ?></button>
	</form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/additional-methods.min.js" type="text/javascript"></script>
<script type="text/javascript">
	jQuery("#subscribe-<?php echo $id;?>").validate({			
			rules: {
				placeholder: {
					required: true,
					email: true,
					remote: {
						url: '<?php echo site_url('newsletter/newsletter/checkemail'); ?>',
						type: "post",
						data: {
						  email: function() {
							return $( "#subscribeEmail-<?php echo $id;?>").val();
						  },
						}
					  }					
				},
			},
			messages: {
				placeholder:{
					required:"Email id is required",
					email:"Please enter a valid email address",
					remote: "Email address already exists", 
				} 	
			},
			submitHandler: function(form,event) {	
				event.preventDefault();
				jQuery.ajax({
					url: '<?php echo site_url('newsletter/newsletter/addSubscriber'); ?>',
					type: 'post',
					data: {email:jQuery("#subscribeEmail-<?php echo $id;?>").val()},
					success: function(data){
						jQuery('.msg').html(data).fadeIn().delay(1000).fadeOut();
						jQuery("#subscribeEmail-<?php echo $id;?>").val('');					
					},            
				});
			},   
	});			
</script>