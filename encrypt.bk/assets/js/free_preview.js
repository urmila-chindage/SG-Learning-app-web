    var __activeCourse              = 0;
    var __courseSelected            = new Array();
    var __courseSelected            = new Array();
    var __roleDescription           = new Object();
    var __courseObject              = new Object();
    var activeCoursePreviewTime     = '0';
    var courseAppend                = false;
    var activeStatus                = true;
    var __currentCourseUser         = [];
    var __searchTimeOut             = '';
    var __sendEmailsBulk            = new Array();
    var __tempCoursepreview         = 0;

    __roleDescription[1] = 'Sub admin can do all the functionalities done by the Super Admin.';
    __roleDescription[3] = 'Tutor can manage contents and students of their assigned courses. Also they can create their own courses.';
    __roleDescription[4] = 'Content editor can manage courses with limited features and manage contents';
    
    $( document ).ready( function() {
        __courseObject =  $.parseJSON(__PreviewCourseObject);
        clearCache();
        $('#loading').remove();
        if(Object.keys( __courseObject).length == 0 ) {
            $('#select_all').hide();
            $('.preview-report-content').prepend(renderPopUpMessagePage('error', 'No courses found.'));
            $('.left-report-container').hide();
            $('#popUpMessagePage .close').css('display', 'none');
        }
        else {
            $('#preview_course_wrapper').html(rendercourseHtml( __courseObject ));
            if( __showLoadButton ) {
                $('#loadmorebutton').show();
                $('#loadmorebutton').html('Load More '+ __remainingCourses +'<ripples></ripples>');
            }
            $('.right-report-container').show();
            courseUserDetail( __activeCourse,activeCoursePreviewTime );
        } 
        $('#redactor_invite').redactor( {
            imageUpload : admin_url+'configuration/redactore_image_upload',
            source      : false,
            plugins     : ['table', 'alignment'],
            callbacks   :   {
                                imageUploadError: function( json, xhr ) {
                                    alert('Please select a valid image');
                                    return false;
                                }
                            }   
        } );
    });

    $( document ).on('keyup', '#course_keyword', function() {
        clearTimeout( __searchTimeOut );
         __searchTimeOut    = setTimeout( function() {
            __activeCourse  = 0;
            __offset        = 1;
            
            $('.preview-report-content').prepend(`<div class="rTableCell text-center" id ="loading" style="margin-top:15px;" >Loading.....</div>`);
            
            loadcourses();
            
            var currentKeyword   = $('#course_keyword').val();
            if ( history.pushState ) {
                var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
                if (currentKeyword != '') {
                    var uSearch         = currentKeyword.split(' ').join('-');
                    link               += '?keyword=' + uSearch;
                }
                
                window.history.pushState({ path: link }, '', link);
            }
         }, 300);
    });

    $( document ).on('click', '#searchclear', function() {
        __activeCourse      = 0;
        __offset            = 1;
        var currentKeyword  = $('#course_keyword').val();
        if (history.pushState) {
            var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
            
            if ( currentKeyword != '') {
                var uSearch     = currentKeyword.split(' ').join('-');
                link           += '?keyword=' + uSearch;
            }
            
            window.history.pushState({ path: link }, '', link);
        }
        loadcourses();
    });

    $( document ).on('click', '#course_search', function() {
        var courseKeyword = $('#course_keyword').val().trim();        
        if(courseKeyword == '')
        {
            lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
        }
        else{
            __activeCourse  = 0;
            __offset        = 1;
            loadcourses();
        }
    });

    $(document).on('click', '.user-checkbox-parent', () =>  {
        var parent_check_box        = this;
        __userSelected              = new Array();
        $('.user-checkbox').not(':disabled').prop('checked', $(parent_check_box).is(':checked'));
        $('.list-button').addClass('list-disabled');
        if ($(parent_check_box).is(':checked') == true) {
            $('.user-checkbox').not(':disabled').each(function (index) {
                __userSelected.push($(this).val());
            });
            $('.list-button').removeClass('list-disabled');
        }
        if ( __userSelected.length > 1 ) {
            $("#selected_user_count").html(' (' + __userSelected.length + ')');
            $("#user_bulk").css('display', 'block');
        } else {
            $("#selected_user_count").html('');
            $("#user_bulk").css('display', 'none');
        }
    });
    
    $( document ).on('click', '.user-checkbox', () =>  {
        var userId = $(this).val();
        if ($('.user-checkbox:checked').length == $('.user-checkbox').length) {
            $('.user-checkbox-parent').prop('checked', true);
        }
        if ($(this).is(':checked')) {
            $('.list-button').removeClass('list-disabled');
            __userSelected.push(userId);
        } else {
            $('.user-checkbox-parent').prop('checked', false);
            $('.list-button').addClass('list-disabled');
            removeArrayIndex( __userSelected, userId );
        }
        if ( __userSelected.length > 1 ) {
            $("#selected_user_count").html(' (' + __userSelected.length + ')');
            $("#user_bulk").css('display', 'block');
        } else {
            $("#selected_user_count").html('');
            $("#user_bulk").css('display', 'none');
        }
    });
   
    function preventSpecialCharector(e)
    {
        var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
    }
    
    function loadMorecourses()
    {
        courseAppend = true;
        
        loadcourses();
    }

    function loadcourses()
    {
        var keyword  = $('#course_keyword').val();
       
        $('#loadmorebutton').html('Loading..');
        $('.left-report-container,.right-report-container').hide();
        $('#popUpMessagePage').remove();
 
        $.ajax({
            url: admin_url+'report/free_preview_ajax',
            type: "POST",
            data:{ "is_ajax" : true, "course_keyword" : keyword, 'limit' : __limit, 'offset' : __offset },
            success: function( response ) {
                var data        = $.parseJSON(response);
                var remaining   = 0;

                $('#loadmorebutton').hide();
                $('#loading').remove();

                if( data['preview'].length > 0 ) {
                    remaining       = ( parseInt( __offset ) - 1 ) * __limit;
                    remaining       = remaining + data['preview'].length;
                    remaining       = data['total_courses'] - remaining;

                    __offset++;   

                    $('.left-report-container,.right-report-container').show();

                    if( courseAppend ) {
                        $('#preview_course_wrapper').append( rendercourseHtml( data['preview'] ) );
                        courseAppend = false;
                    } else {
                        $('#preview_course_wrapper').html( rendercourseHtml( data['preview'] ) );
                    }
                    courseUserDetail( __activeCourse, activeCoursePreviewTime );
                }
                else
                {
                    clearCache();

                    $('#preview_course_users').html('');
                    $('.preview-report-content').prepend(renderPopUpMessagePage('error', 'No courses found.'));
                    $('.left-report-container').hide();
                    $('#popUpMessagePage .close').css('display', 'none');

                }
                if( data['show_load_button'] == true ) {
                    $('#loadmorebutton').show();
                }
                remaining = (remaining>0)?'('+remaining+')':'';
                $('#loadmorebutton').html('Load More '+remaining+'<ripples></ripples>');
            }
        });
    }
    
    function rendercourseHtml( courses ) {
        var courseHtml                  = '';
        var activeClass                 = 'active';
            activeStatus                = true;
            if( courseAppend ) {
                activeStatus    = false;
            }
        if( courses.length > 0 ) {
            $.each( courses, function( courseKey, course ) {
                if( activeStatus === true ){
                    __activeCourse              = course['id'];
                    activeStatus                = false;
                    activeCoursePreviewTime     = course['cb_preview_time'];
                } else {
                    activeClass     = '';
                }
                courseHtml +=  `<div class="report-row `+ activeClass +`" onclick="courseUserDetail('`+ course['id'] +`','`+ course['cb_preview_time'] +`')" id="report-row-`+ course['id'] +`">
                                <div class="report-row-content">
                                <div class="cap-icon"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 408" style="enable-background:new 0 0 512 408;width: 22px;height: 22px;fill: #64277d;" xml:space="preserve"><g><g><path d="M503.7,122.6l-241-121c-4.2-2.1-9.2-2.1-13.5,0l-241,121C3.2,125.1,0,130.3,0,136c0,5.7,3.2,10.9,8.3,13.4L30,160.2v127.4    C12.5,293.8,0,310.4,0,330v61c0,8.3,6.7,15,15,15h60c8.3,0,15-6.7,15-15v-61c0-19.6-12.5-36.2-30-42.4V175.2l31,15.4V256    c0,29.5,18.2,56.7,51.2,76.5c30.6,18.4,71,28.5,113.8,28.5c89.3,0,165-44.6,165-105v-65.4l82.7-41.2    C514.7,143.9,514.8,128.1,503.7,122.6z M60,376H30v-46c0-8.2,6.7-14.9,14.9-15c0,0,0.1,0,0.1,0s0.1,0,0.1,0    c8.2,0,14.9,6.8,14.9,15V376z M391,256c0,40.7-61.8,75-135,75s-135-34.3-135-75v-50.5l128.3,63.9c2.1,1,4.4,1.6,6.7,1.6    s4.6-0.5,6.7-1.6L391,205.5V256z M256,239.2c-11.2-5.6-198.7-98.9-207.5-103.3L256,31.8l207.5,104.2    C454.3,140.5,266.8,233.9,256,239.2z"></path></g></g></svg></div>
                                <div class="prev-report-title">`+ course['cb_title'] +`</div>
                                <div class="status-count">`+ course['user_count'] +`</div>
                                <div class="td-dropdown rTableCell">
                                    <div class="btn-group lecture-control" style="margin: 0px !important;">
                                        <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">   
                                            <span class="label-text">                  
                                            <i class="icon icon-down-arrow"></i>                
                                            </span>                
                                            <span class="tilder"></span>            
                                        </span>            
                                        <ul class="dropdown-menu pull-right" role="menu" id="user_action_138">
                                            <li>                     
                                            <a href="javascript:void(0);" onclick="exportPreview('`+ course['id'] +`')">Export</a>                
                                            </li>
                                            <li> 
                                            <a href="javascript:void(0);" onclick="sendMessageToUser('0','`+ course['id'] +`')">Send Message</a> 
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                </div>
                            </div>`;

            });
        }

        return courseHtml;
    }
    
    function getcourseObjectIndex( course_id ) {
        return index;
    }

    function courseUserDetail( course_id,coursePreview ) {
        __userSelected = [];
        __tempCoursepreview = coursePreview;
        $("#selected_user_count").html('');
        if( ( course_id > 0 ) == false )
        {
            return false;
        }
        __activeCourse  = course_id;
        $('.report-row').removeClass('active');
        $('#report-row-'+ course_id ).addClass('active');
        $('#checkbox-parent').hide();
        $('.user-checkbox-parent').prop('checked', false);
        $('#preview_course_users').html('loading..');
       
        $.ajax( {
                    url     : admin_url+'report/get_preview_users_by_course',
                    type    : "POST",
                    data    : {"is_ajax":true, 'course_id':course_id},
                    success : function(response) {
                                var data = $.parseJSON(response);
                                if( data['preview_user'].length > 0 ){
                                    var previewUsers = '';
                                    coursePreview    = coursePreview * 60 ;
                                    $.each( data['preview_user'], function(userKey, user ) {
                                        previewUsers += `<label class="free-users-info d-flex justify-between align-center"> 
                                                            <div style="margin-right: 10px;">
                                                                <input type="checkbox" class="user-checkbox" value="`+ user['id'] +`" id="">
                                                            </div>
                                                            <div class="previeved-user-row">
                                                                <div class="d-flex justify-between">
                                                                    <div>`+ user['us_name'] +`</div>
                                                                    <div>`+ (( parseInt(user['cpt_course_time']) > parseInt(__tempCoursepreview) ) ? secondsToHms( __tempCoursepreview ) :secondsToHms( user['cpt_course_time'] )) +` / `+ secondsToHms( __tempCoursepreview ) +` min</div>
                                                                </div>
                                                                <div class="d-flex justify-between">
                                                                    <div class="free-users-contact">`+ user['us_email'] +`</div>
                                                                    <div class="free-users-contact">`+ dateToString( user['updated_date'] ) +`</div>
                                                                </div>
                                                            </div>
                                                            <div class="td-dropdown rTableCell" style="margin:0px;">
                                                                <div class="btn-group lecture-control" style="margin: 0px !important;">
                                                                    <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">   
                                                                        <span class="label-text">                  
                                                                        <i class="icon icon-down-arrow"></i>                
                                                                        </span>                
                                                                        <span class="tilder"></span>            
                                                                    </span>            
                                                                    <ul class="dropdown-menu pull-right" role="menu" id="user_action_138">
                                                                        <li> 
                                                                            <a href="javascript:void(0);" onclick="sendMessageToUser(`+user['id']+`)">Send Message</a> 
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </label>`
                                    });
                                    if( data['preview_user'].length > 1 ){
                                        $('#checkbox-parent').show();
                                    }
                                    $('#preview_course_users').html( previewUsers );  
                                } else {
                                    clearCache();
                                    $('#course_detail_wrapper').html('');
                                    $('#preview_course_users').html(renderPopUpMessagePage('error', 'No users found.'));
                                    $('#popUpMessagePage .close').css('display', 'none');
                                }
                       
                    }
            });

    }
    function secondsToHms( seconds,maxTime = 0 ) {
        if( maxTime != 0 && seconds > maxTime ) {
            seconds   = maxTime;
        }
        var date    = new Date(seconds * 1000);
        var hh      = date.getUTCHours();
        var mm      = date.getUTCMinutes();
        var ss      = date.getSeconds();
            if (hh < 10) {hh = "0"+hh;}
            if (mm < 10) {mm = "0"+mm;}
            if (ss < 10) {ss = "0"+ss;}
            // This formats your string to HH:MM:SS
            var t = mm+":"+ss;
            return t;
    }

    function dateToString( data ) {
        if( data == null ) {
            return 'Nill';
        }
        var parsedate   = Date.parse(data);
        var today       = new Date(parsedate);
        var dd          = today.getDate();
        var mm          = today.getMonth() + 1; //January is 0!
        var yyyy        = today.getFullYear();
        if (dd < 10) {
            dd = '0' + dd;
        } 
        if (mm < 10) {
            mm = '0' + mm;
        } 
        return today = dd + '/' + mm + '/' + yyyy;
    }
   
    function sendMessageBulk( userId )
    {
        userId                      = typeof userId != 'undefined' ? userId : '';
        var send_user_bulk_subject  = $('#invite_send_subject').val();
        var send_user_bulk_message  = btoa($('#redactor_invite').val());
        var errorCount              = 0;
        var errorMessage            = '';
        if ($.trim( send_user_bulk_subject ) == '') {
            errorCount++;
            errorMessage += 'Please enter subject<br />';
        }
        if ($.trim( send_user_bulk_message ) == '') {
            errorCount++;
            errorMessage += 'Please enter message<br />';
        }
        if( errorCount > 0 )
        {
            $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', errorMessage ));
            scrollToTopOfPage();    
            return false;
        }
        $('#message_send_button').text('SENDING..');
        var userIds = [];
        if( userId != '' ){
            if( userId == 'sendToCourse' ){
                userIds             = __currentCourseUser;
                __currentCourseUser = [];
            } else {
                userIds.push(userId);
            }
               
            } else {
                if(__userSelected.length > 0){
                    userIds = __userSelected;
                }
            }

        $.ajax({
            url : admin_url + 'user/send_message',
            type: "POST",
            data: {
                "is_ajax": true,
                "send_user_subject": send_user_bulk_subject,
                "send_user_message": send_user_bulk_message,
                "user_ids": JSON.stringify(userIds)
            },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data['error'] == false || data['success'] == true) {
                    $('#invite-user-bulk').modal('hide');
                    var messageObject = { 'body' : data['message'], 'button_yes' : 'OK' };
                    callback_success_modal( messageObject );
                } else {
                    $('#invite-user-bulk .modal-body').prepend( renderPopUpMessage( 'error', data['message'] ) );
                }
                $('#message_send_button').text('SEND');
    
                setTimeout(function () {
                    $('#invite-user-bulk').modal('hide');
                }, 2500);
            }
        });
        
    }
    
    function clearCache() {
        __userSelected   = new Array();
        $('.user-checkbox-parent').prop('checked', false);
        $('#selected_user_count').html('');
        $('#bulk_action_wrapper').hide();
    }
    function exportPreview( id = false ) {
        var param           = {};
        param['keyword']    = $.trim($('#course_keyword').val());
        param['filter']     = '0';
        param['id']         = (id)?id:'0';
        location.href       = admin_url+'report/export_course_preview/'+btoa(JSON.stringify(param));
    }
    function sendMessageToUser( user_id,courseId ) {
        var user_id_temp    = ( typeof user_id != 'undefined' ) ? ( ( user_id != '0' ) ? user_id :'') : '';
        if( typeof courseId != 'undefined' ) {
            $.ajax( {
                url : admin_url+'report/get_preview_users_by_course',
                type: "POST",
                data:{"is_ajax":true, 'course_id':courseId},
                success: function( response ) {
                   
                    var data = $.parseJSON( response );
                    
                    if( data['preview_user'].length > 0 ) {
                        var userIds = [];
                        $.each( data['preview_user'], function( userKey, user ) {
                            userIds.push(user['id']);
                        });

                        if( userIds.length > 0 ){
                            __currentCourseUser = userIds;
                            $('#message_send_button').attr('onclick', 'sendMessageBulk("sendToCourse")');
                        }
                    }
                   }
            });
        } else {
            $('#message_send_button').attr('onclick', 'sendMessageBulk(' + user_id_temp + ')');
        }
        $('#invite-user-bulk').modal();
        $('#popUpMessage').hide();
        $('#invite_send_subject').val('');
        $('#redactor_invite').redactor('insertion.set', '');
        
    }

    
    
   

