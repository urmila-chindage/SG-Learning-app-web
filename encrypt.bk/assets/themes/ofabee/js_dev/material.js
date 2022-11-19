$(document).ready(function(e) {
    $('.discussions').hide();
    $('#add_discussion_input').redactor({		
        imageUpload: admin_url+'configuration/redactore_image_upload',		
        callbacks: {		
            imageUploadError: function(json, xhr)		
            {		
                 // var erorFileMsg = "This file type is not allowed. upload a valid image.";		
                 // $('#course_form').prepend(renderPopUpMessage('error', erorFileMsg));		
                 // scrollToTopOfPage();		
                 // return false;		
            }		
        }  		
    });	
    if(__tab_id!='')
    {
        //$('#'+__tab_id).trigger('click');
    }
    //$('#browse_curriculum').trigger('click');
    questionTimeOutSearch();
});		
			
if(ratting != 0){		
    __ratting = ratting;		
}else{		
    __ratting = '';		
}

var __ratedFill = '';

$(document).on('click', '#popup-rating', function(){
    
    //$('#pop_rating').modal('show');
//    alert("test");
//    $("#rate_student").rateYo({
//        starWidth: "25px",
//        rating: ratting
//    });
});
		
$(document).ready(function(){		
    $('#popup-rating').animate({'opacity': 1}, 1000);		
    $('.maincontent').bind('contextmenu', function(e) {		
        return false;		
    });		

    $("#rateYo").rateYo({		
        starWidth: "18px",		
        rating: ratting,		
        readOnly: true		
    });		
    $("#rate_student").rateYo({		
        starWidth: "25px",		
        rating: ratting		
    });		
//popup for star rating starts here		
    var data = "<div>";			
       data += "   <span class='ts-md-12'>";		
       if(__check_user_rating == 1){		
            data += "       <b>Your rating</b>";		
       }else{		
            data += "       <b>Rate this course</b>";		
       }		
       data += "   </span>";		
       data += "   <span class='star-rating pop-over'>";		
       data += "       <div class='ratting'>";		
       data += "           <div id='rate_student' class='custom-star'></div>";		
       data += "           <span class='count-rating'>"+__ratting+"</span>";		
       data += "       </div>";		
       data += "       <span class='ts-md-12'>";		
       if(__check_user_rating == 1){		
            data += "           <label>"+__user_review+"</label>";		
       }else{		
            data += "           <textarea id='review_course' maxlength='500' placeholder='Why did you leave this rating?' class='comment-report'></textarea>";		
       }		
       data += "       </span>";		
       data += "       <span class='ts-md-12'>";		
       if(__check_user_rating == 1){		
            data += "           <button class='orange-btn read-rating-review'>OK</button>";		
       }else{		
            data += "           <button class='orange-btn rating-review-submit'>Submit</button>";		
       }		
       data += "       </span>";		
       data += "   </span>";		
       data += "</div>";		

        // setting configuration, this is the default configs, you can ommit this		
        var config = {		
                duration: 100, // Duration of box show/hide		
                bgClass: 'bg overlay-content-area', // full with transparent element class name		
                bgColor: 'rgba(0,0,0,.45)', // full with transparent background color		
                boxClass: 'overlay-box-big', // overlay container box class name		
                closeBtnClass: 'overlay-closeBtn', // close button class name		
                closeBtnText: 'x' // close button text appeared in req squre close button, you can insert image through css		
        };		

        if(__check_user_rating == 1){ 		
            $('#popup-rating').overlay(data, config, function(){		
                $("#rate_student").rateYo({		
                    starWidth: "18px",		
                    rating: ratting,		
                    fullStar: true,		
                    readOnly: true		
                });		
                console.log("rating popup created"); //trigger on create		
            },function(){		
                console.log("rating popup closed"); //trigger on close		
            });		
        }else{		
            if(__check_lecture_completed == 2){		
                $('#popup-rating').overlay(data, config, function(){		
                    $("#rate_student").rateYo({		
                        starWidth: "18px",		
                        rating: ratting,		
                        fullStar: true,		
                        load: true		
                    });		

                    $("#rate_student").rateYo("option", "onChange", function () {		
                        /* get the rated fill at the current point of time */		
                        __ratedFill = $("#rate_student").rateYo("rating");		
                        console.log("The color of rating is " + __ratedFill);		
                    });		
                    console.log("rating popup created"); //trigger on create		
                },function(){		
                    console.log("rating popup closed"); //trigger on close		
                });		
            }else if(__check_lecture_completed > 2){		
                $('#popup-rating').overlay(data, config, function(){		
                    $("#rate_student").rateYo({		
                        starWidth: "18px",		
                        rating: ratting,		
                        fullStar: true,		
                        load: true		
                    });		

                    $("#rate_student").rateYo("option", "onChange", function () {		
                        /* get the rated fill at the current point of time */		
                        __ratedFill = $("#rate_student").rateYo("rating");		
                        console.log("The color of rating is " + __ratedFill);		
                    });		
                    console.log("rating popup created"); //trigger on create		
                },function(){		
                    console.log("rating popup closed"); //trigger on close		
                });		
            }else{
                $('#popup-rating').overlay(data, config, function(){		
                    $("#rate_student").rateYo({		
                        starWidth: "18px",		
                        rating: ratting,		
                        fullStar: true,		
                        load: true		
                    });		

                    $("#rate_student").rateYo("option", "onChange", function () {		
                        /* get the rated fill at the current point of time */		
                        __ratedFill = $("#rate_student").rateYo("rating");		
                        console.log("The color of rating is " + __ratedFill);		
                    });		
                    console.log("rating popup created"); //trigger on create		
                },function(){		
                    console.log("rating popup closed"); //trigger on close		
                });
            }		
        }		
//popup for star rating ends here		
});		
			
$(document).on('click','.read-rating-review',function(){		
    $(".overlay-content-area").hide();		
});		

$(document).on('click','.rating-review-submit',function(){		
    var review = $("#review_course").val();		

    $.ajax({		
        url: webConfigs('site_url')+'/material/save_rating_review',		
        type: "POST",		
        data:{"is_ajax":true, 'course_id':__course_id, 'rating':__ratedFill, 'review':review },		
        success: function(response) {		
            var data = $.parseJSON(response);		
            console.log(data);		
            $(".overlay-content-area").hide();		
            location.reload();		
        }		
    });   		
});

var __lecture_id = 0;
$(document).ready(function(){
    var lecture_id = window.location.hash.substr(1);
    $('.lectures-custom').removeClass('activelist');
    if(lecture_id!='')
    {
        $('#lecture_'+lecture_id).addClass('activelist');
        loadLecture(lecture_id);
    }else{
        $.ajax({
            url: webConfigs('site_url')+'/material/last_played_lecture',
            type: "POST",
            data:{"is_ajax":true, 'course_id':__course_id},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['lecture_id'] > 0)
                {
                    loadLecture(data['lecture_id']);
                    $('#lecture_'+data['lecture_id']).addClass('activelist');
                }
                else
                {
                    $('.lectures-custom:first').trigger('click');
                }            
            }
        });  
    }
    startTimer(__course_preview_time);		
});		
$(document).ready(function(){		
    $("#enter_comment").keyup(function(e){		
        if(e.keyCode=='13'){		
            saveCourseComment();		
        }		
    });
});

$(document).on('click','.reply',function(){		
	        hid_val = this.id;		
	});		
			
	var timeRunning = false;		
	var __time_taken    = 0;		
	function startTimer(duration) {		
	    var timer = duration, minutes, seconds;		
	    timeRunning = true;		
	    setInterval(function () {		
	        if(timeRunning == true)		
	        {		
	            minutes = parseInt(timer / 60, 10)		
	            seconds = parseInt(timer % 60, 10);		
			
	            minutes = minutes < 10 ? "0" + minutes : minutes;		
	            seconds = seconds < 10 ? "0" + seconds : seconds;		
			
	            $('#timer_free_preview_course').html("Your trial expires in " + minutes + " : " + seconds);		
			
	            __time_taken = parseInt(duration - parseInt((minutes*60)+seconds));		
	            if(__time_taken != 0){		
	                if(__time_taken % 5 == 0){		
	                    $.ajax({		
	                        url: webConfigs('site_url')+'/material/save_remain_preview_time',		
	                        type: "POST",		
	                        data:{"is_ajax":true, 'course_id':__course_id},		
	                        success: function(response) {		
	                            console.log(__time_taken);		
	                            console.log(response);		
	                            //console.log(webConfigs('site_url')+'/'+__course_slug);		
	                            if(--timer <= 0){		
	                                window.location = webConfigs('site_url')+'/'+__course_slug;		
			
	                            }		
	                        }		
	                    });		
	                }		
	            }		
	            if (--timer < 0) {		
	                		
	                //console.log("end");		
	            }		
	        }		
	    }, 1000);		
	    		
	   		
	    		
	}		
			
	function saveCourseComment(){		
	    var comment = $("#enter_comment").val();		
	    if(comment==""){		
	    }		
	    else{		
	            $.ajax({		
	                url: webConfigs('site_url')+'/material/save_course_comment',		
	                type: "POST",		
	                data:{"is_ajax":true, 'course_id':__course_id, 'comment':comment, 'parent_id':hid_val},		
	                success: function(response) {		
	                    $("#enter_comment").val('');		
	                    var data  = $.parseJSON(response);		
	                    renderCourseComment(data);		
	                    //console.log(data);		
	                }		
	            });		
	    }    		
	}		
			
	function renderCourseComment(data){		
	    var courseCommentHtml  = '';		
	    for(var i =0; i < data['comments'].length; i++){		
	        courseCommentHtml+= '<span class="discussion ts-md-12" id="'+data['comments'][i]['id']+'">';		
	        courseCommentHtml+= '    '+data['comments'][i]['comment'];		
	        courseCommentHtml+= '</span>';		
	        if(data['comments'][i]['parent_id']=='0')		
	        {		
	            courseCommentHtml+= '<span class="discussioncomment">';		
	            courseCommentHtml+= '<a href="#" id="'+data['comments'][i]['id']+'" class="orangelink reply"><span class="orangeicon icon-comment">';		
	            courseCommentHtml+= '</span>';		
	            courseCommentHtml+= 'Reply to this comment</a></span>';		
	        }		
	        else		
	        {		
	            courseCommentHtml+= '<span class="discussioncomment">';		
	            courseCommentHtml+= '<a href="#" id="" class="orangelink"><span class="orangeicon icon-comment">';		
	            courseCommentHtml+= '</span>';		
	            courseCommentHtml+= 'This is a Reply</a></span>';		
	        }                            		
	    }		
	    $("#comment_display").html(courseCommentHtml);		
	}

