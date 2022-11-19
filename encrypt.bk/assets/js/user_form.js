
    function saveFaculty()
    {
        var us_name             = $('#us_name').val();
        //var us_email            = $('#us_email').val();
        var us_phone            = $('#us_phone').val();
        var us_degree           = $('#us_degree').val();
        var us_about            = $('#us_about').val();
        
        var errorCount         = 0;
        var errorMessage       = '';
        
        if(us_name == '')
        {
            errorCount++;
            errorMessage += 'Enter student name<br />';            
        }else if(us_name != ''){
            var regex = new RegExp("^(?![0-9]*$)[a-zA-Z0-9 ]+$");
            if(!(regex.test(us_name))){
                errorCount++;
                errorMessage += 'Enter valid student name<br />';  
            }
             
        }else{
            if(us_name.length > 50)
            {
                errorCount++;
                errorMessage += 'Student name length exceed the limit<br />';                    
            }
        }
        /*if(us_email == '')
        {
            errorCount++;
            errorMessage += 'Enter a valid email address<br />';
        }
        else
        {
            if(!validateEmail(us_email))
            {
                errorCount++;
                errorMessage += 'Invalid email id<br />';
            }
        }*/
        if(us_phone == '')
        {
            errorCount++;
            errorMessage += 'Enter student contact number<br />';            
        }
        else
        {
            if(isNaN(us_phone) == true)
            {
                errorCount++;
                errorMessage += 'Student contact number is invalid<br />';                    
            }
        }
        
        if( us_about == '')
        {
            errorCount++;
            errorMessage += '"About Me" field cannot be empty<br />';            
        }
        
        
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
        var uploadURL                   = admin_url+"user/upload_user_image" ; 
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
