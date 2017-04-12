(function($){
	var webadd = location.protocol+'//'+location.host;
	$(document).on('submit','#addclrfrm',function(event){
		$('#clrmsg').html('');
		event.preventDefault();		
		var colorTitle = document.getElementById('add-color-title').value;
		var id = document.getElementById('addclrfrm');
		var color = document.getElementById('add-color-color').value;
		var dataObj = new FormData(id);
		dataObj.append('colorcode',color);
		if(colorTitle != ''){
			$.ajax({				
				url: webadd+'/colors/saveColor',
				type: "POST",
				data: dataObj,
				contentType: false,
				cache: false,
				processData:false,
				success: function(result){									
					var resobj = $.parseJSON(result);					
					$('#clrmsg').html(resobj.res);
					if(resobj.result == 1){
						dgUI.product.addHex();
						$("#addclrfrm")[0].reset();						
						userList.add({id:resobj.colorCode,singleobj: '<div class="remove remove-item-btn" style="position: absolute;left: 20px;margin-left: 90px;margin-top: 10px;"><i class="fa fa-times" aria-hidden="true"></i></div><a class="box-color" href="javascript:void(0);" onclick="dgUI.product.addColor(\'' + resobj.colorTitle + '\',\'' + resobj.colorCode + '\')"><span class="color-bg" style="background-color:#'+resobj.colorCode+'"></span><span class="name">'+resobj.colorTitle+'</span><span class="colorclear">'+resobj.colorCode+'</span></a>'});
					}
				},
			});			
		}else{
			$('#clrmsg').html('<p class="text-danger"> Title field should not be blank</p>');
		}
	});

	$(document).on('click','.remove-item-btn',function(e) {
		var id = $(this).closest('li').find('.id').text();
		if(id != ''){
			$.ajax({				
				url: webadd+'/colors/removeclr',
				type: "POST",
				data: {clrid:id},
				success: function(result){									
					var resobj = $.parseJSON(result);					
					$('#clrmsg').html(resobj.res);
					userList.remove('id', id);
				},
			});			
		}		
	});
	
	$(document).on('submit','#addCat',function(event){
		event.preventDefault();	
		var id = document.getElementById('addCat');
		var colorCat = document.getElementById('color-category').value;
		var dataObj = new FormData(id);
		if(colorCat != ''){
			$.ajax({				
					url: webadd+'/colors/colorCategory',
					type: "POST",
					data: dataObj,
					contentType: false,
					cache: false,
					processData:false,
					success: function(result){
						$("#addCat")[0].reset();
						var resobj = $.parseJSON(result);
						$('#clrmsg').html(resobj.res);	
						if(resobj.result == 1){							
							$('.colorcatsel').html(resobj.options);	
						}					
					},
				});				
		}	
	});	
	

})(jQuery);
