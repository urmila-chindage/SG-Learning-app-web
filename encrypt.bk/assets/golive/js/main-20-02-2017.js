/************************
Query string parsing 
**************************/
var QueryString = function() {
    var query_string = {};
    var query = window.location;
    query = query.toString();
    query = query.split('?');
  if(query[1]){
    query = query[1];
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (typeof query_string[pair[0]] === "undefined") {
            query_string[pair[0]] = decodeURIComponent(pair[1]);
        } else if (typeof query_string[pair[0]] === "string") {
            var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];
            query_string[pair[0]] = arr;
        } else {
            query_string[pair[0]].push(decodeURIComponent(pair[1]));
        }
    }
    return query_string;
   }else{
    return false;
   }
}();
/************************
Global Variables 
**************************/
if (QueryString.room) {
    var room   = QueryString.room;
}
if (QueryString.name) {
    user_name   = QueryString.name;
}
if (QueryString.email) {
    user_email   = QueryString.email;
}
if (QueryString.user_id) {
    user_id       = QueryString.user_id;
}
if (QueryString.mode) {
    mode       = QueryString.mode;
}
//alert(room+' // '+user_name+' // '+user_email+' // '+user_id+' // '+mode);
//alert(room+'//'+user_id+'//'+user_email+'//'+mode);
//var node_url    = "http://live.nagathanschoolcollege.org:8134";
var node_url    = "http://138.201.204.48:8080";
//var rtmp_url    = "rtmp://149.202.218.31:1936/nagathan";
var rtmp_url     = "rtmp://138.201.196.47:1935/liveskills";
var rtmp_http    = "http://138.201.196.47:1935/liveskills";
var stream_pub  = 0;
var player_loaded = 0;
/************************
Document ready function 
************************/
$(document).ready(function(){
    setDimension();
    if(mode == '1'){
        commonTimerFunction();
    }
    $('#rtmp-url').html(rtmp_url);
    $('#stream-name').html(room);
 	$('#record-start').click(function(){
 	  if($('#record-start').html() == 'Start Recording'){
                    $('#record-start').html('Stop Recording');
                    $('#record-start').addClass('rec-active');
                    
 	  }else{
                    $('#record-start').html('Start Recording');
                    $('#record-start').removeClass('rec-active');
 	  }
 	})
    if(mode == '1'){
       $('#leave').html('Stop Session');
       $('#admin_instr').show();
    }else{
       $('#record-start').remove();
       //$('#user_instr').show();
       
    }
    $('#leave').click(function(){
    	if(mode == '1'){
            if(stream_pub == 1){
                swal("Message", "Your stream is not closed yet. Please unpublish the stream in your desktop encoder")
            }else{
            	//window.location = '/';
                myWindow.close();
            }
    	}else{
    		//window.location = '/';
            myWindow.close();
    	}
    	
    })
    connectSocket();
    var prevdata = localStorage.getItem("chat_" + user_id+room);
            if (prevdata) {
                $("#chat_list").html(prevdata);
            }
    $("#chat_list").scrollTop($("#chat_list").prop("scrollHeight"));
    checkFlash();
});
function closedWin() {
    confirm("close ?");
    return false; /* which will not allow to close the window */
    /*if(mode == '1'){
            if(stream_pub == 1){
                swal("Message", "Your stream is not closed yet. Please unpublish the stream in your desktop encoder before closing this tab");
                return false;
            }else{
            	window.location = '/';
            }
	}else{
		window.location = '/';
	}*/
}
window.onbeforeunload = confirmExit;
    function confirmExit() {

        if (stream_pub == 1 && mode == 1) {
        	swal({
			  title: "Message",
			  text: "Your stream is not closed yet. Please unpublish the stream in your desktop encoder before closing this tab",
			  html: true
			});
           return false;
        }
    }

