var __uploadedFile = '';
$(document).ready(function(){
	var url = document.location.toString();
	if (url.match('#certificate')) {
		setTimeout(function(){
			$('#certificateBtn').trigger("click");
		}, 1000);
		
	}

	$('#testimonial_name').keypress(function(e){
		preventNumbers(e);
	});

	
    /* Function defined in System JS for updating characters left */
    validateMaxLength('meta_description'); 
    // jquery script for showing and hiding active banner
	$(document).on('click','.banner-item',function(){
		var click_id = this.id;
		$('a.banner-item').removeClass('active-banner');
	    $(this).addClass('active-banner');
		$.ajax({
			    url: admin_url+'environment/change_banner_status',
			    type: "POST",
			    data:{"is_ajax":true,'banner_id':click_id},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            //$('#banner_message').prepend(renderPopUpMessage('success', data['message']));
			            //scrollToTopOfPage();
			        }
			        else
			        {
			            $('#banner_message').prepend(renderPopUpMessage('error', 'Error occured'));
			            scrollToTopOfPage();
			        }
			    }
		});
	});

    /* Banner Image Upload */
    var __uploading_file_banner = new Array();
    $(document).on('change', '#site_banner_btn', function(e){
    	$('#popUpMessage').remove();
    	var _URL = window.URL || window.webkitURL;
    	var file, img;
	    if ((file = this.files[0])) {
	        img = new Image();
	        img.onload = function () {
	        	//console.log(file['type']);
	            if(this.width >= 1647 && this.height >= 526)
	            {
	            	if(file['type']=="image/jpg" || file['type']=="image/jpeg")
	            	{
	            		$('#progress_div').css('display','block');
				        __uploading_file_banner = e.currentTarget.files;
				        //console.log(e.currentTarget.files);
				        if( __uploading_file_banner.length > 1 )
				        {
				            lauch_common_message('Error Occured', 'You are not allowed to upload more that one file.');
				            return false;
				        }
				        var i                           = 0;
				        var uploadURL                   = admin_url+"environment/upload_banner_image" ; 
				        var fileObj                     = new processFileName(__uploading_file_banner[i]['name']);
				        var param                       = new Array;
				            param["file_name"]          = fileObj.uniqueFileName();        
				            param["extension"]          = fileObj.fileExtension();
				            param["file"]               = __uploading_file_banner[i];
				        uploadFiles(uploadURL, param, uploadBannerImageCompleted);
	            	}
	            	else
	            	{
	            		$('#banner_message').prepend(renderPopUpMessage('error', 'Please upload a JPG file'));
			        	$('#site_banner_btn').val('');
		    			scrollToTopOfPage();
	            	}
		        }
		        else
		        {
		        	$('#banner_message').prepend(renderPopUpMessage('error', 'Please upload an image of size 1647 x 526 '));
		        	$('#site_banner_btn').val('');
	    			scrollToTopOfPage();
		        }
	        };
	        img.src = _URL.createObjectURL(file);
	    }
    	
    });

    function uploadBannerImageCompleted(response)
    {
       $('#progress_div').css('display','none');
       var data = $.parseJSON(response);
       
	   var banner_html = '';
	   	   banner_html+= '<li class="col-sm-3">'; 
	       banner_html+= '<a href="javascript:void(0)" class="banner-thumb banner-item" id="'+data['banner_id']+'">';
           banner_html+= '<img src="'+data['user_image']+'" width="100%">';
           banner_html+= '<span class="triangle"><i class="icon icon-ok-circled"></i></span>';
           banner_html+= '</a>';
           banner_html+= '</li>';
          
       	$('#banner_list').prepend(banner_html);
       	$('#banner_message').prepend(renderPopUpMessage('success', data['message']));
	    scrollToTopOfPage();
	    $('#site_banner_btn').val('');
	    $('.progress-bar').css('width','0');
	}

    
    $(document).on('change', '#import_certificate', function(e){
        //console.log(e.currentTarget.files[0]);
        $('#percentage_bar').hide();
        var i                       = 0;
        __uploadedFile              = e.currentTarget.files[i];
        $('#upload_certificate_file').val(__uploadedFile['name']);
    });

	$('.btn-save-geteway').on('click',function(){

		cleanPopUpMessage();
		var fields 			= $(this).attr('data-key');
		var key_val 		= $(this).attr('data-val');
		var gateway_status	= '0';
		var baseInfo		= {};
		var as_id 			= $('#as_key_'+key_val).val();
		var errorCount 		= 0;
		var errorMessage	= '';

		if(fields!=''){

			fields 					= fields.split(",");
			var fieldInfo 			= {};

			for(var i=0;i<fields.length;i++){

				var title 			= fields[i];
				var inputField 		= '#'+title+'_'+key_val;
				fieldInfo[title] 	= $(inputField).val();

				if(fieldInfo[title] == ''){

					errorCount++;
					errorMessage += 'Please enter '+title+' <br />';
				}
			}
		}
		if ($('#gateway_status_'+key_val).is(":checked"))
		{
			gateway_status 	= $('#gateway_status_'+key_val).val();
		}
		
		if( errorCount == 0 )
		{ 
			baseInfo['gateway_key'] = key_val;
			baseInfo['status']		= gateway_status;
			gateway_save(fieldInfo,as_id,baseInfo);
		}
		else
		{
			$('#collapseOne_'+key_val).prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}
	});
	/* Create Gateway button click */
    function gateway_save(key={},as_id='',base){

		$.ajax({
			url: admin_url+'environment/save',
			type: "POST",
			data:{"is_ajax":true,"inputs":key,"gateway_basic_id":as_id,"status":base.status,'gateway_key':base.gateway_key},
			success: function(response) {
				var data  = $.parseJSON(response);
				if(data['error'] == false)
				{
					$('#payment_message').prepend(renderPopUpMessage('success', data['message']));
					scrollToTopOfPage();
					setTimeout(function(){
						$('#payment_message').html('');
					}, 2000);
				}
			
			}
		});
		
	}
	
	/* Remove Testimonial Ends here*/

    // /* Delete confirmed */
    // $(document).on('click','#delete_confirm',function(){
    // 	var testimonial_id_new = $('#hidden_testimonial').val();
    // 	$('#RemoveTestimonial').modal('hide');
    // 	//alert(testimonial_id_new);
    // 	$.ajax({
	// 		    url: admin_url+'environment/remove_testimonial',
	// 		    type: "POST",
	// 		    data:{"is_ajax":true,'testimonial_id':testimonial_id_new},
	// 		    success: function(response) {
	// 		        var data  = $.parseJSON(response);
	// 		        if(data['error'] == false)
	// 		        {
	// 		        	$("#list_"+testimonial_id_new).remove();
	// 		            $('#testimonial_message').prepend(renderPopUpMessage('success', data['message']));
	// 		            scrollToTopOfPage();
	// 		        }
	// 		        else
	// 		        {
	// 		            $('#testimonial_message').prepend(renderPopUpMessage('error', data['message']));
	// 		            scrollToTopOfPage();
	// 		        }
	// 		    }
	// 	});
	// });
	
	

    /* Logo Update */
    var __uploading_file = new Array();
    $(document).on('change', '#site_logo_btn', function(e){
    	//showLoading();
    	$("#site_logo").attr('src',''+assets_url+'/images/loading.gif');
        __uploading_file = e.currentTarget.files;
        if( __uploading_file.length > 1 )
        {
            lauch_common_message('Error Occured', 'You are not allowed to upload more that one file.');
            return false;
        }
        saveWebSettings();
    });
    
    function saveWebSettings()
    {
        var i                           = 0;
        var uploadURL                   = admin_url+"environment/upload_logo_image" ; 
        var fileObj                     = new processFileName(__uploading_file[i]['name']);
        var param                       = new Array;
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
            param["file"]               = __uploading_file[i];
        uploadFiles(uploadURL, param, uploadUserImageCompleted);        
    }

    function uploadUserImageCompleted(response)
    {
	   var data = $.parseJSON(response);
	   var errorClass = 'success';
	   if(data.error && data.error != 'false')
	   {
			errorClass = 'error';
	   }
	   //console.log(data.error, 'uploadUserImageCompleted');
       $('#message_basic_div').prepend(renderPopUpMessage(errorClass, data['message']));
	   scrollToTopOfPage();
       $('#site_logo').attr('src', data['user_image']);
	}
	


	//site favicon

	 var __uploading_file = new Array();
	 $(document).on('change', '#site_favicon_btn', function(e){
		 //showLoading();
		 $("#site_favicon").attr('src',''+assets_url+'/images/loading.gif');
		 __uploading_file = e.currentTarget.files;
		 if( __uploading_file.length > 1 )
		 {
			 lauch_common_message('Error Occured', 'You are not allowed to upload more that one file.');
			 return false;
		 }
		 saveWebFavicoSettings();
	 });
	 
	 function saveWebFavicoSettings()
	 {
		 var i                           = 0;
		 var uploadURL                   = admin_url+"environment/upload_favicon" ; 
		 var fileObj                     = new processFileName(__uploading_file[i]['name']);
		 var param                       = new Array;
			 param["file_name"]          = fileObj.uniqueFileName();        
			 param["extension"]          = fileObj.fileExtension();
			 param["favicon"]            = __uploading_file[i];
		 uploadFiles(uploadURL, param, uploadFaviconCompleted);        
	 }
 
	 function uploadFaviconCompleted(response)
	 {
		var data = $.parseJSON(response);
		var errorClass = 'success';
		if(data.error && data.error != 'false')
		{
			 errorClass = 'error';
		}
		//console.log(data.error, 'uploadUserImageCompleted');
		$('#message_basic_div').prepend(renderPopUpMessage(errorClass, data['message']));
		scrollToTopOfPage();
		$('#site_favicon').attr('src', data['user_image']);
	 }

   	/* Basic Settings save button click */
    $(document).on('click','#save_basic_button',function(){

    	var banner_text 	  	= $('#banner_text').val(); 
    		//banner_text  		= banner_text.replace(/\r?\n/g, '<br />');
		var title_text 	  		= $('#title_text').val(); 
		var meta_description 	= $('#meta_description').val();

		var errorCount     =  0;
		var errorMessage   =  '';

		if(banner_text == '')
		{
			errorCount++;
			errorMessage += 'Please enter banner title <br />';
		}

		if(title_text == '')
		{
			errorCount++;
			errorMessage += 'Please enter site title <br />';
		}
		

		cleanPopUpMessage();
		if( errorCount == 0 )
		{  
			$.ajax({
			    url: admin_url+'environment/save',
			    type: "POST",
			    data:{"is_ajax":true,'hidden_basic_id':$('#hidden_basic_id').val(),'banner_text':banner_text, 'title_text':title_text, 'meta_description':meta_description},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#message_basic_div').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#message_basic_div').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#message_basic_div').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}

	});


	/* File Storage & CDN Settings */

	if($('#storage_s3_onoff').is(':checked')) 
	{
	    $('#div_aws_storage').show();
	    $('#storage_s3_onoff').val('on');
	    //$("#head_cdn").show();
	}
	else
	{
	    $('#div_aws_storage').hide();
	    $('#storage_s3_onoff').val('off');
	    //$("#head_cdn").hide();
	    //$("#div_cdn_storage").hide();
	    //$('#storage_cdn_onoff').prop('checked', false);
	    //$('#storage_cdn_onoff').val('off');
	}

	$('#storage_s3_onoff').click(function() {

	    if($('#storage_s3_onoff').is(':checked')) 
	    {
	        $('#div_aws_storage').show();
	    	$('#storage_s3_onoff').val('on');
	    	//$("#head_cdn").show();
	    }
	    else
	    {
	    	$('#div_aws_storage').hide();
		    $('#storage_s3_onoff').val('off');
		    //$("#head_cdn").hide();
		    //$("#div_cdn_storage").hide();
		    //$('#storage_cdn_onoff').prop('checked', false);
		    //$('#storage_cdn_onoff').val('off');
	    }
	});

	if($('#storage_cdn_onoff').is(':checked')) 
	{
	    $('#div_cdn_storage').show();
	    $('#storage_cdn_onoff').val('on');
	}
	else
	{
	    $('#div_cdn_storage').hide();
	    $('#storage_cdn_onoff').val('off');
	}

	$('#storage_cdn_onoff').click(function() {

	    if($('#storage_cdn_onoff').is(':checked')) 
	    {
	        $('#div_cdn_storage').show();
	    	$('#storage_cdn_onoff').val('on');
	    }
	    else
	    {
	    	$('#div_cdn_storage').hide();
	    	$('#storage_cdn_onoff').val('off');
	    }
	});

	/* Storage s3,CDN Save Button Click Function */
	$('#save_storage_button').click(function(){
		
		var storage_app_key_text 	  = $('#storage_app_key_text').val();
		var storage_secret_key_text   = $('#storage_secret_key_text').val();
		var storage_bucket_text   	  = $('#storage_bucket_text').val();
		var storage_cdn_text   	  	  = $('#storage_cdn_text').val();
		var storage_region_text   	  = $('#storage_region_text').val();

		var errorCount     =  0;
		var errorMessage   =  '';

		if($('#storage_s3_onoff').val()=='on')
		{
			if(storage_app_key_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter AWS app key <br />';
			}

			if(storage_secret_key_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter AWS secret key <br />';
			}

			if(storage_bucket_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter AWS bucket name <br />';
			}

			if(storage_region_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter Region <br />';
			}
		}

		if($('#storage_cdn_onoff').val()=='on')
		{
			if(storage_cdn_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter CDN url <br />';
			}
		}
		
		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			$.ajax({
			    url: admin_url+'environment/save',
			    type: "POST",
			    data:{"is_ajax":true,'storage_hidden_key':$('#storage_hidden_key').val(), 'cdn_hidden_key':$('#cdn_hidden_key').val(), 'storage_s3_onoff':$('#storage_s3_onoff').val(), 'storage_app_key_text':storage_app_key_text, 'storage_secret_key_text':storage_secret_key_text, 'storage_bucket_text':storage_bucket_text, 'storage_cdn_onoff':$('#storage_cdn_onoff').val(), 'storage_cdn_text':storage_cdn_text, 'storage_region_text':storage_region_text},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#storage_cdn_div').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#storage_cdn_div').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#storage_cdn_div').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}

	});
	/* Email settings code starts */

	if($('#s3_onoff').is(':checked')) 
	{
	    $('#s3_show_div').show();
	    $('#s3_onoff').val('on');
	    $('#smtp_onoff').prop('checked', false);
	    $('#smtp_onoff').val('off');
	    $('#smtp_show_div').hide();
	}
	else
	{
	    $('#s3_show_div').hide();
	    $('#s3_onoff').val('off');
	    $('#smtp_onoff').prop('checked', true);
	    $('#smtp_onoff').val('on');
	    $('#smtp_show_div').show();
	}

	$('#s3_onoff').click(function() {

	    if($('#s3_onoff').is(':checked')) 
	    {
	        $('#s3_show_div').show();
	        $('#s3_onoff').val('on');
	        $('#smtp_onoff').prop('checked', false);
	        $('#smtp_onoff').val('off');
	        $('#smtp_show_div').hide();
	    }
	    else
	    {
	    	$('#s3_show_div').hide();
	        $('#s3_onoff').val('off');
	        $('#smtp_onoff').prop('checked', true);
	        $('#smtp_onoff').val('on');
	        $('#smtp_show_div').show();
	    }
	});

	if($('#smtp_onoff').is(':checked')) 
	{
	    $('#smtp_show_div').show();
	    $('#smtp_onoff').val('on');
	    $('#s3_onoff').prop('checked', false);
	    $('#s3_onoff').val('off');
	    $('#s3_show_div').hide();
	}
	else
	{
	    $('#smtp_show_div').hide();
	    $('#smtp_onoff').val('off');
	    $('#s3_onoff').prop('checked', true);
	    $('#s3_onoff').val('on');
	    $('#s3_show_div').show();
	}

	$('#smtp_onoff').click(function() {

	    if($('#smtp_onoff').is(':checked')) 
	    {
	        $('#smtp_show_div').show();
	        $('#smtp_onoff').val('on');
	        $('#s3_onoff').prop('checked', false);
	        $('#s3_onoff').val('off');
	        $('#s3_show_div').hide();
	    }
	    else
	    {
	    	$('#smtp_show_div').hide();
	        $('#smtp_onoff').val('off');
	        $('#s3_onoff').prop('checked', true);
	        $('#s3_onoff').val('on');
	        $('#s3_show_div').show();
	    }
	});

	/* Email Save Button Click Function */
	$('#email_save_button').click(function(){
		
		var s3_mail                     = $('#s3_email').val();
                var s3_app_text 		= $('#s3_app_text').val();
		var s3_secret_text 		= $('#s3_secret_text').val();

		var smtp_user_host 		= $('#smtp_user_host').val();
		var smtp_user_port 		= $('#smtp_user_port').val();
		var smtp_user_text 		= $('#smtp_user_text').val();  
		var smtp_pass_text 		= $('#smtp_pass_text').val();

		var errorCount     =  0;
		var errorMessage   =  '';

		if($('#s3_onoff').val()=='on')
		{
                        if( s3_mail == '')
                        {
                                errorCount++;
                                errorMessage += 'Please enter AWS email id<br />';
                        }

                        if( s3_mail != '')
                        {
                                if (!validateEmail(s3_mail)) 
                                {
                                        errorCount++;
                                        errorMessage += 'Please enter valid email id<br />';
                                }
                        }
			if(s3_app_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter AWS app key <br />';
			}

			if(s3_secret_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter AWS secret key <br />';
			}
		}

		if($('#smtp_onoff').val()=='on')
		{
			if(smtp_user_host == '')
			{
				errorCount++;
				errorMessage += 'Please enter SMTP host <br />';
			}
			if(smtp_user_port == '')
			{
				errorCount++;
				errorMessage += 'Please enter SMTP port <br />';
			}
			if(smtp_user_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter SMTP user name <br />';
			}

			if(smtp_pass_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter SMTP password <br />';
			}
		}
		
		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			//alert(smtp_user_host+'//'+smtp_user_port+'//'+smtp_user_text+'//'+smtp_pass_text);
			$.ajax({
			    url: admin_url+'environment/save',
			    type: "POST",
			    data:{"is_ajax":true,'s3_hidden_key':$('#s3_hidden_key').val(), 's3_onoff':$('#s3_onoff').val(), 's3_email':s3_mail, 's3_app_text':s3_app_text, 's3_secret_text':s3_secret_text, 'smtp_hidden_key':$('#smtp_hidden_key').val(), 'smtp_onoff':$('#smtp_onoff').val(), 'smtp_user_host':smtp_user_host, 'smtp_user_port':smtp_user_port, 'smtp_user_text':smtp_user_text, 'smtp_pass_text':smtp_pass_text},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#email_show_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#email_show_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#email_show_message').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}

	});
	/* Email settings code ends */

	/* NewsLetter Settings code starts */

	if($('#newsletter_onoff').is(':checked')){
		$('#newsletter_onoff').val('on');

		if($('#mailchimp_onoff').is(':checked')){
			$('#mailchimp_onoff').val('on');
			$('#zoho_onoff').val('on');
			$('#mailchimp_show_div').show();
			$('#mailchimp_onoff_input').show();
			$('#zoho_onoff_input').show();
    		$('#zoho_label').show();
    		$('#mailchimp_label').show();
    		$('#zoho_show_div').hide();
		}else{
			$('#zoho_onoff').val('on');
			$('#mailchimp_onoff').val('on');
			$('#mailchimp_show_div').hide();
			$('#mailchimp_onoff_input').hide();
    		$('#mailchimp_label').hide();
    		$('#zoho_show_div').show();
    		$('#zoho_onoff_input').show();
    		$('#zoho_label').show();
    		$('#mailchimp_onoff_input').show();
    		$('#mailchimp_label').show();
		}
	}else{
		$('#newsletter_onoff').val('off');
		$('#mailchimp_show_div').hide();
    	$('#zoho_show_div').hide();
    	$('#mailchimp_onoff_input').hide();
    	$('#mailchimp_label').hide();
    	$('#zoho_onoff_input').hide();
    	$('#zoho_label').hide();
	}

	$('#zoho_onoff').click(function() {
		if($('#newsletter_onoff').is(':checked')){
			$('#newsletter_onoff').val('on');
			if($('#zoho_onoff').is(':checked')) 
		    {
		    	$('#zoho_onoff').val('on');
		        $('#zoho_show_div').show();
		        $('#mailchimp_onoff').val('off');
		        $('#mailchimp_onoff').prop('checked', false);
		        $('#mailchimp_onoff').val('off');
		        $('#mailchimp_show_div').hide();
		    }
		    else
		    {	$('#mailchimp_onoff').val('on');
		    	$('#zoho_show_div').hide();
		        $('#zoho_onoff').val('off');
		        $('#mailchimp_onoff').prop('checked', true);
		        $('#mailchimp_show_div').show();
		    }
		}else{
			$('#newsletter_onoff').val('off');
			if($('#zoho_onoff').is(':checked')) 
		    {
		        $('#zoho_onoff').val('on');
		        $('#mailchimp_onoff').prop('checked', false);
		        $('#mailchimp_onoff').val('off');
		    }
		    else
		    {
		    	$('#mailchimp_onoff').val('on');
		        $('#zoho_onoff').val('off');
		        $('#mailchimp_onoff').prop('checked', true);
		    }
		}
	});
	$('#mailchimp_onoff').click(function() {
		if($('#newsletter_onoff').is(':checked')){
			$('#newsletter_onoff').val('on');
			if($('#mailchimp_onoff').is(':checked')) 
		    {
		    	$('#mailchimp_onoff').val('on');
		        $('#mailchimp_show_div').show();
		        $('#zoho_onoff').prop('checked', false);
		        $('#zoho_onoff').val('off');
		        $('#zoho_show_div').hide();
		    }
		    else
		    {	$('#zoho_onoff').val('on');
		    	$('#mailchimp_show_div').hide();
		        $('#mailchimp_onoff').val('off');
		        $('#zoho_onoff').prop('checked', true);
		        $('#zoho_show_div').show();
		    }
		}else{
			$('#newsletter_onoff').val('off');
			if($('#mailchimp_onoff').is(':checked')) 
		    {
		        $('#mailchimp_onoff').val('on');
		        $('#zoho_onoff').prop('checked', false);
		        $('#zoho_onoff').val('off');
		    }
		    else
		    {
		        $('#mailchimp_onoff').val('off');
		        $('#zoho_onoff').prop('checked', true);
		        $('#zoho_onoff').val('on');
		    }
		}
	});

	$('#newsletter_onoff').click(function() {
		if($('#newsletter_onoff').is(':checked')){
			$('#newsletter_onoff').val('on');
			if($('#mailchimp_onoff').is(':checked')){
				$('#zoho_onoff').prop('checked', false);
				$('#mailchimp_onoff').prop('checked', true);
				$('#mailchimp_onoff').val('on');
				$('#zoho_onoff').val('off');
				$('#mailchimp_show_div').show();
				$('#mailchimp_onoff_input').show();
				$('#zoho_onoff_input').show();
	    		$('#zoho_label').show();
	    		$('#mailchimp_label').show();
        		$('#zoho_show_div').hide();
			}else{
				$('#zoho_onoff').prop('checked', true);
				$('#mailchimp_onoff').prop('checked', false);
				$('#zoho_onoff').val('on');
				$('#mailchimp_onoff').val('off');
				$('#mailchimp_show_div').hide();
				$('#mailchimp_onoff_input').hide();
	    		$('#mailchimp_label').hide();
        		$('#zoho_show_div').show();
        		$('#zoho_onoff_input').show();
	    		$('#zoho_label').show();
	    		$('#mailchimp_onoff_input').show();
	    		$('#mailchimp_label').show();
			}
		}else{
			$('#newsletter_onoff').val('off');
			$('#mailchimp_show_div').hide();
        	$('#zoho_show_div').hide();
        	$('#mailchimp_onoff_input').hide();
	    	$('#mailchimp_label').hide();
	    	$('#zoho_onoff_input').hide();
	    	$('#zoho_label').hide();
		}

	});

	$('#newsletter_save_button').click(function() {
		var zoho_api 		= $('#zoho_api_key').val();
		var mailchimp_api 	= $('#mailchimp_api_key').val();

		var errorCount     =  0;
		var errorMessage   =  '';
		//console.log('clicked');

		if($('#zoho_onoff').val()=='on')
		{
			if(zoho_api == '')
			{
				errorCount++;
				errorMessage += 'Please enter zoho campaign api key.<br/>';
			}

		}

		if($('#mailchimp_onoff').val()=='on')
		{
			if(mailchimp_api == '')
			{
				errorCount++;
				errorMessage += 'Please enter mailchimp api key.<br/>';
			}
		}
		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			//alert(smtp_user_host+'//'+smtp_user_port+'//'+smtp_user_text+'//'+smtp_pass_text);
			$.ajax({
			    url: admin_url+'environment/save',
			    type: "POST",
			    data:{"is_ajax":true,'newsletter_hidden_key':$('#newsletter_hidden_key').val(), 'newsletter_onoff':$('#newsletter_onoff').val(), 'zoho_hidden_key':$('#zoho_hidden_key').val(),'zoho_onoff':$('#zoho_onoff').val(), 'zoho_api':zoho_api, 'mailchimp_hidden_key':$('#mailchimp_hidden_key').val(),'mailchimp_onoff':$('#mailchimp_onoff').val(), 'mailchimp_api':mailchimp_api},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#newsletter_show_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#newsletter_show_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#newsletter_show_message').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}
	});

	/* NewsLetter Settings code ends */


	if($('#fb_app_onoff').is(':checked')) 
	{
	    $('#app_div_content').show();
	    $('#fb_app_onoff').val('on');
	}
	else
	{
	    $('#app_div_content').hide();
	    $('#fb_app_onoff').val('off');
	}

	$('#fb_app_onoff').click(function() {

	    if($('#fb_app_onoff').is(':checked')) 
	    {
	        $('#app_div_content').show();
	        $('#fb_app_onoff').val('on');
	    }
	    else
	    {
	    	$('#app_div_content').hide();
	        $('#fb_app_onoff').val('off');
	    }
	});


	function checkSocialMediaUrls(url){
		
		if (/http(s)?:\/\/(www\.)?(facebook|fb)\.com\/[A-z0-9_\-\.]+\/?$/i.test(url)){
			return true;
		}
		
		if (/http(s)?:\/\/(.*\.)?twitter\.com\/[A-z0-9_?=]+\/?$/i.test(url)){
			return true;
		}

		if (/http(s)?:\/\/(www\.)?github\.com\/[A-z0-9_-]+\/?$/i.test(url)){
			return true;
		}

		if (/http(s)?:\/\/([\w]+\.)?linkedin\.com\/pub\/[A-z0-9_-]+(\/[A-z0-9]+){3}\/?$/i.test(url)){
			return true;
		}

		if (/https?:\/\/(www\.)?instagram\.com\/([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)$/i.test(url)){
			return true;
		}

		if (/(?:(?:callto|skype):)(?:[a-z][a-z0-9\\.,\\-_]{5,31})(?:\\?(?:add|call|chat|sendfile|userinfo))?$/i.test(url)){
			return true;
		}

		if (/https?:\/\/(t(elegram)?\.me|telegram\.org)\/([a-z0-9\_]{5,32})\/?$/i.test(url)){
			return true;
		}
		return false;
	}
	/* Social media Save Button Click Function */
	$('#fb_save_button').click(function(){
		
		var fb_app_onoff 		= $('#fb_app_onoff').val();
		var app_text  			= $('#app_text').val();
		var fb_link_text  		= $('#fb_link_text').val();
		var twitter_link_text   = $('#twitter_link_text').val();
		var youtube_link_text  	= $('#youtube_link_text').val();

		var errorCount     =  0;
		var errorMessage   =  '';

		if($('#fb_app_onoff').val()=='on')
		{
			if(app_text == '')
			{
				errorCount++;
				errorMessage += 'Please enter facebook app key <br />';
			}
		}
		
		fb_link_text  = $('#fb_link_text').val();
        fb_link_text  = fb_link_text.trim();
        fb_link_text  = $.trim( $('#fb_link_text').val() );

        twitter_link_text  = $('#twitter_link_text').val();
        twitter_link_text  = fb_link_text.trim();
        twitter_link_text  = $.trim( $('#twitter_link_text').val() );

        youtube_link_text  = $('#youtube_link_text').val();
        youtube_link_text  = youtube_link_text.trim();
		youtube_link_text  = $.trim( $('#youtube_link_text').val() );
		
		if(!checkSocialMediaUrls(twitter_link_text)){
				errorCount++;
				errorMessage += 'Invalid Twitter url <br />';
		}
		if(!checkSocialMediaUrls(fb_link_text)){
				errorCount++;
				errorMessage += 'Invalid Facebook url <br />';
		}

		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			$.ajax({
			    url: admin_url+'environment/save',
			    type: "POST",
			    data:{"is_ajax":true,'fb_app_hidden':$('#fb_app_hidden').val(), 'fb_app_onoff':fb_app_onoff, 'app_text':app_text, 'social_link_hidden':$('#social_link_hidden').val(), 'fb_link_text':fb_link_text, 'twitter_link_text':twitter_link_text, 'youtube_link_text':youtube_link_text},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#show_fb_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#show_fb_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#show_fb_message').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}

	});

	if($('#dropbox_checkbox').is(':checked')) 
	{
	    $('#dropbox_div_content').show();
	    $('#dropbox_checkbox').val('1');
	}
	else
	{
	    $('#dropbox_div_content').hide();
	    $('#dropbox_checkbox').val('0');
	}

	/* Dropbox Save Button Click Function */
	$('#dropbox_button').click(function(){
		
		var dropbox_checkbox 	= $('#dropbox_checkbox').val();
		var dropbox_text  		= $('#dropbox_text').val();

		var errorCount     =  0;
		var errorMessage   =  '';

		if( dropbox_text == '')
		{
			errorCount++;
			errorMessage += 'Please enter dropbox secret key <br />';
		}
		
		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			$.ajax({
			    url: admin_url+'environment/save',
			    type: "POST",
			    data:{"is_ajax":true,'dropbox_checkbox':dropbox_checkbox, 'dropbox_text':dropbox_text, 'dropbox_hidden':$('#dropbox_hidden').val()},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#show_dropbox_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#show_dropbox_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#show_dropbox_message').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}

	});

	$('#dropbox_checkbox').click(function() {

		cleanPopUpMessage();
	    if($('#dropbox_checkbox').is(':checked')) 
	    {
	        $('#dropbox_div_content').show();
	        $('#dropbox_checkbox').val('1');
	    }
	    else
	    {
	        $('#dropbox_div_content').hide();
	        $('#dropbox_checkbox').val('0');
	        $.ajax({
			    url: admin_url+'environment/dropbox_off',
			    type: "POST",
			    data:{"is_ajax":true,'dropbox_checkbox_value':$('#dropbox_checkbox').val(), 'dropbox_hidden':$('#dropbox_hidden').val()},
			    success: function(response) {
			    	//console.log(response);
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#show_dropbox_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#show_dropbox_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
	    }
	});


	if($('#enable_chat').is(':checked')) 
	{
	    $('#support_chat_content').show();
	}else{
	    $('#support_chat_content').hide();
	}

	$('#enable_chat').click(function() {

		cleanPopUpMessage();
	    if($('#enable_chat').is(':checked')) 
	    {
	        $('#support_chat_content').show();
	    }
	    else
	    {
	        $('#support_chat_content').hide();
	        $.ajax({
			    url: admin_url+'environment/support_chat_off',
			    type: "POST",
			    data:{"is_ajax":true,'chat_checkbox_value':0},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#support_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#support_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
	    }
	});

	/* Support Chat Save Button Click Function */
	$('#button_support_chat').click(function(){
		
		var chat_checkbox_value = $('#enable_chat').val();
		var chat_textbox_value  = $('#text_chat_script').val();

		var errorCount     =  0;
		var errorMessage   =  '';

		if( chat_textbox_value == '')
		{
			errorCount++;
			errorMessage += 'Please enter support chat script <br />';
		}
		
		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			$.ajax({
			    url: admin_url+'environment/support_chat',
			    type: "POST",
			    data:{"is_ajax":true,'chat_checkbox_value':chat_checkbox_value, 'chat_textbox_value':btoa(chat_textbox_value)},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#support_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#support_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#support_message').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}

	});

	/* Content Security Save Button Click Function */
	$('#button_content_security').click(function(){
	
		var security_checkbox_value = $('#enable_content_security').val();
		if($('#enable_content_security').prop("checked") == false) {
			security_checkbox_value = 0;
		}
		var setting_key				= $("#content_security_setting_key").val();
		var errorCount     =  0;
		var errorMessage   =  '';
		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			$.ajax({
				url: admin_url+'environment/content_security',
				type: "POST",
				data:{"is_ajax":true,'security_checkbox_value':security_checkbox_value,'setting_key':setting_key},
				success: function(response) {
					var data  = $.parseJSON(response);
					if(data['error'] == false)
					{
						$('#content_security_message').prepend(renderPopUpMessage('success', data['message']));
						scrollToTopOfPage();
					}
					else
					{
						$('#content_security_message').prepend(renderPopUpMessage('error', data['message']));
						scrollToTopOfPage();
					}
					setTimeout(function(){
						$('#content_security_message').html('');
					}, 2000);

				}
			});
		}
		else
		{
			$('#content_security_message').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}

	});

	/* login_restricted Button Click Function */
	$('#login_restricted').click(function(){
	
		var login_restricted = $('#login_restricted').val();
		if($('#login_restricted').prop("checked") == false) {
			login_restricted = 0;
		}
		var setting_key				= $("#login_restricted_setting_key").val();
		var errorCount     =  0;
		var errorMessage   =  '';
		cleanPopUpMessage();
		//alert(login_restricted+' '+setting_key);
		if( errorCount == 0 )
		{
			$.ajax({
				url: admin_url+'environment/restricted_login',
				type: "POST",
				data:{
					"is_ajax":true,
					'login_restricted':login_restricted,
					'setting_key':setting_key
				},
				success: function(response) {
					var data  = $.parseJSON(response);
					if(data.error == false)
					{
						$('#restrict-login_security_message').prepend(renderPopUpMessage('success', data.message));
						scrollToTopOfPage();
					}
					else
					{
						$('#restrict-login_security_message').prepend(renderPopUpMessage('error', data.message));
						scrollToTopOfPage();
					}
					setTimeout(function(){
						$('#restrict-login_security_message').html('');
					}, 2000);

				}
			});
		}
		else
		{
			$('#login_security_message').prepend(renderPopUpMessage('error', errorMessage));
			//scrollToTopOfPage();
		}

	});

	/* Analytics settings */

	if($('#enable_analytics').is(':checked')) 
	{
	    $('#analytics_content').show();
	}else{
	    $('#analytics_content').hide();
	}

	$('#enable_analytics').click(function() {

		cleanPopUpMessage();
	    if($('#enable_analytics').is(':checked')) 
	    {
	        $('#analytics_content').show();
	    }
	    else
	    {
	        $('#analytics_content').hide();
	        $.ajax({
			    url: admin_url+'environment/analytics_off',
			    type: "POST",
			    data:{"is_ajax":true,'analytics_hidden':$('#analytics_hidden').val(),'chat_checkbox_value':0},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
						$('#analytic-menu').remove();
			            $('#analytics_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#analytics_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
	    }
	});

	/* Analytics Save Button Click Function */
	$('#button_analytics').click(function(){
		
		var chat_checkbox_value = $('#enable_analytics').val();
		var chat_textbox_value  = $('#text_analytics_script').val();
		var analyticUrl  		= $('#analytic_url').val();

		var errorCount     =  0;
		var errorMessage   =  '';

		if( chat_textbox_value == '')
		{
			errorCount++;
			errorMessage += 'Please enter support analytics script <br />';
		}
		if( analyticUrl == '')
		{
			errorCount++;
			errorMessage += 'Please enter support analytics access url <br />';
		}
		
		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			$.ajax({
			    url: admin_url+'environment/analytics',
			    type: "POST",
			    data:{"is_ajax":true,'analytics_checkbox_value':chat_checkbox_value,'analytics_hidden': $('#analytics_hidden').val(),'analytics_textbox_value':btoa(chat_textbox_value),'analytics_access_url':btoa(analyticUrl)},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
						$('#bundle-menu').after('<li id="analytic-menu"><a href="'+analyticUrl+'">Analytics Report</a></li>');
			            $('#analytics_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#analytics_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#analytics_message').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}

	});

	/* End Analytics settings */
	
	/* Contact Details Save Button Click Function */
	$('#save_contact_button').click(function(){
		
		var contact_phone    = $('#contact_phone').val();
		var contact_email    = $('#contact_email').val();
		var whatsapp_number  = $('#whatsapp_number').val();
		var contact_address  = $('#contact_address').val();

		//var text = document.forms[0].txt.value;
			contact_address  = contact_address.replace(/\r?\n/g, '<br />');

		var errorCount     =  0;
		var errorMessage   =  '';

		if( contact_phone == '')
		{
			errorCount++;
			errorMessage += 'Please enter contact phone number<br />';
		}

		if( contact_phone != '')
		{
			var phones = contact_phone.replace(/\s/g,'');
				phones = phones.split(',');
			for(var i = 0; i < phones.length; i++){ 
				if(phones[i]){
					//console.log(phones[i]);
					if(!isPhoneNumber(phones[i])){
						errorCount++;
						errorMessage += `Invalid phone number : <b>${phones[i]}</b><br />`;
					}
				}
			}
		}
		
		if( contact_email == '')
		{
			errorCount++;
			errorMessage += 'Please enter contact email id<br />';
		}

		if( contact_email != '')
		{
			var emails = contact_email.replace(' ','');
				emails = emails.split(',');

			for(var i = 0; i < emails.length; i++){
				if(emails[i]){
					if (!validateEmail(emails[i])) 
					{
						errorCount++;
						//errorMessage += 'Please enter valid email id<br />';
						errorMessage += `Invalid email id  : <b>${emails[i]}</b><br />`;
					}
				}
			}
		}

		if( whatsapp_number == '')
		{
			errorCount++;
			errorMessage += 'Please enter whatsapp number<br />';
		}


		if( contact_address == '')
		{
			errorCount++;
			errorMessage += 'Please enter contact address<br />';
		}
		
		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			$.ajax({
			    url: admin_url+'environment/save',
			    type: "POST",
			    data:{"is_ajax":true,'contact_phone':contact_phone, 'contact_email':contact_email, 'whatsapp_number':whatsapp_number, 'contact_address':contact_address, 'hidden_contact_id':$('#hidden_contact_id').val()},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#contact_message').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#contact_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#contact_message').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}

	});

	/* GST Settings Save Button Click Function */
	$('#save_gst_button').click(function(){
		
		var cgst    = parseFloat($('#cgst').val());
		var sgst    = parseFloat($('#sgst').val());

		var errorCount     =  0;
		var errorMessage   =  '';

		if( cgst == '')
		{
			errorCount++;
			errorMessage += 'Please enter CGST value.<br />';
		}

		if( sgst == '')
		{
			errorCount++;
			errorMessage += 'Please enter SGST value.<br />';
		}



		if( contact_address == '')
		{
			errorCount++;
			errorMessage += 'Please enter contact address<br />';
		}
		
		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			$.ajax({
			    url: admin_url+'environment/save',
			    type: "POST",
			    data:{"is_ajax":true,'cgst':cgst, 'sgst':sgst,'hidden_gst_id':$('#hidden_gst_id').val()},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
						if(isFloat(Number(cgst))) {
							$("#cgst").val(cgst.toFixed(2));
						} else {
							$("#cgst").val(cgst);
						}
						if(isFloat(Number(sgst))) {
							$("#sgst").val(sgst.toFixed(2));
						} else {
							$("#sgst").val(sgst);
						}
			            $('#gst_message').prepend(renderPopUpMessage('success', data['message']));
						scrollToTopOfPage();
						setTimeout(function(){
							$('#gst_message').html('');
						}, 2000);
			        }
			        else
			        {
			            $('#gst_message').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#gst_message').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}
	});

	function validateEmail(email) {
	    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	}

	App.init();
    /* Default element fixed is taken for Height Top 
        - minusElement to be added for sidebar
    */
    // App.calcTop(".nav-content");
    App.initOnChange('#myUrl', 389, 265);

    function findParentWrap(elem, parentElem) {
        return $(elem).closest(parentElem);
    }

    /* Setting the previous target to check CLICK FUNCTION */
    var previousTarget = null;

    $(".grp-click-fn").on("click",function() {
        var parentElem = findParentWrap(this,".rTableRow");

        /* Checking if clicked element is previously clicked one */
        if (this === previousTarget && !($(".wrap-left-grp.open-grp").length)) {
            $(".wrap-left-grp").addClass("open-grp");
            //$(".rTableCell").removeClass("active-table");
            return false;
        }


        if ($(".wrap-left-grp.open-grp").length) {
            $(".wrap-left-grp").removeClass("open-grp");

            $(".rTableCell").removeClass("active-table");
            $(parentElem).find(".rTableCell:last").addClass("active-table");
        }else{

             $(".rTableCell").removeClass("active-table");
            $(parentElem).find(".rTableCell:last").addClass("active-table");
        }

        /* Assigning the previous Target */
        previousTarget = this;
    })
			
			
			
	// Jquery script for showing and hiding content based on settings click starts here
	$(".innercontent").hide();
	$("#basicSettings").show();		
	$(".settings-link").click(function(){
		$(".innercontent").hide();
		activeDiv = $(this).attr('data-ID');
		$(activeDiv).fadeIn();
	});	
	var activeSelector = 'span.settings-link';
	$(activeSelector).on('click', function(){
		$(activeSelector).removeClass('activeDiv');
		$(this).addClass('activeDiv');
	});

	// jquery script for showing and hiding content based on facebook checkbox ends here		
	$(function(){
		$('.right-box').slimScroll({
			height: '100%',
			wheelStep : 3,
			distance : '10px'
		});
               
	});
});
function uploadCertificate()
{
    if(__uploadedFile=='')
    {
        lauch_common_message('File missing', 'Please choose file to upload');
        return false;
    }
    //console.log(__uploadedFile);
    $('#percentage_bar').show();
    var uploadURL                   = admin_url+'environment/launch_conversion'
    var param                       = new Array;
        param["file"]               = __uploadedFile;
    uploadFiles(uploadURL, param, uploadCertificateCompleted);    
}

function uploadCertificateCompleted(response)
{
    __uploadedFile = '';
    var data    = $.parseJSON(response);
    //console.log(data);
    window.location = admin_url+'environment/#certificate';
    setTimeout(function(){window.location.reload();}, 500);
    //$("#certificateSettings").trigger('click');
}

/*payment gateway start*/
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  });
}
function savePaymentGatewayConfigs( formId ) {

    var errorCount = 0;
    var errorMessage = '';

    $("#"+formId+" .payment-fields").each(function( key, field ) {
        if($.trim(field.value) == '') {
            errorCount++;
            errorMessage += atob($(field).attr('data-label'))+' required.</br>';
        } 
    });

    if(errorCount > 0) {
        $("#"+formId+" .payment_message").prepend(renderPopUpMessage('error', errorMessage));
        updateAccordianHeight(formId);
    } else {
        $.ajax({
            url : admin_url+'environment/save_payment_gateway_settings',
            type: 'POST',
            data: $('#'+formId).serialize(),
            success : function( response ) {
                var data = $.parseJSON(response);
                if(data['error'] == false) {
                    $("#"+formId+" .payment_message").prepend(renderPopUpMessage('success', data['message']));
                    updateAccordianIcon(formId);
                } else {
                    $("#"+formId+" .payment_message").prepend(renderPopUpMessage('error', data['message']));
                }
                updateAccordianHeight(formId);
            }
        });
    }

}

function updateAccordianHeight( formId ) {
    var accordianObject = $("#"+formId).parent().parent();
    accordianObject.css('max-height', accordianObject.prop('scrollHeight'));
}

function updateAccordianIcon( formId ) {
    var isPaymentGatewayEnabled = $("#"+formId+" .show-checkbox").prop("checked");
    $('#button_'+formId).html(renderPaymentGatewayStatus(isPaymentGatewayEnabled));
}

function clearPaymentGatewayConfigs( formId ) {
    $("#"+formId+" .payment-fields").each(function( key, field ) {
        field.value = '';
    });
}

function renderPaymentGatewayStatus( isPaymentGatewayEnabled ) {
      if(isPaymentGatewayEnabled == true) {
        return '<span class="icon-tick"><svg id="Layer_1" style="enable-background:new 0 0 512 512; vertical-align: super;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">.st0{fill:#41AD49;}</style><g><polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 196.6,440.9 511.7,125.8 434.8,49     "></polygon></g></svg></span>';
      } else {        
        return '<span class="icon-close">&times;</span>';
      }
}
/*payment gateway end*/

function isFloat(n){
    return Number(n) === n && n % 1 !== 0;
}

/* Testimonial Code starts here */
$(document).on('click','#add_testimonial',function() {

	var cancelbuttons 		= $('.cancel-all');
	for(var i = 0; i < cancelbuttons.length; i++){
		$('#cancel'+$(cancelbuttons[i]).attr('data-id')).click();
		$('#cancel'+$(cancelbuttons[i]).attr('data-id')).removeClass('cancel-all');
	}

	var testimonialFormHtml = '';

	testimonialFormHtml += '<div class="testimonial-manager">';
	testimonialFormHtml += '	<div class="testimonial-column">';
	testimonialFormHtml += '		<div class="testimonial-user-info">';
	testimonialFormHtml += '			<div class="user-info-edit">';
	testimonialFormHtml += '				<label class="file-uploader">';
	testimonialFormHtml += '					<img class="img-upload-icon" src="'+assets_url+'images/file-upload.png" />';
	testimonialFormHtml += '					<input type="file" id="testimonial_image_0" class="testimonial-image" />';
	testimonialFormHtml += '				</label>';
	testimonialFormHtml += '				<div class="user-details">';
	testimonialFormHtml += '					<div class="form-group"><input type="text" maxlength="50" onkeypress="return preventNumbers(event)" class="form-control" id="testimonial_name_0"  placeholder="Name" value=""></div>';
	testimonialFormHtml += '					<div class=""><input type="text" maxlength="100" onkeypress="return preventNumbers(event)" class="form-control" id="testimonial_other_detail_0" placeholder="Designation / Company / Place" value=""></div>';
	testimonialFormHtml += '				</div>';
	testimonialFormHtml += '			</div>';
	testimonialFormHtml += '		</div>';
	testimonialFormHtml += '		<div class="testimonial-content">';
	testimonialFormHtml += '			<textarea rows="4" class="form-control" maxlength="200" onkeyup="validateMaxLength(this.id)" id="testimonial_content_0" placeholder="Testimonial"></textarea>';
	testimonialFormHtml += '			<label class="pull-right testimonial-content-remain" id="testimonial_content_0_char_left">200 characters left</label>';
	testimonialFormHtml += '		</div>';
	testimonialFormHtml += '		<div class="clearfix"></div><div class="message-testimonial" id="message_testimonial_0"></div>';
	testimonialFormHtml += '		<div class="text-right testimonial-action">';
	testimonialFormHtml += '			<label class="check-box-holder">';
	testimonialFormHtml += '				<span class="showin-home-text">Show in Home Page</span>';
	testimonialFormHtml += '				<label class="custom-checkbox ">';
	testimonialFormHtml += '					<input type="checkbox" class="edit-checkbox-featured" id="featured_testimonial_0">';
	testimonialFormHtml += '					<span class="checkmark"></span>';
	testimonialFormHtml += '				</label>';
	testimonialFormHtml += '			</label>';
	testimonialFormHtml += '			<div class="">';
	testimonialFormHtml += '				<input type="button" class="btn btn-red cancel_testimonial" data-id="" class="" value="CANCEL">';
	testimonialFormHtml += '				<input type="button" class="btn btn-green" id="create_testimonial_button" value="CREATE">';
	testimonialFormHtml += '			</div>';
	testimonialFormHtml += '		</div>';
	//testimonialFormHtml += '		<div class="clearfix"></div>';
	testimonialFormHtml += '	</div>';
	testimonialFormHtml += '</div>';

	$('#testimonial_create').html(testimonialFormHtml);
});

/* Testimonial Image Upload */
var __uploadTestimonialFlag = 0;
var __uploading_file_testimonial = new Array();
$(document).on('change', '.testimonial-image', function(e) {

	//var cancelbuttons 		= $('.cancel-all');
	//for(var i = 0; i < cancelbuttons.length; i++){
		//$('#cancel'+$(cancelbuttons[i]).attr('data-id')).click();
		//$('#cancel'+$(cancelbuttons[i]).attr('data-id')).removeClass('cancel-all');
	//}
	//console.log('testimonial-image');
	var testimonialID 		= $(this).attr('data-id');
	var imgUploadIcon		= (testimonialID > 0) ? '#list_'+testimonialID+' .img-upload-icon' : '.img-upload-icon';
	var errorMsgID			= (testimonialID > 0) ? '#list_'+testimonialID+' #message_testimonial_'+testimonialID : '#message_testimonial_0'; 

	var _URL = window.URL || window.webkitURL;
	var file, img;
	if ((file = this.files[0])) {	
		__uploading_file_testimonial = e.currentTarget.files;
		img = new Image();
		if(file['type']=="image/jpg" || file['type']=="image/jpeg") {
			var reader = new FileReader();
			reader.onload = function(e) {
				
				__uploadTestimonialFlag = 1;
				img.onload = function () {
					if(this.width <= 90 && this.height <= 90) {
						$(imgUploadIcon).attr('src', e.target.result);
						if( __uploading_file_testimonial.length > 1 ) {
							lauch_common_message('Error Occured', 'You are not allowed to upload more that one file.');
							return false;
						}
					} else {
						if(testimonialID == 0){
							$(imgUploadIcon).attr('src',assets_url+'images/file-upload.png');
						}
						$(errorMsgID).prepend(renderPopUpMessage('error', 'Please upload an image of size 90 x 90 '));
						scrollToTopOfPage();
					}
				};
				img.src = _URL.createObjectURL(file);
			}	
			reader.readAsDataURL(this.files[0]);
		}
		else {
			$(errorMsgID).prepend(renderPopUpMessage('error', 'Please upload a JPG/JPEG file'));
			scrollToTopOfPage();
		}
	}
});

/* Create Testimonial button click */
$(document).on('click','#create_testimonial_button',function(){
		
	var testimonialName 	  		= $('#testimonial_name_0').val();
		testimonialName				= $.trim(testimonialName); 
	var testimonialOtherDetail 		= $('#testimonial_other_detail_0').val();
		testimonialOtherDetail		= $.trim(testimonialOtherDetail); 
	var testimonialContent 			= $('#testimonial_content_0').val();
		testimonialContent			= $.trim(testimonialContent);
	var testimonialImage 			= $('#testimonial_image_0').val();
	var featuredTestimonial 		= '0';

	var errorCount     =  0;
	var errorMessage   =  '';

	if(testimonialName == '') {
		errorCount++;
		errorMessage += 'Please enter Name <br />';
	}

	if(testimonialOtherDetail == '') {
		errorCount++;
		errorMessage += 'Please enter further details <br />';
	}

	if(testimonialImage == '') {
		errorCount++;
		errorMessage += 'Please select an Image <br />';
	} else {
		if(__uploading_file_testimonial.length=="0"){
			errorCount++;
			errorMessage += 'Please upload a JPG/JPEG file <br />';
		}
	}

	if(testimonialContent == '') {
		errorCount++;
		errorMessage += 'Please enter Testimonial <br />';//close
	}

	if($('#featured_testimonial_0'). prop("checked") == true){
		featuredTestimonial = '1';
	}

	cleanPopUpMessage();
	if( errorCount == 0 ) {  
		var i                           = 0;
		var uploadURL                   = admin_url+"environment/upload_testimonial_image" ; 
		var fileObj                     = new processFileName(__uploading_file_testimonial[i]['name']);
		var param                       = new Array;
			param["file_name"]          = fileObj.uniqueFileName();        
		var extension_org				= fileObj.explodeFileName();
			param["extension"]			= extension_org['1'];
			param["file"]               = __uploading_file_testimonial[i];
			param['upload_flag']		= '2';
			param["testimonial_name"]   = testimonialName;
			param["testimonial_detail"] = testimonialOtherDetail;
			param["testimonial_text"]   = testimonialContent;
			param["testimonial_featured"]= featuredTestimonial;
		uploadFiles(uploadURL, param, uploadTestimonialImageCompleted);
		// $('#testimonial_name_0').val('');
		// $('#testimonial_other_detail_0').val('');
		// $('#testimonial_content_0').val('');
		// $('#testimonial_image_0').val('');
		// $("#testimonial_create").html('');
	}else {
		$('#message_testimonial_0').prepend(renderPopUpMessage('error', errorMessage));
		scrollToTopOfPage();
	}
});

function uploadTestimonialImageCompleted(response){
   	var data 						= $.parseJSON(response);
	var checked 					= (data['featured_testimonial'] == '1') ? 'checked="checekd"' : '';
	var generatedTestimonialHtml 	= '';

	if(data['error'] == true) {
		__uploadTestimonialFlag = 0;
		var messageObject = {
			'body': data['message'],
			'button_yes': 'OK',
		};
		callback_danger_modal(messageObject);

	} else {

		$('#testimonial_name_0').val('');
		$('#testimonial_other_detail_0').val('');
		$('#testimonial_content_0').val('');
		$('#testimonial_image_0').val('');
		$("#testimonial_create").html('');

		generatedTestimonialHtml += '<div class="testimonial-manager" id="list_'+data['testimonial_id']+'">';
		generatedTestimonialHtml += '	<div class="testimonial-column preview">';
		generatedTestimonialHtml += '		<div class="testimonial-user-info">';
		generatedTestimonialHtml += '			<div class="user-info-edit">';
		generatedTestimonialHtml += '				<label class="file-uploader">';
		generatedTestimonialHtml += '					<img class="img-upload-icon" id="testimonial_upload_image_preview_'+data['testimonial_id']+'" src="'+data['user_image']+'" />';
		generatedTestimonialHtml += '					<input type="file" data-img = "'+data['user_image_file']+'" id="testimonial_image_'+data['testimonial_id']+'" class="testimonial-image" />';
		generatedTestimonialHtml += '				</label>';
		generatedTestimonialHtml += '				<div class="user-details">';
		generatedTestimonialHtml += '					<div class="form-group"><input type="text" maxlength="50" onkeypress="return preventNumbers(event)" value="'+data['user_name']+'" id="testimonial_name_'+data['testimonial_id']+'" class="form-control"  placeholder="Name"></div>';
		generatedTestimonialHtml += '					<div class=""><input type="text" maxlength="100" onkeypress="return preventNumbers(event)" class="form-control" id="testimonial_other_detail_'+data['testimonial_id']+'"  placeholder="Designation / Company / Place" value="'+data['user_other_detail']+'"></div>';
		generatedTestimonialHtml += '				</div>';
		generatedTestimonialHtml += '			</div>';
		generatedTestimonialHtml += '           <div class="user-info-preview">';
		generatedTestimonialHtml += '				<div class="info-left">';
		generatedTestimonialHtml += '					<div class="testimonial-avatar-preview"><img class="avatar" id="testimonial_image_preview_'+data['testimonial_id']+'" src="'+data['user_image']+'" /></div>';
		generatedTestimonialHtml += '					<div class="testimonial-username-designation-preview">';
		generatedTestimonialHtml += '						<div class="testimonial-username" id="testimonial_name_preview_'+data['testimonial_id']+'">'+data['user_name']+'</div>';
		generatedTestimonialHtml += '						<div class="testimonial-designation" id="testimonial_other_detail_preview_'+data['testimonial_id']+'">'+data['user_other_detail']+'</div>';
		generatedTestimonialHtml += '					</div>';
		generatedTestimonialHtml += '				</div>';
		generatedTestimonialHtml += '				<div class="info-right">';
		generatedTestimonialHtml += '					<label class="check-box-holder">';
		generatedTestimonialHtml += '						<span class="showin-home-text">Show in Home Page</span>';
		generatedTestimonialHtml += '						<label class="custom-checkbox ">';
		generatedTestimonialHtml += '							<input type="checkbox" data-id="'+data['testimonial_id']+'" class="list-checkbox-featured" '+checked+'>';
		generatedTestimonialHtml += '							<span class="checkmark"></span>';
		generatedTestimonialHtml += '						</label>';
		generatedTestimonialHtml += '					</label>';
		generatedTestimonialHtml += '					<div class="dropdown testimonial-settings">';
		generatedTestimonialHtml += '						<div class="dropdown-toggle" data-toggle="dropdown"><span class="dot-icon">...</span></div>';
		generatedTestimonialHtml += '						<ul class="dropdown-menu">';
		generatedTestimonialHtml += '							<li><a href="#" data-id="'+data['testimonial_id']+'" class="edit-testimonial">Edit</a></li>';
		generatedTestimonialHtml += '							<li><a href="#" class="remove-testimonial" data-title="'+data['user_name']+'" id="'+data['testimonial_id']+'">Delete</a></li>';
		generatedTestimonialHtml += '						</ul>';
		generatedTestimonialHtml += '					</div>';
		generatedTestimonialHtml += '				</div>';
		generatedTestimonialHtml += '			</div>';
		generatedTestimonialHtml += '		</div>';
		generatedTestimonialHtml += '		<div class="testimonial-content">';
		generatedTestimonialHtml += '			<textarea rows="4" class="form-control" maxlength="200" onkeyup="validateMaxLength(this.id)" placeholder="Testimonial" id="testimonial_content_'+data['testimonial_id']+'">'+data['testimonial_text']+'</textarea>';
		generatedTestimonialHtml += '			<label class="pull-right testimonial-content-remain" id="testimonial_content_'+data['testimonial_id']+'_char_left">200 characters left</label>';
		generatedTestimonialHtml += '			<div class="review-text"><p id="testimonial_content_preview_'+data['testimonial_id']+'">'+data['testimonial_text']+'</p></div>';
		generatedTestimonialHtml += '		</div>';
		generatedTestimonialHtml += '		<div class="clearfix"></div><div class="message-testimonial" id="message_testimonial_'+data['testimonial_id']+'"></div>';
		generatedTestimonialHtml += '		<div class="text-right testimonial-action">';
		generatedTestimonialHtml += '			<label class="check-box-holder">';
		generatedTestimonialHtml += '				<span class="showin-home-text">Show in Home Page</span>';
		generatedTestimonialHtml += '				<label class="custom-checkbox ">';
		generatedTestimonialHtml += '					<input type="checkbox" data-id="'+data['testimonial_id']+'" class="edit-checkbox-featured" id="featured_testimonial_'+data['testimonial_id']+'" '+checked+'>';
		generatedTestimonialHtml += '					<span class="checkmark"></span>';
		generatedTestimonialHtml += '				</label>';
		generatedTestimonialHtml += '			</label>';
		generatedTestimonialHtml += '			<div class="">';
		generatedTestimonialHtml += '				<input type="button" class="btn btn-red cancel_testimonial" data-id="'+data['testimonial_id']+'" value="CANCEL">';
		generatedTestimonialHtml += '				<input type="button" class="btn btn-green update_testimonial" data-id="'+data['testimonial_id']+'" value="SAVE">';
		generatedTestimonialHtml += '			</div>';
		generatedTestimonialHtml += '		</div>';
		generatedTestimonialHtml += '	</div>';
		generatedTestimonialHtml += '</div>';
		
		$('.testimonials_list').prepend(generatedTestimonialHtml);
		__uploadTestimonialFlag = 0;
		var messageObject = {
			'body': data['message'],
			'button_yes': 'OK',
		};
		callback_success_modal(messageObject);
	}
	//$('#testimonial_message').prepend(renderPopUpMessage('success', data['message']));
	scrollToTopOfPage();
}

$(document).on('click','.cancel_testimonial', function() {
	var testimonialID 		= $(this).attr('data-id');
	$('.close').click();
	if(testimonialID == '') {
		$('#testimonial_create').html('');
	}else {
		$('#list_'+testimonialID).children().addClass('preview');
	}
});

$(document).on('click','.edit-testimonial', function(){

	var cancelbuttons 		= $('.cancel-all');
	for(var i = 0; i < cancelbuttons.length; i++){
		$('#cancel'+$(cancelbuttons[i]).attr('data-id')).click();
		$('#cancel'+$(cancelbuttons[i]).attr('data-id')).removeClass('cancel-all');
	}

	var testimonialID 		= $(this).attr('data-id');
	
	var maxLength 			= '200';
	$('#list_'+testimonialID).children().removeClass('preview');

	var testimonialContent 			= $('#list_'+testimonialID+' #testimonial_content_'+testimonialID).val();
		testimonialContent			= $.trim(testimonialContent);
	
	var testimonialContentLength	= testimonialContent.length;
	var remainingContentLength		= maxLength - testimonialContentLength;

	var testimonialName 		= $('#testimonial_name_preview_'+testimonialID).html();
	var testimonialOtherDetail  = $('#testimonial_other_detail_preview_'+testimonialID).html();
	var testimonialViewContent 	= $('#testimonial_content_preview_'+testimonialID).html();
	var testimonialImg 			= $('#testimonial_image_preview_'+testimonialID).attr('src');

	$('#testimonial_name_'+testimonialID).val(decodeEntities(testimonialName));
	$('#testimonial_other_detail_'+testimonialID).val(decodeEntities(testimonialOtherDetail));
	$('#testimonial_content_'+testimonialID).val(decodeEntities(testimonialViewContent));
	$('#testimonial_upload_image_preview_'+testimonialID).attr('src', testimonialImg);
	$('#testimonial_content_'+testimonialID+'_char_left').html(remainingContentLength+' Characters left');
	$('#cancel'+testimonialID).addClass('cancel_testimonial cancel-all'); //console.log('cancel_testimonial',testimonialID);
	
});
function decodeEntities(encodedString) {
    var textArea = document.createElement('textarea');
    textArea.innerHTML = encodedString;
    return textArea.value;
}
$(document).on('click','.update_testimonial', function(){
	var testimonialID 		= $(this).attr('data-id');
	var testimonialDivID	= $('#list_'+testimonialID);

	var testimonialName 	  		= $('#list_'+testimonialID+' #testimonial_name_'+testimonialID).val();
		testimonialName				= $.trim(testimonialName); 
	var testimonialOtherDetail 		= $('#list_'+testimonialID+' #testimonial_other_detail_'+testimonialID).val();
		testimonialOtherDetail		= $.trim(testimonialOtherDetail); 
	var testimonialContent 			= $('#list_'+testimonialID+' #testimonial_content_'+testimonialID).val();
		testimonialContent			= $.trim(testimonialContent);
	var testimonialImagePreview 	= $('#list_'+testimonialID+' #testimonial_image_'+testimonialID).attr('data-img');
	var featuredTestimonial 		= '0';

	var errorCount     =  0;
	var errorMessage   =  '';

	if(testimonialName == '') {
		errorCount++;
		errorMessage += 'Please enter Name <br />';
	}

	if(testimonialOtherDetail == '') {
		errorCount++;
		errorMessage += 'Please enter further details <br />';
	}

	if(testimonialContent == '') {
		errorCount++;
		errorMessage += 'Please enter Testimonial <br />';
	}

	if($('#list_'+testimonialID+' #featured_testimonial_'+testimonialID). prop("checked") == true){
		featuredTestimonial = '1';
	}

	//console.log(errorCount);

	cleanPopUpMessage();
	if( errorCount == 0 ) {  
		var i                          	 	= 0;
		//$('#progress_div_testimonial').css('display','block');
		var uploadURL                   	= admin_url+"environment/upload_testimonial_image" ; 
		if(__uploadTestimonialFlag == 1) {
			var fileObj                     = new processFileName(__uploading_file_testimonial[i]['name']);
		}
		var param                       = new Array;
		if(__uploadTestimonialFlag == 1) {
			param["file_name"]          	= fileObj.uniqueFileName();        
		var extension_org					= fileObj.explodeFileName();
			param["extension"]				= extension_org['1'];
			param["file"]               	= __uploading_file_testimonial[i];
		}
			param['upload_flag']			= __uploadTestimonialFlag;
			param['testimonial_id']			= testimonialID;
			param["testimonial_name"]   	= testimonialName;
			param["testimonial_detail"] 	= testimonialOtherDetail;
			param["testimonial_text"]   	= testimonialContent;
			param["testimonial_featured"]	= featuredTestimonial;
			param['testimonial_img']		= testimonialImagePreview;
		uploadFiles(uploadURL, param, uploadUpdatedTestimonialImageCompleted);		
	}else {
		$('#list_'+testimonialID+' #message_testimonial_'+testimonialID).prepend(renderPopUpMessage('error', errorMessage));
		scrollToTopOfPage();
	}
});

function uploadUpdatedTestimonialImageCompleted(response){
	var data 			= $.parseJSON(response);
	var testimonialID 	= data['testimonial_id'];
	var checked 		= (data['featured_testimonial'] == '1') ? true : false;
	
	var testimonialName			= data['user_name'];
	var testimonialOtherDetail	= data['user_other_detail'];
	var testimonialContent		= data['testimonial_text'];
	var testimonialUserImage	= data['user_image'];

	if(data['error'] == true) {
		__uploadTestimonialFlag = 0;
		//alert('in here');
		//$('#list_'+testimonialID).children().addClass('preview');
		var messageObject = {
			'body': data['message'],
			'button_yes': 'OK',
		};
		callback_danger_modal(messageObject);

	} else {

		$('#list_'+testimonialID+' #testimonial_name_'+testimonialID).val(testimonialName);
		$('#list_'+testimonialID+' #testimonial_other_detail_'+testimonialID).val(testimonialOtherDetail);
		$('#list_'+testimonialID+' #testimonial_content_'+testimonialID).val(testimonialContent);
		$('#list_'+testimonialID+' #testimonial_upload_image_preview_'+testimonialID).attr('src',testimonialUserImage);

		$('#list_'+testimonialID+' #testimonial_name_preview_'+testimonialID).html(testimonialName);
		$('#list_'+testimonialID+' #testimonial_other_detail_preview_'+testimonialID).html(testimonialOtherDetail);
		$('#list_'+testimonialID+' #testimonial_content_preview_'+testimonialID).html(testimonialContent);
		$('#list_'+testimonialID+' #testimonial_image_preview_'+testimonialID).attr('src',testimonialUserImage);

		$('#list_'+testimonialID+' .list-checkbox-featured').prop('checked',checked);

		__uploadTestimonialFlag == 0;
		$('#list_'+testimonialID).children().addClass('preview');
		var messageObject = {
			'body': 'Testimonial Updated successfully',
			'button_yes': 'OK',
		};
		callback_success_modal(messageObject);
	}
	
}

 /* Remove Testimonial starts */
 $(document).on('click','.remove-testimonial',function(){
	var testimonialID 		= this.id;
	var testimonialName 	= $(this).attr('data-title');
	
	var headerText = 'Are you sure to delete the testimonial of <b>' + testimonialName + '</b>?';
	var messageObject = {
		'body': headerText,
		'button_yes': 'DELETE',
		'button_no': 'CANCEL',
		'continue_params': {
			'testimonial_id': testimonialID
		},
	};
	callback_warning_modal(messageObject, deleteTestimonialConfirmed);
});

function deleteTestimonialConfirmed(params) {
	var testimonialID = params.data.testimonial_id;
	$.ajax({
		url: admin_url + 'environment/remove_testimonial',
		type: "POST",
		data: {
			"testimonial_id": testimonialID
		},
		success: function(response) {
			var data = $.parseJSON(response);
			if (data.error == false) {
				$("#list_"+testimonialID).remove();
				var messageObject = {
					'body': 'Testimonial Deleted successfully',
					'button_yes': 'OK',
				};
				callback_success_modal(messageObject);
			} else {
				var messageObject = {
					'body': 'Error to Delete the testimonial',
					'button_yes': 'OK',
				};
				callback_danger_modal(messageObject);
			}
		}
	});
}

$(document).on('change', '.edit-checkbox-featured', function() {
	if ($(this).is(':checked') == true) {
		var countCheckedCheckboxes 	= $('.edit-checkbox-featured').filter(':checked').length;
		var testimonialID 			= $(this).attr('data-id');
		var errorMsgID				= (testimonialID > 0) ? '#list_'+testimonialID+' #message_testimonial_'+testimonialID : '#message_testimonial_0'; 
		
		if(countCheckedCheckboxes > __testimonialTotalHomeCount) {
			$(this).prop('checked',false);
			$(errorMsgID).prepend(renderPopUpMessage('error', 'Only a total of '+__testimonialTotalHomeCount+' testimonials can be showed in Homepage'));
		}
	}
});

$(document).on('change', '.list-checkbox-featured', function() {
		var countCheckedCheckboxes 	= $('.list-checkbox-featured').filter(':checked').length;
		var testimonialID 			= $(this).attr('data-id');
		var featuredTestimonial 	= 0;

		if ($(this).is(':checked')) {
			featuredTestimonial = 1;
			$('#list_'+testimonialID+' .edit-checkbox-featured').prop('checked',true);
		} else {
			featuredTestimonial = 0;
			$('#list_'+testimonialID+' .edit-checkbox-featured').prop('checked',false);
		}

		if(countCheckedCheckboxes > __testimonialTotalHomeCount) {
			$(this).prop('checked',false);
			$('#list_'+testimonialID+' .edit-checkbox-featured').prop('checked',false);
			var messageObject = {
				'body': 'Only a total of '+__testimonialTotalHomeCount+' testimonials can be showed in Homepage',
				'button_yes': 'OK',
			};
			callback_warning_modal(messageObject);
		} else {
			$.ajax({
				url: admin_url + 'environment/update_featured_testimonial',
				type: "POST",
				data: {
					"testimonial_id"		: testimonialID,
					"testimonial_featured"	: featuredTestimonial
				},
				success: function(response) {
					var data = $.parseJSON(response);
					if (data.error == false) {
						var messageObject = {
							'body': 'Testimonial Updated successfully',
							'button_yes': 'OK',
						};
						callback_success_modal(messageObject);
					} else {
						var messageObject = {
							'body': 'Testimonial was not found',
							'button_yes': 'OK',
						};
						callback_danger_modal(messageObject);
					}
				}
			});
		}
});

/* Mobile Banner Image Upload */
var __uploading_mobile_file_banner = new Array();
$(document).on('change', '#mobile_banner_btn', function(e){
	$('#popUpMessage').remove();
	var _URL = window.URL || window.webkitURL;
	var file, img;
	if ((file = this.files[0])) {
		var fileExtension = file['name'].split('.').pop();
		console.log(file['name']);
		img = new Image();
		img.onload = function () {
			//console.log(file['type']);
			if(this.width > this.height)
			{
				if(this.width >= 720 && this.height >= 405 && this.width <= 1280 && this.height <= 720)
				{
					if(fileExtension == "jpg")
					{
						$('#mobile_progress_div').css('display','block');
						__uploading_mobile_file_banner = e.currentTarget.files;
						//console.log(e.currentTarget.files);
						if( __uploading_mobile_file_banner.length > 1 )
						{
							lauch_common_message('Error Occured', 'You are not allowed to upload more than one file.');
							return false;
						}
						var i                           = 0;
						var uploadURL                   = admin_url+"environment/upload_mobile_banner_image" ; 
						var fileObj                     = new processFileName(__uploading_mobile_file_banner[i]['name']);
						var param                       = new Array;
						param["file_name"]          	= fileObj.uniqueFileName();        
						param["extension"]          	= fileObj.fileExtension();
						param["width"]          		= this.width;
						param["height"]          		= this.height;
						param["file"]               	= __uploading_mobile_file_banner[i];
						uploadFiles(uploadURL, param, uploadMobileBannerImageCompleted);
					}
					else
					{
						$('#mobile_banner_message').prepend(renderPopUpMessage('error', 'Please upload a JPG file'));
						$('#mobile_banner_btn').val('');
						scrollToTopOfPage();
					}
				}
				else
				{
					$('#mobile_banner_message').prepend(renderPopUpMessage('error', 'Please upload an image of min size 720 x 405 and max size 1280 x 720 '));
					$('#mobile_banner_btn').val('');
					scrollToTopOfPage();
				}
			}
			else
			{
				$('#mobile_banner_message').prepend(renderPopUpMessage('error', 'Please upload an image of min size 720 x 405 and max size 1280 x 720'));
				$('#mobile_banner_btn').val('');
				scrollToTopOfPage();
			}
		};
		img.src = _URL.createObjectURL(file);
	}
	
});

function gcd (a, b) {
	return (b == 0) ? a : gcd (b, a%b);
}

function uploadMobileBannerImageCompleted(response)
{
   $('#mobile_progress_div').css('display','none');
   var data = $.parseJSON(response);
   console.log(data);
   if(data['error'] == 'false'){

	var banner_html	= '';
	banner_html		+= '<li class="col-sm-3" id="mobile_banner_'+data['mobile_banner_id']+'">'; 
	banner_html		+= '<span class="remove_banner" id="'+data['mobile_banner_id']+'">&times;</span>' 
	banner_html		+= '<a href="javascript:void(0)" class="banner-thumb mobile-banner-item" id="'+data['mobile_banner_id']+'">';
	banner_html		+= '<img src="'+data['user_image']+'" width="100%">';
	banner_html		+= '<span class="triangle"><i class="icon icon-ok-circled"></i></span>';
	banner_html		+= '</a>';
	banner_html		+= '</li>';

 	$('#mobile_banner_list').prepend(banner_html);
 	$('#mobile_banner_message').prepend(renderPopUpMessage('success', data['message']));
	
   }else{
	$('#mobile_banner_message').prepend(renderPopUpMessage('error', 'Error uploading'));
   }
   	scrollToTopOfPage();
	$('#mobile_banner_btn').val('');
	$('.progress-bar').css('width','0');
}

$(function(){
	$("#mobile_banner_list").sortable({
		placeholder: "ui-state-highlight",
		update: function(event, ui) { 
			updateSectionPositon(ui.item.index(), ui.item[0]['id']);               
		}
	});
	$( "#mobile_banner_list" ).disableSelection();
});

function updateSectionPositon(position, selector) {
	var current_position    = parseInt(position+1);
	var banner_id           = selector.split('_');
		banner_id           = banner_id[2];
	let params = { 
					"is_ajax": true, 
					'structure': $("#mobile_banner_list").sortable('serialize')
				};
	console.log(params);
	$.ajax({
		url: admin_url+'environment/save_mobile_banner_order',
		type: "POST",
		data:params,
		success: function(response) {                                
		}
	});
}

$(document).on('click','.remove_banner',function(){
	var mobileBannerID 		= this.id;
	
	var headerText = 'Are you sure to delete this Mobile Banner?';
	var messageObject = {
		'body': headerText,
		'button_yes': 'DELETE',
		'button_no': 'CANCEL',
		'continue_params': {
			'mobile_banner_id': mobileBannerID
		},
	};
	callback_warning_modal(messageObject, deleteMobileBannerConfirmed);
});

function deleteMobileBannerConfirmed(params) {
	var mobileBannerID = params.data.mobile_banner_id;
	$.ajax({
		url: admin_url + 'environment/remove_mobile_banner',
		type: "POST",
		data: {
			"mobile_banner_id": mobileBannerID
		},
		success: function(response) {
			var data = $.parseJSON(response);
			if (data.error == false) {
				$("#mobile_banner_"+mobileBannerID).remove();
				var messageObject = {
					'body': 'Mobile Banner Deleted successfully.',
					'button_yes': 'OK',
				};
				callback_success_modal(messageObject);
			} else {
				var messageObject = {
					'body': 'Unable to delete mobile banner.',
					'button_yes': 'OK',
				};
				callback_danger_modal(messageObject);
			}
		}
	});
}

$(document).on('click','.mobile-banner-item',function(){
	var mobile_banner_id 	= this.id;
	var mobile_banner_status = 1;
	if($(this).hasClass('active-banner')) {
		$(this).removeClass('active-banner');
		mobile_banner_status = 0;
	} else {
		$(this).addClass('active-banner');
	}
	
	if($('#mobile_banner_list .active-banner').length > 5) {
		$(this).removeClass('active-banner');		
		var messageObject = {
			'body': 'Only a total of five banners can be showed in Mobile Homepage',
			'button_yes': 'OK',
		};
		callback_warning_modal(messageObject);
	} else {
		$.ajax({
			url: admin_url + 'environment/update_mobile_banner',
			type: "POST",
			data: {
				"mobile_banner_id"		: mobile_banner_id,
				"mobile_banner_status"	: mobile_banner_status
			},
			success: function(response) {
				var data = $.parseJSON(response);
				if (data.error == false) {
					//$('#mobile_banner_message').prepend(renderPopUpMessage('error', 'Mobile banner updated successfully'));
				} else {
					$('#mobile_banner_message').prepend(renderPopUpMessage('error', 'Unable to update mobile banner'));
			            scrollToTopOfPage();
				}
			}
		});
	}
});