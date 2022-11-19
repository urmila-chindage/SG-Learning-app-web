var __offset = 0;
var __reviewsCount = '0';
var editShow                 = jQuery.inArray( '3', __userPrivilege) != -1 ? 'block' : 'none';
var editPerimssion           = jQuery.inArray( '3', __userPrivilege) != -1 ? 'visible' : 'hidden';
var deletePerimssion         = jQuery.inArray( '4', __userPrivilege) != -1 ? 'block' : 'none';
console.log(editShow, editPerimssion, deletePerimssion);

function loadReviewssAdmin() {
    
    $('#reviewblock').html('<h5>Loading..</h5>');
    $('#loadmorebutton').css('display', 'none');
    var flag = __offset;
    $.ajax({
        url: admin_url + 'course/load_reviews',
        type: "POST",
        data: {
            "is_ajax": '1', 
            'limit': __limit,
            'offset': __offset,
            'course_id': __course_id,
            "count": __reviewsCount
        },
        success: function(response) {
            var data            = $.parseJSON(response);
                __reviewsCount  = data.total_records;
                __defaultpath   = data.default_user_path;
                __userpath      = data.user_path;
                //console.log(data.user_path, 'data.user_path');
            if (data.success == true) {
                __offset = data.start;
                var groupsHtml = '';
                if (Object.keys(data.reviews).length > 0) {
                    $('#reviewExportButton').show();
                    $.each(data.reviews, function(reviewsid, reviews) {
                        groupsHtml += renderhtml(reviews);
                    });

                    var load_button = '<div class="rTableCell text-center">' +
                        '<button id="loadmorebutton"  class="btn btn-green selected margin-12 " onclick="loadReviewssAdmin()">Load More' +
                        '<ripples></ripples>' +
                        '</button>' +
                        '</div>';
                    if (flag == '0') {
                        $('#reviewblock').html(groupsHtml);
                        $('#review').append(load_button);
                    } else {
                        $('#reviewblock').append(groupsHtml);

                    }
                    if (data.show_load_button == true) {
                        $('#loadmorebutton').show();
                    } else {
                        $('#loadmorebutton').hide();
                    }
                    $('#review').fadeIn('slow');
                }else{
                    $('#reviewExportButton').hide();
                    $('#reviewblock').html(renderPopUpMessage('error', 'No Review found.'));
                }
            }
        }
    });

}

