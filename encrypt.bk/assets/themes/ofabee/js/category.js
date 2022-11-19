$(document).ready(function(){
	
	for(i=0;i<rattings.length;i++){

		$("#"+rattings[i][0]).rateYo({
		    starWidth: "18px",
		    rating: rattings[i][1],
		    readOnly: true
		  });
		if(rattings[i][1]!="0"){
			$("#"+rattings[i][0]).next('span').append(rattings[i][1]);
		}
	}
});

function add_wishlist(cid, uid, obj){

	key = $(obj).attr('data-key');
	if(uid != ''){
		$.ajax({
			url: base_url+'course/change_whishlist',
			method: "POST",
			data: {
				cid: cid,
				uid: uid,
				stat: 1,
				page: 'search'
			},
			success: function(response){
				data = $.parseJSON(response);
				if(data.stat == '1'){
					$("#whishdiv_"+key).html(data.str);
					$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });
				}
				else{
					window.location = base_url+'login';
				}
				
			}
		});
	}
	else{
		window.location = base_url+'login';
	}
	
}

function remove_wishlist(cid, uid, obj){

	key = $(obj).attr('data-key');
	if(uid != ''){
		$.ajax({
			url: base_url+'course/change_whishlist',
			method: "POST",
			data: {
				cid: cid,
				uid: uid,
				stat: 0,
				page: 'search'
			},
			success: function(response){
				data = $.parseJSON(response);
				if(data.stat == '1'){
					$("#whishdiv_"+key).html(data.str);
					$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });
				}
				else{
					window.location = base_url+'login';
				}
				
			}
		});
	}
	else{
		window.location = base_url+'login';
	}

}

$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });

function create_test(){

	duration = $("#duration").val();
	temp  = new Array();
	temp2 = new Array();
	err   = 0;
	cnt   = 0;

	$(".user_gen_q_count").each(function (i,obj){
		temp2[0] = $(obj).attr('data-category');
		temp2[1] = $(obj).val();
		console.log($(obj).val().trim());
		if($(obj).val()){
			cnt++;
		}
                
		max         = $(obj).attr('max');
		if(parseInt(max) < parseInt($(obj).val())){
			err   = 1;
		}
                if($(obj).val().trim() != ''){
                    temp.push(JSON.stringify(temp2));
                }
	});

	if(duration == ''){
		err = 2;
	}
	
	if(cnt == 0){
		err = 3;
	}

	if(err == 1){
            var error_html = '<div class="alert alert-error alert-danger" id="alert_danger">';
            error_html+= '    <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>';
            error_html+= '  Number of questions greater than maximum  </div>';
            error_html+= '</div>';
	    $("#message").css('display','block');
            $("#message").html(error_html);
	}
	else if(err == 2){
            var error_html = '<div class="alert alert-error alert-danger" id="alert_danger">';
            error_html+= '    <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>';
            error_html+= '  Please select duration  </div>';
            error_html+= '</div>';
	    $("#message").css('display','block');
            $("#message").html(error_html);
	}
	else if(err == 3){
            var error_html = '<div class="alert alert-error alert-danger" id="alert_danger">';
            error_html+= '    <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>';
            error_html+= '  Please enter number of questions  </div>';
            error_html+= '</div>';
	    $("#message").css('display','block');
            $("#message").html(error_html);
	}
	else{
		str = JSON.stringify(temp);
		$.ajax({
			method: "POST",
			url: base_url+'user_generated_test/generate_test',
			data: {'course_details': str, category_id: categoryid, user_id: user_id, duration: duration},
			success: function(response){
				data = $.parseJSON(response);
				$("#myModal").modal('hide');
                location.href= data.link;
			}
		});
	}
        
        
}
  //  console.log($(this).val());
  //  alert('tet');
    //preventSpecialCharector($(this).val());
    //preventAlphabets($(this).val());

function preventSpecialCharector(e)
{
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}

function preventAlphabets(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    console.log(charCode);
    if ((charCode != 46 && charCode > 31
    && (charCode < 48 || charCode > 57)) || charCode == 46 )
        return false;

    return true;
}