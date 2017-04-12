function Redirect (url) {
	var ua        = navigator.userAgent.toLowerCase(),
		isIE      = ua.indexOf('msie') !== -1,
		version   = parseInt(ua.substr(4, 2), 10);

	// Internet Explorer 8 and lower
	if (isIE && version < 9) {
		var link = document.createElement('a');
		link.href = url;
		document.body.appendChild(link);
		link.click();
	}

	// All other browsers
	else { window.location.href = url; }
}  

jQuery(document).ready(function(){
	//jQuery('#shippRtes').DataTable();	
});
var hostname = location.protocol+'//'+location.host;
var printful = {
	ajaxurl: '',
	calculateShipping:function(id){
		jQuery('.msg').html(' ');
		jQuery('.loading2').css('display','block');
		var form = true;
		var label,msg = '';
		  jQuery("#calculateShipping .required").each(function(){
			if(!jQuery.trim(jQuery(this).val())) {
				form = false;
				label = jQuery(this).closest(".form-group").find("label").text();
				msg = '<p class="text-danger">Please enter required field: <strong>'+label+'</strong></p>';
				jQuery('.loading2').css('display','none');
			}    
		  });	
		
		if(form == false){
			jQuery('.msg').html(msg);	
		}else{
						
			var element = document.getElementById(id);
			formData = new FormData(element);
			formData.append('type','checkoutShipping');
			jQuery.ajax({				
				url: '/PrintfulRequest/shippingRates',
				type: "POST",
				data: formData,
				contentType: false,
				cache: false,
				processData:false,
				success: function(result){					
					var table = jQuery('#shippRtes').DataTable({
						"bDestroy": true,	
						"order": [[ 1, "asc" ]],
						"columnDefs": [{
										"targets": 2,
										"orderable": false
										}],						
					});		
					table.clear();
					var delta = result;
					table.rows.add(jQuery(result)).draw();	
					jQuery('.shipprates').css('display','block');					
					jQuery('.loading2').css('display','none');
				},
			});	
		}
	},
	checkout:function(id){
		var shipRate = '';
		jQuery('.msg').html(' ');
		jQuery('.loading2').css('display','block');
		var form = true;
		var label,msg = '';
		  jQuery("#calculateShipping .required").each(function(){
			if(!jQuery.trim(jQuery(this).val())) {
				form = false;
				label = jQuery(this).closest(".form-group").find("label").text();
				msg = '<p class="text-danger">Please enter required field: <strong>'+label+'</strong></p>';
				jQuery('.loading2').css('display','none');
			}    
		  });			
		
		jQuery('input[name="shippingrate"]').each(function(){
			if (jQuery(this).is(":checked")){
				shipRate = jQuery(this).val();
			}			
		});	
		if(form == false){
			jQuery('.msg').html(msg);	
		}else if(shipRate == ''){
			jQuery('.loading2').css('display','none');
			alert('Please select shipping.');
			return false;
		}else{
			jQuery('.loading2').css('display','block');
			var element = document.getElementById(id);
			formData = new FormData(element);
			formData.append('type','addShipping');	
			formData.append('shipping',shipRate);			
			jQuery.ajax({				
				url: '/PrintfulRequest/shippingRates',
				type: "POST",
				data: formData,
				contentType: false,
				cache: false,
				processData:false,
				success: function(result){
					var result  = JSON.parse(result);
					jQuery('.loading2').css('display','none');
					if(result.response == true){
						Redirect(hostname+'/cart/checkout')						
					}else if(result.response == false){
						jQuery('.msg').html(result.msg);	
						jQuery('#shippingRates').html(result.updateshipping);						
					}
				},
			});			
		}
	}
};	
