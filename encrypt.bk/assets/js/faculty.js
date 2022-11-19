//var __facultyCount  = 1;
var __activeFaculty     = 0;
var __facultySelected   = new Array();
var __courseSelected    = new Array();
var __roleDescription   = new Object();

    __roleDescription[1] = 'Sub admin can do all the functionalities done by the Super Admin.';
    __roleDescription[3] = 'Tutor can manage contents and students of their assigned courses. Also they can create their own courses.';
    __roleDescription[4] = 'Content editor can manage courses with limited features and manage contents';
function renderFacultiesHtml(faculties)
{
    var facultiesHtml  = '';
    if(Object.keys(faculties).length > 0 )
    {
        $.each(faculties, function(facultyKey, faculty )
        {
            $('#faculty_'+faculty['id']).remove();
            facultiesHtml += '<div class="rTableRow settings-table" data-faculty-id="'+faculty['id']+'" id="faculty_'+faculty['id']+'">';
            facultiesHtml += renderFacultyHtml(faculty);
            facultiesHtml += '</div>';
            __activeFaculty = (__activeFaculty == 0)?faculty['id']:__activeFaculty;
        });
    }
    return facultiesHtml;
}

function getFacultyObjectDetail(faculty_id)
{
    var _faculty = {};
    if(Object.keys(__facultyObject).length > 0)
    { 
        $.each(__facultyObject, function(key, faculty )
        {
            if(faculty['id'] == faculty_id)
            {
                _faculty = faculty;
                return;
            }
        });    
    }
    return _faculty;
}

function getFacultyObjectIndex(faculty_id)
{
    if(Object.keys(__facultyObject).length > 0)
    {
        $.each(__facultyObject, function(key, faculty )
        {
            if(faculty['id'] == faculty_id)
            {
                index = key;
                return;
            }
        });    
    }
    return index;
}

function addFacultyForm()
{
    cleanPopUpMessage();
    $('#faculty_name').val('');
    $('#faculty_email').val('');
    $('#faculty_password').val('');
    $('#faculty_role').val('');
    $('#faculty_institute').val('');
    $('#institute_select').attr('style','display:none;');
    $('#send_mail').prop('checked',false);
    $('#role_funcationlity_details').html('');
    $('#create_faculty').modal();
}

function addFaculty()
{
    var facultyName        = $('#faculty_name').val();
    var facultyEmail       = $('#faculty_email').val();
    var facultyPassword    = $('#faculty_password').val();
    var facultyRole        = $('#faculty_role').val();
    var sendMail           = $('#send_mail').prop('checked');
        sendMail           = (sendMail==true)?'1':'0';
    var errorCount         = 0;
    var errorMessage       = '';
    var facultyInstitute   = '';
    
    if(facultyName == '')
    {
        errorCount++;
        errorMessage += 'Enter faculty name<br />';            
    }
    else if(facultyName != ''){
        var regex = new RegExp("^(?![0-9]*$)[a-zA-Z0-9 &-.]+$");
        if(!(regex.test(facultyName)))
        {
            errorCount++;
            errorMessage += 'Enter valid faculty name <br />';  
        }
         
    }
    else{
        if(facultyName.length > 50)
        {
            errorCount++;
            errorMessage += 'Faculty name length exceed the limit<br />';                    
        }
    }
    if(facultyEmail == '')
    {
        errorCount++;
        errorMessage += 'Enter a valid email id<br />';
    }
    else
    {
        if(!validateEmail(facultyEmail))
        {
            errorCount++;
            errorMessage += 'Invalid email id<br />';
        }
    }
    if(facultyPassword == '')
    {
        errorCount++;
        errorMessage += 'Enter faculty password<br />';            
    }
    else
    {
        if(facultyPassword.length < 6)
        {
            errorCount++;
            errorMessage += 'Password must be atleast 6 characters<br />';                    
        }
    }
    if(facultyRole == '')
    {
        errorCount++;
        errorMessage += 'Choose faculty role<br />';            
    }
    if(facultyRole == '8')
    {
        facultyInstitute = $('#faculty_institute').val();
        if(facultyInstitute == ''){
            errorCount++;
            errorMessage += 'Choose faculty institute<br />'; 
        }
    }
    $('#popUpMessage').remove();
    if(errorCount > 0)
    {
        $('#create_faculty .modal-body').prepend(renderPopUpMessage('error', errorMessage));  
    }
    else
    {
        $.ajax({
            url: admin_url+'faculties/create_faculty',
            type: "POST",
            data:{"faculty_name":facultyName, "faculty_email":facultyEmail, "faculty_password":facultyPassword, "faculty_role":facultyRole, "faculty_institute":facultyInstitute, 'send_mail':sendMail, "is_ajax":true},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    window.location = admin_url+'faculties';
                }
                else
                {
                    $('#create_faculty .modal-body').prepend(renderPopUpMessage('error', data['message']));  
                }
            }
        });
    }
}

var __uploaded_files    = '';
$(document).on('change', '#import_faculty', function(e){
  $('#percentage_bar').hide();
  __uploaded_files                = e.currentTarget.files[0];
  __uploaded_files['extension']   = __uploaded_files['name'].split('.').pop();
  $('#upload_faculty_file').val(__uploaded_files['name']);
});

$(document).on('click', '#bulk_import_faculties', function(){
    $('#faculty_role_bulk').val('');
});
function uploadFaculty()
{
    var facultyRole        = $('#faculty_role_bulk').val();
    if(facultyRole == '')
    {
        lauch_common_message('Choose Faculties Role', 'Please choose faculties role before upload.');
        return false; 
    }
    if(__uploaded_files=='')
    {
        lauch_common_message('File missing', 'Please choose file to upload.');
        return false;
    }
    
    var filename = __uploaded_files['name'];
    var valid_extensions = /(\.csv)$/i;
    if(valid_extensions.test(filename))
    {
        $('#percentage_bar').show();
        var uploadURL                   = admin_url+'faculties/import_faculties'
        var fileObj                     = new processFileName(__uploaded_files['name']);
        var param                       = new Array;
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
            param["file"]               = __uploaded_files;
            param['role_id']            = facultyRole;
        uploadFiles(uploadURL, param, uploadUserCompleted); 
    }
    else
    {
       lauch_common_message('Invalid File', 'Choose .csv file to upload');
       return false;
    }
}

