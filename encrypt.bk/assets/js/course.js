var course_selected = new Array();

$(document).ready(function(){
    var keyword         = getQueryStringValue('keyword');
    var category_id     = getQueryStringValue('category');
    var filter          = getQueryStringValue('filter');
    var createCourse    = getQueryStringValue('create');

    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#course_keyword').val(keyword);
    }
    if (category_id != '') {
        __category_id = category_id;
        $('#dropdown_text').html('<span style="width:100%;max-width:240px;" title="'+ $('#dropdown_list_' + category_id).text() +'" class="category-text-ellipsis">' + $('#dropdown_list_' + category_id).text() + '</span><span class="caret"  style="margin-top: -8px;"></span>');
    }
    if (filter != '') {
        __filter_dropdown = filter;
        $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
        
    }
    if(createCourse == 'true') {
        $("#course_create").trigger("click");
    }
    setBulkAction();
    if(__filter_dropdown == "deleted"){
        $('.select-all-style').hide();
    } else {
        $('.select-all-style').show();
    }    
});

$(document).on('click', '#basic-addon2', function(){
    var course_keyword = $('#course_keyword').val().trim();        
        if(course_keyword == '')
        {
            lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
        }
        else{
            __offset = 1;
            getCourses();
            scrollToTopOfPage();
            $("#selected_course_count").html(''); 
        }
});

var timeOut = '';
$(document).on('keyup', '#course_keyword', function(){
    clearTimeout(timeOut);
    timeOut = setTimeout(function(){
        __offset = 1;
        getCourses();
    }, 600);
    scrollToTopOfPage();
    $("#selected_course_count").html(''); 
});

$(document).on('click', '#searchclear', function(){
    __offset = 1;
    getCourses();
    $("#selected_course_count").html(''); 
});