function renderhtml(reviews) {
    
    var cc_review_reply          = reviews.cc_admin_reply ? $.parseJSON(reviews.cc_admin_reply) : '';
    user_img                     = __userpath + reviews.cc_user_image;
    var reviewStatus             =  reviews.cc_status == '0' ? reviewStatus = 'Unpublished' : (reviews.cc_status == '2' ? 'Unpublished' : 'Published');
    var reviewElement            =  reviews.cc_status == '0' ? reviewElement = '<span class="warning-icon">!</span>' : (reviews.cc_status == '2' ? '<span class="warning-icon">!</span>' : '<i class="icon icon-ok-circled"></i>');
    var labelClass               = reviews.cc_status == '0' || reviews.cc_status == '2' ? 'Inactive-section' : 'active-section';
    

    reviewHtml = '';
    reviewHtml += `<div class="panel-group anouncement-pannel" id="an_id${reviews.id}" data-id="${reviews.id}">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="anouncement-holder">
                <div class="review-width-65">
                    <div class="media">
                        <div class="media-left">                          
                            <span class="icon-wrap-round img">                              
                                <img src="${user_img}">                          
                            </span>                      
                        </div>
                        <div class="media-body reviewer-info">
                            <span class="media-heading review-name">${reviews.cc_user_name}</span>                          
                            <p class="date">${dateFormat(reviews.created_date)}</p>
                        </div>
                    </div>
                </div>

                <div id="section_status_wraper_${reviews.id}" class="${labelClass}">
                    <span class="ap_cont section-main-18" id="section_status_text_${reviews.id}">${reviewElement} ${reviewStatus}</span>
                </div>`;
                
        var deletoredit = 'hidden';
    if(deletePerimssion == 'block' || editPerimssion == 'visible'){
        deletoredit = 'visible';
    }

    reviewHtml += `<div class="td-dropdown rTableCell" style="visibility:${deletoredit}">
                    <div class="btn-group lecture-control">
                        <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">                          
                            <span class="label-text"><i class="icon icon-down-arrow"></i></span>                          
                            <span class="tilder"></span>                      
                        </span>                      
                        <ul class="dropdown-menu pull-right" role="menu" id="review_menu_${reviews.id}">`;
    
    if(reviews.cc_status == '0'){
        reviewHtml += `     <li style="display:${editShow}">  <a href="javascript:void(0)" onclick="changeReviewStatus(${reviews.id +',\'1\', 0,'+reviews.cc_user_id})">Publish</a></li>`;
    }else if(reviews.cc_status == '1'){
        reviewHtml += `     <li style="display:${editShow}">  <a href="javascript:void(0)" onclick="changeReviewStatus(${reviews.id +',\'0\', 0,'+reviews.cc_user_id})">Unpublish</a></li>`;
    }
        reviewHtml += `     <li style="display:${deletePerimssion}">  <a href="javascript:void(0)" onclick="deleteReview(${reviews.id +','+reviews.cc_user_id})">Delete</a></li>`;
    
    reviewHtml += `
                        </ul>
                    </div>
                </div>`;
    

    reviewHtml += `</div>
            <input type="hidden" value="${cc_review_reply.cc_review_reply ? cc_review_reply.cc_review_reply : ''}" id="adminReplyMessage_${reviews.id}"/>
            <div class="anouncement-content">
                <div id="an_${reviews.id}_des" class="redactor-editor">
                    ${reviews.cc_reviews}
                </div>
                <div class="review-actions d-flex justify-between align-center hide-review-actions" id="rateQuickReplyBtn_${reviews.id}">
                    <div class="star-ratings-sprite star-ratings-sprite-block">                  
                        <span style="width:${(reviews.cc_rating/5)*100}%" class="star-ratings-sprite-rating"></span>              
                    </div>`;
    if(reviews.cc_status == '2'){
    reviewHtml += `
                    <div class="publish-ignores"  style="visibility:${editPerimssion}">
                        <label class="label label-success labelstatus_${reviews.id}" onclick="changeReviewStatus(${reviews.id},1, '0',${reviews.cc_user_id})">Publish</label>
                        <label class="label label-warning labelstatus_${reviews.id}" onclick="changeReviewStatus(${reviews.id},0,1,${reviews.cc_user_id})">Ignore</label>
                    </div>`;
    }
        if(cc_review_reply == '' && reviews.cc_status == '1'){
        reviewHtml += `  
                    <div class="text-right /*reply-btn-holder*/ publish-ignores" id="adminReplyButton_${reviews.id}" style="visibility:${editPerimssion}">
                        <a class="reply-btn" href="javascript:void(0)" onclick="admin_reply(${reviews.id})">
                            <span><svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 77.025 1792 1571.686"><path fill="#57ba56" stroke="#57ba56" stroke-width="90" stroke-miterlimit="10" d="M1720.697,1099.628 c0,101.86-38.964,240.229-116.894,415.109c-1.841,4.296-5.063,11.659-9.664,22.091c-4.603,10.431-8.744,19.635-12.426,27.612 s-7.67,14.727-11.966,20.249c-7.363,10.432-15.953,15.647-25.771,15.647c-9.204,0-16.414-3.067-21.63-9.204 s-7.823-13.807-7.823-23.011c0-5.522,0.767-13.652,2.301-24.392c1.534-10.738,2.301-17.947,2.301-21.629 c3.067-41.726,4.603-79.463,4.603-113.212c0-61.976-5.369-117.508-16.107-166.597c-10.739-49.089-25.618-91.582-44.641-127.479 s-43.566-66.884-73.634-92.962c-30.067-26.079-62.435-47.402-97.104-63.97c-34.669-16.567-75.475-29.607-122.416-39.118 s-94.189-16.107-141.745-19.789c-47.555-3.681-101.399-5.522-161.534-5.522H660.372v235.627c0,15.954-5.829,29.76-17.488,41.419 c-11.659,11.659-25.465,17.488-41.419,17.488s-29.76-5.829-41.419-17.488L88.791,699.245 c-11.658-11.659-17.488-25.465-17.488-41.419s5.83-29.76,17.488-41.419l471.256-471.255c11.659-11.659,25.465-17.488,41.419-17.488 s29.76,5.83,41.419,17.488c11.659,11.658,17.488,25.465,17.488,41.419v235.627h206.174c437.506,0,705.963,123.643,805.369,370.93 C1704.437,875.352,1720.697,977.519,1720.697,1099.628z"></path></svg></span>
                            Reply
                        </a>
                    </div>`;
        }
        reviewHtml += `
                </div>

                <!-- Admin reply -->
                <div class="admin-reply-container adminresponse_${reviews.id}" id="adminresponse_${reviews.id}">
                    <!-- reply writer -->
                    
                    <!-- reply preview -->
                    <div class="admin-reply-preview" id="adminreplypreview_${reviews.id}">`;
        if(cc_review_reply){
        reviewHtml += `
                        <div class="anouncement-holder">
                            <div class="">
                                <div class="media">
                                    <div class="media-left">                          
                                        <span class="icon-wrap-round img">                              
                                        <img src="${__assets_url}images/logo.png">                          
                                        </span>                      
                                    </div>
                                    <div class="media-body">
                                        <span class="media-heading review-name">${cc_review_reply.cc_user_name}</span>                          
                                        <p>${cc_review_reply.cc_review_reply}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right"  style="visibility:${editPerimssion}">
                            <a class="edit-review" href="javascript:void(0)" onclick="admin_reply(${reviews.id})"><i class="icon icon-pencil"></i> Edit</a>
                        </div>`;
                    }
        reviewHtml += `
                    </div>
                    <!-- reply preview ends -->
                </div>
                <!-- Admin reply ends -->

            </div>
        </div>
    </div>
</div>`;

return reviewHtml;
}

