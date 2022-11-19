$(document).ready(function(){
	$('#save_survey_details').click(function(){
		var survey_title   =  $('#s_title').val();
		survey_title  	   =  survey_title.replace(/["<>{}]/g, '');
		survey_title  	   =  survey_title.trim();
		var s_description  =  $('#s_description').val();
		var start_date     =  $('#start_date').val();
		var end_date       =  $('#end_date').val();
		var s_content      =  $('#s_content').val();
		var errorCount     =  0;
		var errorMessage   =  '';

		if( survey_title == '')
		{
			errorCount++;
			errorMessage += 'please enter survey title <br />';
		}
		if( s_description == '')
		{
			errorCount++;
			errorMessage += 'please enter survey description <br />';
		}
		if( start_date == '')
		{
			errorCount++;
			errorMessage += 'please enter survey start date <br />';
		}
		if( end_date == '')
		{
			errorCount++;
			errorMessage += 'please enter survey end date <br />';
		}
		if( s_content == '')
		{
			errorCount++;
			errorMessage += 'please enter survey html code <br />';
		}

		cleanPopUpMessage();
		if( errorCount == 0 )
		{
			$.ajax({
			    url: admin_url+'survey/enable_survey',
			    type: "POST",
			    data:{"is_ajax":true, 'survey_title':survey_title, 's_description':s_description, 'start_date':start_date, 'end_date':end_date, 's_content':btoa(s_content)},
			    success: function(response) {
			        var data  = $.parseJSON(response);
			        if(data['error'] == false)
			        {
			            $('#page_form').prepend(renderPopUpMessage('success', data['message']));
			            scrollToTopOfPage();
			        }
			        else
			        {
			            $('#page_form').prepend(renderPopUpMessage('error', data['message']));
			            scrollToTopOfPage();
			        }
			    }
			});
		}
		else
		{
			$('#page_form').prepend(renderPopUpMessage('error', errorMessage));
			scrollToTopOfPage();
		}
	});
});