function setDimension(){
    var height = $(window).height();
    $('#chat_list').height(height-100);
}
/************************
Connect to nodejs server 
*************************/
/*
socket script started
*/
function connectSocket() {
    socket = io.connect(node_url);
    socket.on("connect", function(id) {
        var user_obj = {};
        user_obj['user_name'] = user_name;
        user_obj['room']      = room;
        user_obj['mode']      = mode;
        user_obj['user_id']   = user_id;
        user_obj['avatar_path'] = avatar_path;
        socket.emit('addUser',user_obj);
    });
    socket.on("id", function(id) {
        socket_id = id;
    });
   
    socket.on("onUserList", function(data) {
        console.log(data);
        renderUserList(data);
    });
    socket.on("onReceiveSendToAll", function(data) {
        onReceiveSendToAll(data);
    });
    socket.on("onReceiveSendToOne", function(data) {
        //onReceiveSendToOne(data);
    });
}
/***********************
function for getting and 
routing sent to all
************************/
function onReceiveSendToAll(obj){
    var method = obj['method'];
    var curr_room = obj['room'];
    if(curr_room != room){
        //You are not allowed to 
        //receive this signal
        return;
    }
    switch (method) {   
            case 'chat':
                loadChat(obj.name,obj.message,obj.img,false);
                break;
            default:
                console.log("Unrecognized message: " + message.id);
        }

}
$('.send-chat').click(function(){
    var msg = $("#chat-input").val();
        if(msg){
            loadChat(user_name,msg,avatar_path,true);
            $("#chat-input").val('');
        }
})
$("#chat-input").keypress(function(event){
    if(event.keyCode == 13)
    {
        var msg = $("#chat-input").val();
        if(msg){
            loadChat(user_name,msg,avatar_path,true);
            $("#chat-input").val('');
        }
        
    }

});
/***********************
function for send data
to all users
************************/
function sendToAll(value_object){
    value_object['room']         = room;
    socket.emit('sendToAll',value_object);
    console.log(value_object);
}
/*****************************
Function for rendering userlist
******************************/
function renderUserList(list){
    var user_html = '';
   for(var key in list){
     if(list.hasOwnProperty(key)){
        var user      = list[key];
            user_html += '<p class="member-name">';
            user_html += '<img src="'+user['avatar_path']+'" class="img-circle img-sm img-members"> ';
            user_html += user['user_name']+'</p>';
     }
   }
   $('.chat-participants').html(user_html);
}
function loadChat(name,message,img,send){
    var chat_html = '';
    if(send){
        chat_html += '<li class="mar-btm">';
        chat_html += '   <div class="media-left">';
        chat_html += '      <img src="'+img+'" class="img-circle img-sm" alt="Profile Picture">';
        chat_html += '   </div>';
        chat_html += '   <div class="media-body pad-hor">';
        chat_html += '       <a class="media-heading">'+name+'</a>';
        chat_html += '      <div class="speech">';
        chat_html += '          <p>'+message+'</p>';
        chat_html += '       </div>';
        chat_html += '   </div>';
        chat_html += '</li>';
        var chat_data = {};
        chat_data['method']  = 'chat';
        chat_data['name']    = name;
        chat_data['message'] = message;
        chat_data['img']     = img;
        sendToAll(chat_data);
    }else{
        chat_html += '<li class="mar-btm">';
        chat_html += '<div class="media-right">';
        chat_html += '<img src="'+img+'" class="img-circle img-sm" alt="Profile Picture">';
        chat_html += '</div>';
        chat_html += '<div class="media-body pad-hor speech-right">';
        chat_html += '       <a class="media-heading">'+name+'</a>';
        chat_html += '<div class="speech">';
        chat_html += '<p>'+message+'</p>';
        chat_html += '</div>';
        chat_html += '</div>';
        chat_html += '</li>';
    }
    $('#chat_list').append(chat_html);
    $("#chat_list").scrollTop($("#chat_list").prop("scrollHeight"));
    if (typeof(Storage) !== "undefined") {
        localStorage.setItem("chat_" + user_id+room, $("#chat_list").html());
    }
 
}
$('.livevideotabs li').click(function(){
    if(this.id == 'tabtwo'){
        $('#box-one').hide();
        $('#box-two').show();
        $('#tabtwo').addClass('active');
        $('#tabone').removeClass('active');
    }else{
         $('#box-one').show();
        $('#box-two').hide();
        $('#tabtwo').removeClass('active');
        $('#tabone').addClass('active');
    }
});

