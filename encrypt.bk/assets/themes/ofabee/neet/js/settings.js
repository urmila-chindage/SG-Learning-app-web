$(document).ready(function() {
    $(".nav-left-ul li").on("click", function() {
        $(".nav-left-ul li").removeClass("active-dashbord");
        $(this).addClass("active-dashbord");
    });
    $(".dpdropbtn").on("click", function() {
        $(this).next().stop().slideToggle("fast");
        return false;
    });

    $(document).on("click", function() {
        $(".dpdropbtn").next().stop().slideUp("fast");
    });
    $(".neetrefresh-drops").on("click", function(e) {
        e.stopPropagation();
    });
    $('[data-toggle="tooltip"]').tooltip();

    __birthDetails          = $.parseJSON(__birthDetails);
    __months                = $.parseJSON(__months);
});

function liAnimate(e){
    /*var elem = $(e).next().find('li.active');
    $(e).next().scrollTop(0);
    $(e).next().scrollTop($(elem).position().top); */
    /*var $s = $(e);

    setTimeout(function() {
        var optionTop = $s.next().find('li.active').offset().top;
        var selectTop = $s.next().offset().top;
        //$s.next().scrollTop($s.next().scrollTop() + (optionTop - selectTop));
        $($s.next()).animate({ scrollTop: (optionTop - selectTop) }, 'slow', function () {
        });
    }, 200);*/
}
var __uploading_file = new Array();
$("#file_upload").on("change", function(f)
{
    __uploading_file = f.currentTarget.files;
    if (__uploading_file.length > 1)
    {
        alert('Error Occured - File count exceeded the limit.');
        return false;
    }
    if (__uploading_file[0].size > 1148744)
    {
        alert('Error Occured - File size exceeded the limit.');
        return false;
    }

    var files = !!this.files ? this.files : [];
    if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

    if (/^image/.test( files[0].type)){ // only image file
        var reader = new FileReader(); // instance of the FileReader
        reader.onloadend = function(e){ // set image data as background of div
            $('#pro_pic').attr('src',__assets_url+'/neet/progress.gif');
        }
        reader.readAsDataURL(files[0]);
        saveImage();
    }
});

function saveImage(){
    var i = 0;
    var uploadURL = __site_url + "dashboard/upload_user_image";
    var fileObj = new processFileName(__uploading_file[i]['name']);
    var param = new Array;
    param["file_name"] = fileObj.uniqueFileName();
    param["extension"] = fileObj.fileExtension();
    param["file"] = __uploading_file[i];
    uploadFiles(uploadURL, param, uploadUserImageCompleted);
}

function uploadUserImageCompleted(response)
{
    var data = $.parseJSON(response);
    $('#pro_pic').attr('src', data['user_image']);
    $('.dpimgae').attr('src', data['user_image']);
}

function daysInmonth(){
    var year = __birthDetails['my_year'];
    var month = __birthDetails['my_month'];
    var days = Math.round(((new Date(year, month))-(new Date(year, month-1)))/86400000);

    return days;
}

function saveProfile(){
    var formData        = new FormData();
    var fname           = $('#first_name').val();
    var lname           = $('#last_name').val();
    var phone           = $('#Number').val();
    var bmonth          = __birthDetails['my_month'];
    var bday            = __birthDetails['my_day'];
    var byear           = __birthDetails['my_year'];
    var emailNotifiy    = $('#myonoffswitch').is(":checked")?1:0;
    if(fname == ''){
        alert('First name is mandatory.');
        return false;
    }

    if(phone == ''){
        alert('Phone number is mandatory.');
        return false;
    }

    if(!IsmobileNumber(phone)){
        alert('Phone number is invalid.');
        return false;
    }

    if(!((bmonth == 0&&bday == 0&&byear == 0)||(bmonth != 0&&bday != 0&&byear != 0))){
        alert('Please choose valid birth details.');
        return false;
    }

    formData.append('fname',fname);
    formData.append('lname',lname);
    formData.append('phone',phone);
    formData.append('bmonth',bmonth);
    formData.append('bday',bday);
    formData.append('byear',byear);
    formData.append('notification',emailNotifiy);
    $('#save_button').val('Saving...');
    $.ajax({
        url: __site_url+'dashboard/save_settings',
        type: "POST",
        data:formData,cache: false,
        processData: false,  
        contentType: false,
        success: function(response){
            var data  = $.parseJSON(response);
            if(data['success'] == true)
            {
                $('#save_button').val('Save');
            }else{
                $('#save_button').val('Save');
                alert('Error Occured -'+data['message']);
            }
        }
    });
}

