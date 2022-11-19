__progress = false;
function add_wishlist(cid, uid, obj){
	key = $(obj).attr('data-key');
	if(uid != ''){
		if(!__progress){
			__progress = true;
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
					__progress = false;
					data = $.parseJSON(response);
					if(data.stat == '1'){
						$("#whishdiv_"+key).html(data.str);
						//$('#'+cid).addClass('wish-added');
						$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });
					}
					else{
						window.location = base_url+'login';
					}
					
				}
			});
		}else{
			console.log('on progress');
		}
	}
	else{
		window.location = base_url+'login';
	}
	
}

function remove_wishlist(cid, uid, obj){
	key = $(obj).attr('data-key');
	if(uid != ''){
		if(!__progress){
			__progress = true;
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
					__progress = false;
					data = $.parseJSON(response);
					if(data.stat == '1'){
						//$('#'+cid).removeClass('wish-added');
						$("#whishdiv_"+key).html(data.str);
						$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });
					}
					else{
						window.location = base_url+'login';
					}
					
				}
			});
		}else{
			console.log('on progress');
		}
	}
	else{
		window.location = base_url+'login';
	}
}