function dateFormat(data) {
    var mydate = new Date(data);
    var month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ][mydate.getMonth()];
    str = mydate.getDate() + ' ' + month + ' ' + mydate.getFullYear();

    return str;
}

function deleteReviewConfirmed(params =''){
    var review_id       = params.data.review_id;
    //var courseId        = params.data.course_id;
    var userId          = params.data.userId;
    $.ajax({
        url: admin_url + 'course/delete_review',
        type: "POST",
        data: {
            "review_id": review_id,
            "course_id": __course_id,
            "user_id"  : userId
        },
        success: function (response) {
    
            //console.log(response);return;
            var data = $.parseJSON(response);
    
            var messageObject = {
                'body': data.message,
                'button_yes': 'OK',
            };
    
            if (data.error == false) {
                __reviewsCount--; 
                //loadReviewssAdmin();
                $('#an_id'+review_id).remove();
                if(__reviewsCount == 0){
                    $('#reviewblock').html(`<div id="popUpMessage" class="alert alert-danger">    <a data-dismiss="alert" class="close">×</a>    No Reviews found.</div>`);
                    $('#reviewExportButton').hide();
                }
                callback_success_modal(messageObject);
                
            } else {
                
                callback_danger_modal(messageObject);
            }
        }
    });

}

function deleteReview(reviewId, userId){
    //console.log('Delete',reviewId);

    var actionMessage   = 'Are you sure to Delete this review ?';
   
// console.log();
    var messageObject = {
        'body': actionMessage ,
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            "review_id": reviewId,
            "userId": userId,
            "course_id":__course_id
        },
    };
    callback_warning_modal(messageObject, deleteReviewConfirmed);
}