function changeMonth(eve){
    var monthChoosen    = $(eve).val();
    var monthLabel      = (monthChoosen!=0)?__months[monthChoosen]:'mm';
    $('#month_button').next().children().removeClass("active");
    $(eve).addClass('active');
    $('#month_button').find(':first-child').remove();
    $('#month_button').prepend('<span>'+monthLabel+'</span>');
    __birthDetails['my_month'] = monthChoosen;
    $('#day_button').next().html(renderDays(daysInmonth()));
}

function renderDays(daycount){
    var html            = '';
    var clas            = '';
    for(i=0;i<=daycount;i++){
        clas            = '';
        if(__birthDetails['my_day'] == i){
            clas        = 'active';
        }
        if(i == 0){
            html += '<li class="'+clas+'" role="presentation" onclick="changeDay(this)" value="'+i+'">';
            html += '<a role="menuitem" tabindex="-1" href="javascript:void(0)">dd</a></li>';
        }else{
            html += '<li class="'+clas+'" role="presentation" onclick="changeDay(this)" value="'+i+'">';
            html += '<a role="menuitem" tabindex="-1" href="javascript:void(0)">'+i+'</a></li>';
        }
    }

    return html;
}

function changeDay(eve){
    var dayChoosen      = $(eve).val();
    $('#day_button').next().children().removeClass("active");
    $(eve).addClass('active');
    $('#day_button').find(':first-child').remove();
    __birthDetails['my_day'] = dayChoosen;
    if(dayChoosen == 0){
       $('#day_button').prepend('<span>dd</span>'); 
    }else{
        $('#day_button').prepend('<span>'+dayChoosen+'</span>'); 
    }
    $('#day_button').next().html(renderDays(daysInmonth()));
}

function changeYear(eve){
    var yearChoosen      = $(eve).val();
    $('#year_button').next().children().removeClass("active");
    $(eve).addClass('active');
    $('#year_button').find(':first-child').remove();
    __birthDetails['my_year'] = yearChoosen;
    if(yearChoosen == 0){
       $('#year_button').prepend('<span>yyyy</span>');
    }else{
        $('#year_button').prepend('<span>'+yearChoosen+'</span>'); 
    }
}

function IsmobileNumber(number){
    var Numbers = number;
    var IndNum = /^([0|\+[0-9]{1,5})?([7-9][0-9]{9})$/;
    if(IndNum.test(Numbers)){
        return true;
    }else{
        return false;
    }
}

function markAllAsRead()
{
    $('.clear_text').html('Clearing..');
    $.ajax({
        url: __admin_url+'site_notification/clear_message',
        type: "POST",
        data:{ "is_ajax":true,},
        success: function(response) {
            var notificationHtml = '';
                notificationHtml += '<li>';
                notificationHtml += '    <a href="javascript:void(0)">';
                notificationHtml += '        <div class="neetfresh-bullets-content">';
                notificationHtml += '            <h1 class="neetfresh-li-title"></h1>';
                notificationHtml += '            <div class="neetfresh-li-para">No notification to show</div>';
                notificationHtml += '        </div>';
                notificationHtml += '    </a>';
                notificationHtml += '</li>';
            $('.neetrefresh-ul').html(notificationHtml);
            $('.cleardropsbtn, .site_notification_count').remove();
        }
    });
}

 $('#new_pswd,#confirm_new_pswd,#current_pswd').bind("cut copy paste",function(e) {
     e.preventDefault();
 });

function resetPassword(){
    var cpassword           = $('#current_pswd').val();
    var npassword           = $('#new_pswd').val();
    var cnpassword          = $('#confirm_new_pswd').val();

    if(cpassword == ''){
        alert('Current password field cannot be empty.');
        return false;
    }

    if(npassword == ''){
        alert('New password field cannot be empty.');
        return false;
    }

    if(cnpassword == ''){
        alert('New password field cannot be empty.');
        return false;
    }

    if(npassword.length<6){
        alert('New password length must be greater than 6.');
        return false;
    }

    if(npassword != cnpassword){
        alert('New password field doesnot match confirm new password.');
        return false;
    }

    var formData        = new FormData();

    formData.append('current_pass',cpassword);
    formData.append('new_password',npassword);
    formData.append('confirm_pass',cnpassword);
    $('#reset_password').val('Resetting...');
    $.ajax({
        url: __site_url+'dashboard/change_password',
        type: "POST",
        data:formData,
        cache: false,
        processData: false,  
        contentType: false,
        success: function(response){
            var data  = $.parseJSON(response);
            if(data['success'] == true)
            {
                $('#current_pswd').val('');
                $('#new_pswd').val('');
                $('#confirm_new_pswd').val('');
                $('#reset_password').val('Reset Password');
                alert('Password reset success.');
            }else{
                $('#reset_password').val('Reset Password');
                alert('Error Occured -'+data['message']);
            }
        }
    });
}