$(document).on('click', '.course-checkbox', function(){
    var course_id = $(this).val();
    if ($('.course-checkbox:checked').length == $('.course-checkbox').length) {
        $('.course-checkbox-parent').prop('checked',true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        course_selected.push(course_id);
    }else{
        $('.course-checkbox-parent').prop('checked',false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(course_selected, course_id);
    }
    if(course_selected.length > 0){
        $("#selected_course_count").html(' ('+course_selected.length+')');
    }else{
        $("#selected_course_count").html(''); 
    }

    if(course_selected.length > 1){
        $("#course_bulk").css('display','block');
    }else{
        $("#course_bulk").css('display','none');
    }
});
$(document).on('click', '.course-checkbox-parent', function(){
    
    var parent_check_box = this;
    course_selected = new Array();    
    $( '.course-checkbox' ).not(':disabled').prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $( '.course-checkbox' ).not(':disabled').each(function( index ) {
           course_selected.push($( this ).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if(course_selected.length > 0){
        $("#selected_course_count").html(' ('+course_selected.length+')');
    }else{
        $("#selected_course_count").html(''); 
    }

    if(course_selected.length > 0){
        $("#course_bulk").css('display','block');
    }else{
        $("#course_bulk").css('display','none');
    }
});



var __filter_dropdown = 'active';
var __category_id      = '';
var __gettingCourseInProgress = false;

// var __totalUsers    = 0;
// var __shownUsers    = 0;
function getCourses()
{
    if(__gettingCourseInProgress == true)
    {
        return false;
    }
    __gettingCourseInProgress = true;
    var keyword  = $('#course_keyword').val().trim();
    $('#loadmorebutton').show().html('Loading...');

    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if ( __filter_dropdown != '' || __category_id != '' || keyword != '') {
            link += '?';
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + __filter_dropdown;
        }
        if (__category_id != '') {
            link += '&category=' + __category_id;
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        
        window.history.pushState({ path: link }, '', link);
    }

    $.ajax({
        url: admin_url+'course/course_json',
        type: "POST",
        data:{"is_ajax":true, 
            "filter":__filter_dropdown, 
            "category_id":__category_id, 
            "keyword":keyword,
            'limit':__limit,
            'offset':__offset
        },
        success: function(response) {
            $('.course-checkbox-parent').prop('checked',false);
            course_selected = new Array();
            if(course_selected.length > 0){
                $("#course_bulk").css('display','block');
            }else{
                $("#course_bulk").css('display','none');
            }
            var data = $.parseJSON(response);
            var remainingCourse = 0;
            $('#loadmorebutton').hide();
            __offset++;
            if(data['courses'].length > 0){
                if(__offset == 2)
                {
                    __totalUsers = data['total_courses'];
                    __shownUsers = data['courses'].length;
                    remainingCourse = (data['total_courses'] - data['courses'].length);
                    var totalCourseHtml = data['courses'].length+' / '+data['total_courses']+' '+((data['total_courses'] == 1)?"Course":"Courses");
                    scrollToTopOfPage();
                    $('.course-count').html(totalCourseHtml);
                    $('#course_row_wrapper').html(renderCourseHtml(response));
                }
                else
                {
                    __totalUsers = data['total_courses'];
                    __shownUsers = ((__offset - 2) * data['limit']) + data['courses'].length;
                    remainingCourse = (data['total_courses'] - (((__offset-2)*data['limit'])+data['courses'].length));
                    var totalCourseHtml = (((__offset-2)*data['limit'])+data['courses'].length)+' / '+data['total_courses']+' Courses';
                    $('.course-count').html(totalCourseHtml);
                    $('#course_row_wrapper').append(renderCourseHtml(response));                     
                }
            }
            else
            {
                $('.course-count').html("No Courses");
                $('#course_row_wrapper').html(renderPopUpMessage('error', 'No Courses found.'));
                $('#popUpMessage > .close').remove();
            }
            if(data['show_load_button'] == true)
            {
                $('#loadmorebutton').show();
                remainingCourse = (remainingCourse>0)?'('+remainingCourse+')':'';
                $('#loadmorebutton').html('Load More '+remainingCourse+'<ripples></ripples>');
            }
            
            __gettingCourseInProgress = false;
        }
    });
}

function loadMoreCourse(){
    getCourses();
}

function filter_category(category_id)
{
   __category_id        = category_id;
   __offset             = 1;
   $('#dropdown_text').html('<span style="width:100%;max-width:240px;" title="'+ $('#dropdown_list_' + category_id).text() +'" class="category-text-ellipsis">' + $('#dropdown_list_'+category_id).text()+'</span><span class="caret" style="margin-top: -8px;"></span>');
   getCourses();
   scrollToTopOfPage();
    $("#selected_course_count").html(''); 
}

function filter_course_by(filter)
{  
   __offset             = 1;
   __filter_dropdown    = filter;
   $('#filter_dropdown_text').html($('#filer_dropdown_list_'+filter).text()+'<span class="caret"></span>');
   getCourses();
   scrollToTopOfPage();
   setBulkAction();
    $("#selected_course_count").html(''); 
    if(__filter_dropdown == "deleted"){
        $('.select-all-style').hide();
    } else {
        $('.select-all-style').show();
    }
   
}

function setBulkAction() {
    var course_bulk = '';
    if(__filter_dropdown != 'deleted')
    {
        if(__course_privilege.edit() == true){
            course_bulk   += '<span class="dropdown-tigger" data-toggle="dropdown">';
            course_bulk   += '       <span class="label-text">';
            course_bulk   += '       Bulk Action ';
            course_bulk   += '       </span>';
            course_bulk   += '       <span class="tilder"></span>';
            course_bulk   += '   </span>';
            course_bulk   += '   <ul class="dropdown-menu pull-right" role="menu">';
            if(__filter_dropdown != 'active')
                course_bulk   += '       <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk(\'1\')">Activate </a></li>';
            if(__filter_dropdown != 'inactive')
                course_bulk   += '       <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk(\'0\')">Deactivate</a></li>';
            course_bulk   += '   </ul>';
        }
       
    }
    $('#course_bulk').html(course_bulk);
}

function changeCourseStatus(course_id, status, course_name)
{
    var action = '';
    switch(status) {
        case "deactivate":
            ok_button_text = 'DEACTIVATE';//lang
            action         = 'Unpublish';
        break;
        case "activate":
            ok_button_text = 'ACTIVATE';//lang
            action         = 'Publish';
        break;
    }
    var header_text = 'Are you sure to ' + action + ' the course named "' + course_name + '"';

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'course_id': course_id
        },
    };
    callback_warning_modal(messageObject, changeStatusConfirmed);
}

function changeStatusConfirmed(params){
    var course_id = params.data.course_id;
    $.ajax({
        url: admin_url+'course/change_status',
        type: "POST",
        data:{"course_id":params.data.course_id, "is_ajax":true},
        success: function(response) {
            // console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                if (__filter_dropdown == 'all') {
                    $('#course_row_' + course_id).html(renderCourseRow(data['course']));
                } else {
                    $('#course_row_' + course_id).remove();
                    __totalUsers = __totalUsers - 1;
                    __shownUsers = __shownUsers - 1;
                    if (__totalUsers == 0 && __shownUsers == 0) {
                        var totalUsersHtml = 'No Courses';
                    } else {
                        var totalUsersHtml = __shownUsers +' / '+__totalUsers + ' Courses';//__shownUsers + ' / ' + __totalUsers + ' Students';
                    }
                    $('.course-count').html(totalUsersHtml);
                }

                var messageObject = {
                    'body': 'Course status changed successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                // scrollToTopOfPage();                
            }
            else
            {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}


var statusMessage = '';
function changeStatusBulk(status)
{
    var ok_button_text = '';
    if(status=='1'){
        statusMessage   = 'activated';
        ok_button_text     = 'ACTIVATE';
    }
    else {
        statusMessage = 'deactivated';
        ok_button_text     = 'DEACTIVATE';
    }       
    var header_text     = 'Are you sure to '+ ok_button_text +' selected courses';
    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status
        },
    };
    callback_warning_modal(messageObject, ChangeStatusBulkConfirmed);
}

function ChangeStatusBulkConfirmed(params){
    var status  = params.data.status;
    $.ajax({
        url: admin_url+'course/change_status_bulk',
        type: "POST",
        data:{"courses":JSON.stringify(course_selected), "status_bulk":params.data.status, "is_ajax":true},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == true)
            {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                    'prevent_button_no': true
                };
                callback_danger_modal(messageObject);
            } 
            else 
            {
                var message = (status == '1') ? 'activated' : 'deactivated';
                var messageObject = {
                    'body': 'Courses '+ message +' successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
            }
            if (data['courses'].length > 0) {
                for (var i in data['courses']) {
                    if (__filter_dropdown == 'all') {
                        $('#course_row_' + data['courses'][i]['id']).html(renderCourseRow(data['courses'][i]));
                    } else {
                        $('#course_row_' + data['courses'][i]['id']).remove();
                        __totalUsers = __totalUsers - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalUsers <= 0 && __shownUsers <= 0) {
                            var totalUsersHtml = 'No Courses';
                        } else {
                            var totalUsersHtml = __shownUsers +' / '+__totalUsers + ' Courses';//__shownUsers + ' / ' + __totalUsers + ' Students';
                        }
                        $('.course-count').html(totalUsersHtml);
                    }
                }
            }
            course_selected = new Array(); 
            $('.course-checkbox-parent').prop('checked',false);
            $('.course-checkbox').prop('checked', false);   
            $('.course-count').html('');         
        }
    });
}

function restoreCourse(course_id, course_name)
{  
    var header_text     = 'Are you sure to Restore Course named '+course_name+' ? ';
    var messageObject = {
        'body': header_text,
        'button_yes': 'RESTORE',
        'button_no': 'CANCEL',
        'continue_params': {
            'course_id': course_id
        },
    };
    callback_warning_modal(messageObject, restoreCourseConfirmed);    
}

function restoreCourseConfirmed(params){
    var course_id   = params.data.course_id;
    $.ajax({
        url: admin_url+'course/restore',
        type: "POST",
        data:{"course_id":params.data.course_id, "is_ajax":true},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                if(__filter_dropdown == 'all') {
                    $('#course_row_' + course_id).html(renderCourseRow(data['course']));
                } else {
                    $('#course_row_' + course_id).remove();
                    __totalUsers = __totalUsers - 1;
                    __shownUsers = __shownUsers - 1;
                    if (__totalUsers == 0 && __shownUsers == 0) {
                        var totalUsersHtml = 'No Courses';
                    } else {
                        var totalUsersHtml = __shownUsers +' / '+__totalUsers + ' Courses';//__shownUsers + ' / ' + __totalUsers + ' Students';
                    }
                    $('.course-count').html(totalUsersHtml);
                }
                var messageObject = {
                    'body': 'Course restored successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
            }
            else
            {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function deleteCourse(course_id, course_name)
{
    $.ajax({
        url: admin_url+'course/delete_check',
        type: "POST",
        data:{"course_id":course_id, "is_ajax":true},
        success: function(response) {
            var data        = $.parseJSON(response);   
            var header_text = '<p>Are you sure to Delete Course named '+course_name+' ? </p>';
            if(data['student_count'] != ''){
                header_text     += '<p> -'+data['student_count']+' enrolled on this course.</p>';
            }
            if(data['bundle'].length > 0){
                header_text     += '<p> - This course is enrolled to bundle/s</p>';
                var bundleCount=1 ; 
                var bundle = data['bundle'];
                for (var i =0;i<bundle.length;i++) {
                    header_text     += '<h5>'+bundleCount+'.'+bundle[i]+'</h5>';
                    bundleCount++;
                }
                header_text     += '<p style="font-size:13px">Note : Once deleted the course will automaticaly remove from bundle.</p>';
            
            }
            
            var message         = header_text;
            var messageObject = {
                'body': message,
                'button_yes': 'DELETE',
                'button_no': 'CANCEL',
                'continue_params': {
                    'course_id': course_id
                },
            };
            callback_warning_modal(messageObject, deleteCourseConfirmed);
        }
    });        
}


function deleteCoursePermanently(course_id, course_name, p_remove)
{
    $.ajax({
        url: admin_url+'course/delete_check',
        type: "POST",
        data:{"course_id":course_id, "is_ajax":true},
        success: function(response) {
            var data        = $.parseJSON(response);   
            var header_text = '<p>Are you sure to Permanently Delete the Course <b>'+course_name+'</b> ? </p>';
            if(data['student_count'] != ''){
                header_text     += '<p> -'+data['student_count']+' enrolled on this course.</p>';
            }
            if(data['bundle'].length > 0){
                header_text     += '<p> - This course is enrolled to bundle/s</p>';
                var bundleCount=1 ; 
                var bundle = data['bundle'];
                for (var i =0;i<bundle.length;i++) {
                    header_text     += '<h5>'+bundleCount+'.'+bundle[i]+'</h5>';
                    bundleCount++;
                }
                header_text     += '<p style="font-size:13px">Note : Once deleted the course will automaticaly remove from bundle.</p>';
            
            }
            
            var message         = header_text;
            var messageObject = {
                'body': message,
                'button_yes': 'DELETE PERMANENTLY',
                'button_no': 'CANCEL',
                'continue_params'   : {
                    'course_id'     : course_id,
                    'p_remove'      : p_remove
                },
            };
            callback_danger_modal(messageObject, deleteCourseConfirmed);
        }
    });        
}

function deleteCourseConfirmed(params){
    var course_id   = params.data.course_id;
    var p_remove    = false;

    if(typeof params.data.p_remove !="undefined" && params.data.p_remove == course_id){
        p_remove    = params.data.p_remove;
        console.log('delete permanently');
    }

    $.ajax({
        url: admin_url+'course/delete',
        type: "POST",
        data:{"course_id":params.data.course_id, "is_ajax":true, p_remove : p_remove},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                if(__filter_dropdown == 'all') {
                    $('#course_row_' + course_id).html(renderCourseRow(data['course']));
                } else {
                    $('#course_row_' + course_id).remove();
                    __totalUsers = __totalUsers - 1;
                    __shownUsers = __shownUsers - 1;
                    if (__totalUsers == 0 && __shownUsers == 0) {
                        var totalUsersHtml = 'No Courses';
                    } else {
                        var totalUsersHtml = __shownUsers +' / '+__totalUsers + ' Courses';//__shownUsers + ' / ' + __totalUsers + ' Students';
                    }
                    $('.course-count').html(totalUsersHtml);
                }
                var messageObject = {
                    'body': 'Course deleted successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
            } else {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

var __param_addToCatalog = new Array();
var __param_saveCatalog  = new Array();
var __totalCost = 0;
function addToCatalog(id, title, price)
{
    __isBulkRequest = false;
    if( __create_catalog_as_new == true)
    {
        __create_catalog_as_new = false;
        $('#create_new_catalog_cancel, #catalog_name').hide();
        $('#create_new_catalog, #catalog_id').show();
    }
    __param_addToCatalog['id']    = id;
    __param_addToCatalog['title'] = atob(title);
    __param_addToCatalog['price'] = price;
    __totalCost                   = price;
    __param_saveCatalog.push(id);
    //setting selected cousre details
    var courseCatalogHtml = '';
    courseCatalogHtml += '<div class="checkbox-wrap" id="catalog_course_'+__param_addToCatalog['id']+'" data-cost="'+__param_addToCatalog['price']+'">';
    courseCatalogHtml += '    <span class="chk-box">';
    courseCatalogHtml += '        <label class="font14">';
    courseCatalogHtml += '            '+__param_addToCatalog['title'];
    courseCatalogHtml += '        </label>';
    courseCatalogHtml += '    </span>';
    courseCatalogHtml += '    <span class="email-label pull-right">';
    courseCatalogHtml += '        <span>'+__param_addToCatalog['price']+' INR</span>';
    courseCatalogHtml += '        <span class="delete-cover"><a href="javascript:void(0)" onclick="removeCourseIdFromCatalog('+__param_addToCatalog['id']+')"><i class="icon icon-cancel-1 delte"></i></a></span>';
    courseCatalogHtml += '    </span>';
    courseCatalogHtml += '</div>';
    $('#catalog_course_list').html(courseCatalogHtml);
    $('#total_price').html(__totalCost+' INR');
    //end
    
    $.ajax({
        url: admin_url+'course/catalogs',
        type: "POST",
        data:{"is_ajax":true},
        success: function(response) {
            var catalogHtml = '<option value="">Choose '+lang('catalog')+'</option>';
            var data        = $.parseJSON(response);
            if( data['catalogs'].length > 0 )
            {
                var catalog_name = '';
                for (var i=0; i < data['catalogs'].length; i++)
                {
                    catalog_name = data['catalogs'][i]['c_title'];
                    catalog_name = (catalog_name.length > 37)?(catalog_name.substr(0, 34)+'..'):catalog_name;
                    catalogHtml += '<option value="'+data['catalogs'][i]['id']+'">'+catalog_name+'</option>';
                }
            }
            $('#catalog_id').html(catalogHtml);
            $('#create-catalog').modal('show');
        }
    });
}

function addToCatalogProceed()
{
    var catalog_name            = $('#catalog_name').val();
        catalog_name            = catalog_name.trim();
    var catalog_id              = $('#catalog_id').val();
    var catalog_price           = $('#catalog_price').val();
    var catalog_price_discount  = $('#catalog_price_discount').val();
    var errorCount              = 0;
    var errorMessage            = '';

    
    if(__create_catalog_as_new == true && catalog_name == '')
    {
        errorCount++;
        errorMessage += 'please enter catalog name <br />';
    }

    if(__create_catalog_as_new == false && catalog_id == '')
    {
        errorCount++;
        errorMessage += 'please choose catalog <br />';
    }

    if( catalog_price == '')
    {
        errorCount++;
        errorMessage += 'please choose catalog price <br />';
    }
    else
    {
        if(parseInt(catalog_price) < parseInt(catalog_price_discount))
        {
            errorCount++;
            errorMessage += 'Discount price should not be greater than price amount. <br />';
        }else if(parseInt(catalog_price) == parseInt(catalog_price_discount)){
            errorCount++;
            errorMessage += 'Discount price should not be equal to price amount. <br />';
        }
    }
    if( catalog_price_discount == '')
    {
        errorCount++;
        errorMessage += 'please choose catalog discount<br />';
    }

    if( __param_saveCatalog.length == 0)
    {
        errorCount++;
        errorMessage += 'please choose course<br />';
    }
    cleanPopUpMessage();
    if( errorCount == 0 )
    {
        $.ajax({
            url: admin_url+'course/save_catalogs_courses',
            type: "POST",
            data:{"is_ajax":true, 'catalog_name':catalog_name, 'catalog_id':catalog_id, 'catalog_price':catalog_price, 'catalog_price_discount':catalog_price_discount, 'course_ids':JSON.stringify(__param_saveCatalog) },
            success: function(response) {
                $('#create-catalog').modal('hide');
                $('.course-checkbox, .course-checkbox-parent').prop('checked', false);
                course_selected = new Array();
                $("#selected_course_count").html(''); 
            }
        });
    }
    else
    {
        $('#create-catalog .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        $('#popUpMessage > .close').remove();
        scrollToTopOfPage();
    }
}

var __isBulkRequest = false;
function initFetchCatalogCourse(catalog_id)
{
    if(catalog_id == ''){
        $('#catalog_price').val('');
        $('#catalog_price_discount').val('');
    }
    if( __isBulkRequest == true )
    {
        fetchCatalogCourseBulk(catalog_id);
    }
    else
    {
        fetchCatalogCourse(catalog_id);
    }
}

function fetchCatalogCourse(catalog_id)
{
    var courseCatalogHtml = '';
        //setting selected cousre details
        courseCatalogHtml += '<div class="checkbox-wrap" id="catalog_course_'+__param_addToCatalog['id']+'" data-cost="'+__param_addToCatalog['price']+'">';
        courseCatalogHtml += '    <span class="chk-box">';
        courseCatalogHtml += '        <label class="font14">';
        courseCatalogHtml += '            '+__param_addToCatalog['title'];
        courseCatalogHtml += '        </label>';
        courseCatalogHtml += '    </span>';
        courseCatalogHtml += '    <span class="email-label pull-right">';
        courseCatalogHtml += '        <span>'+__param_addToCatalog['price']+' INR</span>';
        courseCatalogHtml += '        <span class="delete-cover"><a href="javascript:void(0)" onclick="removeCourseIdFromCatalog('+__param_addToCatalog['id']+')"><i class="icon icon-cancel-1 delte"></i></a></span>';
        courseCatalogHtml += '    </span>';
        courseCatalogHtml += '</div>';
       //end
    $('#catalog_course_list').html(courseCatalogHtml);
    
    __param_saveCatalog  = new Array();
    __param_saveCatalog.push(__param_addToCatalog['id']);
    
    if( catalog_id != '' )
    {
        $.ajax({
            url: admin_url+'course/catalog_courses',
            type: "POST",
            data:{"is_ajax":true, "catalog_id":catalog_id},
            success: function(response) {
                var data              = $.parseJSON(response);
                    courseCatalogHtml = '';
                    __totalCost       = Number(__param_addToCatalog['price']);

                if( data['catalog_courses'].length > 0 )
                {
                    for (var i=0; i < data['catalog_courses'].length; i++)
                    {
                        if( __param_addToCatalog['id'] != data['catalog_courses'][i]['id'] )
                        {
                            __totalCost        = __totalCost+Number(data['catalog_courses'][i]['cb_price']);
                            courseCatalogHtml += '<div class="checkbox-wrap" id="catalog_course_'+data['catalog_courses'][i]['id']+'" data-cost="'+data['catalog_courses'][i]['cb_price']+'">';
                            courseCatalogHtml += '    <span class="chk-box">';
                            courseCatalogHtml += '        <label class="font14">';
                            courseCatalogHtml += '            '+data['catalog_courses'][i]['cb_title'];
                            courseCatalogHtml += '        </label>';
                            courseCatalogHtml += '    </span>';
                            courseCatalogHtml += '    <span class="email-label pull-right">';
                            courseCatalogHtml += '        <span>'+data['catalog_courses'][i]['cb_price']+' INR</span>';
                            courseCatalogHtml += '        <span class="delete-cover"><a href="javascript:void(0)" onclick="removeCourseIdFromCatalog('+data['catalog_courses'][i]['id']+')"><i class="icon icon-cancel-1 delte"></i></a></span>';
                            courseCatalogHtml += '    </span>';
                            courseCatalogHtml += '</div>';
                            __param_saveCatalog.push(data['catalog_courses'][i]['id']);
                        }
                    }
                }
                //setting catlog default values
                $('#catalog_price').val(data['catalog']['c_price']);
                $('#catalog_price_discount').val(data['catalog']['c_discount']);
                $('#total_price').html(__totalCost+' INR');
                //End
                $('#catalog_course_list').append(courseCatalogHtml);
                $('#create-catalog').modal('show');
            }
        });
    }
}

function fetchCatalogCourseBulk(catalog_id)
{
    var courseCatalogHtml = '';
        __totalCost       = 0;
        //setting selected cousre details
    __param_saveCatalog  = new Array();
    if( course_selected.length > 0 )
    {
        for (var i=0; i < course_selected.length; i++)
        {
            var multi_id     = course_selected[i];
            var multi_title  = $('#course_row_'+course_selected[i]).attr('data-title');
            var multi_price  = $('#course_row_'+course_selected[i]).attr('data-price');
            __totalCost      = __totalCost + Number(multi_price);
            courseCatalogHtml += '<div class="checkbox-wrap" id="catalog_course_'+multi_id+'" data-cost="'+multi_price+'">';
            courseCatalogHtml += '    <span class="chk-box">';
            courseCatalogHtml += '        <label class="font14">';
            courseCatalogHtml += '            '+multi_title;
            courseCatalogHtml += '        </label>';
            courseCatalogHtml += '    </span>';
            courseCatalogHtml += '    <span class="email-label pull-right">';
            courseCatalogHtml += '        <span>'+multi_price+' INR</span>';
            courseCatalogHtml += '        <span class="delete-cover"><a href="javascript:void(0)" onclick="removeCourseIdFromCatalog('+multi_id+')"><i class="icon icon-cancel-1 delte"></i></a></span>';
            courseCatalogHtml += '    </span>';
            courseCatalogHtml += '</div>';
            __param_saveCatalog.push(multi_id);
        }
    }
       //end
    $('#catalog_course_list').html(courseCatalogHtml);
    
    if( catalog_id != '' )
    {
        $.ajax({
            url: admin_url+'course/catalog_courses',
            type: "POST",
            data:{"is_ajax":true, "catalog_id":catalog_id},
            success: function(response) {
                var data              = $.parseJSON(response);
                    courseCatalogHtml = '';

                if( data['catalog_courses'].length > 0 )
                {
                    for (var i=0; i < data['catalog_courses'].length; i++)
                    {
                        if( !inArray(data['catalog_courses'][i]['id'], course_selected) )
                        {
                            __totalCost        = __totalCost+Number(data['catalog_courses'][i]['cb_price']);
                            courseCatalogHtml += '<div class="checkbox-wrap" id="catalog_course_'+data['catalog_courses'][i]['id']+'" data-cost="'+data['catalog_courses'][i]['cb_price']+'">';
                            courseCatalogHtml += '    <span class="chk-box">';
                            courseCatalogHtml += '        <label class="font14">';
                            courseCatalogHtml += '            '+data['catalog_courses'][i]['cb_title'];
                            courseCatalogHtml += '        </label>';
                            courseCatalogHtml += '    </span>';
                            courseCatalogHtml += '    <span class="email-label pull-right">';
                            courseCatalogHtml += '        <span>'+data['catalog_courses'][i]['cb_price']+' INR</span>';
                            courseCatalogHtml += '        <span class="delete-cover"><a href="javascript:void(0)" onclick="removeCourseIdFromCatalog('+data['catalog_courses'][i]['id']+')"><i class="icon icon-cancel-1 delte"></i></a></span>';
                            courseCatalogHtml += '    </span>';
                            courseCatalogHtml += '</div>';
                            __param_saveCatalog.push(data['catalog_courses'][i]['id']);
                        }
                    }
                }
                //setting catlog default values
                $('#catalog_price').val(data['catalog']['c_price']);
                $('#catalog_price_discount').val(data['catalog']['c_discount']);
                $('#total_price').html(__totalCost+' INR');
                //End
                $('#catalog_course_list').append(courseCatalogHtml);
                $('#create-catalog').modal('show');
            }
        });
    }
}

function removeCourseIdFromCatalog(course_id)
{
    if( __isBulkRequest == true)
    {
        course_selected.pop(course_id);
        $("#selected_course_count").html(' ('+course_selected.length+')');
        $('#course_details_'+course_id).prop('checked',false);
    }
    __param_saveCatalog.pop(course_id);
    __totalCost = __totalCost - Number($('#catalog_course_'+course_id).attr('data-cost'));
    $('#total_price').html(__totalCost+' INR');
    $('#catalog_course_'+course_id).remove();
}

function addToCatalogBulk()
{
    $('#catalog_price').val('');
    $('#catalog_price_discount').val('');
    if( course_selected.length == 0)
    {
        $('#add_catalog_null').modal('show');
        return false;
    }
    if($('.item-deleted:checked').length > 0)
    {
        $('#notify_deleted').modal('show');
        return false;
    }

    if($('.item_inactive:checked').length > 0)
    {
        $('#notify_inactive').modal('show');
        return false;
    }

    __isBulkRequest = true;
    if( __create_catalog_as_new == true)
    {
        __create_catalog_as_new = false;
        $('#create_new_catalog_cancel, #catalog_name').hide();
        $('#create_new_catalog, #catalog_id').show();
    }
    var courseCatalogHtml           = '';
        __param_saveCatalog         = new Array();
        __totalCost                 = 0;
        
    if( course_selected.length > 0 )
    {
        $.ajax({
            url: admin_url+'course/check_inactive_courses',
            type: "POST",
            data:{"is_ajax":true, 'course_id': JSON.stringify(course_selected)},
            success: function(response) {
                var courses_inactive_list = new Array();
                var data_course           = JSON.parse(response);
                if(data_course['courses'].length > 0 ){
                    for (var i=0; i<data_course['courses'].length; i++)
                    {   
                        courses_inactive_list.push(data_course['courses'][i]['inactive_courses'].length);
                    }        
                }
                var found = courses_inactive_list.indexOf(1);
                if(found == '0')
                {
                    $('#inactive_course').modal('show');
                }
                else
                {
                        for (var i=0; i < course_selected.length; i++)
                        {
                            var multi_id     = course_selected[i];
                            var multi_title  = $('#course_row_'+course_selected[i]).attr('data-title');
                            var multi_price  = $('#course_row_'+course_selected[i]).attr('data-price');
                            __totalCost      = __totalCost + Number(multi_price);
                            courseCatalogHtml += '<div class="checkbox-wrap" id="catalog_course_'+multi_id+'" data-cost="'+multi_price+'">';
                            courseCatalogHtml += '    <span class="chk-box">';
                            courseCatalogHtml += '        <label class="font14">';
                            courseCatalogHtml += '            '+multi_title;
                            courseCatalogHtml += '        </label>';
                            courseCatalogHtml += '    </span>';
                            courseCatalogHtml += '    <span class="email-label pull-right">';
                            courseCatalogHtml += '        <span>'+multi_price+' INR</span>';
                            courseCatalogHtml += '        <span class="delete-cover"><a href="javascript:void(0)" onclick="removeCourseIdFromCatalog('+multi_id+')"><i class="icon icon-cancel-1 delte"></i></a></span>';
                            courseCatalogHtml += '    </span>';
                            courseCatalogHtml += '</div>';
                            __param_saveCatalog.push(multi_id);
                        }

                                                //setting selected cousre details
                        $('#catalog_course_list').html(courseCatalogHtml);
                        $('#total_price').html(__totalCost+' INR');
                        //end
                        
                        $.ajax({
                            url: admin_url+'course/catalogs',
                            type: "POST",
                            data:{"is_ajax":true},
                            success: function(response) {
                                var catalogHtml = '<option value="">Choose '+lang('catalog')+'</option>';
                                var data        = $.parseJSON(response);
                                var catalog_name = '';
                                if( data['catalogs'].length > 0 )
                                {
                                    for (var i=0; i < data['catalogs'].length; i++)
                                    {
                                        catalog_name = data['catalogs'][i]['c_title'];
                                        catalog_name = (catalog_name.length > 37)?(catalog_name.substr( 0, 34)+'..'):catalog_name;
                                        catalogHtml += '<option value="'+data['catalogs'][i]['id']+'">'+catalog_name+'</option>';
                                    }
                                }
                                $('#catalog_id').html(catalogHtml);
                                $('#create-catalog').modal('show');
                            }
                        });
                }
            }
        }); 

    }
    
}


function createCourse(header_text, label)
{
    cleanPopUpMessage();
    $('#course_code').val('');
    $('#course_name').val('');
    $('#create_course').modal();
}

function createCourseConfirmed()
{
    var courseName      = $('#course_name').val();
        courseName      =  courseName.replace(/["<>{}]/g, '');
        courseName      =  courseName.trim();
    var courseCode      = $('#course_code').val();
    var errorCount       = 0;
    var errorMessage     = '';
    
    if(courseCode == '')
    {
        errorCount++;
        errorMessage += 'Enter Course code<br />';            
    }
    if( courseName == '')
    {
        errorCount++;
        errorMessage += 'Enter Course name <br />';
    }
    
    cleanPopUpMessage();
    if( errorCount == 0 )
    {
        $.ajax({
            url: admin_url+'course/create_course',
            type: "POST",
            data:{"is_ajax":true, 'course_name':courseName, 'course_code':courseCode, async:false},
            success: function(response) { 
                var data  = $.parseJSON(response);
                if(data.error == false)
                {
                    window.location = admin_url+'course/basic/'+data['id'];
                }
                else
                {
                    $('#create_course .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    $('#popUpMessage > .close').remove();
                    scrollToTopOfPage();
                }
            }
        });
    }
    else
    {
        $('#create_course .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        //$('#popUpMessage > .close').remove();
        scrollToTopOfPage();
    }
}


function renderCourseHtml(response)
{
    // $("#selected_course_count").html(''); 
    var data        = $.parseJSON(response);
    var courseHtml  = '';
    if(data['courses'].length > 0 )
    {
        for (var i=0; i<data['courses'].length; i++)
        {
            
            courseHtml += '<div class="rTableRow" id="course_row_'+data['courses'][i]['id']+'" data-title="'+data['courses'][i]['cb_title']+'">';
            
            courseHtml += renderCourseRow(data['courses'][i]);

            courseHtml += '</div> ';
        }
    }
    return courseHtml;
}
function formatDate (input) {
    var datePart = input.match(/\d+/g),
    year = datePart[0], // get only two digits
    month = datePart[1], day = datePart[2];
  
    return day+'/'+month+'/'+year;
  }
  
 
function renderCourseRow(data) {

    var courseHtml = '';
    if(data) {
        //consider the record is deleted and set the value if record deleted
        var label_class    = 'spn-delete';
        var action_class   = 'label-danger';
        var item_deleted   = 'item-deleted';
        var disabled       = 'disabled="disabled"';
        var item_inactive  = '' ;
        var action         = lang('deleted');     
        //case if record is not deleted
        if(data['cb_deleted'] == 0)
        {
            item_deleted = '';                
            switch (data['cb_status']) {
                case '1':
                    action_class   = 'label-success';                                                                
                    label_class    = 'spn-active';                                        
                    action         = lang('active');                            
                break;
                
                case '2':
                    action_class   = 'label-warning';                                                                
                    label_class    = 'spn-inactive';    
                    item_inactive  = 'item_inactive';                                     
                    action         = lang('pending_approval');                            
                break;
                    
                default:
                    action_class   = 'label-warning';                                                                
                    label_class    = 'spn-inactive';   
                    item_inactive  = 'item_inactive';                                      
                    action         = lang('inactive');
                break;
            }
        }

        courseHtml += '    <div class="rTableCell cours-fix ellipsis-hidden nopadding"> ';
        courseHtml += '        <div class="ellipsis-style display-initial">';  
        courseHtml += '            <input type="checkbox" class="course-checkbox '+item_deleted+' '+item_inactive+'" value="'+data['id']+'" id="course_details_'+data['id']+'" '+ ((data['cb_deleted'] == 1)?disabled:'') +'>'; 
        
        if(data['cb_deleted'] != '1'){
        courseHtml += '            <a href="'+admin_url+'course/basic/'+data['id']+'" class="cust-sm-6 padd0"> ';
        }
        courseHtml += '                 <span class="icon-wrap-round color-box" data-name="'+data['cb_title'].replace(/'/g, "\\'")+'">';
        courseHtml += '                     <i class="icon icon-graduation-cap"></i>';
        courseHtml += '                 </span>';

        courseHtml += '             <span class="institution-code">'+data['cb_code'].toUpperCase()+'</span> - '+data['cb_title'].replace(/'/g, "\\'")+'';
        if(data['cb_deleted'] != '1'){
        courseHtml += '             </a>';
        }
        // courseHtml += '             '+admin_label+'';
        courseHtml += '        </div>';
        courseHtml += '    </div>';
        var cb_date='';
        if(data['cb_access_validity'] == 0){
            cb_date = '<span class="text-green">Unlimited</span>';
        }else if(data['cb_access_validity'] == 1){
            cb_date = '<span class="text-green">'+data['cb_validity']+" days</span>";
        }else if(data['cb_access_validity'] == 2){
            var end = new Date();
                end.setHours(23,59,59,59);
            var starttime = new Date(data['cb_validity_date']);
                starttime.setHours(23,59,59,59);
            var current_date   = parseInt((end.getTime() / 1000).toFixed(0))
            var db_date        = parseInt((new Date(starttime).getTime() / 1000).toFixed(0));
           //console.log(current_date,db_date, starttime);
            if(current_date > db_date){
                cb_date = '<span class="text-red">Expired</span>';
            }else if(current_date==db_date){
                cb_date = '<span class="text-orange">Today</span>';
            }else{
                cb_date ='<span class="text-green">'+ formatDate (data['cb_validity_date'])+'</span>';
            }
        }
        courseHtml += '    <div class="rTableCell text-center ">';
        courseHtml += cb_date;
        courseHtml += '    </div>';
        courseHtml += '    <div class="rTableCell pad0 cours-fix width70">';
        courseHtml += '        <div class="col-sm-12 pad0">';
        courseHtml += '            <label class="pull-right label '+action_class+'" id="action_class_'+data['id']+'">';
        courseHtml +=               action;
        courseHtml += '            </label>';
        courseHtml += '        </div>';
        courseHtml += '    </div>';
        courseHtml += '    <div class="td-dropdown rTableCell">';
        courseHtml += '        <div class="btn-group lecture-control">';
        courseHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
        courseHtml += '                 <span class="label-text">';
        courseHtml += '                  <i class="icon icon-down-arrow"></i>';
        courseHtml += '                </span>';
        courseHtml += '                <span class="tilder"></span>';
        courseHtml += '            </span>';
        courseHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="course_action_'+data['id']+'">'; 
        if(data['cb_deleted'] == 0){
            
                courseHtml += '                    <li id="status_btn_'+data['id']+'">';
                var cb_status = (data['cb_status']==1)?'deactivate':'activate'; 
                var cb_action = (data['cb_status']==1)?'unpublish':'publish'; 
                courseHtml += '                        <a href="javascript:void(0);" onclick="changeCourseStatus(\''+data['id']+'\', \''+cb_status+'\',\''+data['cb_title'].replace(/'/g, "\\'")+'\')" >'+lang(cb_status)+'</a>';
                courseHtml += '                    </li>';
            
            courseHtml += '                    <li>';
            courseHtml += '                        <a href="'+webConfigs('admin_url')+'course_settings/basics/'+data['id']+'">'+'Settings'+'</a>';
            courseHtml += '                    </li>';
            courseHtml += '                    <li>';
            courseHtml += '                        <a href="javascript:void(0);" id="delete_btn_'+data['id']+'" onclick="deleteCourse(\''+ data['id'] +'\',\''+ data['cb_title'].replace(/'/g, "\\'") +'\')" >'+lang('delete')+'</a>';
            courseHtml += '                    </li>';
        }
        else
        {
            courseHtml += '                    <li>';
            courseHtml += '                        <a href="javascript:void(0);" id="restore_btn_'+data['id']+'" onclick="restoreCourse(\''+ data['id'] +'\',\''+ data['cb_title'].replace(/'/g, "\\'") +'\')" >'+lang('restore')+'</a>';
            courseHtml += '                    </li>';
            courseHtml += '                    <li>';
            courseHtml += '                        <a href="javascript:void(0);" id="restore_btn_'+data['id']+'" onclick="deleteCoursePermanently(\''+ data['id'] +'\',\''+ data['cb_title'].replace(/'/g, "\\'") +'\',\''+ data['id'] +'\')" >Delete Permanently</a>';
            courseHtml += '                    </li>';
        }
        courseHtml += '            </ul>';
        courseHtml += '        </div>';
        courseHtml += '    </div>';
    }
    return courseHtml;    
}

var __create_catalog_as_new = false;
$(document).on('click', '#create_new_catalog', function(){
    __create_catalog_as_new = true;
    $('#create_new_catalog, #catalog_id').hide();
    $('#create_new_catalog_cancel, #catalog_name').show();
    $('#catalog_id').val("");
    
});

$(document).on('click', '#create_new_catalog_cancel', function(){
    __create_catalog_as_new = false;
    $('#create_new_catalog_cancel, #catalog_name').hide();
    $('#create_new_catalog, #catalog_id').show();
    $('#catalog_name').val("");
});
function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}