/***********************************
Load jwplayer on viewer side 
************************************/
function reloadPlayer(){
    if(stream_pub == 0){
       return;
    }
    $('.video-area').html('');
    $('.video-area').html('<div style="height:100%;" id="player"></div>');
    setTimeout(loadPlayer,200);

}
function unLoadPlayer(){
    player_loaded = 0;
    $('.video-area').html('');
    $('.video-area').html('<div style="height:100%;" id="player"></div>');
}
var record_saved = 0;
function loadPlayer(){
    if(stream_pub == 0){
       return;
    }
    var volume = 1;
    if(mode == '1'){
       volume = 0;
    }
    if(record_saved == 0 && mode == '1'){
        saveRecording();
    }
    
   if(flash == 1){
            player_loaded = 1;
            var playerInstance1=jwplayer("player");
            playerInstance1.setup({
            width:'100%',
            height:'100%',
            playlist: [{
                     sources: [{ 
                      file: rtmp_url+"/"+room
                  }]
              }],
            autostart:true,
            bufferlength:0,
            primary: "flash",
            Volume:volume
            });
            jwplayer().onError(function(){
                reloadPlayer();
            });

            jwplayer("player").onComplete(function(){
                 reloadPlayer();
            });
   }else{
        file_name     =  rtmp_http+"/"+room+"/playlist.m3u8"; 
        loadVideoJsPlayer(file_name);
        if(device == 'web'){
            swal({
              title: "No flash",
            text: "Flash player plugin is not installed in your browser or it is not enabled",
            type: "warning",
            showCancelButton: true,
             confirmButtonColor: "#DD6B55",
            confirmButtonText: "Install Flash",
            closeOnConfirm: false
            },
            function(){
                    window.open('https://get.adobe.com/flashplayer/');
            });
        }
        //loadMobilePlayer(file_name);
   }
    
}
function loadMobilePlayer(file_name){
   player_loaded = 1;           
        var video_html = '<video style="width:100% !important;height:100% !important;" width=100% height=100% autoplay>';
            video_html += '<source src="'+file_name+'" type="application/x-mpegURL"/>';
            video_html += '</video>';
        $('#player').html(video_html);
}
/********************************************
commo timer for this application 
********************************************/
function loadVideoJsPlayer(file_name){
        player_loaded = 1;           
        var video_html = '<video id=example-video style="width:100% !important;height:100% !important;" width=100% height=100% class="video-js vjs-default-skin" controls autoplay>';
            video_html += '<source src="'+file_name+'" type="application/x-mpegURL"/>';
            video_html += '</video>';
        $('#player').html(video_html);
        videojs('example-video', {
            controls: true,
          }, function(){
            var player = this;
            window.player = player;
          })
}
function saveRecording(){
    $.ajax({
                    url: '../../live_service/SaveRecordDetails',
                    data: { tittle: lecture_name,session_id: room, course_id: room, video_name: room ,type: '1'},
                    method: "POST",
                    success: function(d) {                      
                           record_saved = 1; 
                    }
            });
}
var count = 0;
function commonTimerFunction(){

    count ++;
    if(count % 5 == 0){
        //alert('send');
       $.ajax({
                    url: '../getLiveStatus/'+room+'?t='+new Date().getTime(),
                    method: "GET",
                    dataType: 'json',
                    success: function(d) {                      
                       var status = d['status'];
                       if(status == '1'){
                         stream_pub = 1;

                         if(mode == '1'){
                           $('#admin_instr').hide();
                         }else{
                           $('#user_instr').hide();
                         }
                         if(player_loaded == 0){
                            loadPlayer();
                         } 
                       }else{
                         stream_pub = 0;
                         if(mode == '1'){
                           $('#admin_instr').show();
                         }else{
                           $('#user_instr').show();
                         }
                         unLoadPlayer();
                       }                       
                    }
            });
    }
    setTimeout(commonTimerFunction,1000);
}
function checkFlash(){
    if ((navigator.appName == "Microsoft Internet Explorer" &&
        navigator.appVersion.indexOf("Mac") == -1 &&   navigator.appVersion.indexOf("3.1") == -1) ||

        (navigator.plugins && navigator.plugins["Shockwave Flash"])|| navigator.plugins["Shockwave Flash 2.0"]){
         flash = 1;
             if(mode == '0'){
                stream_pub = 1;
                loadPlayer();
             }
         
        }else {
         flash = 0;
         if(mode == '0'){
            stream_pub = 1;
            loadPlayer();
         }
        }
}