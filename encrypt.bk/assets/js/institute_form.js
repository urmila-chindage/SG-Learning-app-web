function addInstituteToCourse(institute_id)
    {
        __instituteSelected   = new Array();
        __courseSelected    = new Array();
        $('#popUpMessage').remove();
        $('#add-users-course').modal('show');
        $('#course_list_wrapper').html('<div class="checkbox-wrap"><span class="chk-box"><label class="font14">Loading...</label></span></div>');
        $.ajax({
            url: admin_url+'institutes/institute_course',
            type: "POST",
            data:{"is_ajax":true, 'institute_id' : institute_id},
            success: function(response) {
                var data        = $.parseJSON(response);
                var courseHtml  = '';
                var checked     = '';
                if( data['courses'].length > 0 )
                {
                    for(var i=0; i<data['courses'].length; i++)
                    {
                        checked     = '';
                        if( inArray(data['courses'][i]['id'], data['institute_courses']) == true )
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
                    $('#add_user_ok').click({"institute_id": institute_id}, addInstituteToCourseConfirmed);    
                }
            }
        });
    }
    
    function addInstituteToCourseConfirmed(params)
    {
        $.ajax({
            url: admin_url+'institutes/add_institute_to_course',
            type: "POST",
            data:{"courses":JSON.stringify(__courseSelected), "institute_id":params.data.institute_id, "is_ajax":true},
            success: function(response) {
                __instituteSelected   = new Array();
                __courseSelected    = new Array();
                var instituteCourses  = '';
                var data = $.parseJSON(response);
                if(typeof data['institute']['courses'] != 'undefined' && Object.keys(data['institute']['courses']).length > 0 )
                {
                    $.each(data['institute']['courses'], function(courseKey, course )
                    {
                        instituteCourses += '<div class="rTableRow" id="course_row_'+course['ct_course_id']+'">';
                        instituteCourses += '    <div class="rTableCell cours-fix ellipsis-hidden no-border no-padding"> ';
                        instituteCourses += '        <div class="ellipsis-style">  ';
                        instituteCourses += '            <span class="icon-rounder"><i class="icon icon-graduation-cap"></i></span>';
                        instituteCourses += '            <a href="javascript:void(0)" class="cust-sm-6 padd0" id="course_title_'+course['ct_course_id']+'">'+course['cb_title']+'</a>';
                        instituteCourses += '        </div>';
                        instituteCourses += '    </div>';
                        instituteCourses += '    <div class="rTableCell pad0 cours-fix no-border"> ';
                        instituteCourses += '        <div class="col-sm-12 pad0"><span class="delete-cover pull-right"><a href="javascript:void(0)" onclick="unassignCourse(\''+course['ct_course_id']+'\');"><i class="icon icon-cancel-1 delte"></i></a></span></div>';
                        instituteCourses += '    </div>';
                        instituteCourses += '</div>';
                    });
                }
                $('#assigned_course_wrapper').html(instituteCourses);
                $('#add-users-course').modal('hide');
            }
        });
    }
    
    $(document).on('change', '.institute-course', function(){
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
        $('#confirm_box_title').html('Are you sure to '+lang('unassign_course')+' <b>"'+$('#course_title_'+courseId).text()+'"</b>?');
        $('#confirm_box_content, #confirm_box_content_1').html('');
        $('#confirm_box_ok').unbind().html(lang('unassign')+'<ripples></ripples>');
        $('#confirm_box_ok').click({"course_id": courseId}, unassignCourseConfirmed);    
        $('#activate_user').modal();
    }
    
    function unassignCourseConfirmed(param)
    {
        $.ajax({
            url: admin_url+'institutes/unassign_course',
            type: "POST",
            data:{"is_ajax":true, 'course_id' : param.data.course_id, 'institute_id' : __instituteId},
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
    
    function saveInstitute()
    {
        var us_name             = $('#us_name').val();
        var us_institute_code   = $('#us_institute_code').val();
        var us_phone            = $('#us_phone').val();
        var us_about            = $('#us_about').val();
        var us_native           = $('#us_native').val();
        var institute_state     = $('#institute_state').val();
        
        var ib_head_email       = $('#ib_head_email').val();
        var ib_head_phone       = $('#ib_head_phone').val();
        var ib_officer_email    = $('#ib_officer_email').val();
        var ib_officer_phone    = $('#ib_officer_phone').val();
        var ib_class_code       = $('#ib_class_code').val();
        var ib_address          = $('#ib_address').val();
        var ib_class_strength   = $('#ib_class_strength').val();
        var ib_head_name        = $('#ib_head_name').val();
        
        var errorCount         = 0;
        var errorMessage       = '';
        
        if(us_name == '')
        {
            errorCount++;
            errorMessage += 'Enter institute name<br />';            
        }else if(us_name != ''){
            var regex = new RegExp("^(?![0-9]*$)[a-zA-Z0-9 .&]+$");
            if(!(regex.test(us_name))){
                errorCount++;
                errorMessage += 'Enter valid institute name<br />';  
            }
             
        }else{
            if(us_name.length > 50)
            {
                errorCount++;
                errorMessage += 'Institute name length exceed the limit<br />';                    
            }
        }
        
        if(us_institute_code == '')
        {
            errorCount++;
            errorMessage += 'Enter institute code<br />';            
        }
        else
        {
            if(us_institute_code.length > 6)
            {
                errorCount++;
                errorMessage += 'Institute code length exceed the limit<br />';                    
            }
        }
        
        if(us_phone == '')
        {
            errorCount++;
            errorMessage += 'Enter institute contact number<br />';            
        }
        else
        {
            console.log(us_phone.replace(/\s/g,''));
            if(isNaN(us_phone.replace(/\s/g,'')) == true)
            {
                errorCount++;
                errorMessage += 'Institute contact number is invalid<br />';                    
            }
        }
       
        if( us_about == '')
        {
            errorCount++;
            errorMessage += '"About Institution" field cannot be empty<br />';            
        }
        if( ib_class_code == '')
        {
            errorCount++;
            errorMessage += 'Enter Class Room Code<br />';            
        }
        if( ib_address == '')
        {
            errorCount++;
            errorMessage += 'Enter Institute address<br />';            
        }
        if( us_native == '' || institute_state == '')
        {
            errorCount++;
            if(institute_state=='')
            {
                errorMessage += 'Choose institute state<br />';                            
            }
            errorMessage += 'Choose institute district<br />'; 
        }        
               
        if(ib_head_email == '')
        {
            errorCount++;
            errorMessage += 'Enter a valid email id for Institution head<br />';
        }
        else
        {
            if(!validateEmail(ib_head_email))
            {
                errorCount++;
                errorMessage += 'Invalid email id entered for Institution head <br />';
            }
        }
        if( ib_head_name == '')
        {
            errorCount++;
            errorMessage += 'Enter Institute head name<br />';            
        }
        if(ib_head_phone == '')
        {
            errorCount++;
            errorMessage += 'Enter Institution head contact number<br />';            
        }
        else
        {
            if(isNaN(ib_head_phone) == true)
            {
                errorCount++;
                errorMessage += 'Invalid contact number entered for Institution head<br />';                    
            }
        }
        
        if(ib_officer_email != '')       
        {
            if(!validateEmail(ib_officer_email))
            {
                errorCount++;
                errorMessage += 'Invalid email id entered for Officer <br />';
            }
        }
        
        if(ib_officer_phone != '')
        {
            if(isNaN(ib_officer_phone) == true)
            {
                errorCount++;
                errorMessage += 'Invalid contact number entered for Officer<br />';                    
            }
        }
        
        // if(ib_class_strength == '')
        // {
        //     errorCount++;
        //     errorMessage += 'Enter Institute class strength<br />';            
        // }

        $('#popUpMessage').remove();
        if(errorCount > 0)
        {
            $('#institute_form').prepend(renderPopUpMessage('error', errorMessage));  
            scrollToTopOfPage();
        }
        else
        {     
            $('#institute_form').submit();
        }
    }
    
    $(document).ready(function(){
        $('#rating_'+__instituteId).rateYo({
            starWidth: "18px",
            rating: $('#rating_'+__instituteId).attr('data-rate'),
            readOnly: true,
            ratedFill : '#d94d38',
            normalFill : '#000000'
        });
        $('.right-box').slimScroll({
                height: '100%',
                wheelStep : 3,
                distance : '10px'
        });
    });
    
    var __uploading_file = new Array();
    $(document).on('change', '#ib_image', function(e){
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
        var uploadURL                   = admin_url+"institutes/upload_user_image" ; 
        var fileObj                     = new processFileName(__uploading_file[i]['name']);
        var param                       = new Array;
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
            param["file"]               = __uploading_file[i];
            param["id"]                 = __instituteId;
        uploadFiles(uploadURL, param, uploadUserImageCompleted);
    });


    function uploadUserImageCompleted(response)
    {
       var data = $.parseJSON(response);
       $('#institute_image').attr('src', data['user_image']);
       if(data['error'] == 'true')
       {
            lauch_common_message('Error', data['error_msg']);
       }
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
            case "category_id":
                getCategories(keyword);
            break;
            case "expertise_id":
                getExpertises(keyword);
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
    
    function getCategories(keyword)
    {
        clearTimeout(__timeOutLanguage);
        __timeOutLanguage = setTimeout(function(){ 
            var url 	= admin_url+'course_settings/get_category_list';
            var tagHTML	= '';
            if( keyword )
            {
                $("#listing_categories").html('<li>Loading..</li>').show();       
                $.ajax({
                        url: url,
                        type: "POST",
                        data:{ 'cb_category':keyword, 'is_ajax':true},
                        success: function (response){
                            var data    = $.parseJSON(response);
                            if( data['tags'].length > 0 ){
                                for( var i = 0; i < data['tags'].length; i++){
                                    tagHTML += '<li id="'+data['tags'][i]['id']+'">'+data['tags'][i]['ct_name']+'</li>';
                                }
                            }
                            $("#listing_categories").html(tagHTML).show();       
                        },
                });
            }
        }, 300);
    }
    
    function getExpertises(keyword)
    {
        clearTimeout(__timeOutLanguage);
        __timeOutLanguage = setTimeout(function(){ 
            var url 	= admin_url+'institutes/get_expertise_list';
            var tagHTML	= '';
            if( keyword )
            {
                $("#listing_expertise").html('<li>Loading..</li>').show();       
                $.ajax({
                        url: url,
                        type: "POST",
                        data:{ 'expertise_keyword':keyword, 'is_ajax':true},
                        success: function (response){
                            var data    = $.parseJSON(response);
                            if( data['tags'].length > 0 ){
                                for( var i = 0; i < data['tags'].length; i++){
                                    tagHTML += '<li id="'+data['tags'][i]['id']+'">'+data['tags'][i]['fe_title']+'</li>';
                                }
                            }
                            $("#listing_expertise").html(tagHTML).show();       
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
    
    $(document).on('click', '.auto-search-lister-category li', function(){
        $('#us_category_id').tagsinput('add', $(this).text());
        $('.bootstrap-tagsinput input').val('');
        $(__currentLanguage).keypress();
        $(this).parent().html('').hide();
    });
    
    $(document).on('click', '.auto-search-lister-expertise li', function(){
        $('#us_expertise').tagsinput('add', $(this).text());
        $('.bootstrap-tagsinput input').val('');
        $(__currentLanguage).keypress();
        $(this).parent().html('').hide();
    });
    
    $(document).on('change', '#institute_state', function(){
        var state_id = $(this).val();
        if(state_id == '')
        {
            $('#us_native').html('');            
        }
        else
        {
            $('#us_native').html('<option value="">Loading..</option>');
            $.ajax({
                url: admin_url+'institutes/cities',
                type: "POST",
                data:{ 'state_id':state_id, 'is_ajax':true},
                success: function (response){
                    var data     = $.parseJSON(response);
                    var cityHtml = '<option value="">Choose District</option>';
                    if( data['cities'].length > 0 ){
                        for( var i = 0; i < data['cities'].length; i++){
                            cityHtml += '<option value="'+data['cities'][i]['id']+'">'+data['cities'][i]['city_name']+'</option>';
                        }
                    }
                    $("#us_native").html(cityHtml).show();       
                },
            });
            
        }

    });
    function preventSpecialCharector(e)
    {
        console.log(e);
       var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57||k == 46||k==38));
    }