function validateMaxLength(selector) {
    
    var maxlength = $('#' + selector).attr('maxlength');
    var current_length = $('#' + selector).val().length;
    var remaining = parseInt(maxlength - current_length);
    var left_character = (remaining == 1) ? lang('character_left') : lang('characters_left');
    $('#' + selector + '_char_left').html(remaining + ' ' + left_character);
}

function changeReviewStatus(review_id,status, ignore = '', userId){
    
    var actionMessage   = 'Are you sure to Publish this review ?';
    var label           = 'Unpublish';
    var actinoLabel     = 'Publish';
    if (status == 0) {
        var actinoLabel = 'Unpublish';
        actionMessage   = 'Are you sure to '+actinoLabel+' this review ?';
        label           = 'Publish';
    }else if(status == 2){
        var actinoLabel = 'Ignore';
        actionMessage   = 'Are you sure to '+actinoLabel+' this review ?';
        label           = 'Publish';
    }
    
    var messageObject = {
        'body': actionMessage ,
        'button_yes': actinoLabel.toUpperCase(),
        'button_no': 'CANCEL',
        'continue_params': {
            "review_id": review_id,
            "status": status,
            "label":label,
            "course_id":__course_id,
            "ignore" : ignore,
            "cc_user_id" : userId
        },
    };

    if(ignore != '1'){
        callback_warning_modal(messageObject, changeReviewStatusConfirmed);
        return;
    }
    if(ignore == '1'){

        var messageObject = {
            'data': {
                "review_id": review_id,
                "status": status,
                "label":label,
                "course_id":__course_id,
                "ignore" : ignore,
                "cc_user_id" : userId
            },
        };

        changeReviewStatusConfirmed(messageObject);
    }

}

