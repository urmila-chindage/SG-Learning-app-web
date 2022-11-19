function addFacultyToCourse(faculty_id)
    {
        __facultySelected   = new Array();
        __courseSelected    = new Array();
        $('#popUpMessage').remove();
        $('#add-users-course').modal('show');
        $('#course_list_wrapper').html('<div class="checkbox-wrap"><span class="chk-box"><label class="font14">Loading...</label></span></div>');
        $.ajax({
            url: admin_url+'profile/faculty_course',
            type: "POST",
            data:{"is_ajax":true, 'faculty_id' : faculty_id},
            success: function(response) {
                var data        = $.parseJSON(response);
                var courseHtml  = '';
                var checked     = '';
                if( data['courses'].length > 0 )
                {
                    for(var i=0; i<data['courses'].length; i++)
                    {
                        checked     = '';
                        if( inArray(data['courses'][i]['id'], data['faculty_courses']) == true )
                        {
                            checked  = 'checked="checked"';
                            __courseSelected.push(data['courses'][i]['id']);
                        }
                        courseHtml += '<div class="checkbox-wrap" id="user_course_'+data['courses'][i]['id']+'">';
                        courseHtml += '    <span class="chk-box">';
                        courseHtml += '        <label class="font14">';
                        courseHtml += '            <input type="checkbox" '+checked+' class="faculty-course" value="'+data['courses'][i]['id']+'"> '+data['courses'][i]['cb_title'];
                        courseHtml += '        </label>';
                        courseHtml += '    </span>';
                        courseHtml += '    <span class="email-label pull-right">';
                        if( data['courses'][i]['cb_is_free'] == 0 )
                        {
                            courseHtml += '        <span>'+data['courses'][i]['cb_price']+' INR</span>';
                        }
                        else
                        {
                            courseHtml += '        <span>FREE</span>';                        
                        }
                        courseHtml += '    </span>';
                        courseHtml += '</div>';
                    }
                    $('#course_list_wrapper').html(courseHtml);
                    $('#add_user_ok').unbind();
                    $('#add_user_ok').click({"faculty_id": faculty_id}, addFacultyToCourseConfirmed);    
                }
            }
        });
    }
    
    function addFacultyToCourseConfirmed(params)
    {
        $.ajax({
            url: admin_url+'profile/add_faculty_to_course',
            type: "POST",
            data:{"courses":JSON.stringify(__courseSelected), "faculty_id":params.data.faculty_id, "is_ajax":true},
            success: function(response) {
                __facultySelected   = new Array();
                __courseSelected    = new Array();
                var facultyCourses  = '';
                var data = $.parseJSON(response);
                if(typeof data['faculty']['courses'] != 'undefined' && Object.keys(data['faculty']['courses']).length > 0 )
                {
                    $.each(data['faculty']['courses'], function(courseKey, course )
                    {
                        facultyCourses += '<div class="rTableRow" id="course_row_'+course['ct_course_id']+'">';
                        facultyCourses += '    <div class="rTableCell cours-fix ellipsis-hidden no-border no-padding"> ';
                        facultyCourses += '        <div class="ellipsis-style">  ';
                        facultyCourses += '            <span class="icon-rounder"><i class="icon icon-graduation-cap"></i></span>';
                        facultyCourses += '            <a href="javascript:void(0)" class="cust-sm-6 padd0" id="course_title_'+course['ct_course_id']+'">'+course['cb_title']+'</a>';
                        facultyCourses += '        </div>';
                        facultyCourses += '    </div>';
                        facultyCourses += '    <div class="rTableCell pad0 cours-fix no-border"> ';
                        facultyCourses += '        <div class="col-sm-12 pad0"><span class="delete-cover pull-right"><a href="javascript:void(0)" onclick="unassignCourse(\''+course['ct_course_id']+'\');"><i class="icon icon-cancel-1 delte"></i></a></span></div>';
                        facultyCourses += '    </div>';
                        facultyCourses += '</div>';
                    });
                }
                $('#assigned_course_wrapper').html(facultyCourses);
                $('#add-users-course').modal('hide');
            }
        });
    }
    
    $(document).on('change', '.faculty-course', function(){
        if( $(this).prop('checked') == true)
        {
            __courseSelected.push($(this).val());
        }
        else
        {
            removeArrayIndex(__courseSelected, $(this).val());        
        }
    });

    function unassignCourse(courseId)
    {
        $('#confirm_box_title').html(lang('unassign_course')+' <b>'+$('#course_title_'+courseId).text()+'</b>');
        $('#confirm_box_content, #confirm_box_content_1').html('');
        $('#confirm_box_ok').unbind().html(lang('unassign')+'<ripples></ripples>');
        $('#confirm_box_ok').click({"course_id": courseId}, unassignCourseConfirmed);    
        $('#activate_user').modal();
    }
    
    function unassignCourseConfirmed(param)
    {
        $.ajax({
            url: admin_url+'profile/unassign_course',
            type: "POST",
            data:{"is_ajax":true, 'course_id' : param.data.course_id, 'faculty_id' : __facultyId},
            success: function(response) {
                var data        = $.parseJSON(response);
                if(data['error'] == 'false')
                {
                    $('#course_row_'+param.data.course_id).remove();
                    $('#activate_user').modal('hide');
                }
                else
                {
                    $('#activate_user .modal-body').prepend(renderPopUpMessage('error', data['message']));  
                }
            }
        });
    }
    
    function IsmobileNumber(Numbers){
        var IndNum = /^([0|\+[0-9]{1,5})?([7-9][0-9]{9})$/;
        if(IndNum.test(Numbers)){
            return true;
        }else{
            return false;
        }
    }

    function IsLandLineNumber(Numbers)
    {
        var IndNum = /\d{5}([- ]*)\d{6}/g;
        if(IndNum.test(Numbers)){
            return true;
        }else{
            return false;
        }
    }


    function saveFaculty()
    {
        var us_name             = $('#us_name').val();
        var us_email            = $('#us_email').val();
        var us_phone            = $('#us_phone').val();
        var us_degree           = $('#us_degree').val();
        var us_about            = $('#us_about').val();
        var us_native           = $('#us_native').val();
        var faculty_state       = $('#faculty_state').val();
        var us_language_speaks  = $('#us_language_speaks').val();
        var us_badge            = $('#us_badge').val();
        var us_role_id          = $('#us_role_id').val();
        var us_experiance       = $('#us_experiance').val();

        var errorCount         = 0;
        var errorMessage       = '';
        
        if(us_name == '')
        {
            errorCount++;
            errorMessage += 'Enter faculty name<br />';            
        }
        if(us_email == '')
        {
            errorCount++;
            errorMessage += 'Enter a valid email id<br />';
        }
        else
        {
            if(!validateEmail(us_email))
            {
                errorCount++;
                errorMessage += 'Invalid email id<br />';
            }
        }
        if(us_phone == '')
        {
            errorCount++;
            errorMessage += 'Enter faculty mobile number<br />';            
        }
        else
        {
            if((!IsmobileNumber(us_phone) && !IsLandLineNumber(us_phone)) || isNaN(us_phone) == true)
            {
                errorCount++;
                errorMessage += 'Faculty mobile number is invalid<br />';                    
            }
        }
        if(us_degree == '' && us_role_id == 3)
        {
            errorCount++;
            errorMessage += 'Enter faculty qualification<br />';            
        }
        if( us_about == '')
        {
            errorCount++;
            errorMessage += '"About Me" field cannot be empty<br />';            
        }
        
        if(us_experiance == '' && us_role_id == 3)
        {
            errorCount++;
            errorMessage += 'Enter faculty experiance<br />';            
        }
        else
        {
            if(isNaN(us_experiance) == true && us_role_id == 3)
            {
                errorCount++;
                errorMessage += 'Faculty experiance is invalid<br />';                    
            }
        }

        if( us_native == '' && us_role_id == 3)
        {
            errorCount++;
            errorMessage += 'Enter faculty location<br />';            
        }
        else
        {
            if(faculty_state != '' && us_native == '')
            {
                errorCount++;
                errorMessage += 'Enter faculty location<br />';            
            }    
        }
        if( us_language_speaks == '' && us_role_id == 3)
        {
            errorCount++;
            errorMessage += 'Enter faculty language<br />';            
        }
        if( us_badge == '' && us_role_id == 3)
        {
            errorCount++;
            errorMessage += 'Choose faculty badge<br />';            
        }
        
        
        var oldPassword     = $('#old_password').val();
        var password        = $('#password').val();
        var ConfirmPassword = $('#confirm_password').val();
        if((oldPassword+password+ConfirmPassword) != '' )
        {
            if(oldPassword == '')
            {
                errorCount++;
                errorMessage += 'Please enter old password<br />';                            
            }
            if(password+ConfirmPassword == '')
            {
                errorCount++;
                errorMessage += 'Password and Confirm password cannot be empty<br />';                                            
            }
            else
            {
                if(password != ConfirmPassword)
                {
                    errorCount++;
                    errorMessage += 'Password and Confirm-Password missmatch<br />';                            
                }
            }
        }

        /* Youtube url Validation starts here */
        var urlErr =false; 
        $( ".us_youtube_url" ).each(function( index ) {
          //console.log( index + ": " + $( this ).val() );
          if($(this).val() != ''){
            var validateUrl = youtubeUrlValidation($(this).val(),$(this));
            if(!validateUrl){
                urlErr = true;
            }
          }else{
            $(this).removeClass('border-error');
          }
          
        });

        if(urlErr){
            errorCount++;
            errorMessage += 'Enter Valid Youtube URL<br />'; 
        }
     /* Youtube url Validation ends here */   
     
        $('#popUpMessage').remove();
        if(errorCount > 0)
        {
            $('#faculty_form').prepend(renderPopUpMessage('error', errorMessage));  
            scrollToTopOfPage();
        }
        else
        {
            $('#faculty_form').submit();
        }
    }
    
    $(document).ready(function(){
        $('#rating_'+__facultyId).rateYo({
            starWidth: "18px",
            rating: $('#rating_'+__facultyId).attr('data-rate'),
            readOnly: true,
            ratedFill : '#d94d38',
            normalFill : '#000000'
        });
    });
    
    var __uploading_file = new Array();
    $(document).on('change', '#us_image', function(e){
        __uploading_file = e.currentTarget.files;
        if( __uploading_file.length > 1 )
        {
            lauch_common_message('Error Occured', 'You are not allowed to upload more that one file.');
            return false;
        }
        if( __uploading_file[0].size > 1148744 )
        {
            lauch_common_message('Error Occured', 'File size exceeded the limit.');
            return false;
        }
        var i                           = 0;
        var uploadURL                   = admin_url+"profile/upload_user_image" ; 
        var fileObj                     = new processFileName(__uploading_file[i]['name']);
        var param                       = new Array;
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
            param["file"]               = __uploading_file[i];
            param["id"]                 = __facultyId;
        uploadFiles(uploadURL, param, uploadUserImageCompleted);
    });


    function uploadUserImageCompleted(response)
    {
       var data = $.parseJSON(response);
       $('#faculty_image').attr('src', data['user_image'])
    }
    
    
    var __timeOutLanguage   = null;
    var __currentLanguage   = null;
    $(document).on('keyup', '.bootstrap-tagsinput input', function(){
        var keyword         = $(this).val();
        var method          = $(this).parents('.user-taginput-type').attr('id');
        __currentLanguage   = this;
        switch(method)
        {
            case "language_speaks":
                getLanguages(keyword);
            break;
        }
        
    });
    function getLanguages(keyword)
    {
        clearTimeout(__timeOutLanguage);
        __timeOutLanguage = setTimeout(function(){ 
            var url 	= admin_url+'course_settings/get_language_list';
            var tagHTML	= '';
            if( keyword )
            {
                $.ajax({
                        url: url,
                        type: "POST",
                        data:{ 'cb_language':keyword, 'is_ajax':true},
                        success: function (response){
                            var data    = $.parseJSON(response);
                            if( data['tags'].length > 0 ){
                                for( var i = 0; i < data['tags'].length; i++){
                                    tagHTML += '<li id="'+data['tags'][i]['id']+'">'+data['tags'][i]['cl_lang_name']+'</li>';
                                }
                            }
                            $("#listing_language").html(tagHTML).show();       
                        },
                });
            }
        }, 300);
    }


    function youtubeUrlValidation(URL,$this){

        var regExp      = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
        var match       = URL.match(regExp);
        if (URL != undefined || URL != '') {
            if (match && match[7].length==11){
                $this.removeClass('border-error');
                return true;
            }
            else{ 
                $this.addClass('border-error');
                scrollToTopOfPage();
                return false;
            }

        }
    }

    $(document).on('click', '.auto-search-lister-lang li', function(){
        $('#us_language_speaks').tagsinput('add', $(this).text());
        $('.bootstrap-tagsinput input').val('');
        $(__currentLanguage).keypress();
        $(this).parent().html('').hide();
    });
    $(document).on('change', '#faculty_state', function(){
        var state_id = $(this).val();
        $('#us_native').html('<option value="">Loading..</option>');
        $.ajax({
            url: admin_url+'profile/cities',
            type: "POST",
            data:{ 'state_id':state_id, 'is_ajax':true},
            success: function (response){
                var data     = $.parseJSON(response);
                var cityHtml = '<option value="">Choose City</option>';
                if( data['cities'].length > 0 ){
                    for( var i = 0; i < data['cities'].length; i++){
                        cityHtml += '<option value="'+data['cities'][i]['id']+'">'+data['cities'][i]['city_name']+'</option>';
                    }
                }
                $("#us_native").html(cityHtml).show();       
            },
        });

    });