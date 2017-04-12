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
var host = location.protocol+'//'+location.host;
jQuery(window).load(function(){
	jQuery('.progressloader').fadeOut();
});
jQuery(document).ready(function(){	
	if(jQuery('.specializ').length > 0){
		jQuery('.skillbar').each(function(){
			jQuery(this).find('.skillbar-bar').animate({
				width:jQuery(this).attr('data-percent')
			},6000);
		});
	}
	if(jQuery('.home-capabititie').length > 0)
	{
		var checkRun = true;
		var checkWidths = jQuery(window).width();
		function inViews(){
				var bottom_of_object = jQuery('.block-capabititie-w').offset().top;
				var bottom_of_window = jQuery(window).scrollTop() + jQuery(window).height();
				if((bottom_of_window > bottom_of_object) && (checkWidths > 767)){
					return true;
				}
		};

		function trustView(elem){
			var bottom_of_object = jQuery(elem).offset().top;
			var bottom_of_window = jQuery(window).scrollTop() + jQuery(window).height();
			if(bottom_of_window > bottom_of_object){
				return true;
			}
		};
		function addClassView(addClass, elem){
			if (trustView(elem) == true) {
				$(addClass).addClass('inview');
			}else{
				$(addClass).removeClass('inview');
			};
		}

		if(checkWidths < 768){
			var doughnutData = [
				{value:90,color:"#e92890"},
				{value:100-90,color:"rgba(0,0,0,0)"}
			];
			jQuery("#myDoughnut").doughnutit({
				dnData: doughnutData,
				dnSize: 187, 
				dnInnerCutout: 90,
				dnAnimation: true,
				dnAnimationSteps: 60,
				dnAnimationEasing: 'linear',
				dnStroke: false,
				dnShowText: true,
				dnFontSize: '30px',
				dnFontColor: "#e92890",
				dnText: '90%', 
				dnFontOffset:20,
				dnStartAngle: 90,
				dnCounterClockwise: false, 
			});// End Doughnut
			var doughnutData = [
				{value:75,color:"#fbc443"},
				{value:100-75,color:"rgba(0,0,0,0)"}
			];
			jQuery( "#myDoughnut2" ).doughnutit({
				dnData: doughnutData,
				dnSize: 187,
				dnInnerCutout: 90,
				dnAnimation: true,
				dnAnimationSteps: 60,
				dnAnimationEasing: 'linear',
				dnStroke: false,
				dnShowText: true,
				dnFontOffset:20,
				dnFontSize: '30px',
				dnFontColor: "#fbc443",
				dnText: '75%',
				dnStartAngle: 90,
				dnCounterClockwise: false,
			});// End Doughnut
			var doughnutData = [
				{value:80,color:"#25bce9"},
				{value:100-80,color:"rgba(0,0,0,0)"}
			];
			jQuery( "#myDoughnut3" ).doughnutit({
				dnData: doughnutData,
				dnSize: 187,
				dnInnerCutout: 90,
				dnAnimation: true,
				dnAnimationSteps: 60,
				dnAnimationEasing: 'linear',
				dnStroke: false,
				dnFontOffset:20,
				dnShowText: true,
				dnFontSize: '30px',
				dnFontColor: "#25bce9",
				dnText: '80%',
				dnStartAngle: 90,
				dnCounterClockwise: false,
			});
			var doughnutData = [
				{value:65,color:"#94eae3"},
				{value:100-65,color:"rgba(0,0,0,0)"}
			];
			jQuery( "#myDoughnut4" ).doughnutit({
				dnData: doughnutData,
				dnSize: 187,
				dnInnerCutout: 90,
				dnAnimation: true,
				dnAnimationSteps: 60,
				dnFontOffset:20,
				dnAnimationEasing: 'linear',
				dnStroke: false,
				dnShowText: true,
				dnFontSize: '30px',
				dnFontColor: "#94eae3",
				dnText: '65%',
				dnStartAngle: 90,
				dnCounterClockwise: false,
			});
				
		}
		jQuery(window).on('scroll', function() {  
		   inView(); 
		   addClassView('.trust-w','.trust-w');
		}); 
			
			
		function inView(){   
			var b = inViews();
			if(b == true && checkRun == true){
				checkRun = false;
				var doughnutData = [
					{value:90,color:"#fd5b4e"},
					{value:100-90,color:"rgba(0,0,0,0)"}
				];
				jQuery("#myDoughnut" ).doughnutit({
					dnData: doughnutData,
					dnSize: 187, 
					dnInnerCutout: 90,
					dnAnimation: true,
					dnAnimationSteps: 60,
					dnAnimationEasing: 'linear',
					dnStroke: false,
					dnShowText: true,
					dnFontSize: '24px',
					dnFontColor: "#fd5b4e",
					dnText: '90%', 
					dnFontOffset:20,
					dnStartAngle: 90,
					dnCounterClockwise: false, 
				});// End Doughnut
				var doughnutData = [
					{value:75,color:"#ffa63e"},
					{value:100-75,color:"rgba(0,0,0,0)"}
				];
				jQuery( "#myDoughnut2" ).doughnutit({
					dnData: doughnutData,
					dnSize: 187,
					dnInnerCutout: 90,
					dnAnimation: true,
					dnAnimationSteps: 60,
					dnAnimationEasing: 'linear',
					dnStroke: false,
					dnShowText: true,
					dnFontOffset:20,
					dnFontSize: '24px',
					dnFontColor: "#ffa63e",
					dnText: '75%',
					dnStartAngle: 90,
					dnCounterClockwise: false,
				});// End Doughnut
				var doughnutData = [
					{value:80,color:"#25bce9"},
					{value:100-80,color:"rgba(0,0,0,0)"}
				];
				jQuery( "#myDoughnut3" ).doughnutit({
					dnData: doughnutData,
					dnSize: 187,
					dnInnerCutout: 90,
					dnAnimation: true,
					dnAnimationSteps: 60,
					dnAnimationEasing: 'linear',
					dnStroke: false,
					dnFontOffset:20,
					dnShowText: true,
					dnFontSize: '24px',
					dnFontColor: "#25bce9",
					dnText: '80%',
					dnStartAngle: 90,
					dnCounterClockwise: false,
				});
				var doughnutData = [
					{value:65,color:"#5cc99f"},
					{value:100-65,color:"rgba(0,0,0,0)"}
				];
				jQuery( "#myDoughnut4" ).doughnutit({
					dnData: doughnutData,
					dnSize: 187,
					dnInnerCutout: 90,
					dnAnimation: true,
					dnAnimationSteps: 60,
					dnFontOffset:20,
					dnAnimationEasing: 'linear',
					dnStroke: false,
					dnShowText: true,
					dnFontSize: '24px',
					dnFontColor: "#5cc99f",
					dnText: '65%',
					dnStartAngle: 90,
					dnCounterClockwise: false,
				});
				b = false;  
			}
		};  
	}

  jQuery(".numbers-row").append('<div class="inc cartbutton"><i class="fa fa-plus"></i></div><div class="dec cartbutton"><i class="fa fa-minus"></i></div>');
  jQuery(".cartbutton").on("click", function() {
    var $button = jQuery(this);
    var oldValue = $button.parent().parent().find("input").val();
    if ($button.find('i.fa-plus').length !=0){
  	  var newVal = parseFloat(oldValue) + 1;
  	} else {
	   // Don't allow decrementing below zero
      if (oldValue > 1) {
        var newVal = parseFloat(oldValue) - 1;
	    } else {
        newVal = 1;
      }
	}
    $button.parent().parent().find("input").val(newVal);
  });	
});	