function loadLectureInit(params)
{
    $('.lectures-custom').removeClass('activelist');
    $('#lecture_'+params.data.lecture_id).addClass('activelist');
    loadLecture(params.data.lecture_id);
}


var __lastLecture = '';
var __lectureRequestInProgress = false;
function loadLecture(id){
    console.log(id);
    __lecture_id = id;
    var base_url = __base_url;
    if(__lectureRequestInProgress == true)
    {
        return false;
    }
    if( __lastLecture != '' )
    {
        clearLectureCache(__lastLecture);
    }
    //window.location = webConfigs('site_url')+'/materials/course/'+__course_id+((__tab_id!='')?'/'+__tab_id:'')+'#'+id;
    __lectureRequestInProgress = true;
    $.ajax({
        url: webConfigs('site_url')+'/material/item/'+id,
        type: "POST",
        data:{"is_ajax":true},
        success: function(response) {

            var data        = $.parseJSON(response);
            if(data['error'] == 'false')
            {
                var lecture     = data['content'];
                __lastLecture   = lecture['cl_lecture_type'];
                console.log(base_url);
                $('#lecture_title').html(lecture['cl_lecture_name']);
                //setting previous and next button
                $('#previous_button, #next_button').unbind().css('visibility', 'hidden');
                
                var previous_lecture = $('#lecture_'+lecture['id']).attr('data-prev');
                var next_lecture     = $('#lecture_'+lecture['id']).attr('data-next');
                if(previous_lecture > 0)
                {
                    $('#previous_button').css('visibility', 'visible').click({"lecture_id": previous_lecture}, loadLectureInit);       
                }
                if(next_lecture > 0)
                {
                    $('#next_button').css('visibility', 'visible').click({"lecture_id": next_lecture}, loadLectureInit);       
                }
                //End
                
                $('#limit_reached_container').hide();
                if(lecture['ll_attempt'] > lecture['cl_limited_access'] && lecture['cl_limited_access'] != 0)
                {
                   $('.innercontent').hide();
                   $('#limit_reached_container').show();
                }
                else
                {
                    switch(lecture['cl_lecture_type']){
                        case '1':
                            renderVideo(lecture);
                        break;
                        case '2':
                            renderDocument(lecture);
                        break;
                        case '3':
                             if(lecture['cl_limited_access'] > 0 && (lecture['cl_limited_access'] == lecture['ll_attempt'])) 
                             { 
                                var wrapper_selector = 'lecture_message';		
                                $('.innercontent').hide();		
                                var message_html  = '';		
                                    message_html += '<div class="alert alert-error alert-danger" id="alert_danger">';		
                                    message_html += '   Sorry you\'ve reached the maximum limit';		
                                    message_html += '</div>';		
                                $('#'+wrapper_selector).html(message_html).show();		
                            } 
                            else 
                            {		
                                renderExam(lecture);		
                            }
                        break;
                        case '4':
                            renderYoutubeVideo(lecture);
                        break;
                        case '5':
                            renderText(lecture);
                        break;
                        case '6':
                            renderWikipedia(lecture);
                        break;
                        case '7':
                            renderLive(lecture);
                        break;
                        case '8':
                            renderDescritpiveTest(lecture);
                        break;
                        case '9':
                            renderRecordedVideo(lecture);
                        break;
                        /*open link in new tab */
                        case '10':
                             renderScrom(lecture);
                        break;
                        case '11':
                             renderCiscoRecordedVideos(lecture);
                        break;
                        case '12':
                             renderAudio(lecture);
                        break;
                    }
                }
                if(lecture['cl_lecture_type']!='1')
                {
                    __lectureRequestInProgress = false;
                }
            }
            else
            {
                
            }
        }
    });
}
function renderExam(lecture)
{
    var wrapper_selector = 'textcontent';
    $('.innercontent').hide();
    $('#'+wrapper_selector).show();
    var textHtml  = '';
        textHtml += '<div class="scrom-class">';
        textHtml += '   <div class="scrom-content" >';
        textHtml += '       <h2>'+lecture['cl_lecture_name']+'</h2>';
        textHtml += '       <span>'+lecture['cl_lecture_description']+'</span><br>';
        textHtml += '       <a class="btn green-btn" href="'+webConfigs('site_url')+'material/test/'+lecture['assesment']['assesment_id']+'#instruction" target="_blank" style="text-decoration:none;color: #f5f5f5;">Attend</a>';
        textHtml += '   </div>';    
        textHtml += '</div>';
        $('#textcontent').html(textHtml).show() ;
    /*
    $('.innercontent').hide();
    $('#lecture_iframe').show();
    $('#lecture_iframe iframe').attr('src', webConfigs('site_url')+'/material/exam/'+lecture['assesment']['assesment_id']).show();

    $('#lecture_iframe iframe').on('load', function() {		
        $('#lecture_iframe iframe').contents().find('#load_exam_button').unbind('click');    		
        $('#lecture_iframe iframe').contents().find('#load_exam_button').attr('href', webConfigs('site_url')+'/material/exam/'+lecture['assesment']['assesment_id']+'#force').attr('target', '_blank');    		
        $('#lecture_iframe iframe').contents().find('#instruction_tab').show();    		
    });*/
}

function clearLectureCache(lecture_type)
{
    $('#textcontent').html('');
    switch (lecture_type)
    {
        case '1':
            if( player != null)
            {
                //console.log(player.engine);
                //player.engine.hlsjs.unload();
                player.pause();
                player.stop();
                player.unload();
                player.shutdown()
                $('#player').remove();
            }
            $('#content').html('');
        break;
        case '2':
            //document
            clearTimeout(__documentScroll);
            $('#pdfcontent').html('');
        break;
        case '3':
            //asesment
        break;
        case '4':
            //youtube
            $('#lecture_iframe iframe').attr('src', '');
        break;
        case '5':
            //text
            clearTimeout(__documentScroll);
            $('#textcontent').html('');
        break;
        case '6':
            //wikipedia
            clearTimeout(__documentScroll);
            $('#textcontent').html('');
        break;
        case '7':
            //live
        break;
        case '8':
            $('#descriptivecontent').html('');
        break;
        case '10':
            $('#scromcontent').html('');
        break;
        case '11':
            if( player != null)
            {
                player.pause();
                player.stop();
                player.unload();
                player.shutdown()
                $('#player').remove();
            }
        break;
        case '12':
            //$('#player').remove();
            if( player != null)
            {
                player.pause();
                player.stop();
                player.unload();
                player.shutdown()
                $('#player').remove();
            }
        break;
    }
}

var __player            = '';
var __isPlaying         = false;
var __isPlayingDefined  = false;
var __playerTimeOut     = null;
var __documentScroll    = null;



var __currentSecond = 0;
var __currentStatus = '';
var __timeOutDuration = 5;
var __lastUpdatedSecond = 0;
var __completed = 0;
var player = null;
var __isPlayingDefined  = false;

var __canUpdateSeconds = true;
var __isPlayAgain = false;		
var __normalCount   = 0;		
var __timeOutInProgress;		


var __callAlreadyInitiated = false;		
function updateNormalTime()		
{		
    __normalCount++;		
    console.log(__normalCount);		
}		

function startUpdateTimeOut() {		
    if(__callAlreadyInitiated == false)		
    {		
        __callAlreadyInitiated = true;		
        console.log('update cal init');		
        __timeOutInProgress = setInterval(function(){ updateNormalTime() }, 1000);		
    }		
}		

function stopUpdateTimeOut() {		
    console.log('update cal stopped');		
    clearInterval(__timeOutInProgress);		
    __callAlreadyInitiated = false;		
}		


