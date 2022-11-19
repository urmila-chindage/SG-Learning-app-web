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
					$('.icon-heart').addClass('wish-added');
					$("#whishdiv_"+key).html(data.str);
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
					$('.icon-heart').removeClass('wish-added');
					$("#whishdiv_"+key).html(data.str);
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