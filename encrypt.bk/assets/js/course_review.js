var __filled_rate = '';

function changeReviewStatus(user_id, course_id, user_name, message, button_text)
{
    
    $('#confirm_box_content_review').html(message+" "+button_text.toLowerCase()+" comment from "+user_name+ "?");
    $('#confirm_box_ok_review').html(button_text.toUpperCase());
    $('#confirm_box_ok_review').unbind();
    $('#confirm_box_ok_review').click({"course_id": course_id, "user_id": user_id}, changeReviewStatusConfirmed);    
}

function changeReviewStatusConfirmed(params){
    $.ajax({
        url: admin_url+'course/change_review_status',
        type: "POST",
        data:{"course_id":params.data.course_id, "user_id":params.data.user_id, "is_ajax":true},
        success: function(response) {
            var data  = $.parseJSON(response);
            console.log(data);
            var user_name = data['user_name'];
            
            if(data['error'] == 'false')
            {
                
                if( data['review_status'] == '1')
                {
                    //alert("true");
                    $('#action_status_display_'+data['id']).html('Approved');
                    $('#review_status_wraper_'+data['id']).removeClass('active-section').removeClass('Inactive-section').addClass('active-section');
                    $('#action_status_'+data['id']).html('<a href="javascript:void(0);" data-toggle="modal" data-target="#publish-review" onclick="changeReviewStatus(\''+params.data.user_id+'\',\''+params.data.course_id+'\', \''+user_name+'\', \''+lang('are_you_sure_to')+'\', \''+lang('disapprove')+'\')">'+lang('disapprove')+'</a>');
                }
                else
                {
                    //alert("not true");
                    $('#action_status_display_'+data['id']).html('Disapproved');
                    $('#review_status_wraper_'+data['id']).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
                    $('#action_status_'+data['id']).html('<a href="javascript:void(0);" data-toggle="modal" data-target="#publish-review" onclick="changeReviewStatus(\''+params.data.user_id+'\',\''+params.data.course_id+'\', \''+user_name+'\', \''+lang('are_you_sure_to')+'\', \''+lang('approve')+'\')">'+lang('approve')+'</a>');
                }
                $('#publish-review').modal('hide');
                
            }
            else
            {
                
                $('#confirm_box_title_review').html(data['message']);
                $('#confirm_box_content_review').html('');
            }
        }
    });
}
 
$(document).ready(function(){
    $("#review_add_admin").rateYo({
        starWidth: "18px",
        fullStar: true
    });
    $("#student_rate_five").rateYo({
        starWidth: "15px",
        rating: 5,
        readOnly: true
    });
    $("#student_rate_four").rateYo({
        starWidth: "15px",
        rating: 4,
        readOnly: true
    });
    $("#student_rate_three").rateYo({
        starWidth: "15px",
        rating: 3,
        readOnly: true
    });
    $("#student_rate_two").rateYo({
        starWidth: "15px",
        rating: 2,
        readOnly: true
    });
    $("#student_rate_one").rateYo({
        starWidth: "15px",
        rating: 1,
        readOnly: true
    });
    
    $("#review_add_admin").rateYo("option", "onChange", () =>  {
        /* get the rated fill at the current point of time */
        __filled_rate = $("#review_add_admin").rateYo("rating");
        console.log("The rating is " + __filled_rate);
    });
    
    $(function(){
        $('.review-form-section').slimScroll({
                height: '100%',
                wheelStep : 3,
                distance : '10px'
        });
    });
});

var __uploading_file = new Array();
$(document).on('change', '#user_review_img', function(e){
    readURL(this);
    __uploading_file = e.currentTarget.files;
    console.log(__uploading_file);
    if( __uploading_file.length > 1 )
    {
        lauch_common_message('Error Occured', 'You are not allowed to upload more that one file.');
        return false;
    }
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#review_image_user').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
var __review_progress = false;

 function get_review_admin()
{
    if(__review_progress == true){
        return false;
    }
    var user_name   = $("#cc_user_name").val();
    var user_image  = $("#user_review_img").val();
    var user_rating = __filled_rate;
    var user_review = $("#cc_review").val();
    var errorCount       = 0;
    var errorMessage     = '';
    
    if( user_name == '')
    {
        errorCount++;
        errorMessage += 'User name required <br />';
    }
    if(user_rating == 0)
    {
        errorCount++;
        errorMessage += 'User rating required <br />';
    }
    
//    if(user_image == '')
//    {
//        errorCount++;
//        errorMessage += 'User image required <br />';
//    }
    cleanPopUpMessage();
    if( errorCount > 0 )
    {
        $('#review_form_admin').prepend(renderPopUpMessage('error', errorMessage));
        return false;
    }
    
    __review_progress = true;
    $('#submit_admin_review').html("Adding Review...");
    
    var i                           = 0;
    var uploadURL                   = admin_url+"course/save_rating_review" ;
    if(user_image != ''){
    var fileObj                     = new processFileName(__uploading_file[i]['name']);
    }
    var param                       = new Array;
        if(user_image != ''){
        param["file_name"]          = fileObj.uniqueFileName();        
        param["extension"]          = fileObj.fileExtension();
        param["file"]               = __uploading_file[i];
        }
        param["user_name"]          = user_name;
        param["user_rating"]        = user_rating;
        param["user_review"]        = user_review;
        param["course_id"]          = __course_id;
    uploadFiles(uploadURL, param, uploadReviewImageCompleted);        
}

 function uploadReviewImageCompleted(response)
{
    var data = $.parseJSON(response);
    $('#review_form_admin').prepend(renderPopUpMessage('success', data['message']));
    scrollToTopOfPage();
    __review_progress = false;
    location.reload();
}