function renderCiscoRecordedVideos(video)
{
    __systemSeeking = false;
    __normalCount   = 0;		
    stopUpdateTimeOut();
    var file_name       = video['cl_filename'];
    var video_path      = webConfigs('cisco_path')+file_name;
    $('.innercontent').hide();
    $('#videocontent').show();
    __currentSecond = 0;

    $('#video_quick_tools').remove();
    var wrapper_selector = 'videocontent';
    var videoHtml     = '';
    videoHtml += '<div class="quicktools" id="video_quick_tools">';
    if(video['cl_downloadable'] != '0')
    {
        videoHtml += '    <span style="z-index:99999;" class="pdf_download greybg"><a target="_blank" href="'+webConfigs('site_url')+'material/download_lecture/'+video['id']+'"><span class="icon-download"></span>Download</a></span>';
    }
    videoHtml += '</div>  ';
    $('#'+wrapper_selector).prepend(videoHtml).show();
    
    $('#content').html('<div id="player" class="fixed-controls"></div>');
    
    player = flowplayer("#player", {
        embed: false,
        seekable:true,
        clip: {
          sources: [
            { type: "video/mp4", src: video_path }
          ]
        }
    }).one('ready', function(ev, api) {
    // exclude devices which do not allow autoplay
    // because this will have no effect anyway
        if (flowplayer.support.firstframe) {
            __systemSeeking = true;
            __canUpdateSeconds = true;
            if(video['ll_percentage'] < 99)
            {
                //console.log(video);
                __currentSecond = (Math.round((video['ll_percentage']*video['cl_duration'])/100));
                api.seek(__currentSecond);
                api.play();
            }
            else
            {
                api.play();
            }
            startUpdateTimeOut();   
            
            player.bind("progress", function(e, api) {
                $('.fp-brand').remove();        $('.fp-player').next('a').remove();
                __currentSecond = parseInt(player.video.time);
                console.log('progress');
                if((parseInt(__currentSecond)%__timeOutDuration == 0 ) && __currentSecond >= 1 && __lastUpdatedSecond != __currentSecond)
                {
                    __lastUpdatedSecond = __currentSecond;
                    __completed = 0;
                    var percentage = ((__currentSecond/video['cl_duration'])*100);   
                    if(__canUpdateSeconds == true)
                    {                        
                        updateLecturePercentage(video['id'],  percentage);
                    }
                    __currentStatus = 'progress';
                }
            });

            player.bind("pause", function(e, api) {
                __currentStatus = 'pause';
                stopUpdateTimeOut();
            });

            player.bind("resume", function(e, api) {
                __currentStatus = 'resume';
                startUpdateTimeOut();
            });

            player.bind("seek", function(e, api) {
                var currentSecondLocal = (Math.round((video['ll_percentage']*video['cl_duration'])/100));
                    currentSecondLocal = (Number(currentSecondLocal)+Number(__normalCount));
                    console.log(player.video.time +' > '+ currentSecondLocal);
                if((player.video.time > currentSecondLocal) && __systemSeeking == false)
                {
                    //__canUpdateSeconds = false;
                }
                else
                {
                    __canUpdateSeconds = true;
                }
                __currentStatus     = 'seek';
            });

            player.bind("stop", function(e, api) {
                __currentStatus = 'stop';
                stopUpdateTimeOut();
            });

            player.bind("finish", function(e, api) {
                __currentStatus = 'finish';
                __completed = 1;
                if(__canUpdateSeconds == true)
                {
                    updateLecturePercentage(video['id'],  100);
                }
                console.log('finish');
                stopUpdateTimeOut();
            });  
        }
        __lectureRequestInProgress = false;
        $('#videocontent').show();
  });
}

var __systemSeeking = false;
function renderVideo(video)
{
    __systemSeeking = false;
    __normalCount   = 0;		
    stopUpdateTimeOut();
    var file_name       = video['cl_filename'];
    var file_directory  = file_name+'/'+file_name+'.m3u8';
    var video_path      = webConfigs('video_path')+file_directory;
    $('.innercontent').hide();
    $('#videocontent').show();
    __currentSecond = 0;
    __lastUpdatedSecond = 0;


    $('#video_quick_tools').remove();
    var wrapper_selector = 'videocontent';
    var videoHtml     = '';
    videoHtml += '<div class="quicktools" id="video_quick_tools">';
    if(video['cl_downloadable'] != '0')
    {
        videoHtml += '    <span style="z-index:99999;" class="pdf_download greybg"><a target="_blank" href="'+webConfigs('site_url')+'material/download_lecture/'+video['id']+'"><span class="icon-download"></span>Download</a></span>';
    }
    videoHtml += '</div>  ';
    $('#'+wrapper_selector).prepend(videoHtml).show();
    
    $('#content').html('<div id="player" class="fixed-controls"></div>');
    player = flowplayer("#player", {
        embed: false,
        hlsQualities: true,
        seekable:true,
        clip: {
          sources: [
            { type: "application/x-mpegurl", src: video_path }
          ]
        }
    }).one('ready', function(ev, api) {
    // exclude devices which do not allow autoplay
    // because this will have no effect anyway
        if (flowplayer.support.firstframe || 1==1) {
            __systemSeeking = true;
            __canUpdateSeconds = true;
            if(video['ll_percentage'] < 99)
            {
                //console.log(video);
                __currentSecond = (Math.round((video['ll_percentage']*video['cl_duration'])/100));
                api.seek(__currentSecond);
                api.play();
            }
            else
            {
                if(__isPlayAgain == false){
                    api.play();
                }
            }
            startUpdateTimeOut();   
            
            player.bind("progress", function(e, api) {
                $('.fp-brand').remove();        $('.fp-player').next('a').remove();
                __currentSecond = parseInt(player.video.time);
                if((parseInt(__currentSecond)%__timeOutDuration == 0 ) && __currentSecond >= 1 && __lastUpdatedSecond != __currentSecond)
                {
                    __lastUpdatedSecond = __currentSecond;
                    __completed = 0;
                    var percentage = ((__currentSecond/video['cl_duration'])*100);   
                    if(__canUpdateSeconds == true)
                    {                        
                        updateLecturePercentage(video['id'],  percentage);
                    }
                    __currentStatus = 'progress';
                }
            });

            player.bind("pause", function(e, api) {
                __currentStatus = 'pause';
                stopUpdateTimeOut();
            });

            player.bind("resume", function(e, api) {
                __currentStatus = 'resume';
                startUpdateTimeOut();
            });

            player.bind("seek", function(e, api) {
                var currentSecondLocal = (Math.round((video['ll_percentage']*video['cl_duration'])/100));
                    currentSecondLocal = (Number(currentSecondLocal)+Number(__normalCount));
                    //console.log(player.video.time +' > '+ currentSecondLocal);
                if((player.video.time > currentSecondLocal) && __systemSeeking == false)
                {
                    //__canUpdateSeconds = false;
                }
                else
                {
                    __canUpdateSeconds = true;
                }
                __currentStatus     = 'seek';
                __systemSeeking = false;
            });

            player.bind("stop", function(e, api) {
                __currentStatus = 'stop';
                stopUpdateTimeOut();
            });

            player.bind("finish", function(e, api) {
                __currentStatus = 'finish';
                __completed = 1;
                __isPlayAgain = true;
                if(__canUpdateSeconds == true)
                {
                    var remaining_views = (video['ll_attempt'])+"/"+video['cl_limited_access']+" views";
                    $("#remain_view_"+video['id']).html(remaining_views);
                    updateLecturePercentage(video['id'],  100);

                    if((video['ll_attempt']) >= video['cl_limited_access'] && video['cl_limited_access'] != 0)
                    {
                       $('.innercontent').hide();
                       $('#limit_reached_container').show();
                    }
                }
                console.log('finish');
                stopUpdateTimeOut();
                loadLecture(video['id']);
            });  
        }
        __lectureRequestInProgress = false;
        $('#videocontent').show();
  });
  
    
}


function renderAudio(audio)
{
    __systemSeeking = false;
    __normalCount   = 0;		
    stopUpdateTimeOut();
    var file_name       = audio['cl_org_file_name'];
    var audio_path      = webConfigs('audio_path')+file_name;
    $('.innercontent').hide();
    $('#videocontent').show();
    __currentSecond = 0;


    $('#video_quick_tools').remove();
    var wrapper_selector = 'videocontent';
    var audioHtml     = '';
    audioHtml += '<div class="quicktools" id="video_quick_tools">';
    if(audio['cl_downloadable'] != '0')
    {
        audioHtml += '    <span style="z-index:99999;" class="pdf_download greybg"><a target="_blank" href="'+webConfigs('site_url')+'material/download_lecture/'+audio['id']+'"><span class="icon-download"></span>Download</a></span>';
    }
    audioHtml += '</div>  ';
    $('#'+wrapper_selector).prepend(audioHtml).show();
    
    $('#content').html('<div id="player" class="fixed-controls"></div>');
    
    player = flowplayer("#player", {
        embed: false,
        audio: true,
        coverImage: webConfigs('assets_url')+"images/speaker.png",
        seekable:true,
        clip: {
          sources: [
            { type: "audio/mpeg", src: audio_path }
          ]
        }
    }).one('ready', function(ev, api) {
    // exclude devices which do not allow autoplay
    // because this will have no effect anyway
        if (flowplayer.support.firstframe) {
            if(audio['ll_percentage'] < 99)
            {
                //console.log(audio);
                __currentSecond = (Math.round((audio['ll_percentage']*audio['cl_duration'])/100));
                __systemSeeking = true;
                api.seek(__currentSecond);
                api.play();
            }
            else
            {
                api.play();
            }
            __canUpdateSeconds = true;
            startUpdateTimeOut();   
            
            player.bind("progress", function(e, api) {
                $('.fp-brand').remove();        $('.fp-player').next('a').remove();
                __currentSecond = parseInt(player.video.time);
                
                if((parseInt(__currentSecond)%__timeOutDuration == 0 ) && __currentSecond >= 1 && __lastUpdatedSecond != __currentSecond)
                {
                    __lastUpdatedSecond = __currentSecond;
                    __completed = 0;
                    var percentage = ((__currentSecond/audio['cl_duration'])*100);   
                    if(__canUpdateSeconds == true)
                    {                        
                        updateLecturePercentage(audio['id'],  percentage);
                    }
                    __currentStatus = 'progress';
                }
            });

            player.bind("pause", function(e, api) {
                __currentStatus = 'pause';
                stopUpdateTimeOut();
            });

            player.bind("resume", function(e, api) {
                __currentStatus = 'resume';
                startUpdateTimeOut();
            });

            player.bind("seek", function(e, api) {
                var currentSecondLocal = (Math.round((audio['ll_percentage']*audio['cl_duration'])/100));
                    currentSecondLocal = (Number(currentSecondLocal)+Number(__normalCount));
                    console.log(player.video.time +' > '+ currentSecondLocal);
                if((player.video.time > currentSecondLocal) && __systemSeeking == false)
                {
                    //__canUpdateSeconds = false;
                }
                else
                {
                    __canUpdateSeconds = true;
                }
                __currentStatus     = 'seek';
            });

            player.bind("stop", function(e, api) {
                __currentStatus = 'stop';
                stopUpdateTimeOut();
            });

            player.bind("finish", function(e, api) {
                __currentStatus = 'finish';
                __completed = 1;
                if(__canUpdateSeconds == true)
                {
                    updateLecturePercentage(audio['id'],  100);
                }
                console.log('finish');
                stopUpdateTimeOut();
            });  
        }
        __lectureRequestInProgress = false;
        $('.fp-player').css('background-size', 'auto 100% !important' );
        $('.fp-player').css('background-position', 'center center' );
        $('.fp-player').css('background-repeat', 'no-repeat' );
        $('.fp-player').css('background-color', '#333 !important');
        $('#videocontent').show();
  });
  
    
}