function uploadUserCompleted(response)
{
    var response = $.parseJSON(response);
    __uploading_file = '';
    switch(response.status)
    {
        case 1:
            $('#import-faculties .modal-body').prepend(renderPopUpMessage('success', response.message));
            setTimeout(function(){window.location.reload();}, 2500);
        break;
        case 2:
            $('#import-faculties .modal-body').prepend(renderPopUpMessage('error', response.message));
            setTimeout(function(){
                location.href = response['redirect_url'];
            }, 500) ;
            /*$(window).on('beforeunload', function(){
                return 'Please allow system to refresh the page inorder to get inserted contents.';
            });*/              
        break;
        case 3:
            $('#import-faculties .modal-body').prepend(renderPopUpMessage('error', response.message));
        break;
    }
    $('#percentage_bar').hide();
    scrollToTopOfPage();
}

$(document).on('hidden.bs.modal', '#import-faculties', function(){
    $(this).find("input").val('').end();
    $('#percentage_bar').hide();
    $('#popUpMessage').remove();
});

function loadMoreFaculties()
{
    loadFaculties();
}

var __filter_dropdown = 'active';
function filter_faculties_by(filter)
{  
    __activeFaculty      = 0;
    __offset             = 1;
    __filter_dropdown    = filter;
    $('#filter_dropdown_text').html($('#filer_dropdown_list_'+filter).text()+'<span class="caret"></span>');
    loadFaculties();
    scrollToTopOfPage();
    if(__filter_dropdown == "deleted")
    {
        $('#select_all').hide();
    }else{
        $('#select_all').show();
    }
}

function loadFaculties()
{
    $('#loadmorebutton').html('Loading..');
    var keyword  = $('#faculty_keyword').val();
    var faculty_type = $('#dropdown_role_text').attr('data-role-id');
    var currentFaculties   = __facultyObject;
    $.ajax({
        url: admin_url+'faculties/faculties_json',
        type: "POST",
        data:{"is_ajax":true, "keyword":keyword,"role_id":faculty_type ,'limit':__limit,'offset':__offset, 'filter':__filter_dropdown},
        success: function(response) {
            var data = $.parseJSON(response);
            var remaining = 0;
            $('#loadmorebutton').hide();
            if(data['total_faculties'] > 0){
                for( var faculty in data['faculties'])
                {
                    currentFaculties.push(data['faculties'][faculty]);
                }
                __facultyObject = currentFaculties;
                __activeFaculty = 0;

                 __offset++;
                if(__offset == 2)
                {
                    remaining = (data['total_faculties'] - Object.keys(data['faculties']).length);
                    
                    scrollToTopOfPage();
                    clearCache();
                    $('#faculty_wrapper').html(renderFacultiesHtml(data['faculties']));
                    loadFacultyDetail(data['faculties'][0]['id']);
                }
                else
                {
                    remaining = (data['total_faculties'] - (((__offset-2)*data['limit'])+Object.keys(data['faculties']).length));
                    $('.faculty-checkbox-parent').prop('checked', false);
                    $('#faculty_wrapper').append(renderFacultiesHtml(data['faculties']));                     
                }
            }
            else
            {
                clearCache();
                $('#faculty_detail_wrapper').html('');
                $('#faculty_wrapper').html(renderPopUpMessagePage('error', 'No Faculties found.'));
                $('#popUpMessagePage .close').css('display', 'none');
            }
            if(data['show_load_button'] == true)
            {
                $('#loadmorebutton').show();
            }
            remaining = (remaining>0)?'('+remaining+')':'';
            $('#loadmorebutton').html('Load More '+remaining+'<ripples></ripples>');
        }
    });
    
}

function preventSpecialCharector(e)
{
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}

$(document).on('change', '#faculty_role', function(){
    var faculty_role = $(this).val();
    $('#role_funcationlity_details').html('');
    if(typeof __roleDescription[faculty_role] != 'undefined')
    {
        $('#role_funcationlity_details').html(__roleDescription[faculty_role] );
    }
    if(faculty_role == '8'){
        $.ajax({
            url: admin_url+'faculties/get_institutes',
            type: "POST",
            data:{"is_ajax":true},
            success: function(response) {
                var data  = $.parseJSON(response);
                var instituteHtml = "";
                if( data['institutes'].length > 0 ){
                    instituteHtml   += '<option value="">Choose Institute</option>';
                    for(let i in data['institutes'])
                    {
                        instituteHtml   += '<option value="'+ data['institutes'][i]['id'] +'">'+ data['institutes'][i]['ib_institute_code'] +' - '+ data['institutes'][i]['ib_name'] +'</option>';
                    }
                }
                $('#faculty_institute').html(instituteHtml);
                $('#institute_select').attr('style','');
            }
        });
    }
    else{
        $('#faculty_institute').html('');
        $('#institute_select').attr('style','display:none;');
    }
});

var __searchTimeOut = '';
$(document).on('keyup', '#faculty_keyword', function(){
    clearTimeout(__searchTimeOut);
     __searchTimeOut = setTimeout(function(){
        __activeFaculty = 0;
        __offset = 1;
        loadFaculties();
     }, 300);
});
$(document).on('click', '#searchclear', function(){
    __activeFaculty = 0;
    __offset = 1;
    loadFaculties();
});

$(document).on('click', '#faculty_search', function(){
    var faculty_keyword = $('#faculty_keyword').val().trim();        
    if(faculty_keyword == '')
    {
        lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
    }
    else{
        __activeFaculty = 0;
        __offset = 1;
        loadFaculties();
    }
});

function filter_faculty(faculty_type)
{
    clearCache();
    //__facultyCount      = 1;
    __activeFaculty     = 0;
    __offset            = 1;
    var keyword         = $('#faculty_keyword').val();
        keyword         = keyword.toLowerCase();
    if(faculty_type == 0)
    {
        $('#dropdown_role_text').html('All Roles <span class="caret"></span>');
        $('#dropdown_role_text').attr('data-role-id', 0);
        $('#faculty_keyword').val('');
    }
    else
    {
        $('#dropdown_role_text').html($('#dropdown_list_'+faculty_type).text()+' <span class="caret"></span>');
        $('#dropdown_role_text').attr('data-role-id', faculty_type);
    }
    loadFaculties();        
}

