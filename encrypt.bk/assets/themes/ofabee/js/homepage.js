/*$(document).ready(function(){
	$('#challenge_zone option:nth-child(1)').prop('selected', true);
	cid = $('#challenge_zone option:nth-child(1)').val();
	if(cid != ''){
		str = '';
		for(i=0;i<challenge_details_arr.length;i++){
			if(challenge_details_arr[i].id == cid){
				for(j=0;j<challenge_details_arr[i].challenges.length;j++){
					console.log(challenge_details_arr[i]);
                                        __challenge_id = challenge_details_arr[i].challenges[j].id;
                                        __challenge_link = challenge_details_arr[i].challenges[j].challenge_link; 
					str = str + '<p>' +  challenge_details_arr[i].challenges[j].cz_title + '</p>';
					str = str + '<h3>' + challenge_details_arr[i].challenges[j].challenge_text+ ' ' + challenge_details_arr[i].challenges[j].user_enddate + '</h3>';
					str = str + '<a href="javascript:void(0);" class="'+challenge_details_arr[i].challenges[j].btn_class+'">'+challenge_details_arr[i].challenges[j].challenge_btn+'</a>';
				}
				$("#challenges_user").html(str);
			}
		}
	}
	else{
		$("#challenges_user").html('');
	}
});*/

$(document).on("click",".home-challenge-btn", function(){
    $.ajax({
        url: base_url+'homepage/check_challenge_attempt',
        method: "POST",
        data:{challenge_id: __challenge_id},
        success: function(response){
            var data = $.parseJSON(response);
            console.log(data);
            if(data.error == "true"){
                toastr["error"](data.message);
            }else{
                window.location = __challenge_link;
            }
        }
    });
});

$(document).on("click",".home-challenge-report-btn", function(){
    window.location = __challenge_link;
});

function select_challenge(obj){
	cid = $(obj).val();
	if(cid != ''){
		str = '';
		for(i=0;i<challenge_details_arr.length;i++){
			if(challenge_details_arr[i].id == cid){
				for(j=0;j<challenge_details_arr[i].challenges.length;j++){
                                        __challenge_id = challenge_details_arr[i].challenges[j].id;
                                        __challenge_link = challenge_details_arr[i].challenges[j].challenge_link; 
					str = str + '<p>' +  challenge_details_arr[i].challenges[j].cz_title + '</p>';
					str = str + '<h3>' + challenge_details_arr[i].challenges[j].challenge_text+' '+ challenge_details_arr[i].challenges[j].user_enddate + '</h3>';
					str = str + '<a href="javascript:void(0);" class="'+challenge_details_arr[i].challenges[j].btn_class+'">'+challenge_details_arr[i].challenges[j].challenge_btn+'</a>';
				}
				$("#challenges_user").html(str);
			}
		}
	}
	else{
		$("#challenges_user").html('');
	}
}

$("#searchid").keypress(function(e){

	var val = $("#searchid").val().trim();
	var res = val.split(" ");
	str = res.join('+');
	if(e.which == 13){
		if(val.length=="0"){
			$(this).css('border', '2px solid rgb(220, 81, 81)'); 
		}
		else{
			window.location = base_url + 'course/listing/'+str;
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
		window.location = base_url + 'course/listing/'+str;
	}
});

/*$(document).ready(function(){
	
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
});*/

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

$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });

function show_video_modal(video){
	$("#vid_frame").attr('src',video);
	$("#video1").modal('show');
}

$("#video1").on("hidden.bs.modal", function () {
    $("#vid_frame").attr('src','');
})