function updateLecturePercentage(lecture_id, percentage){
    percentage = parseInt(percentage);
    console.log(lecture_id+' ajax called '+percentage);
    
    $.ajax({
        url: webConfigs('site_url')+'/material/percentage',
        type: "POST",
        data:{"is_ajax":true, 'lecture_id':lecture_id, 'percentage':percentage},
        success: function(response) {
            console.log(response);
        }
    });
}

function renderDocument(document)
{
    var wrapper_selector = 'pdfcontent';
    var documentHtml     = '';
    
        documentHtml    += '<div id="document_zoomer_wrapper">';
    if( document['cl_total_page'] == 1 )
    {
        documentHtml += '<img id="pdf_image" src="'+webConfigs('document_path')+document['cl_filename']+'/page.jpg" class="block" />';
    }
    else
    {
        if( document['cl_total_page'] > 1 )
        {
            for(var i = 0; i< document['cl_total_page']; i++)
            {
                documentHtml += '<img id="pdf_image" src="'+webConfigs('document_path')+document['cl_filename']+'/page-'+i+'.jpg" class="block" />';
            }
        }
    }
    documentHtml    += '</div>';    
    

    documentHtml += '<div class="quicktools">';
    if(document['cl_downloadable'] != '0')
    {
        documentHtml += '    <span class="pdf_download greybg"><a target="_blank" href="'+webConfigs('site_url')+'material/download_lecture/'+document['id']+'"><span class="icon-download"></span>Download</a></span>';
    }
    documentHtml += '    <span class="pdf_zoomin greybg"><a href="javascript:void(0)" onclick="zoomOut()"><span class="icon-zoom-in"></span></a></span>';
    documentHtml += '    <span class="pdf_zoomout greybg"><a href="javascript:void(0)" onclick="zoomIn()"><span class="icon-zoom-out"></span></a></span>';
    documentHtml += '</div>  ';
    $('.innercontent').hide();
    $('#'+wrapper_selector).html(documentHtml).show();
    
    if(document['ll_percentage'] < 99)
    {
       $('#'+wrapper_selector).animate({scrollTop:Math.round((document['ll_percentage']*$('#'+wrapper_selector).prop('scrollHeight'))/100)}, 1000);
    }

    var previousScroll  = 0;
    var currentScroll   = 0;
    var latestPercetage = 0
    $( '#'+wrapper_selector ).unbind('scroll');
    $( '#'+wrapper_selector ).scroll(function() {
        currentScroll = $(this).scrollTop();
        
        //check the user is scrolling top up
        if (currentScroll > previousScroll)
        {
            clearTimeout(__documentScroll);
            __documentScroll =  setTimeout(function(){ 
                var percentage = (($('#'+wrapper_selector).scrollTop()/($('#'+wrapper_selector).prop('scrollHeight')-$('#'+wrapper_selector).height()))*100);
                    percentage = (percentage>98)?100:percentage;
                    if(percentage > latestPercetage)
                    {
                        latestPercetage = percentage;
                        updateLecturePercentage(document['id'],  percentage);                
                    }
            }, 600);
        }
        previousScroll = currentScroll;
    });

    $('#pdf_image').on('dragstart', function(event) { event.preventDefault(); });

    $('#pdf_image').draggable = false;

    //document.getElementById('pdf_image').draggable = false;
}
/* RenderScrom html function by ambitha*/
function renderScrom(scrom)
{
    var wrapper_selector = 'scromcontent';
    var documentHtml     = '';
    documentHtml         += '<div class="scrom-class">';
    documentHtml         += '<div class="scrom-content" id="scrom-id">';
    documentHtml         += '<h2>'+scrom['cl_lecture_name']+'</h2>';
    documentHtml         += '<span>'+scrom['cl_lecture_description']+'</span><br>';

    documentHtml         += '<a class="btn green-btn" href="'+uploads_url+scrom['cl_filename']+'" target="_blank" style="text-decoration:none;color: #f5f5f5;">View</a>';

    documentHtml         += '</div>';    
    documentHtml         += '</div>';
    $('#pdfcontent').html('').hide();
    $('#scromcontent').html(documentHtml).show() ;
    //document.getElementById('pdf_image').draggable = false;
}


function renderYoutubeVideo(video)
{
    $('.innercontent').hide();
    $('#lecture_iframe iframe').attr('src', video['cl_filename'])
    $('#lecture_iframe').show();
    updateLecturePercentage(video['id'],  100);
}

function renderRecordedVideo(video)		
{		
    $('.innercontent').hide();		
    $('#lecture_recorded_iframe iframe').attr('src', webConfigs('site_url')+"/live/play/"+video['cl_filename'])		
    $('#lecture_recorded_iframe').show();		
    updateLecturePercentage(video['id'],  100);		
}

function renderText(lecture)
{
    var wrapper_selector = 'textcontent';
    $('.innercontent').hide();
    $('#'+wrapper_selector).html(lecture['cl_lecture_content']).show(); 

    if(lecture['ll_percentage'] < 99)
    {
       $('#'+wrapper_selector).animate({scrollTop:Math.round((lecture['ll_percentage']*$('#'+wrapper_selector).prop('scrollHeight'))/100)}, 1000);
    }

    var previousScroll  = 0;
    var currentScroll   = 0;
    var latestPercetage = 0
    $( '#'+wrapper_selector ).unbind('scroll');
    $( '#'+wrapper_selector ).scroll(function() {
        currentScroll = $(this).scrollTop();
        //check the user is scrolling top up
        if (currentScroll > previousScroll)
        {
            clearTimeout(__documentScroll);
            __documentScroll =  setTimeout(function(){ 
                var percentage = (($('#'+wrapper_selector).scrollTop()/($('#'+wrapper_selector).prop('scrollHeight')-$('#textcontent').height()))*100);
                    percentage = (percentage>98)?100:percentage;
                    if(percentage > latestPercetage)
                    {
                        latestPercetage = percentage;
                        updateLecturePercentage(lecture['id'],  percentage);                
                    }
            }, 600);
        }
        previousScroll = currentScroll;
    });
}

function renderWikipedia(lecture)
{
    var wrapper_selector = 'textcontent';
    $('.innercontent').hide();
    $('#'+wrapper_selector).html(atob(lecture['cl_lecture_content'])).show();

    if(lecture['ll_percentage'] < 99)
    {
            var percentage = (($('#'+wrapper_selector).scrollTop()/($('#'+wrapper_selector).prop('scrollHeight')-$('#'+wrapper_selector).height()))*100);
    }

    var previousScroll  = 0;
    var currentScroll   = 0;
    var latestPercetage = 0
    $( '#'+wrapper_selector ).unbind('scroll');
    $( '#'+wrapper_selector ).scroll(function() {
        currentScroll = $(this).scrollTop();
        //check the user is scrolling top up
        if (currentScroll > previousScroll)
        {
            clearTimeout(__documentScroll);
            __documentScroll =  setTimeout(function(){ 
                var percentage = (($('#'+wrapper_selector).scrollTop()/($('#'+wrapper_selector).prop('scrollHeight')-$('#'+wrapper_selector).height()))*100);
                    percentage = (percentage>98)?100:percentage;
                    if(percentage > latestPercetage)
                    {
                        latestPercetage = percentage;
                        updateLecturePercentage(lecture['id'],  percentage);                
                    }
            }, 600);
        }
        previousScroll = currentScroll;
    });
}

function renderLive(video)
{
    /*console.log(video);
    if(video['live_is_online'] == '2'){
        //window.open(webConfigs('site_url')+'/live/join/'+video['live_id'], '_blank');
       location.href = webConfigs('site_url')+'/live/join/'+video['live_id'];
    }else if(video['live_is_online'] == '1'){
        //window.open(webConfigs('site_url')+'/live/golive/'+video['live_id'], '_blank');
        location.href = webConfigs('site_url')+'/live/golive/'+video['live_id'];

    }*/
    var __liveHtml = '';
        __liveHtml += '<div class="scrom-class" style="background: #fff;text-align: center">   ';
        __liveHtml += '    <div class="scrom-content">       ';
        __liveHtml += '        <h2>'+video['cl_lecture_name']+'</h2>       ';
        __liveHtml += '        <p style="font-weight: bold;">'+video['live_objects']['live_date']+'</p>       ';
        __liveHtml += '        <span>'+video['cl_lecture_description']+'</span><br>       ';
        if(video['live_objects']['live_is_active'] == true || 1==1)
        {
            var destinationLink =  video['live_objects']['live_link'];
            __liveHtml += '        <a class="btn green-btn" href="'+destinationLink+'" target="_blank" >Attend</a>   ';
        }
        else
        {
            __liveHtml += '        <a class="btn green-btn " href="javascript:void(0)" target="_blank" >Attend</a>   ';        
        }
        __liveHtml += '    </div>';
        __liveHtml += '</div>';
    $('#pdfcontent').html(__liveHtml).show();
}

function zoomIn(){
	var value  = $('#document_zoomer_wrapper').width() - 100 ;
	$('#document_zoomer_wrapper').width(value);
}
function zoomOut(){
	var value = $('#document_zoomer_wrapper').width() + 100;
	$('#document_zoomer_wrapper').width(value);
}