function renderFacultyHtml(faculty)
{
    var user_img        = '';    
    var facultyHtml     = '';
    var facultyBgColor  = '';

    //consider the record is deleted and set the value if record deleted
    var action_class   = 'label-danger';
    var item_deleted   = 'item-deleted';
    var action         = 'Deleted';
    var disabled       = 'disabled="disabled"';
    var visibility     = '';
    if(__facultySelected.length > 1)
    {
        visibility = 'style="visibility:hidden;"';
    }
    //case if record is not deleted
    if(faculty['us_deleted'] == 0)
    {
        item_deleted = '';
        
        if(faculty['us_status'] == 1)
        {
            action_class   = 'label-success';                                                                
            action         = 'active';
        }
        else
        {
            action_class   = 'label-warning';                                                                
            action         = 'inactive';
        }
    }
    
    facultyHtml += '';
    facultyHtml += '    <div class="rTableCell"> ';
    facultyHtml += '        <input type="checkbox" class="faculty-checkbox" id="faculty_checkbox_'+faculty['id']+'" value="'+faculty['id']+'" '+((faculty['us_deleted'] == 1)?disabled:'')+'> ';
    //facultyHtml += '        <span class="font-bold">'+(__facultyCount++)+'</span>';
    facultyHtml += '        <span class="icon-wrap-round img" onclick="loadFacultyDetail(\''+faculty['id']+'\')">';
    user_img     = ((faculty['us_image'] == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path')); 
    facultyHtml += '            <img src="'+user_img+''+faculty['us_image']+'">';                
    facultyHtml += '        </span>';
    facultyHtml += '        <span class="wrap-mail ellipsis-hidden"> ';
    facultyHtml += '            <span class="ellipsis-style"><a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="'+faculty['us_name'] +'" class="faculty-link" onclick="loadFacultyDetail(\''+faculty['id']+'\')">'+((faculty['us_name'].length > 33)?(faculty['us_name'].substr(0, 30)+'...'):faculty['us_name'])+'</a></span>';
    facultyHtml += '        </span>';
    facultyHtml += '        <span class="normal-base-color"> ';
    facultyHtml += '            <span class="badge '+facultyBgColor+' group-total">'+faculty['rl_name']+'</span>';
    facultyHtml += '        </span>';
    facultyHtml += '    </div>';
    facultyHtml += '    <div class="rTableCell pad0">';
    facultyHtml += '        <div class="col-sm-12 pad0"><label class="pull-right label '+action_class+'">'+action+'</label></div>';
    facultyHtml += '    </div>';
    facultyHtml += '    <div class="td-dropdown rTableCell">';

    facultyHtml += '        <div class="btn-group lecture-control" '+visibility+'>';
    facultyHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
    facultyHtml += '                <span class="label-text">';
    facultyHtml += '                    <i class="icon icon-down-arrow"></i>';
    facultyHtml += '                </span>';
    facultyHtml += '                <span class="tilder"></span>';
    facultyHtml += '            </span>';
    facultyHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="user_action_'+faculty['id']+'">';
        if(faculty['us_deleted'] == 0){
            facultyHtml += '<li id="status_btn_'+faculty['id']+'">';
            var cb_status = (faculty['us_status']==1)?'deactivate':'activate'; 
            var cb_action = cb_status; 
            if(__permissions.indexOf('3') >= 0)
            {
                facultyHtml += '<li>';
                facultyHtml += '    <a href="'+admin_url+'faculties/faculty/'+faculty['id']+'" data-toggle="modal">'+lang('edit')+'</a>';
                facultyHtml += '</li>';
            }
            if(__permissions.indexOf('1') >= 0)
            {
                facultyHtml += '<li>';
                facultyHtml += '     <a href="javascript:void(0);" onclick="sendMessageToFaculty(\''+faculty['id']+'\')">'+lang('send_message')+'</a>';
                facultyHtml += '</li>';
            }
            if(__permissions.indexOf('3') >= 0)
            {
                facultyHtml += '<li>';
                facultyHtml += '     <a href="javascript:void(0);" onclick="resetPassword(\''+faculty['id']+'\')">'+lang('reset_password')+'</a>';
                facultyHtml += '</li>';
                // if(faculty['us_role_id'] != 1 && faculty['us_role_id'] != 7 )
                // {
                //     facultyHtml += '<li>';
                //     facultyHtml += '    <a href="javascript:void(0);" onclick="addFacultyToCourse(\''+faculty['id']+'\')">'+lang('add_to_course')+'</a>';
                //     facultyHtml += '</li>';
                // }
                facultyHtml += '<li id="status_btn_'+faculty['id']+'">';
                facultyHtml += '        <a href="javascript:void(0);" onclick="changeUserStatus(\''+faculty['id']+'\')">'+lang('account')+' '+lang(cb_status)+'</a>';
                facultyHtml += '</li>';
            }
            if(__permissions.indexOf('4') >= 0)
            {
                facultyHtml += '<li>';
                facultyHtml += '       <a href="javascript:void(0);" id="delete_btn_'+faculty['id']+'" onclick="deleteFaculty(\''+ faculty['id']+'\')">'+lang('delete_account')+'</a>';
                facultyHtml += '</li>';
            }
        }
        else
        {
            if(__permissions.indexOf('4') >= 0)
            {
                facultyHtml += '<li>';
                facultyHtml += '    <a href="javascript:void(0);" id="restore_btn_'+faculty['id']+'" onclick="restoreFaculty(\''+faculty['id']+'\')" >'+lang('restore')+'</a>';
                facultyHtml += '</li>';
            }
        }
    facultyHtml += '           </ul>';
    facultyHtml += '        </div>';
    facultyHtml += '    </div>';
    facultyHtml += '    <div class="rTableCell pos-rel active-faculty-custom">';
    facultyHtml += '        <span class="active-arrow"></span>';
    facultyHtml += '    </div>';
    return facultyHtml;
}

function loadFacultyDetail(faculty_id)
{
    if((faculty_id > 0) == false)
    {
        return false;
    }
    var faculty             = getFacultyObjectDetail(faculty_id);
    // var faculty             = __facultyObject[faculty_id];
    var user_img            = ((faculty['us_image'] == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path')); 
    var facultyDetailHtml   = '';
    
    $('#faculty_detail_wrapper').html('<div class="row center-block"><h2>Loading...</h2></div>');
    $('.active-faculty-custom').removeClass('active-table');
    $('#faculty_'+faculty_id+' .active-faculty-custom').addClass('active-table');
        facultyDetailHtml += '<div class="row pattern-bg">';
        facultyDetailHtml += '    <div class="faculty-img pull-left" style="width:120px"> ';
        facultyDetailHtml += '<div class="" style="padding: 20px 0 15px 30px;"><img width="60" class="img-circle img-responsive" src="'+user_img+''+faculty['us_image']+'"></div>';       
        if(faculty['us_role_id'] == 3)
        {
            facultyDetailHtml += '<a target="_blank" class="" href="'+__site_url+'teachers/view/'+faculty_id+'" style="padding: 3px 20px;display:  inline-block;color: #0781d7;">View Profile</a>';
        }
        facultyDetailHtml += '</div>'; 
        facultyDetailHtml += '    <div class="faculty-info pull-left">   ';
        if(faculty['us_role_id'] != 8){
                
            if(__assign_faculty.view()==true && __assign_faculty.add()==true){
                facultyDetailHtml += '<div class="no-padding" style="position: absolute;right: 20px;top: 34px;">';
                facultyDetailHtml += '<input type="button" class="pull-right btn btn-green marg10 selected" onclick="addFacultyToCourse('+faculty_id+')" value="ADD TO COURSE"></input>';
                facultyDetailHtml += '</div>';
            }
        }
        facultyDetailHtml += '        <span class="center-block faculty-name"><h1>';            
        facultyDetailHtml += '          <a href="javascript:void(0)">'+faculty['us_name']+'</a>';            
        facultyDetailHtml += '          </h1></span>';
        facultyDetailHtml += '        <span class="center-block wrap faculty-qualification">';
        if(faculty['us_degree'] != null && faculty['us_degree'] != '')
        {
            facultyDetailHtml += faculty['us_degree'];
        }
        else
        {
            facultyDetailHtml += faculty['rl_name'];
        }
        facultyDetailHtml += '          <span class="public-profile-view">';            
        facultyDetailHtml += '          </span>';
        facultyDetailHtml += '        </span>';
        
        if((faculty['us_experiance'] != null && faculty['us_experiance'] != '' && faculty['us_experiance'] > 0) || (typeof faculty['rating'] != 'undefined' && faculty['rating'] != null && faculty['rating'] != '' && faculty['rating'] > 0) )
        {
            facultyDetailHtml += '        <span class="center-block line"></span>';
        }
        if(faculty['us_experiance'] != null && faculty['us_experiance'] != '' && faculty['us_experiance'] > 0 )
        {
            var experianceHtml  = '0';
            var years           = Math.floor(faculty['us_experiance'] / 12); // 1
            var remainingMonths = Math.floor(faculty['us_experiance'] % 12); // 6
            if(years > 0 )
            {
                experianceHtml = years+'';
            }
            if(remainingMonths > 0 )
            {
                experianceHtml += '.'+remainingMonths;
            }
            facultyDetailHtml += '        <span class="teach-exp pull-right">Experience: <span class="font-bold">'+experianceHtml+' yrs</span>';
            
        }            
        facultyDetailHtml += '        </span>';
        facultyDetailHtml += '<span>'+ faculty['us_email'] +'</span>';
        facultyDetailHtml += '    </div>';
        facultyDetailHtml += '</div>';
        
        facultyDetailHtml += '<div class="row line"></div>';
        
        if(faculty['us_about'] != null && faculty['us_about'] != '' )
        {
            facultyDetailHtml += '<div class="faculty-intro" style="max-height: 140px;overflow-y: auto;">';
            facultyDetailHtml += '    <h4 class="text-uppercase small-head">'+lang('about_me')+'</h4>';
            facultyDetailHtml +=  '<p class="wrap">'+faculty['us_about']+'</p>';
            facultyDetailHtml += '</div> ';
        }
        facultyDetailHtml += '<div class="row">';
        facultyDetailHtml += '    <div class="col-sm-12">';
        facultyDetailHtml += '        <ul class="faculty-specs">';
        if(faculty['us_phone'] != null && faculty['us_phone'] != '' && faculty['us_phone'] != '0')
        {
            facultyDetailHtml += '            <li><i class="icon"></i><b>Contact Number</b> : '+faculty['us_phone']+'</li>';
        }
        if(faculty['us_native'] != null && faculty['us_native'] != '' )
        {
            facultyDetailHtml += '            <li><i class="icon icon-location"></i><b>From</b> : '+faculty['us_native']+'</li>';
        }
        if(faculty['us_language_speaks'] != null && faculty['us_language_speaks'] != '' )
        {
            var facultyLanguages    = faculty['us_language_speaks'].split(',');
            var languagePieces      = new Array;
            if(facultyLanguages.length > 0 )
            {
                for(var l=0; l<facultyLanguages.length; l++)
                {
                    languagePieces[l] = __facultylanguages[facultyLanguages[l]];
                }
            }
            facultyDetailHtml += '            <li><i class="icon icon-volume"></i><b>Speaks</b> : '+(languagePieces.join(', '))+'</li>';
        }
        facultyDetailHtml += '        </ul>    ';
        facultyDetailHtml += '    </div>';
        facultyDetailHtml += '</div> ';
        if(typeof faculty['courses'] != 'undefined' && Object.keys(faculty['courses']).length > 0 )
        {
            facultyDetailHtml += '<h4 class="text-uppercase small-head">'+lang('course_handling')+'</h4>';
            facultyDetailHtml += '<div class="line"></div>   ';
            facultyDetailHtml += '<div class="row course-cont-wrap wrap-fix-course"> ';
            facultyDetailHtml += '    <div class="table course-cont only-course rTable" style="">';
            $.each(faculty['courses'], function(courseKey, course )
            {
                facultyDetailHtml += '        <div class="rTableRow">';
                facultyDetailHtml += '            <div class="rTableCell cours-fix course-handling-icn ellipsis-hidden no-border"> ';
                facultyDetailHtml += '                <div class="ellipsis-style">  ';
                facultyDetailHtml += '                    <span class="icon-rounder">';
                facultyDetailHtml += '                        <i class="icon icon-graduation-cap"></i>';
                facultyDetailHtml += '                    </span>';
                facultyDetailHtml += '                    <a href="#" class="cust-sm-6 padd0">'+course['cb_title']+'</a>';
                facultyDetailHtml += '                </div>';
                facultyDetailHtml += '            </div>';
                facultyDetailHtml += '        </div>    ';
            });
            facultyDetailHtml += '    </div>';
            facultyDetailHtml += '</div>   ';
        }
    $('#faculty_detail_wrapper').html(facultyDetailHtml);
    if(typeof faculty['rating'] != 'undefined' && faculty['rating'] != null && faculty['rating'] != '' && faculty['rating'] > 0 )
    {
        rating('#rating_'+faculty['id'], faculty['rating'])
    }
    __activeFaculty = faculty['id'];
}

function changeUserStatus(faculty_id)
{
    // var faculty         = __facultyObject[faculty_id];
    var faculty         = getFacultyObjectDetail(faculty_id);
    var afterMath       = 'If you activate the faculty, he/she can be able to login into the website. Do you wish to proceed?';
    var action          = 'activate';
    var ok_button_text  = 'ACTIVATE';
    if(faculty['us_status'] == 1)
    {
        ok_button_text  = 'DEACTIVATE';
        afterMath       = 'If you deactivate the faculty, he/she won\'t be able to login into the website. Do you wish to proceed?';
        action          = 'deactivate';
    }
    var header_text = 'Are you sure to '+action+' the faculty named "'+faculty['us_name']+'" ?';
    /*
    $('#confirm_box_title').html(header_text);
    $('#confirm_box_content, #confirm_box_content_1').html('');
    $('#confirm_box_content_1').html(afterMath);
    $('#confirm_box_ok').unbind().html(ok_button_text+'<ripples></ripples>');
    $('#confirm_box_ok').click({"faculty_id": faculty_id}, changeStatusConfirmed);*/

    var messageObject = {
        'body':header_text,
        'button_yes':ok_button_text, 
        'button_no':'CANCEL',
        'continue_params':{'faculty_id':faculty_id},
    };
    callback_warning_modal(messageObject, changeStatusConfirmed);
}

function changeStatusConfirmed(params){
    $.ajax({
        url: admin_url+'faculties/change_status',
        type: "POST",
        data:{"faculty_id":params.data.faculty_id, "is_ajax":true},
        success: function(response) {
            clearCache();
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                var index   = getFacultyObjectIndex(data['faculty']['id']);
                __facultyObject[index] = data['faculty'];
                //$('#activate_user').modal('hide');
                if(__filter_dropdown != 'all')
                {
                    $('#faculty_'+data['faculty']['id']).remove();   
                    if(data['faculty']['id'] == __activeFaculty || __activeFaculty == 0)
                    {
                        $('#faculty_detail_wrapper').html('');
                    }
                    if( $('.settings-table').length < 1 )
                    {
                        $('#select_all').hide();
                    }
                    else{
                        $('#select_all').show();
                    }                     
                    if( $('.rTableRow').length < 1 ) {
                        __offset = 1;
                        loadFaculties();
                    }                       
                }
                else
                {
                    $('#faculty_'+data['faculty']['id']).html(renderFacultyHtml(__facultyObject[index]));
                    initToolTip();
                }
                var messageObject = {
                    'body':'Faculty status changed successfully',
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject);
            }
            else
            {
                var messageObject = {
                    'body':data['message'],
                    'button_yes':'OK', 
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function restoreFaculty(faculty_id)
{
    var faculty         = getFacultyObjectDetail(faculty_id);
    /*$('#confirm_box_title').html('');
    $('#confirm_box_content, #confirm_box_content_1').html('');
    $('#confirm_box_ok').unbind().html(lang('RESTORE')+'<ripples></ripples>');
    $('#confirm_box_ok').click({"faculty_id": faculty_id}, restoreFacultyConfirmed);*/

    var messageObject = {
        'body':'Are you sure to '+lang('restore')+' faculty '+faculty['us_name']+'?',
        'button_yes':lang('RESTORE'), 
        'button_no':'CANCEL',
        'continue_params':{'faculty_id':faculty_id},
    };
    callback_warning_modal(messageObject, restoreFacultyConfirmed);
}

function restoreFacultyConfirmed(params){
    $.ajax({
        url: admin_url+'faculties/restore',
        type: "POST",
        data:{"faculty_id":params.data.faculty_id, "is_ajax":true},
        success: function(response) {
            clearCache();
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                var index       = getFacultyObjectIndex(data['faculty']['id']);
                __facultyObject[index] = data['faculty'];
                if(__filter_dropdown != 'all')
                {
                    $('#faculty_'+data['faculty']['id']).remove();
                    if(data['faculty']['id'] == __activeFaculty || __activeFaculty == 0)
                    {
                        $('#faculty_detail_wrapper').html('');
                    } 
                    // lauch_common_success_message('Restored', 'The faculty is restored successfully');
                    if( $('.settings-table').length < 1 )
                    {
                        $('#select_all').hide();
                    }
                    else{
                        $('#select_all').show();
                    }
                    if( $('.rTableRow').length < 1 ) {
                        __offset = 1;
                        loadFaculties();
                    }   
                }
                else
                {
                    $('#faculty_'+data['faculty']['id']).html(renderFacultyHtml(__facultyObject[index]));
                    initToolTip();
                }     
                var messageObject = {
                    'body':'Faculty restored successfully',
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject);               
            }
            else
            {
                var messageObject = {
                    'body':data['message'],
                    'button_yes':'OK', 
                };
                callback_danger_modal(messageObject);
                //$('#confirm_box_title').html(data['message']);
                //$('#confirm_box_content').html('');
            }
        }
    });
}
var __faculty_courses_ids = [];
function deleteFaculty(faculty_id)
{
    // var faculty   = __facultyObject[faculty_id];
    var faculty         = getFacultyObjectDetail(faculty_id);
    console.log(faculty.courses);
    /*$('#confirm_box_title').html();
    $('#confirm_box_content, #confirm_box_content_1').html('');
    $('#confirm_box_content_1').html('If you delete the faculty, he cannot be able to login to the site. However you have an option to restore.');
    $('#confirm_box_ok').unbind().html(lang('DELETE')+'<ripples></ripples>');
    $('#confirm_box_ok').click({"faculty_id": faculty_id}, deleteFacultyConfirmed);*/
    var courselenght = faculty.courses.length;
    var warning = '';
    if(courselenght > 0){
        warning = `This faculty is assigned to <b>${courselenght} more corses </b><br/>`;
        
        for(var i = 0; i < courselenght; i++){
            __faculty_courses_ids.push(faculty.courses[i].ct_course_id);
        }
        //console.log(__faculty_courses_ids);
    }
    var messageObject = {
        'body': warning+'Are you sure to delete the faculty "'+faculty['us_name']+'"?',
        'button_yes':lang('DELETE'), 
        'button_no':'CANCEL',
        'continue_params':{'faculty_id':faculty_id, 'faculty_courses_ids' : __faculty_courses_ids},
    };
    callback_warning_modal(messageObject, deleteFacultyConfirmed);
}

function deleteFacultyConfirmed(params){
    $.ajax({
        url: admin_url+'faculties/delete',
        type: "POST",
        data:{"faculty_id":params.data.faculty_id, "is_ajax":true, 'faculty_courses_ids' : params.data.faculty_courses_ids},
        success: function(response) {
            clearCache();
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                __faculty_courses_ids = [];
                var index       = getFacultyObjectIndex(data['faculty']['id']);
                __facultyObject[index] = data['faculty'];
                //$('#activate_user').modal('hide');
                if(__filter_dropdown != 'all')
                {
                    var index   = getFacultyObjectIndex(data['faculty']['id']);
                    __facultyObject[index] = data['faculty'];
                    //$('#activate_user').modal('hide');
                    if(__filter_dropdown != 'all')
                    {
                        $('#faculty_'+data['faculty']['id']).remove();   
                        if(data['faculty']['id'] == __activeFaculty || __activeFaculty == 0)
                        {
                            $('#faculty_detail_wrapper').html('');
                        } 
                        if( $('.settings-table').length < 1 )
                        {
                            $('#select_all').hide();
                        }
                        else{
                            $('#select_all').show();
                        }        
                        if( $('.rTableRow').length < 1 ) {
                            __offset = 1;
                            loadFaculties();
                        }                                   
                    }
                    else
                    {
                        $('#faculty_'+data['faculty']['id']).html(renderFacultyHtml(__facultyObject[index]));
                        initToolTip();
                    }
                    var messageObject = {
                        'body':'Faculty status changed successfully',
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);
                }
                else
                {
                    $('#faculty_'+data['faculty']['id']).html(renderFacultyHtml(__facultyObject[index]));
                    initToolTip();
                }
                var messageObject = {
                    'body':'Faculty deleted successfully',
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject);
            }
            else
            {
                var messageObject = {
                    'body':data['message'],
                    'button_yes':'OK', 
                };
                callback_danger_modal(messageObject);
                //$('#confirm_box_title').html(data['message']);
                //$('#confirm_box_content').html('');
            }
        }
    });
}

function resetPassword(faculty_id)
{
    var messageObject = {
        'body':lang('sure_to_reset_password'),
        'button_yes':lang('RESET'), 
        'button_no':'CANCEL',
        'continue_params':{'faculty_id':faculty_id},
    };
    callback_warning_modal(messageObject, resetPasswordConfirmed);
    /*$('#confirm_box_title').html(lang('reset_password'));
    $('#confirm_box_content_1').html(lang('sure_to_reset_password'));
    $('#confirm_box_ok').unbind().html(lang('RESET')+'<ripples></ripples>');
    $('#confirm_box_ok').click({"faculty_id": faculty_id}, resetPasswordConfirmed);*/
}

function resetPasswordConfirmed(params){
    //$('#confirm_box_ok').unbind().html('RESETTING..<ripples></ripples>');
    $.ajax({
        url: admin_url+'faculties/reset_password',
        type: "POST",
        data:{"faculty_id":params.data.faculty_id, "is_ajax":true},
        success: function(response) {
            clearCache();
            var data  = $.parseJSON(response);
            if(data['error'] == 'false')
            {
                //$('#activate_user').modal('hide');
                var messageObject = {
                    'body':'Password has been reset successfully. An email has been send to the corresponding faculty.',
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject);
            }
            else
            {
                var messageObject = {
                    'body':data['message'],
                    'button_yes':'OK', 
                };
                callback_danger_modal(messageObject);
                // $('#confirm_box_title').html(data['message']);
                // $('#confirm_box_content').html('');
            }
        }
    });
}

function addFacultyToCourse(faculty_id)
{
    //__facultySelected   = new Array();  purpse for error 3388
    __courseSelected    = new Array();
    var faculty         = getFacultyObjectDetail(faculty_id);
    // var faculty         =  __facultyObject[faculty_id]
    $('#popUpMessage').remove();
    $('#add-users-course').modal('show');
    $('#course_list_wrapper').html('<div class="checkbox-wrap"><span class="chk-box"><label class="font14">Loading...</label></span></div>');
    $('#myModalLabel').html('ADD "'+(((faculty['us_name'].length > 25)?(faculty['us_name'].substr(0, 25).toUpperCase()+'...'):faculty['us_name'].toUpperCase()))+'" TO COURSE');
    $.ajax({
        url: admin_url+'faculties/faculty_course',
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
                    if( data['courses'][i]['cb_status'] == 0 )
                    {
                        courseHtml += '        <span class="label-warning">Inactive</span>';
                    }
                    else
                    {
                        courseHtml += '        <span class="label-success">Active</span>';                        
                    }
                    courseHtml += '    </span>';
                    courseHtml += '</div>';
                }
                $('#course_list_wrapper').html(courseHtml);
                $('#add_user_ok').unbind();
                $('#add_user_ok').click({"faculty_id": faculty_id}, addFacultyToCourseConfirmed);    
            }
            else
            {
                $('#course_list_wrapper').html('<div class="checkbox-wrap"><span class="chk-box"><label class="font14">No Courses</label></span></div>');
            }
        }
    });
}

function addFacultyToCourseConfirmed(params)
{
    $.ajax({
        url: admin_url+'faculties/add_faculty_to_course',
        type: "POST",
        data:{"courses":JSON.stringify(__courseSelected), "faculty_id":params.data.faculty_id, "is_ajax":true},
        success: function(response) {
            //clearCache(); purpse for error 3388
            var data = $.parseJSON(response);
            var index       = getFacultyObjectIndex(data['faculty']['id']);
            __facultyObject[index] = data['faculty'];
            if(data['faculty']['id'] == __activeFaculty || __activeFaculty == 0)
            {                
                loadFacultyDetail(data['faculty']['id']);
            }
            $('#add-users-course').modal('hide');
        }
    });
}

function sendMessageToFaculty(faculty_id)
{
    $('#invite-user-bulk').modal();
    $('#popUpMessage').hide();
    $('#invite_send_subject').val('');        
    // $('#redactor_invite').redactor('set', '');
    $('#redactor_invite').redactor('insertion.set','');
    // $('#redactor_invite').redactor('core.destroy');
    startTextToolbar();
    $('#message_send_button').attr('onclick','sendMessageBulk('+ faculty_id +')');
}

var __sendEmailsBulk = new Array();
function sendMessageBulk(faculty_id_obj )
{
    var faculty_id = typeof faculty_id_obj != 'undefined' ? faculty_id_obj : '';
    var send_user_bulk_subject = $('#invite_send_subject').val();
    var send_user_bulk_message = btoa($('#redactor_invite').val());        

    var errorCount   = 0;
    var errorMessage = '';
    
    var faculty_ids = [];
    if(__facultySelected.length > 0)
    {
        faculty_ids = __facultySelected;
    }
    else
    {
        if(faculty_id != '' && faculty_id > 0)
        {
            faculty_ids.push(faculty_id);
        }
        else
        {
            errorCount++;
            errorMessage += 'Email id cannot be empty<br />';
        }
    }
    if ($.trim(send_user_bulk_subject) == '') {
        errorCount++;
        errorMessage += 'Please enter subject<br />';
    }
    if ($.trim(send_user_bulk_message) == '') {
        errorCount++;
        errorMessage += 'Please enter message<br />';
    }
    
    if(errorCount > 0)
    {
        $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();    
        return false;
    }
    $('#message_send_button').text('SENDING..');
    $.ajax({
        url: admin_url+'faculties/send_message',
        type: "POST",
        data:{"is_ajax":true, "send_user_subject":send_user_bulk_subject, "send_user_message":send_user_bulk_message, "faculty_ids":JSON.stringify(faculty_ids)},
        success: function(response) {
            clearCache();
            var data            = $.parseJSON(response);
            if(data['error'] == false)
            {
                // $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('success', data['message']));                    
                $('#invite-user-bulk').modal('hide');                    
                var messageObject = {
                    'body':data['message'],
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject);
            }else{
                $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', data['message']));
            }
            $('#message_send_button').text('SEND'); 
        
            setTimeout(function(){
                $('#faculty_wrapper .lecture-control').css('visibility', 'visible');
                $('#invite-user-bulk').modal('hide');               
            }, 2500);
        }
    }); 
}

function deleteFacultyBulk()
{
    /*$('#activate_user').modal('show');
    $('#confirm_box_title').html('Are you sure to '+lang('delete_selected_faculties')+'?');
    $('#confirm_box_content, #confirm_box_content_1').html('');
    $('#confirm_box_ok').unbind().html('DELETE<ripples></ripples>');;
    $('#confirm_box_ok').click({}, deleteFacultyBulkConfirmed);    */
    var messageObject = {
        'body':'Are you sure to '+lang('delete_selected_faculties')+'?',
        'button_yes':'DELETE', 
        'button_no':'CANCEL',
    };
    callback_warning_modal(messageObject, deleteFacultyBulkConfirmed);
}

function deleteFacultyBulkConfirmed(){
    $.ajax({
        url: admin_url+'faculties/delete_faculties_bulk',
        type: "POST",
        data:{"faculties":JSON.stringify(__facultySelected), "is_ajax":true},
        success: function(response) {
            clearCache();
            var data            = $.parseJSON(response);
            var totalfaculties = Object.keys(data['faculties']).length;
            if( totalfaculties > 0 )
            {
                $.each(data['faculties'], function(facultyKey, faculty )
                {
                    var index       = getFacultyObjectIndex(faculty['id']);
                    __facultyObject[index] = faculty;
                    // $('#faculty_'+faculty['id']).html(renderFacultyHtml(faculty));
                    if(__filter_dropdown != 'all')
                    {
                        $('#faculty_'+faculty['id']).remove();    
                        if(faculty['id'] == __activeFaculty || __activeFaculty == 0)
                        {
                            $('#faculty_detail_wrapper').html('');
                        }            
                        if( $('.rTableRow').length < 1 ) {
                            __offset = 1;
                            loadFaculties();
                        }             
                    }
                    else
                    {
                        $('#faculty_'+faculty['id']).html(renderFacultyHtml(faculty));
                        initToolTip();
                    }
                });
                var messageObject = {
                    'body':'Selected '+((totalfaculties>1)?'faculties':'faculty')+' deleted successsfully.',
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject);
                filter_faculties_by(__filter_dropdown);
            }
            $('#faculty_wrapper .lecture-control').css('visibility', 'visible');
            //$('#activate_user').modal('hide');
        }
    });
}

function changeStatusBulk(status)
{ 
    var ok_button_text  = 'DEACTIVATE';
    if( status == 1)
    {
        ok_button_text  = 'ACTIVATE';
    }
    /*$('#activate_user').modal('show');
    $('#confirm_box_title').html('Are you sure to '+lang(((status==1)?'activate':'deactivate')+'_selected_faculties')+'?');
    $('#confirm_box_content, #confirm_box_content_1').html('');
    $('#confirm_box_ok').unbind().html(ok_button_text+'<ripples></ripples>');
    $('#confirm_box_ok').click({'status':status}, changeStatusBulkConfirmed);    */
    var messageObject = {
        'body':'Are you sure to '+lang(((status==1)?'activate':'deactivate')+'_selected_faculties')+'?',
        'button_yes':ok_button_text, 
        'button_no':'CANCEL',
        'continue_params':{'status':status},
    };
    callback_warning_modal(messageObject, changeStatusBulkConfirmed);
}

function changeStatusBulkConfirmed(param){
    $.ajax({
        url: admin_url+'faculties/change_status_bulk',
        type: "POST",
        data:{"faculties":JSON.stringify(__facultySelected), "is_ajax":true, 'status':param.data.status},
        success: function(response) {
            clearCache();
            var data            = $.parseJSON(response);
            var totalfaculties = Object.keys(data['faculties']).length;
            if(totalfaculties > 0 )
            {
                $.each(data['faculties'], function(facultyKey, faculty )
                {
                    var index       = getFacultyObjectIndex(faculty['id']);
                    __facultyObject[index] = faculty;
                    if(__filter_dropdown != 'all')
                    {
                        $('#faculty_'+faculty['id']).remove();       
                        if(faculty['id'] == __activeFaculty)
                        {
                            $('#faculty_detail_wrapper').html('');
                        }   
                        if( $('.rTableRow').length < 1 ) {
                            __offset = 1;
                            loadFaculties();
                        }                      
                    }
                    else
                    {
                        $('#faculty_'+faculty['id']).html(renderFacultyHtml(faculty));
                        initToolTip();
                    }
                });
                var messageObject = {
                    'body':'Selected '+((totalfaculties>1)?'faculties':'faculty')+' status changed successsfully.',
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject);
                filter_faculties_by(__filter_dropdown);
            }
            $('#faculty_wrapper .lecture-control').css('visibility', 'visible');
            //$('#activate_user').modal('hide');
        }
    });
}

function validateEmail(email)
{
    var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
    if (filter.test(email)) {
        return true;
    }
    else {
        return false;
    }
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

$(document).on('change', '.faculty-checkbox', function(){
    var faculty_id = $(this).val();
    if($(this).is(':checked'))
    {
        __facultySelected.push(faculty_id);
        if(__facultySelected.length > 1)
        {
            $('#faculty_wrapper .lecture-control').css('visibility', 'hidden');
        }
        if($('.faculty-checkbox').not(':disabled').length == $('.faculty-checkbox:checked').not(':disabled').length)
        {
            $('.faculty-checkbox-parent').prop('checked', true);
        }
    }
    else
    {
        removeArrayIndex(__facultySelected, faculty_id);
        $('.faculty-checkbox-parent').prop('checked', false);
        if(__facultySelected.length <= 1)
        {
            $('#faculty_wrapper .lecture-control').css('visibility', 'visible');
        }
    }
    
    $("#selected_faculties_count").html('');
    $('#bulk_action_wrapper').hide();
    
    
    if(__facultySelected.length > 0){
        $("#selected_faculties_count").html(' ('+__facultySelected.length+')');
    }else{
        $("#selected_faculties_count").html('');
    }
    
    if(__facultySelected.length > 1){
        $("#bulk_action_wrapper").css('display','block');
    }else{
        $("#bulk_action_wrapper").css('display','none');
    }
});

$(document).on('change', '.faculty-checkbox-parent', function(){
    __facultySelected = new Array();
    $("#selected_faculties_count").html('');
    if($('.faculty-checkbox-parent').is(':checked'))
    {
        $('.faculty-checkbox').not(':disabled').each(function( index, value ) {
            __facultySelected.push($(this).val());
        });
        $('#faculty_wrapper .lecture-control').css('visibility', 'hidden');
        $('.faculty-checkbox').not(':disabled').prop('checked', true);
        $("#selected_faculties_count").html(' ('+__facultySelected.length+')');
        if(__facultySelected.length > 1){
            $('#bulk_action_wrapper').show();
        }            
    }
    else
    {
        $('#faculty_wrapper .lecture-control').css('visibility', 'visible');
        $('.faculty-checkbox').prop('checked', false);
        $('#bulk_action_wrapper').hide();
    }
});

$(document).ready(function() {
    __facultyObject      = $.parseJSON(__facultyObject);
    clearCache();
    if(Object.keys(__facultyObject).length == 0 )
    {
        $('#select_all').hide();
        $('#faculty_wrapper').html(renderPopUpMessagePage('error', 'No Faculties found.'));
        $('#popUpMessagePage .close').css('display', 'none');
    }
    else
    {
        $('#faculty_wrapper').html(renderFacultiesHtml(__facultyObject));
        loadFacultyDetail(__activeFaculty);        
        $(function(){
            $('.right-box').slimScroll({
                    height: '100%',
                    wheelStep : 3,
                    distance : '10px'
            });
        });
    }
});

function clearCache()
{
    __facultySelected   = new Array();
    __courseSelected    = new Array();
    $('.faculty-checkbox-parent').prop('checked', false);
    $('.faculty-checkbox').prop('checked',false);
    $('#selected_faculties_count').html('');
    $('#bulk_action_wrapper').hide();
}
function rating(selector, rate)
{
    $(selector).rateYo({
        starWidth: "18px",
        rating: rate,
        readOnly: true,
        ratedFill : '#d94d38',
        normalFill : '#000000'
    });
}

function generatePassword()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 8; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    $('#faculty_password').val(text);
}

$(function()
{
    startTextToolbar();
});

function startTextToolbar()
{
    $('#redactor_invite').redactor({
        imageUpload: admin_url+'configuration/redactore_image_upload',
        source: false,
        plugins: ['table', 'alignment'],
        callbacks: {
            imageUploadError: function(json, xhr)
            {
                 alert('Please select a valid image');
                 return false;
            }
        }   
    });
}