function changeReviewStatusConfirmed(params) {
//console.log('changeReviewStatusConfirmed', params);
    var review_id       = params.data.review_id;
    var status          = params.data.status;
    var label           = params.data.label;
    var label_status    = (status == 0 || status == 2) ? '1' : '0';
    var user_id         = params.data.cc_user_id;

    if(status == '0'){
        if(!$('#adminReplyMessage_'+review_id).val()){
            $('#adminreplypreview_'+review_id).hide();
        }
    }
    
    $.ajax({
        url: admin_url + 'course/change_reviews_status',
        type: "POST",
        data: {
            "review_id": review_id,
            "status": status,
            "course_id": __course_id,
            "user_id" : user_id
        },
        success: function (response) {
            
            var data = $.parseJSON(response);

            if (data.error == false) {
                reviewHtml  = '';
                reviewHtml += '<li>';
                reviewHtml += '  <a href="javascript:void(0)" onclick="changeReviewStatus(' + review_id +','+label_status+', 0,'+user_id+')">'+label+'</a>';
                reviewHtml += '</li>';
                reviewHtml += '<li>';
                reviewHtml += '  <a href="javascript:void(0)" onclick="deleteReview(' + review_id +','+user_id+')">Delete</a>';
                reviewHtml += '</li>'

                var reviewStatus    =  status == '0' ? reviewStatus = 'Unpublished' : ( status == '2' ? 'Pending Approval' :'Published' );
                var reviewElement            =  status == '0' ? reviewElement = '<span class="warning-icon">!</span>' : (status == '2' ? '<span class="warning-icon">!</span>' : '<i class="icon icon-ok-circled"></i>');
                var labelClass      =  status == '0' || status == '2' ? 'Inactive-section' : 'active-section';
                $('#section_status_text_'+review_id).html(reviewElement+reviewStatus);
                $('#section_status_wraper_'+review_id).removeClass();
                $('#section_status_wraper_'+review_id).addClass(labelClass);
                $('.labelstatus_'+review_id).hide();

                $('#review_menu_'+review_id).html(reviewHtml);
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',    
                };
                //console.log(status, 'status', data);
                if(status == '1'){
                    var adminReplyMessage = $('#adminReplyMessage_'+review_id).val();
                    if(adminReplyMessage !==''){
                        $('#adminreplypreview_'+review_id).show();
                        $('#adminreplypreview_'+review_id).html(`<div class="anouncement-holder" >
                                                                    <div class="review-width-65">
                                                                        <div class="media">
                                                                            <div class="media-left">                          
                                                                                <span class="icon-wrap-round img">                              
                                                                                <img src="${__assets_url}images/logo.png">                          
                                                                                </span>                      
                                                                            </div>
                                                                            <div class="media-body">
                                                                                <span class="media-heading review-name">${__admin_name}</span>                          
                                                                                <p>${adminReplyMessage}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="text-right" style="visibility:${editPerimssion}">
                                                                    <a class="edit-review" href="javascript:void('0')" onclick="admin_reply(${review_id})"><i class="icon icon-pencil"></i> Edit</a>
                                                                </div>`);

                    }else{
                        
                        $('#adminReplyButton_'+review_id).remove();
                            $('#rateQuickReplyBtn_'+review_id).append(`<div class="text-right /*reply-btn-holder*/ publish-ignores" id="adminReplyButton_${review_id}" style="visibility:${editPerimssion}">
                            <a class="reply-btn" href="javascript:void(0)" onclick="admin_reply(${review_id})">
                                <span><svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 77.025 1792 1571.686"><path fill="#57ba56" stroke="#57ba56" stroke-width="90" stroke-miterlimit="10" d="M1720.697,1099.628 c0,101.86-38.964,240.229-116.894,415.109c-1.841,4.296-5.063,11.659-9.664,22.091c-4.603,10.431-8.744,19.635-12.426,27.612 s-7.67,14.727-11.966,20.249c-7.363,10.432-15.953,15.647-25.771,15.647c-9.204,0-16.414-3.067-21.63-9.204 s-7.823-13.807-7.823-23.011c0-5.522,0.767-13.652,2.301-24.392c1.534-10.738,2.301-17.947,2.301-21.629 c3.067-41.726,4.603-79.463,4.603-113.212c0-61.976-5.369-117.508-16.107-166.597c-10.739-49.089-25.618-91.582-44.641-127.479 s-43.566-66.884-73.634-92.962c-30.067-26.079-62.435-47.402-97.104-63.97c-34.669-16.567-75.475-29.607-122.416-39.118 s-94.189-16.107-141.745-19.789c-47.555-3.681-101.399-5.522-161.534-5.522H660.372v235.627c0,15.954-5.829,29.76-17.488,41.419 c-11.659,11.659-25.465,17.488-41.419,17.488s-29.76-5.829-41.419-17.488L88.791,699.245 c-11.658-11.659-17.488-25.465-17.488-41.419s5.83-29.76,17.488-41.419l471.256-471.255c11.659-11.659,25.465-17.488,41.419-17.488 s29.76,5.83,41.419,17.488c11.659,11.658,17.488,25.465,17.488,41.419v235.627h206.174c437.506,0,705.963,123.643,805.369,370.93 C1704.437,875.352,1720.697,977.519,1720.697,1099.628z"></path></svg></span>
                                Reply
                            </a>
                        </div>`);
                        }
                }else{
                    
                     $('#adminReplyButton_'+review_id).hide();
                }
                
                if( params.data === null){
                    callback_success_modal(messageObject);
                }
                
                
            } else {
                var messageObject = {
                    'body': 'Failed to change subscription',
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function admin_reply(rvId){
    var admin_message = $('#adminReplyMessage_'+rvId).val();
    //console.log(admin_message); //return;
    $('#adminReplyButton_'+rvId).hide();
    
    $('#adminreplypreview_'+rvId).show().html(`<div class="d-flex flex-row" style="visibility:${editPerimssion}">
                                            <div class="media-left">                          
                                                <span class="icon-wrap-round img">                              
                                                    <img src="${__assets_url}images/logo.png">                          
                                                </span>                      
                                            </div>
                                            <div class="width100">
                                                <span class="media-heading review-name">${__admin_name}</span>
                                                <textarea placeholder="Write Your Reply" class="form-control" rows="5" maxlength="500" id="adminreply_${rvId}">${admin_message}</textarea>
                                            </div>
                                        </div>
                                        <div class="text-right reply-row">
                                            <label class="label label-warning" onclick="cancelReply(${rvId})">Cancel</label>
                                            <label class="label label-success" onclick="adminReply(${rvId})">Post</label>
                                        </div>`);
    $('#adminreply_'+rvId).focus();
}

function cancelReply(rvId){
    var adminMessage = $('#adminReplyMessage_'+rvId).val();
    if(adminMessage){

        $('#adminreplypreview_'+rvId).html(`<div class="admin-reply-preview">
                                                <div class="anouncement-holder">
                                                    <div class="review-width-65">
                                                        <div class="media">
                                                            <div class="media-left">                          
                                                                <span class="icon-wrap-round img">                              
                                                                <img src="${__assets_url}images/logo.png">                          
                                                                </span>                      
                                                            </div>
                                                            <div class="media-body">
                                                                <span class="media-heading review-name">${__admin_name}</span>                          
                                                                <p>${adminMessage}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <a class="edit-review" href="javascript:void('0')" onclick="admin_reply(${rvId})"><i class="icon icon-pencil"></i> Edit</a>
                                                </div>
                                                </div>`);

    }else{
        $('#adminreplypreview_'+rvId).hide();
        $('#adminReplyButton_'+rvId).remove();
        $('#rateQuickReplyBtn_'+rvId).append(`<div class="text-right /*reply-btn-holder*/ publish-ignores" id="adminReplyButton_${rvId}" style="visibility:${editPerimssion}">
            <a class="reply-btn" href="javascript:void(0)" onclick="admin_reply(${rvId})">
                <span><svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 77.025 1792 1571.686"><path fill="#57ba56" stroke="#57ba56" stroke-width="90" stroke-miterlimit="10" d="M1720.697,1099.628 c0,101.86-38.964,240.229-116.894,415.109c-1.841,4.296-5.063,11.659-9.664,22.091c-4.603,10.431-8.744,19.635-12.426,27.612 s-7.67,14.727-11.966,20.249c-7.363,10.432-15.953,15.647-25.771,15.647c-9.204,0-16.414-3.067-21.63-9.204 s-7.823-13.807-7.823-23.011c0-5.522,0.767-13.652,2.301-24.392c1.534-10.738,2.301-17.947,2.301-21.629 c3.067-41.726,4.603-79.463,4.603-113.212c0-61.976-5.369-117.508-16.107-166.597c-10.739-49.089-25.618-91.582-44.641-127.479 s-43.566-66.884-73.634-92.962c-30.067-26.079-62.435-47.402-97.104-63.97c-34.669-16.567-75.475-29.607-122.416-39.118 s-94.189-16.107-141.745-19.789c-47.555-3.681-101.399-5.522-161.534-5.522H660.372v235.627c0,15.954-5.829,29.76-17.488,41.419 c-11.659,11.659-25.465,17.488-41.419,17.488s-29.76-5.829-41.419-17.488L88.791,699.245 c-11.658-11.659-17.488-25.465-17.488-41.419s5.83-29.76,17.488-41.419l471.256-471.255c11.659-11.659,25.465-17.488,41.419-17.488 s29.76,5.83,41.419,17.488c11.659,11.658,17.488,25.465,17.488,41.419v235.627h206.174c437.506,0,705.963,123.643,805.369,370.93 C1704.437,875.352,1720.697,977.519,1720.697,1099.628z"></path></svg></span>
                Reply
            </a>
        </div>`);

    }
}


function adminReply(rvId){
var adminReply = $('#adminreply_'+rvId).val().trim();
//console.log(adminReply); return;
//if(!adminReply){
    //cancelReply(rvId);  return false;
//}
$('#adminReplyMessage_'+rvId).val(adminReply);
$.ajax({
    url: admin_url + 'course/admin_review_reply',
    type: "POST",
    data: {
        "review_id": rvId,
        "admin_reply": adminReply,
        "course_id": __course_id
    },
    success: function (response) {

        //console.log(response);
        var data = $.parseJSON(response);
        if(!adminReply){
            data.message = 'Admin reply removed';
        }
        var messageObject = {
            'body': data.message,
            'button_yes': 'OK',
        };
        
        if (data.error == false) { 
            $('#adminreplywrite_'+rvId).hide();
            if(adminReply){
                $('#adminreplypreview_'+rvId).html(`<div class="admin-reply-preview">
                                                        <div class="anouncement-holder">
                                                            <div class="review-width-65">
                                                                <div class="media">
                                                                    <div class="media-left">                          
                                                                        <span class="icon-wrap-round img">                              
                                                                        <img src="${__assets_url}images/logo.png">                          
                                                                        </span>                      
                                                                    </div>
                                                                    <div class="media-body">
                                                                        <span class="media-heading review-name">${__admin_name}</span>                          
                                                                        <p>${adminReply}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <a class="edit-review" href="javascript:void('0')" onclick="admin_reply(${rvId})"><i class="icon icon-pencil"></i> Edit</a>
                                                        </div>
                                                        </div>`);
            }else{
                $('#adminreplypreview_'+rvId).hide();
                $('#adminReplyButton_'+rvId).remove();
                $('#rateQuickReplyBtn_'+rvId).append(`<div class="text-right /*reply-btn-holder*/ publish-ignores" id="adminReplyButton_${rvId}" style="visibility:${editPerimssion}">
                <a class="reply-btn" href="javascript:void(0)" onclick="admin_reply(${rvId})">
                    <span><svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 77.025 1792 1571.686"><path fill="#57ba56" stroke="#57ba56" stroke-width="90" stroke-miterlimit="10" d="M1720.697,1099.628 c0,101.86-38.964,240.229-116.894,415.109c-1.841,4.296-5.063,11.659-9.664,22.091c-4.603,10.431-8.744,19.635-12.426,27.612 s-7.67,14.727-11.966,20.249c-7.363,10.432-15.953,15.647-25.771,15.647c-9.204,0-16.414-3.067-21.63-9.204 s-7.823-13.807-7.823-23.011c0-5.522,0.767-13.652,2.301-24.392c1.534-10.738,2.301-17.947,2.301-21.629 c3.067-41.726,4.603-79.463,4.603-113.212c0-61.976-5.369-117.508-16.107-166.597c-10.739-49.089-25.618-91.582-44.641-127.479 s-43.566-66.884-73.634-92.962c-30.067-26.079-62.435-47.402-97.104-63.97c-34.669-16.567-75.475-29.607-122.416-39.118 s-94.189-16.107-141.745-19.789c-47.555-3.681-101.399-5.522-161.534-5.522H660.372v235.627c0,15.954-5.829,29.76-17.488,41.419 c-11.659,11.659-25.465,17.488-41.419,17.488s-29.76-5.829-41.419-17.488L88.791,699.245 c-11.658-11.659-17.488-25.465-17.488-41.419s5.83-29.76,17.488-41.419l471.256-471.255c11.659-11.659,25.465-17.488,41.419-17.488 s29.76,5.83,41.419,17.488c11.659,11.658,17.488,25.465,17.488,41.419v235.627h206.174c437.506,0,705.963,123.643,805.369,370.93 C1704.437,875.352,1720.697,977.519,1720.697,1099.628z"></path></svg></span>
                    Reply
                </a>
            </div>`);
            }
            
            callback_success_modal(messageObject);
            
        } else {
            
            callback_danger_modal(messageObject);
        }
    }
});
}

function exportReviews(){
    //console.log('__course_id', __course_id);
    var param           = {
                            "course_id":    __course_id
                        };
    param               = JSON.stringify(param);
    var pathname        = '/admin/course/export_reviews';
    var link            = window.location.protocol + "//" + window.location.host + pathname;
    window.location     = link + '/' + btoa(param);
}
