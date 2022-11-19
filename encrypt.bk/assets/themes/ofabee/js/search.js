$(document).ready(function(){
	
	for(i=0;i<rattings.length;i++){

		$("#"+rattings[i][0]).rateYo({
		    starWidth: "18px",
		    rating: rattings[i][1],
		    readOnly: true
		  });
		if(rattings[i][1]!='0'){
				$("#"+rattings[i][0]).next('span').append(rattings[i][1]);
		}
	}

	$(".wish-icon-search").on('click', function(e) {e.preventDefault(); });
	
});

$("#searchid").keypress(function(e){

	var val = $("#searchid").val().trim();
	var res = val.split(" ");
	str = res.join('+');
	if(e.which == 13){
		if(val.length=="0"){
			$(this).css('border', '2px solid rgb(220, 81, 81)'); 
		}
		else{
			window.location = base_url + 'search/index/?query='+str;
		}
	}
});

$("#searchbtn").click(function(){

	var val = $("#searchid").val().trim();
	var res = val.split(" ");
	str = encodeURIComponent(val);
	if(val.length=="0"){
		$("#searchid").css('border', '2px solid rgb(220, 81, 81)'); 
	}
	else{
		window.location = base_url + 'search/index/?query='+str;
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
					$(".wish-icon-search").on('click', function(e) {  e.preventDefault(); });
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
					$(".wish-icon-search").on('click', function(e) {  e.preventDefault(); });
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

function create_test(){

	$("#user_gen_q_count").each(function (e){
	});
}