function renderDescritpiveTest(lecture)
{
    var descriptiveHtml  = '', user_img = '';
        
descriptiveHtml += '<div class="instructions">';
descriptiveHtml += '    <h4 class="caps">Instructions</h4>';
descriptiveHtml += '    '+lecture['descriptive']['dt_description'];
descriptiveHtml += '</div>';
descriptiveHtml += '<div class="fullwidthbox">';
descriptiveHtml += '    <div class="inner">';
descriptiveHtml += '        <div class="ts-md-6 usdfw"><button onclick="window.location=\''+webConfigs('site_url')+'/material/download_question_paper/'+lecture['id']+'\'" class="downloadquestion smt">Download Que paper<span  class="icon-download dwldbtn"></span></button></div>';
descriptiveHtml += '        <div class="ts-md-6 usdfw"><div class="marks">Marks : '+((lecture['descriptive']['marks']>0)?(lecture['descriptive']['marks']+'/'+lecture['descriptive']['dt_total_mark']):'Not Reviewed')+'</div></div>';
descriptiveHtml += '    </div>';
descriptiveHtml += '</div>';
descriptiveHtml += '<div class="inner" id="test_comments">';

if(lecture['descriptive']['comments'].length > 0 )
{
    for(var i =0; i < lecture['descriptive']['comments'].length; i++)
    {
        descriptiveHtml += '    <div class="ts-md-12 bb sbtp" id="comment_id_'+lecture['descriptive']['comments'][i]['comment_id']+'">';
        user_img = ((lecture['descriptive']['comments'][i]['us_image'] == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path')); 
        descriptiveHtml += '        <div class="ts-md-2 reviewimg"><img src="'+user_img+lecture['descriptive']['comments'][i]['us_image']+'" width="42" height="42" class="rounded" /></div>';
        descriptiveHtml += '        <div class="ts-md-7">';
        descriptiveHtml += '            <h4 class="nmtb">'+lecture['descriptive']['comments'][i]['us_name']+'</h4>';
        if(lecture['descriptive']['comments'][i]['comment']){
        descriptiveHtml += '            <p class="nmtb">'+lecture['descriptive']['comments'][i]['comment']+'</p>';
        }
        if(lecture['descriptive']['comments'][i]['file']){
        descriptiveHtml += '            <p class="nmtb"><a class="" target="_blank" href="'+webConfigs('assignment_path')+lecture['descriptive']['comments'][i]['file']+'" download>Attachment<span  class="icon-download dwldbtn"></span></a></p>';    
        }
        descriptiveHtml += '        </div>';
        descriptiveHtml += '        <div class="ts-md-3"><span class="ts-md-10">'+lecture['descriptive']['comments'][i]['updated_date']+'</span>';
        if(lecture['descriptive']['user_id'] == lecture['descriptive']['comments'][i]['da_user_id']){
        descriptiveHtml += '            <a class="ts-md-2 comment-delete-btn" onclick="deleteComment('+lecture['descriptive']['comments'][i]['comment_id']+',\''+lecture['descriptive']['comments'][i]['file']+'\')">X</a>';
        }
        descriptiveHtml += '        </div>';
        descriptiveHtml += '    </div>   ';        
    }
}


    descriptiveHtml += '    <div class="ts-md-12 bb sbtp">';
    descriptiveHtml += '        <div class="ts-md-2 reviewimg"><img src="'+lecture['descriptive']['user_image']+'" width="42" height="42" class="rounded" /></div>';
    descriptiveHtml += '        <div class="ts-md-12">';
    descriptiveHtml += '            <textarea id="descriptive_test_comment" style="resize:none;" name="message" class="small-bx assignment-area" cols="" rows="" placeholder="Paste your Google drive/dropbox link and hit enter.Enter comments if any" onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'Paste your Google drive/dropbox link and hit enter.Enter comments if any\'"></textarea>';
    descriptiveHtml += '            <div class="ts-md-6 add-sm-margin">';
    descriptiveHtml += '                <div class="">';
    descriptiveHtml += '                    <input type="file" id="doc_file" accept=".doc,.docx,.pdf">';
    descriptiveHtml += '                    <p class="help-block">Only .doc, .docx and .pdf files are allowed.</p>';
    descriptiveHtml += '                </div>';
    descriptiveHtml += '            </div>';
    descriptiveHtml += '            <div class="ts-md-4 add-sm-margin">';
    descriptiveHtml += '                <div class="progress assignment-progress">';
    descriptiveHtml += '                    <div class="progress-bar" role="progressbar" style="width:0%">';
    descriptiveHtml += '                        <span class="sr-only">70% Complete</span>';
    descriptiveHtml += '                    </div>';
    descriptiveHtml += '                </div>';
    descriptiveHtml += '            </div>';
    descriptiveHtml += '            <div class="ts-md-2 add-sm-margin">';
    descriptiveHtml += '                &nbsp;<button type="button" class="btn btn-orange flex-round-btn" id="descriptive_file_upload">Upload</button>';
    descriptiveHtml += '            </div>';
    descriptiveHtml += '        </div>';
    descriptiveHtml += '    </div> ';
    descriptiveHtml += '</div>';
    $('#pdfcontent').html('').hide();
    $('#descriptivecontent').html(descriptiveHtml).show() ;
}

function generateCertificate() 
{		
    $('#lecture_iframe','#pdfcontent','#textcontent','#videocontent').hide();		
    $('#certificateContent').show();		
    $('#videocontent').css('display','none');		
    $.ajax({		
        url: webConfigs('site_url')+'/material/generate_certificate',		
        type: "POST",		
        data:{"is_ajax":true, 'course_id':__course_id},		
        success: function(response) {		
           var data = $.parseJSON(response);		
           //console.log(data);		
           $('#show_percentage').html(data.completed_percentage+"%");		
           $('#show_percentage_div').css('width',data.completed_percentage+"%");		
           if(Number(data.completed_percentage) >= Number(data.course_details_saved.cb_need_percentage)){		
                $('#show_button_div').html('<button class="green-btn" id="green_cert_button">Generate Your Certificate</button>');		
                $('#status_certificate').html('Congratulations! You have successfully completed this course and you are eligible for the Certificate.');		
           }		
           else{		
                $('#show_button_div').html('');		
                $('#status_certificate').html('Oops, you have completed only <b>'+data.completed_percentage+'%</b> of this course. You need to complete 100% of the course to get your certificate. Keep learning ! ');		
           }		
        }		
    });  		
}		

    var __generatingCertificateInProgress = false;
    $(document).on('click','#green_cert_button',function(){		
        if(__generatingCertificateInProgress == true)
        {
            return false;
        }
        __generatingCertificateInProgress = true;
        $('#green_cert_button').html('Generating your Certificate..');
        $.ajax({		
            url: webConfigs('site_url')+'/material/create_document',		
            type: "POST",		
            data:{"is_ajax":true, 'course_id':__course_id},		
            success: function(response) {		
                var data = $.parseJSON(response);		
                //console.log(response);
                $('#green_cert_button').html('Generate Your Certificate');
                __generatingCertificateInProgress = false;
                document.location = webConfigs('site_url')+'/material/download_certificate/'+data+'/'+__course_id;		
            }		
        }); 		
    });

function deleteComment(comment_id, file)
{
    $.ajax({
            url: webConfigs('site_url')+'/material/delete_comment',
            type: "POST",
            data:{"is_ajax":true, 'comment_id':comment_id, 'file':file},
            success: function(response) {
                $('#comment_id_'+comment_id).remove();
            }
        });  
}

var __uploading_file = new Array();
function uploadAssignment(__uploading_file, comment, callback) {
    if (__uploading_file.length > 1)
    {
        $("#descriptive_test_comment").val(comment);
        return false;
    }
    if (__uploading_file[0].size > 20000000)
    {
        $("#descriptive_test_comment").val(comment);
        alert('File size exceeded the limit.');
        return false;
    }
    var i = 0;
    var uploadURL = __site_url + "material/upload_assignment";
    var fileObj = new processFileName(__uploading_file[i]['name']);
    var param = new Array;
    param["file_name"] = fileObj.uniqueFileName();
    param["extension"] = fileObj.fileExtension();
    param["file"] = __uploading_file[i];
    var ext = fileObj.fileExtension().toLowerCase();
    if(ext != "" && $.inArray(ext, ['pdf', 'doc', 'docx']) == -1) {
        $("#descriptive_test_comment").val(comment);
        alert('Only .doc, .docx and .pdf files are allowed.');
        return false;
    }
    uploadFiles(uploadURL, param, callback);
}

function saveQuestionComment(comment, __lecture_id, doc){
    $.ajax({
        url: webConfigs('site_url')+'/material/save_question_comment',
        type: "POST",
        data:{"is_ajax":true, 'comment':comment, 'lecture_id':__lecture_id, 'file':doc},
        success: function(response) {
            var descriptiveHtml = '';
            var data = $.parseJSON(response);
            for(var i =0; i < data['comments'].length; i++)
            {
                descriptiveHtml += '    <div class="ts-md-12 bb sbtp" id="comment_id_'+data['comments'][i]['comment_id']+'">';
                user_img = ((data['comments'][i]['us_image'] == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path')); 
                descriptiveHtml += '        <div class="ts-md-2 reviewimg"><img src="'+user_img+data['comments'][i]['us_image']+'" width="42" height="42" class="rounded" /></div>';
                descriptiveHtml += '        <div class="ts-md-7">';
                descriptiveHtml += '            <h4 class="nmtb">'+data['comments'][i]['us_name']+'</h4>';
                if(data['comments'][i]['comment']){
                descriptiveHtml += '            <p class="nmtb">'+data['comments'][i]['comment']+'</p>';
                }
                if(data['comments'][i]['file']){
                descriptiveHtml += '            <p class="nmtb"><a class="" target="_blank" href="'+webConfigs('assignment_path')+data['comments'][i]['file']+'" download>Attachment<span  class="icon-download dwldbtn"></span></a></p>';    
                }
                descriptiveHtml += '        </div>';
                descriptiveHtml += '        <div class="ts-md-3"><span class="ts-md-10">'+data['comments'][i]['updated_date']+'</span>';
                if(data['user_id'] == data['comments'][i]['da_user_id']){
                descriptiveHtml += '            <a class="ts-md-2 comment-delete-btn" onclick="deleteComment('+data['comments'][i]['comment_id']+',\''+data['comments'][i]['file']+'\')">X</a>';
                }
                descriptiveHtml += '        </div>';
                descriptiveHtml += '    </div>   ';        
            }
            descriptiveHtml += '    <div class="ts-md-12 bb sbtp">';
            descriptiveHtml += '        <div class="ts-md-2 reviewimg"><img src="'+data['user_image']+'" width="42" height="42" class="rounded" /></div>';
            descriptiveHtml += '        <div class="ts-md-12">';
            descriptiveHtml += '            <textarea id="descriptive_test_comment" style="resize:none;" name="message" class="small-bx assignment-area" cols="" rows="" placeholder="Enter your comment here" onfocus="this.placeholder = \'\'" onblur="this.placeholder = \'Enter your comment here\'"></textarea>';
            descriptiveHtml += '            <div class="ts-md-6 add-sm-margin">';
            descriptiveHtml += '                <div class="">';
            descriptiveHtml += '                    <input type="file" id="doc_file" accept=".doc,.docx,.pdf">';
            descriptiveHtml += '                    <p class="help-block">Only .doc, .docx and .pdf files are allowed.</p>';
            descriptiveHtml += '                </div>';
            descriptiveHtml += '            </div>';
            descriptiveHtml += '            <div class="ts-md-4 add-sm-margin">';
            descriptiveHtml += '                <div class="progress">';
            descriptiveHtml += '                    <div class="progress-bar" role="progressbar" style="width:0%">';
            descriptiveHtml += '                        <span class="sr-only">70% Complete</span>';
            descriptiveHtml += '                    </div>';
            descriptiveHtml += '                </div>';
            descriptiveHtml += '            </div>';
            descriptiveHtml += '            <div class="ts-md-2 add-sm-margin">';
            descriptiveHtml += '                &nbsp;<button type="button" class="btn btn-orange flex-round-btn" id="descriptive_file_upload">Upload</button>';
            descriptiveHtml += '            </div>';
            descriptiveHtml += '        </div>';
            descriptiveHtml += '    </div> ';
            descriptiveHtml += '</div>';
            $('#test_comments').html(descriptiveHtml);
        }
    });
}

$(document).on('keyup', '#descriptive_test_comment', function(e){
    if(e.which == 13)
    {
        var comment         = $(this).val(), user_img = '';
        var nonEscapedValue = $("#descriptive_test_comment").val();
        if (!$.trim($("#descriptive_test_comment").val())) 
        {
            // textarea is empty or contains only white-space
        }
        else
        {
            // added line below to escape typed text
            var value = nonEscapedValue;
            var words = value.split(" ");
            for (var i=0;i<words.length;i++)
            {
                var n = isUrl(words[i]);
                if (n)  {
                    var deadLink = '<a href="'+words[i]+'" target="_blank" rel="nofollow">'+words[i]+'</a>';
                    // changed line below to assign replace()'s result
                    words[i] = words[i].replace(words[i], deadLink);
                }
            }
            // added line below to put the result in to the div #myResultDiv
            comment = words.join(" ");

            if($("#doc_file")[0].files.length > 0) {
                uploadAssignment($("#doc_file")[0].files, comment, function(response){
                    var data = $.parseJSON(response);
                    saveQuestionComment(comment, __lecture_id, data['assignment']);  
                })
            } else {
                saveQuestionComment(comment, __lecture_id, '');  
            }
        }
    }
});

$(document).on('click', '#descriptive_file_upload', function(e){
    if($("#doc_file")[0].files.length > 0) {
        uploadAssignment($("#doc_file")[0].files, '', function(response){
            var data = $.parseJSON(response);
            saveQuestionComment('', __lecture_id, data['assignment']);  
        })
    }
});

function isUrl(s) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return regexp.test(s);
}

/* Discussion js starts here */		
	/* Created by Yadu Chandran */		
			
	$(document).on('click','#add_discussion',function(){		
			
	    var discussion_title  = $('#add_discussion_title').val();		
	        discussion_title  = discussion_title.trim();		
	        discussion_title  = $.trim( $('#add_discussion_title').val() );		
			
		var discussion_topic  = $('#add_discussion_input').val();		
	        discussion_topic  = discussion_topic.trim();		
	        discussion_topic  = $.trim( $('#add_discussion_input').val() );		
		 if(discussion_title!=''){		
			 $.ajax({		
				url: site_url+'material/post_new_discussion',		
				type: "POST",		
				data:{"is_ajax":true,'course_id' : __course_id,'discussion_title':discussion_title, 'discussion_comment':discussion_topic},		
				success: function(response) {		
					var  appendNewDisc =  '';		
					var data           = $.parseJSON(response);  		
	                $('#add_discussion_title').val('');		
					$('#add_discussion_input').val('');		
	                $("#add_discussion_input").redactor('code.set', ''); 		
	                $('#add_discussion').prop('disabled', true);		
	                $("#add_discussion").css('cursor','not-allowed');		
	                		
	                appendNewDisc+= '<li class="individual-question" id="'+data.inserted_id+'">';		
	                appendNewDisc+= '  <span class="question-avatar"><img src="'+((data.user_details.us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.user_details.us_image+'" alt="'+data.user_details.us_name+'" width="50" height="50"></span>';		
	                appendNewDisc+= '   <span class="question-description">';		
			
	                appendNewDisc+= '<div class="archive-question">'+discussion_title+'</div>';		
	                 if(discussion_topic!=''){		
	                    appendNewDisc+= '       <div class="archive-answer">'+discussion_topic+'</div>';		
	                }		
	                appendNewDisc+= '   </span>'		
	                appendNewDisc+= '</li>';		
	                if($('.fx-c').length == 0)		
	                {		
	                    $('#show_parent').prepend(appendNewDisc);    		
	                }		
	                else		
	                {		
	                    $('#show_parent').html(appendNewDisc);    		
	                }		
	                $('#search_count').html('<span id="q_count">'+$('#show_parent li').length+'</span> Questions in this section');		
	    			$("html, body").animate({ scrollTop: 0 }, "slow");			
				}		
			});		
		 }		
	});		
			
	/* Function for posting user comments */		
	$(document).on('click','.add_answer',function(){		
	    var text_id = this.id; //getter		
	    var comment_textarea  = $('#comment_textarea').val();		
	        comment_textarea  = comment_textarea.trim();		
	        comment_textarea  = $.trim( $('#comment_textarea').val() );		
	    if(comment_textarea!=""){		
	        postUserComments(text_id,comment_textarea);		
	        $("#comment_textarea").redactor('code.set', '');		
	        $('#comment_textarea').val('');		
	        $('#comment_textarea').redactor('core.destroy');		
	        $('#comment_textarea').attr('placeholder','Add an answer');		
	    }		
	});		
			
	/* Function for posting user comments */		
	function postUserComments(comment_id,value){		
			
	    $.ajax({		
	            url: site_url+'material/post_user_comment',		
	            type: "POST",		
	            data:{"is_ajax":true,'course_id' : __course_id, 'comment_id':comment_id,'comment':value},		
	            success: function(response) {		
	    			var appendHtml = '';		
	                var data       = $.parseJSON(response);			
	                		
	                var parentDate      = data.posted_user[0].created_date;		
			
	                var parentNew       = parentDate.replace(/-/g, '/');		
	                var date            = new Date(parentNew);		
	                var childstrTime     = 'just now';//timeSince(date);		
			
	                appendHtml+= '<li class="single-answer" id="'+data.inserted_id+'">';		
	                appendHtml+= '<span class="question-avatar thumb-avatar"><img src="'+((data.user_details.us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.user_details.us_image+'" alt="'+data.user_details.us_name+'"/></span>';		
	                appendHtml+= '<span class="answer-detailed-desc">';		
	                appendHtml+= '<span class="ts-md-12">';		
	                appendHtml+= '<span class="question-author">'+data.user_details.us_name+' '+'</span>';		
	                appendHtml+= '<span class="posted-on"> '+childstrTime+'</span>';		
	                appendHtml+= '</span>'+value+'</span>';		
	                appendHtml+= '<span class="answer-close">';		
	                appendHtml+= '    <span class="dropdown">';		
	                appendHtml+= '        <button class="dropbtz"><span class="transform-ninteen">...</span></button>';		
	                appendHtml+= '        <span class="dropdown-content">';		
	                appendHtml+= '     <a href="#" class="delete_comment" onclick="setID('+comment_id+','+data.inserted_id+')">Delete</a>';		
	                appendHtml+= '        </span>';		
	                appendHtml+= '    </span>';		
	                appendHtml+= '</span>';		
	                appendHtml+= '</li>';		
			
	    		   $('#append_answer').append(appendHtml);		
	            }		
	    });		
	}		
			
	/* Function for loading previous comments */		
	var limit               = 5;		
	var click_view_previous = 0;		
	var previous_id         = 0;		
	function loadComments(id){ 		
	    if(id!=previous_id){		
	        limit               = 5;		
	        click_view_previous = 0;		
	    }		
	if(click_view_previous=='0'){		
	    click_view_previous = 1; 		
	    limit               = limit;		
	}		
	else{		
	    limit               = limit*2;		
	}		
	    previous_id         = id;		
			
	    $.ajax({		
	            url: site_url+'material/load_previous_comments',		
	            type: "POST",		
	            data:{"is_ajax":true,'course_id' : __course_id,'discussion_id' : id,'limit_value' : limit},		
	            success: function(response) {		
	                var data  = $.parseJSON(response);	
	                if(data.course_comments.length > 0){		
	                    $('#'+id).html(renderPreviousComments(response));		
	                }		
	            }		
	    });		
	}		
	function reportCommentConfirm()		
	{		
	    var report_reason  = $('#report_reason').val();		
	        report_reason  = report_reason.trim();		
	        report_reason  = $.trim( $('#report_reason').val() );		
	    if(report_reason!="")		
	    {		
	        reportCommentUser();		
	    }		
	    else		
	    {		
	        $('#show_error_report').html('Please enter a reason for reporting this discussion');		
	    }		
	}		
	function reportCommentUser()		
	{		
	    var parent_id = $('#modal_parent_id_report').val();		
	    var child_id  = $('#modal_child_id_report').val();        		
	    $.ajax({		
	            url: site_url+'material/report_comments_admin',		
	            type: "POST",		
	            data:{"is_ajax":true,'course_id' : __course_id, 'parent_id':parent_id,'child_id':child_id,'report_reason':$('#report_reason').val()},		
	            success: function(response) { 		
					$('#report_reason').val(''); 		
	                $('#show_error_report').html('');		
	                $('#report_comment').modal('hide');		
	            }		
	   });  		
	}		
			
			
	/* Code for discussion panel new design starts here */		
	$(document).ready(function() {		
			
	    $('#report_reason').keyup(function(){		
	        $('#show_error_report').html('');		
	    });		
	     $('#add_discussion').prop('disabled', true);		
	     $("#add_discussion").css('cursor','not-allowed');		
	     $('#add_discussion_title').keyup(function() {		
	        if($(this).val() != '') {		
	           $('#add_discussion').prop('disabled', false);		
	           $("#add_discussion").css('cursor','pointer');		
	        }		
			
	        if ($.trim( $(this).val() ) == '' )		
	        {		
	            $('#add_discussion').prop('disabled', true);		
	            $("#add_discussion").css('cursor','not-allowed');		
	        }		
			
	     });		
			
	 });		
	/* Comment on click show its details */		
	    //$(document).ready(function(){		
			
	        $(document).on('click','.individual-question',function(){		
	            $('#show_discussion_div').html('');		
	            showDiscDetails(this.id);		
	        });		
			
	        /* show discussion details starts */		
	        function showDiscDetails(id)		
	        {		
                    __currentDiscussionId = id;
	            $.ajax({		
	                url: site_url+'material/load_previous_comments',		
	                type: "POST",		
	                data:{"is_ajax":true,'course_id' : __course_id,'discussion_id' : id},		
	                success: function(response) { 		
	                    var data = $.parseJSON(response);		
	                    if(data.course_comments.length > 0){		
	                        $('#show_discussion_div').html(renderPreviousComments(response));		
	                    }		
	                }		
	            });		
	        }		
	        /* show discussion details ends */
                    var __questionCommentsSearchTimeOut = null;
                    var __currentDiscussionId = 0;
                    __questionCommentsSearchTimeOut = setInterval(function(){
                        if(__currentDiscussionId > 0)
                        {
                            $.ajax({		
                                url: site_url+'material/load_previous_comments',		
                                type: "POST",		
                                data:{"is_ajax":true,'course_id' : __course_id,'discussion_id' : __currentDiscussionId},		
                                success: function(response) { 		
                                    var data = $.parseJSON(response);		
                                    if(data.course_comments.length > 0){		
                                        $('#append_answer').html(renderPreviousQuestionComments(response));		
                                    }		
                                }		
                            });
                        }
                    }, 4000);                    
                    function renderPreviousQuestionComments(response)
                    {		
	                var data               = $.parseJSON(response);		
	                var prevcommentHtml    = '';		
	                if(data.course_comments.length > 0 )		
	                {		
	                    for (var i=0; i<data.course_comments.length; i++)		
	                    {		
	                        var parentDate  = data.course_comments[i].created_date;		
	                        var parentNew   = parentDate.replace(/-/g, '/');		
	                        var date        = new Date(parentNew);		
	                        var newDate     = timeSince(date);		
	                        data.children_comments[data.course_comments[i].id] = data.children_comments[data.course_comments[i].id].reverse();		
                                for (var j=0; j<data.children_comments[data.course_comments[i].id].length; j++)		
                                {		

                                    var childDate        = data.children_comments[data.course_comments[i].id][j].created_date;		
                                    var childNew         = childDate.replace(/-/g, '/');		
                                    var childDate        = new Date(childNew);		
                                    var childnewDate     = timeSince(childDate);		

                                    prevcommentHtml+= '<li class="single-answer" id="'+data.children_comments[data.course_comments[i].id][j].id+'">';		
                                    prevcommentHtml+= '<span class="question-avatar thumb-avatar"><img src="'+((data.children_comments[data.course_comments[i].id][j].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.children_comments[data.course_comments[i].id][j].us_image+'" alt="'+data.children_comments[data.course_comments[i].id][j].us_name+'"/></span>';		
                                    prevcommentHtml+= '<span class="answer-detailed-desc">';		
                                    prevcommentHtml+= '<span class="ts-md-12">';		
                                    prevcommentHtml+= '<span class="question-author">'+data.children_comments[data.course_comments[i].id][j].us_name+' '+'</span>';		
                                    if(childnewDate.charAt(0) == '-')
                                    {
                                        childnewDate = 'just now';
                                    }
                                    prevcommentHtml+= '<span class="posted-on"> '+childnewDate+'</span>';		
                                    prevcommentHtml+= '</span>'+data.children_comments[data.course_comments[i].id][j].comment+'</span>';		

                                    if(data.children_comments[data.course_comments[i].id][j].rl_type!='1'){		

                                        prevcommentHtml+= '<span class="answer-close">';		
                                        prevcommentHtml+= '    <span class="dropdown">';		
                                        prevcommentHtml+= '        <button class="dropbtz"><span class="transform-ninteen">...</span></button>';		
                                        prevcommentHtml+= '        <span class="dropdown-content">';		
                                        if(data.children_comments[data.course_comments[i].id][j].user_id!=data.user_details.id){		
                                            prevcommentHtml+= '            <a class="report_discussion" href="#" onclick="setIDReport('+data.children_comments[data.course_comments[i].id][j].parent_id+','+data.children_comments[data.course_comments[i].id][j].id+')">Report Abuse</a>';		
                                        }		
                                        if(data.children_comments[data.course_comments[i].id][j].user_id==data.user_details.id){		
                                            prevcommentHtml+= '     <a href="#" class="delete_comment" onclick="setID('+data.children_comments[data.course_comments[i].id][j].parent_id+','+data.children_comments[data.course_comments[i].id][j].id+')">Delete</a>';		
                                        }		
                                        prevcommentHtml+= '        </span>';		
                                        prevcommentHtml+= '    </span>';		
                                        prevcommentHtml+= '</span>';		

                                    }		
                                    prevcommentHtml+= '</li>';		
                                } 		
	                    }		
	                }		
                        return prevcommentHtml;		
	            }	
			
	        /* Render comment details if on click parent comment starts */		
			
	            function renderPreviousComments(response){		
	                var data               = $.parseJSON(response);		
	                var prevcommentHtml    = '';		
	                if(data.course_comments.length > 0 )		
	                {		
	                    for (var i=0; i<data.course_comments.length; i++)		
	                    {		
	                        var parentDate  = data.course_comments[i].created_date;		
			
	                        var parentNew   = parentDate.replace(/-/g, '/');		
	                        var date        = new Date(parentNew);		
			
	                        var newDate     = timeSince(date);		
	                        		
                                if(newDate.charAt(0) == '-')
                                {
                                    newDate = 'just now';
                                }
                                
	                        prevcommentHtml+= '<li class="single-question" id="'+data.course_comments[i].id+'">';		
			
	                        prevcommentHtml+= '<span class="question-avatar thumb-avatar"><img src="'+((data.course_comments[i].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.course_comments[i].us_image+'" alt="'+data.course_comments[i].us_name+'"/></span>';		
	                        		
	                        prevcommentHtml+= '<span class="question-detailed-desc">';		
			
			
			
	                        prevcommentHtml+= '<span class="ts-md-12 btxt">'+data.course_comments[i].comment_title;		
	                        prevcommentHtml+= '</span>';		
			
	                        prevcommentHtml+= '<span class="ts-md-12">';   		
	                        prevcommentHtml+= '            <span class="question-author">'+data.course_comments[i].us_name+'</span>';     		
	                        prevcommentHtml+= '            <span class="posted-on"> '+newDate+'</span>';    		
	                        prevcommentHtml+= '</span>';		
			
	                        if(data.course_comments[i].comment)		
	                        {		
	                            prevcommentHtml+= '<span class="ts-md-12">'+data.course_comments[i].comment;  		
	                            prevcommentHtml+= '</span>';		
	                        }		
	                        		
	                        prevcommentHtml+= '</span>';		
			
			
	                        if(data.course_comments[i].rl_type!='1'){		
	                            prevcommentHtml+= '<span class="major-close">';                		
	                            prevcommentHtml+=      '<span class="dropdown">';		
	                            prevcommentHtml+=            '<button class="dropbtz"><span class="transform-ninteen">...</span></button>';		
			
	                            prevcommentHtml+=             '<span class="dropdown-content">';		
	                            if(data.course_comments[i].user_id!=data.user_details.id){		
	                                prevcommentHtml+=                  '<a class="report_discussion" href="#" onclick="setIDReport('+data.course_comments[i].id+',0)">Report Abuse</a>';		
	                            }		
	                            if(data.course_comments[i].user_id==data.user_details.id){		
	                                prevcommentHtml+=       '<a class="delete_discussion" href="#" onclick="setID('+data.course_comments[i].id+',0)">Delete</a>';		
	                            }		
	                            prevcommentHtml+=             '</span>';		
	                            prevcommentHtml+=      '</span>';  		
	                            prevcommentHtml+= '</span>';		
	                        } 		
	                        		
	                        data.children_comments[data.course_comments[i].id] = data.children_comments[data.course_comments[i].id].reverse();		
	                        		
			
	                        //if(data.children_comments[data.course_comments[i].id].length > 0 )		
	                        //{		
			
	                        prevcommentHtml+= '<ul class="all-answers">';		
	                            prevcommentHtml+= '<span id="append_answer">';		
	                           for (var j=0; j<data.children_comments[data.course_comments[i].id].length; j++)		
	                            {		
	    		
	                                var childDate        = data.children_comments[data.course_comments[i].id][j].created_date;		
	                                var childNew         = childDate.replace(/-/g, '/');		
	                                var childDate        = new Date(childNew);		
			
	                                var childnewDate     = timeSince(childDate);		
	                            		
	                                prevcommentHtml+= '<li class="single-answer" id="'+data.children_comments[data.course_comments[i].id][j].id+'">';		
	                                prevcommentHtml+= '<span class="question-avatar thumb-avatar"><img src="'+((data.children_comments[data.course_comments[i].id][j].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.children_comments[data.course_comments[i].id][j].us_image+'" alt="'+data.children_comments[data.course_comments[i].id][j].us_name+'"/></span>';		
	                                prevcommentHtml+= '<span class="answer-detailed-desc">';		
	                                prevcommentHtml+= '<span class="ts-md-12">';		
	                                prevcommentHtml+= '<span class="question-author">'+data.children_comments[data.course_comments[i].id][j].us_name+' '+'</span>';		
                                        if(childnewDate.charAt(0) == '-')
                                        {
                                            childnewDate = childnewDate.substring(1, childnewDate.length);
                                        }
	                                prevcommentHtml+= '<span class="posted-on"> '+childnewDate+'</span>';		
	                                prevcommentHtml+= '</span>'+data.children_comments[data.course_comments[i].id][j].comment+'</span>';		
			
			
			
			
			
			
			
	                                if(data.children_comments[data.course_comments[i].id][j].rl_type!='1'){		
			
	                                    prevcommentHtml+= '<span class="answer-close">';		
	                                    prevcommentHtml+= '    <span class="dropdown">';		
	                                    prevcommentHtml+= '        <button class="dropbtz"><span class="transform-ninteen">...</span></button>';		
	                                    prevcommentHtml+= '        <span class="dropdown-content">';		
	                                    if(data.children_comments[data.course_comments[i].id][j].user_id!=data.user_details.id){		
	                                        prevcommentHtml+= '            <a class="report_discussion" href="#" onclick="setIDReport('+data.children_comments[data.course_comments[i].id][j].parent_id+','+data.children_comments[data.course_comments[i].id][j].id+')">Report Abuse</a>';		
	                                    }		
	                                    if(data.children_comments[data.course_comments[i].id][j].user_id==data.user_details.id){		
	                                        prevcommentHtml+= '     <a href="#" class="delete_comment" onclick="setID('+data.children_comments[data.course_comments[i].id][j].parent_id+','+data.children_comments[data.course_comments[i].id][j].id+')">Delete</a>';		
	                                    }		
	                                    prevcommentHtml+= '        </span>';		
	                                    prevcommentHtml+= '    </span>';		
	                                    prevcommentHtml+= '</span>';		
			
	                                }		
			
	                                prevcommentHtml+= '</li>';		
			
	                            } 		
			
	                            prevcommentHtml+= '</span>';		
			
	                                prevcommentHtml+= '<li class="single-answer">';		
	                                prevcommentHtml+= '<span class="question-avatar thumb-avatar"><img class="imag-res" src="'+((data.user_details.us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.user_details.us_image+'" alt="'+data.user_details.us_name+'"/></span>';		
	                                prevcommentHtml+= '<span class="answer-detailed-desc">';		
	                                prevcommentHtml+= '<textarea placeholder="Add an answer" class="add-answer" id="comment_textarea" maxlength="1000"></textarea>';		
	                                prevcommentHtml+= '<button class="green-btn lefty add-btn sbtm add_answer" style="display:none;" id="'+data.course_comments[i].id+'">Add an answer</button>';		
	                                prevcommentHtml+= '</span>';		
	                                prevcommentHtml+= '</span>';		
	                                prevcommentHtml+= '</li>';		
			
	                        prevcommentHtml+= '</ul>';		
	                        //}		
	                    }		
	                }		
                        return prevcommentHtml;		
	            }		
	        /* Render comment details ends here */		
			
	    //});		
	/* Comment on click show its details ends here */		
			
	    /* Search textbox keyup function */		
                var __questionSearchTimeOut = null;
	        $(document).on('keyup','#search_text',function(){		
                    clearInterval(__questionSearchTimeOut);
                    __questionSearchTimeOut = null;
	            $('.loader').css('display','block');		
		    proceedToQuestionSearch();
	        });		
                
                function proceedToQuestionSearch()
                {
                    var keyword = $('#search_text').val();		
	            $.ajax({		
	            url: site_url+'material/commented_users_json',		
	            type: "POST",		
	            data:{"is_ajax":true,'course_id' : __course_id, 'keyword':keyword},		
	                success: function(response) {		
	                    var data           = $.parseJSON(response);		
	                    //console.log(data);		
	                    if(data.course_comments.length > 0){		
	                        $('#show_parent').html(renderNewHtml(response));		
	                        $('.loader').css('display','none');		
	                        $('#search_count').html('We found '+data.course_comments.length+' related questions');		
	                    }		
	                    else		
	                    {		
	                        $('.loader').css('display','none');		
	                        $('#search_count').html('We found '+data.course_comments.length+' related questions');		
	                        $('#show_parent').html(renderNullHtml());		
	                    }		
	                    if(keyword === '')		
	                    {		
	                        $('#search_count').html('<span id="q_count">'+$('#show_parent li').length+'</span> Questions in this section');		
	                    }	
                            if(__questionSearchTimeOut == null)
                            {
                                questionTimeOutSearch();
                            }
	                }		
	            });
                }
                
                function questionTimeOutSearch()
                {
                    __questionSearchTimeOut = setInterval(function(){
                        if($('.discussions').is(':visible') == true)
                        {
                            proceedToQuestionSearch();                        
                        }
                    }, 2000);                    
                }
                             
	        function renderNewHtml(response)		
	        {		
	            var data    = $.parseJSON(response);		
	            console.log(data);		
	            var newHtml = '';		
	            if(data.course_comments.length > 0 )		
	                {		
	                    for(var i=0; i<data.course_comments.length; i++)		
	                    {		
	                        newHtml+= '<li class="individual-question" id="'+data.course_comments[i].id+'">';		
	                        newHtml+= '     <span class="question-avatar"><img src="'+((data.course_comments[i].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.course_comments[i].us_image+'" alt="'+data.course_comments[i].us_name+'" width="50" height="50"></span>';		
	                        newHtml+= '    <span class="question-description">';		
			
	                        newHtml+= '<div class="archive-question">'+data.course_comments[i].comment_title+'</div>';		
	                        newHtml+= ' <div class="archive-answer">'+data.course_comments[i].comment+'</div>';		
	   		
	                        newHtml+= '   </span>';		
	                        newHtml+= '</li> ';		
	                    }		
	                    return newHtml;		
	                }		
	        }		
			
	        function renderNullHtml()		
	        {		
	            var Html = '';		
	                Html+= '<div class="fx-c p20" style="">';		
	                Html+= '                <div class="fx tac">';		
	                Html+= '                    <div class="bold pt10">';		
	                Html+= '                        <span>No related questions found for this search</span>';		
	                Html+= '                    </div>';		
	                Html+= '               <div translate="">';		
	                Html+= '                     <span>Be the first to ask your question! You’ll be able to add details in the next step.</span>';		
	                Html+= '               </div>';		
	                Html+= '             <img src="'+theme_url+'/images/raise_your_hand.png" alt="img">';		
	                Html+= '        </div> </div>';		
	                return Html;		
	        }		
	    /* search function ends here */		
			
	    /* Delete comments onclick */		
			
	    $(document).on('click','.delete_discussion',function(){		
	        $('#delete_comment').modal('show');		
	        $('#confirm_box_content_1').html('Are you sure you want to delete this discussion ?');		
	    });		
			
	    $(document).on('click','.delete_comment',function(){		
	        $('#delete_comment').modal('show');		
	        $('#confirm_box_content_1').html('Are you sure you want to delete this comment ?');		
	    });		
			
	    function setID(parent_id,child_id)		
	    {		
	        $('#modal_parent_id').val(parent_id);		
	        $('#modal_child_id').val(child_id);		
	    }		
			
	    function deleteCommentUser()		
	    {		
	        var parent_id = $('#modal_parent_id').val();		
	        var child_id  = $('#modal_child_id').val();		
			
	        $.ajax({		
	                url: site_url+'material/delete_comments_admin',		
	                type: "POST",		
	                data:{"is_ajax":true,'course_id' : __course_id, 'parent_id':parent_id,'child_id':child_id},		
	                success: function(response) {		
	                    if(child_id>0){		
	                        $('#'+child_id).remove();               		
	                    }		
	                    if(child_id==0){		
	                        $('[id="'+parent_id+'"]').remove();		
	                        $('#q_count').html($('#q_count').html() - 1);		
	                    }		
	                    if($('#q_count').html()=="0")		
	                    {		
	                        $('.discussions').fadeIn('slow');		
	                        $('.question-detail').hide();		
	                        $('#show_parent').html(renderNullHtml());		
	                    }		
			
	                    if($('ul .single-question').length == 0)		
	                    {		
	                        $('.discussions').fadeIn('slow');		
	                        $('.question-detail').hide();		
	                    }		
	                    $('#delete_comment').modal('hide');		
	                }		
	       });		
	    }		
	    /* Delete section ends */		
			
	    /* Report section for discussion */		
			
	    $(document).on('click','.report_discussion',function(){		
	        $('#report_comment').modal('show');		
	    });		
			
	    function setIDReport(parent_id,child_id)		
	    {		
	        $('#modal_parent_id_report').val(parent_id);		
	        $('#modal_child_id_report').val(child_id);		
	    }		
			
	    /* Report section ends here */		
			
	    /* Formatting  time */		
	    function timeSince(date) {		
			
	        var seconds = Math.floor((new Date() - date) / 1000);		
	        if(seconds=='0')		
	        {		
	            seconds = 1;		
	        }		
	        var interval = Math.floor(seconds / 31536000);		
	        if (interval >= 1) {		
	            return interval + " years ago";		
	        }		
	        interval = Math.floor(seconds / 2592000);		
	        if (interval >= 1) {		
	            return interval + " months ago";		
	        }		
	        interval = Math.floor(seconds / 86400);		
	        if (interval >= 1) {		
	            return interval + " days ago";		
	        }		
	        interval = Math.floor(seconds / 3600);		
	        if (interval >= 1) {		
	            return interval + " hours ago";		
	        }		
	        interval = Math.floor(seconds / 60);		
	        if (interval >= 1) {		
	            return interval + " minutes ago";		
	        }		
			
	        return Math.floor(seconds) + " seconds ago";		
	    }		
	    /* formatting time ends*/ 		
			
	/* Code for discussion panel new design ends here */