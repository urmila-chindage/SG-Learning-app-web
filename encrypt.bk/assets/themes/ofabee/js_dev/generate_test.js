$(document).ready(function(){
	var __duration;
	$('.difficulty_drop li').on('click', function(){
    	var difficulty = $(this).attr('data-value');
    	$.ajax({
			method: "POST",
			dataType : 'html',
			url: base_url+'user_generated_test/get_category_list',
			data: {'difficulty': difficulty },
			success: function(response){
				$('.category-sel').append(response);
				$('.category-sel').multiselect({
		            includeSelectAllOption: true,
		            maxHeight: 200,
					buttonContainer: '<div class="btn-group topic-group" />',
					 buttonClass: 'btn btn-default btn-topic'
		        });

		        $('.topic-group ul').addClass('topic-dropdown');
			}
		});

	})

$(document).on('click','.duration_drop li', function(){
	__duration =  $(this).attr('data-value');
})


$(document).on('click','.create', function(){

	duration   = __duration;
	categoryid = $('.category_id').val();
	user_id    = $('.user_id').val();
	temp  = new Array();
	temp2 = new Array();
	err   = 0;
	cnt   = 0;

	$(".topic-dropdown li.active").each(function (i,obj){
		var count = 2;
		temp2[0] = $(obj).find('input').val();
		temp2[1] = count;
       // if(count.trim() != ''){
             temp.push(JSON.stringify(temp2));
       // }
                
	});

	if(duration == ''){
		err = 2;
	}
	
	if(err == 2){
            var error_html = '<div class="alert alert-error alert-danger" id="alert_danger">';
            error_html+= '    <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>';
            error_html+= '  Please select duration  </div>';
            error_html+= '</div>';
	    $("#message").css('display','block');
            $("#message").html(error_html);
	}else{
		str = JSON.stringify(temp);
		$.ajax({
			method: "POST",
			url: base_url+'user_generated_test/generate_test',
			data: {'course_details': str, category_id: categoryid, user_id: user_id, duration: duration},
			success: function(response){
				data = $.parseJSON(response);
               // window.open(data.link,'_blank');
			}
		});
	}
        
        
})
})