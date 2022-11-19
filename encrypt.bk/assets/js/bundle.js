
var __filter_dropdown           = 'active';
var __gettingBundleInProgress   = false;
var __category_id               = '';
var course_selected = new Array();
$(document).ready(function(){

    //Bundle listing section 

    var keyword         = getQueryStringValue('keyword');
    var category_id     = getQueryStringValue('category');
    var filter          = getQueryStringValue('filter');

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
    
    setBundleBulkAction();
    if(__filter_dropdown == "deleted"){
        $('.select-all-style').hide();
    } else {
        $('.select-all-style').show();
    }

      //Bundle edit section
      $("#site_logo_btn").change(function() {
        readImageData(this); //Call image read and render function
    });
});
    $(document).on('click', '#basic-addon2', function(){
        var course_keyword = $('#course_keyword').val().trim();        
            if(course_keyword == '')
            {
                lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
            }
            else{
                __offset = 1;
                getBundles();
                scrollToTopOfPage();
                $("#selected_course_count").html(''); 
            }
    });

    var timeOut = '';
    $(document).on('keyup', '#course_keyword', function(){
        clearTimeout(timeOut);
        timeOut = setTimeout(function(){
            __offset = 1;
            getBundles();
        }, 600);
        scrollToTopOfPage();
        $("#selected_course_count").html(''); 
    });
    
    $(document).on('click', '#searchclear', function(){
        __offset = 1;
        getBundles();
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

    function setBundleBulkAction() {
        var bundle_bulk = '';
        if(__filter_dropdown != 'deleted')
        {
            bundle_bulk   += '<span class="dropdown-tigger" data-toggle="dropdown">';
            bundle_bulk   += '       <span class="label-text">';
            bundle_bulk   += '       Bulk Action ';
            bundle_bulk   += '       </span>';
            bundle_bulk   += '       <span class="tilder"></span>';
            bundle_bulk   += '   </span>';
            bundle_bulk   += '   <ul class="dropdown-menu pull-right" role="menu">';
            if(__filter_dropdown != 'active')
                bundle_bulk   += '       <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk(\'1\')"> Make Public </a></li>';
            if(__filter_dropdown != 'inactive')
                bundle_bulk   += '       <li><a href="javascript:void(0)" class="list-disabled list-button" onclick="changeStatusBulk(\'0\')"> Make Private</a></li>';
            bundle_bulk   += '   </ul>';
        
        }
        $('#course_bulk').html(bundle_bulk);
    }
    $(document).on('click', '#basic-search', function(){
        var bundle_keyword = $('#bundle_keyword').val().trim();        
            if(bundle_keyword == '')
            {
                lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
            }
            else{
                __offset = 1;
                getBundles();
                scrollToTopOfPage();
                $("#selected_course_count").html(''); 
            }
    });

    function getBundles()
    {
        if(__gettingBundleInProgress == true)
        {
            return false;
        }
        __gettingBundleInProgress = true;
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
            url: admin_url+'bundle/index',
            type: "POST",
            data:{"is_ajax":true,"filter":__filter_dropdown,"keyword":keyword,"category":__category_id,'limit':__limit,'offset':__offset},
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
                if(data.bundles.length > 0){
                    if(__offset == 2)
                    {
                        __totalUsers = data.total_bundles;
                        __shownUsers = data.bundles.length;
                        remainingBundle = (data.total_bundles - data.bundles.length);
                        var totalbundleHtml = data.bundles.length+' / '+data.total_bundles+' '+((data.total_bundles == 1)?"Bundle":"Bundles");
                        scrollToTopOfPage();
                        $('.course-count').html(totalbundleHtml);
                        $('#course_row_wrapper').html(renderbundleHtml(response));
                    }
                    else
                    {
                        __totalUsers = data.total_bundles;
                        __shownUsers = ((__offset - 2) * data.limit) + data.bundles.length;
                        remainingBundle = (data.total_bundles - (((__offset-2)*data.limit)+data.bundles.length));
                        var totalbundleHtml = (((__offset-2)*data.limit)+data.bundles.length)+' / '+data.total_bundles+' Bundles';
                        $('.course-count').html(totalbundleHtml);
                        $('#course_row_wrapper').append(renderbundleHtml(response));                     
                    }
                }
                else
                {
                    $('.course-count').html("No Bundles");
                    $('#course_row_wrapper').html(renderPopUpMessage('error', 'No Bundles found.'));
                    $('#popUpMessage > .close').remove();
                }
                if(data.show_load_button == true)
                {
                    $('#loadmorebutton').show();
                    remainingBundle = (remainingBundle>0)?'('+remainingBundle+')':'';
                    $('#loadmorebutton').html('Load More '+remainingBundle+'<ripples></ripples>');
                }
                
                __gettingBundleInProgress = false;
            }
        });
    }

    function loadMoreBundle(){
        getBundles();
    }

    function renderbundleHtml(response)
    {
        var data        = $.parseJSON(response);
        var bundleHtml  = '';
        if(data.bundles.length > 0 )
        {
            for (var i=0; i<data.bundles.length; i++)
            {
                
                bundleHtml += '<div class="rTableRow bundle-list" id="course_row_'+data.bundles[i].id+'" data-title="'+data.bundles[i].c_title+'">';
                
                bundleHtml += renderBundleRow(data.bundles[i]);

                bundleHtml += '</div> ';
            }
        }
        return bundleHtml;
    }


    function renderBundleRow(data) {
        var bundleHtml = '';
        if(data) {
            var label_class    = 'spn-delete';
            var action_class   = 'label-danger';
            var item_deleted   = 'item-deleted';
            var disabled       = 'disabled="disabled"';
            var item_inactive  = '' ;
            var action         = lang('deleted'); 
            var bundle_items   = '';    
            var bundle_items_count = '';
            var bundle_items_size  = 0;
            //case if record is not deleted
            if(data.c_courses === null || data.c_courses === "null" || data.c_courses === '' || data.c_courses === "undefined")
            {
                bundle_items_size      = 0;
            }else{
                bundle_items       = $.parseJSON(data.c_courses);
                //console.log(data);
                bundle_items_size      = bundle_items.length;
            }
            var bundle_items_count = (bundle_items_size>1)?' Items' :' Item'; 

                
            var bundle_counts_text = ' ( '+bundle_items_size+bundle_items_count+' )';
              
                  //case if record is not deleted
            if(data.c_deleted == 0)
            {
                switch (data.c_status) {
                    case '1':
                        action_class   = 'label-success';                                                                
                        label_class    = 'spn-active';                                        
                        action         = lang('bundle_active');                            
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
                        action         = lang('bundle_inactive');
                    break;
                }
            }
            bundleHtml += '    <div class="rTableCell cours-fix ellipsis-hidden nopadding"> ';
            bundleHtml += '        <div class="ellipsis-style display-initial">';  
            bundleHtml += '            <input type="checkbox" class="course-checkbox '+item_deleted+' '+item_inactive+'" value="'+data.id+'" id="course_details_'+data.id+'" '+ ((data.c_deleted == 1)?disabled:'') +'>'; 
            
            if(data.c_deleted != '1'){
            bundleHtml += '            <a href="'+admin_url+'bundle/overview/'+data.id+'" class="cust-sm-6 padd0"> ';
            }
            bundleHtml += '                 <span class="icon-wrap-round color-box bundle-icon" data-name="'+data.c_title+'">';
            bundleHtml += '                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 510.4 334" style="enable-background:new 0 0 510.4 334;" xml:space="preserve">';
            bundleHtml += '                     <style type="text/css">';
            bundleHtml += '                         .st0{fill:#fdeeee;}';
            bundleHtml += '                     </style>';
            bundleHtml += '    <g>';
            bundleHtml += '        <path class="st0" d="M218,236.5L87.9,193.3v42.5v29.7c0,33.4,58.3,60.4,130.1,60.4c71.8,0,130.1-27,130.1-60.4   c0-0.3-0.1-0.5-0.1-0.8v-71.4L218,236.5z"/>';
            bundleHtml += '        <path class="st0" d="M0,143.6l46.5,17.9l4-9.2l17.1-1.6l2.4,2.7l-14.6,3.7l-2.1,6.8c0,0-33.1,74.6-28.2,111.2c0,0,20.7,13.3,41.3,0   l5.5-99.8v-8.3l30.7-7.5l-2.2,5.8l-22.9,8l10.6,4.1L218,220.8l130.1-43.2l87.9-33.9L218,53.1L0,143.6z"/>';
            bundleHtml += '    </g>';
            bundleHtml += '    <path class="st0" d="M371.1,267.5c0,0-1.3,15.3-8.2,28.2c38.1-10.5,63.6-29.8,63.6-51.9c0-0.3-0.1-0.5-0.1-0.8v-71.4l-55.6,18.5l0,0  L371.1,267.5z"/>';
            bundleHtml += '    <polygon  class="st0" points="514.4,122 296.4,31.4 254,49 468.1,139.8 "/>';
            bundleHtml += '</svg>';
            bundleHtml += '</span>';

            bundleHtml += '             <span class="institution-code">'+data.c_code+'</span> - '+data.c_title+''+bundle_counts_text;
            if(data.c_deleted != '1'){
            bundleHtml += '             </a>';
            }
            bundleHtml += '        </div>';
            bundleHtml += '    </div>';
            var c_date='';
            if(data.c_access_validity == 0){
                c_date = '<span class="text-green">Unlimited</span>';
            }else if(data.c_access_validity == 1){
                c_date = '<span class="text-green">'+data.c_validity+" days</span>";
            }else if(data.c_access_validity == 2){
                var end = new Date();
                    end.setHours(23,59,59,59);
                var starttime = new Date(data.c_validity_date);
                    starttime.setHours(23,59,59,59);
                var current_date   = parseInt((end.getTime() / 1000).toFixed(0))
                var db_date        = parseInt((new Date(starttime).getTime() / 1000).toFixed(0));
               //console.log(current_date,db_date, starttime);
                if(current_date > db_date){
                    c_date = '<span class="text-red">Expired</span>';
                }else if(current_date==db_date){
                    c_date = '<span class="text-orange">Today</span>';
                }else{
                    c_date ='<span class="text-green">'+ formatDate (data.c_validity_date)+'</span>';
                }
            }
            bundleHtml += '    <div class="rTableCell text-center ">';
            bundleHtml += c_date;
            bundleHtml += '    </div>';
            bundleHtml += '    <div class="rTableCell pad0 cours-fix width70">';
            bundleHtml += '        <div class="col-sm-12 pad0">';
            bundleHtml += '            <label class="pull-right label '+action_class+'" id="action_class_'+data.id+'">';
            bundleHtml +=               action;
            bundleHtml += '            </label>';
            bundleHtml += '        </div>';
            bundleHtml += '    </div>';
            bundleHtml += '    <div class="td-dropdown rTableCell">';
            bundleHtml += '        <div class="btn-group lecture-control">';
            bundleHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            bundleHtml += '                 <span class="label-text">';
            bundleHtml += '                  <i class="icon icon-down-arrow"></i>';
            bundleHtml += '                </span>';
            bundleHtml += '                <span class="tilder"></span>';
            bundleHtml += '            </span>';
            bundleHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="bundle_action_'+data.id+'">'; 
            if(data.c_deleted == 0){
                
                    bundleHtml += '                    <li id="status_btn_'+data.id+'">';
                    var c_status        = (data.c_status==1)?'deactivate':'activate'; 
                    var language_status = (data.c_status==1)?'bundle_deactivate':'bundle_activate';
                    bundleHtml += '                        <a href="javascript:void(0);" onclick="changeBundlestatus(\''+data.id+'\', \''+c_status+'\',\''+encodeURI(data.c_title)+'\')" >'+lang(language_status)+'</a>';
                    bundleHtml += '                    </li>';
                
                bundleHtml += '                    <li>';
                bundleHtml += '                        <a href="'+webConfigs('admin_url')+'bundle/basic/'+data.id+'">'+'Settings'+'</a>';
                bundleHtml += '                    </li>';
                bundleHtml += '                    <li>';
                bundleHtml += '                        <a href="javascript:void(0);" id="delete_btn_'+data.id+'" onclick="deleteBundle(\''+ data.id +'\', \''+c_status+'\',\''+ encodeURI(data.c_title) +'\')" >'+lang('delete')+'</a>';
                bundleHtml += '                    </li>';
            }
            else
            {
                bundleHtml += '                    <li>';
                bundleHtml += '                        <a href="javascript:void(0);" id="restore_btn_'+data.id+'" onclick="restoreBundle(\''+ data.id +'\',\''+ encodeURI(data.c_title) +'\')" >'+lang('restore')+'</a>';
                bundleHtml += '                    </li>';
            }
            bundleHtml += '            </ul>';
            bundleHtml += '        </div>';
            bundleHtml += '    </div>';
        }
        return bundleHtml;    
    }
    function formatDate (input) {
        var datePart = input.match(/\d+/g),
        year = datePart[0], // get only two digits
        month = datePart[1], day = datePart[2];
      
        return day+'/'+month+'/'+year;
    }
    function filter_category(category_id)
    {
    __category_id        = category_id;
    __offset             = 1;
    $('#dropdown_text').html('<span style="width:100%;max-width:240px;" title="'+ $('#dropdown_list_' + category_id).text() +'" class="category-text-ellipsis">' + $('#dropdown_list_'+category_id).text()+'</span><span class="caret" style="margin-top: -8px;"></span>');
    getBundles();
    scrollToTopOfPage();
        $("#selected_course_count").html(''); 
    }

    function filter_course_by(filter)
    {  
        __offset             = 1;
        __filter_dropdown    = filter;
        $('#filter_dropdown_text').html($('#filer_dropdown_list_'+filter).text()+'<span class="caret"></span>');
        getBundles();
        scrollToTopOfPage();
        setBundleBulkAction();
            $("#selected_course_count").html(''); 
            if(__filter_dropdown == "deleted"){
                $('.select-all-style').hide();
            } else {
                $('.select-all-style').show();
            }
        
    }

    function createBundle()
    {
        cleanPopUpMessage();
        $('#bundle_code').val('');
        $('#bundle_name').val('');
        $('#create_bundle').modal();
    }

    function createBundleConfirmed()
    {
        var bundleName      = $('#bundle_name').val();
            bundleName      = bundleName.replace(/["<>{}]/g, '');
            bundleName      = bundleName.trim();
        var bundleCode      = $('#bundle_code').val().trim();
        var errorCount      = 0;
        var errorMessage    = '';
        
        if(bundleCode == '')
        {
            errorCount++;
            errorMessage += 'Enter Bundle code<br />';            
        }
        if( bundleName == '')
        {
            errorCount++;
            errorMessage += 'Enter Bundle name <br />';
        }
        
        cleanPopUpMessage();
        if( errorCount == 0 )
        {
            $.ajax({
                url: admin_url+'bundle/create_bundle',
                type: "POST",
                data:{"is_ajax":true, 'bundle_name':bundleName, 'bundle_code':bundleCode},
                success: function(response) {
                    var data  = $.parseJSON(response);
                    if(data.error == false)
                    {
                        window.location = admin_url+'bundle/overview/'+data.id;
                    }
                    else
                    {
                        $('#create_bundle .modal-body').prepend(renderPopUpMessage('error', data.message));
                        // $('#popUpMessage > .close').remove();
                        scrollToTopOfPage();
                    }
                }
            });
        }
        else
        {
            $('#create_bundle .modal-body').prepend(renderPopUpMessage('error', errorMessage));
            // $('#popUpMessage > .close').remove();
            scrollToTopOfPage();
        }
    }

    function getQueryStringValue(key) {
        return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
    }

    function deleteBundle(bundle_id,bundle_status, bundle_name)
    {
        bundle_name = decodeURI(bundle_name);
        $.ajax({
            url: admin_url+'bundle/delete_check',
            type: "POST",
            data:{"bundle_id":bundle_id, "is_ajax":true},
            success: function(response) {
                var data            = $.parseJSON(response); 
                if(data.success == true){
                    var header_text     = 'Are you sure to Delete Bundle named '+bundle_name+' ? ';
                    var message         = data.message+header_text;
                    var params          = { 
                        'data': {
                        'bundle_id': bundle_id
                        },
                    };
                    if(bundle_status=='deactivate') {
                        var messageObject = {
                            'body': message,
                            'button_yes': 'DELETE',
                            'button_no': '<span onclick="deactivateBundle(\''+bundle_id+'\')">MAKE PRIVATE, INSTEAD<span>',
                            'continue_params': {
                                'bundle_id': bundle_id
                            },
                        };
                    }
                    else
                    {
                        var messageObject = {
                            'body': message,
                            'button_yes': 'DELETE',
                            'button_no': 'CANCEL',
                            'continue_params': {
                                'bundle_id': bundle_id
                            },
                        };
                    }
                  
                    callback_warning_modal(messageObject, deleteBundleConfirmed);
                }
            }
        });        
    }

    function deleteBundleConfirmed(params){

        var bundle_id   = params.data.bundle_id;
        $.ajax({
            url: admin_url+'bundle/delete',
            type: "POST",
            data:{"bundle_id":bundle_id, "is_ajax":true},
            success: function(response) {
                
                var data  = $.parseJSON(response);
                console.log(data);
                if(data.error == false)
                {
                    if(__filter_dropdown == 'all') {
                        $('#course_row_' + bundle_id).html(renderBundleRow(data.bundle));
                    } else {
                        $('#course_row_' + bundle_id).remove();
                        __totalUsers = __totalUsers - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalUsers == 0 && __shownUsers == 0) {
                            var totalUsersHtml = 'No Bundles';
                        } else {
                            var totalUsersHtml = __shownUsers +' / '+__totalUsers + ' Bundles';
                        }
                        $('.course-count').html(totalUsersHtml);
                    }
                    var messageObject = {
                        'body': 'Bundle deleted successfully',
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                } else {
                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                    };
                    callback_danger_modal(messageObject);
                }
                clearCacheBundle();
            }
        });
        
    }

    function changeBundlestatus(bundle_id, status, bundle_name)
    {
        bundle_name = decodeURI(bundle_name);
        var action  = '';
        switch(status) {
            case "deactivate":
                ok_button_text = 'MAKE PRIVATE';
                action         = 'private';
            break;
            case "activate":
                ok_button_text = 'MAKE PUBLIC';
                action         = 'public';
            break;
        }
        var header_text = 'Are you sure to make the bundle named "' + bundle_name + '" '+ action+'?';

        var messageObject = {
            'body': header_text,
            'button_yes': ok_button_text,
            'button_no': 'CANCEL',
            'continue_params': {
                'bundle_id': bundle_id
            },
        };
        callback_warning_modal(messageObject, changeStatusConfirmed);
    }

    function deactivateBundle(bundle_id){
        $("#common_message_advanced").modal('hide');
        $.ajax({
            url: admin_url+'bundle/change_status',
            type: "POST",
            data:{"bundle_id":bundle_id, "is_ajax":true},
            success: function(response) {
                
                var data  = $.parseJSON(response);
                if(data.error == false)
                {
                    if (__filter_dropdown == 'all') {
                        $('#course_row_' + bundle_id).html(renderBundleRow(data.bundle));
                    } else {
                        $('#course_row_' + bundle_id).remove();
                        __totalUsers = __totalUsers - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalUsers == 0 && __shownUsers == 0) {
                            var totalUsersHtml = 'No Bundles';
                        } else {
                            var totalUsersHtml = __shownUsers +' / '+__totalUsers + ' Bundles';//__shownUsers + ' / ' + __totalUsers + ' Students';
                        }
                        $('.course-count').html(totalUsersHtml);
                    }

                    var messageObject = {
                        'body': 'Bundle status changed successfully',
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                    // scrollToTopOfPage();                
                }
                else
                {
                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                    };
                    callback_danger_modal(messageObject);
                }
                clearCacheBundle();
            }
        });
       
    }

    function changeStatusConfirmed(params){
        //console.log(params);
        var bundle_id = params.data.bundle_id;
        $.ajax({
            url: admin_url+'bundle/change_status',
            type: "POST",
            data:{"bundle_id":bundle_id, "is_ajax":true},
            success: function(response) {
                
                var data  = $.parseJSON(response);
                if(data.error == false)
                {
                    if (__filter_dropdown == 'all') {
                        $('#course_row_' + bundle_id).html(renderBundleRow(data.bundle));
                    } else {
                        $('#course_row_' + bundle_id).remove();
                        __totalUsers = __totalUsers - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalUsers == 0 && __shownUsers == 0) {
                            var totalUsersHtml = 'No Bundles';
                        } else {
                            var totalUsersHtml = __shownUsers +' / '+__totalUsers + ' Bundles';//__shownUsers + ' / ' + __totalUsers + ' Students';
                        }
                        $('.course-count').html(totalUsersHtml);
                    }

                    var messageObject = {
                        'body': 'Bundle status changed successfully',
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                    // scrollToTopOfPage();                
                }
                else
                {
                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                    };
                    callback_danger_modal(messageObject);
                }
                clearCacheBundle();
            }
        });
    }

    var statusMessage = '';
    function changeStatusBulk(status)
    {
        var ok_button_text  = '';
        if(status == '1'){
            statusMessage   = 'activated';
            ok_text         = 'PUBLIC';
            ok_button_text  = 'MAKE PUBLIC';
        }
        else {
            statusMessage   = 'deactivated';
            ok_text         = 'PRIVATE';
            ok_button_text  = 'MAKE PRIVATE';
        }       
        var header_text     = 'Are you sure to make the selected bundles '+ok_text +'?';
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
            url: admin_url+'bundle/change_status_bulk',
            type: "POST",
            data:{"bundles":JSON.stringify(course_selected), "status_bulk":status, "is_ajax":true},
            success: function(response) {
                var data  = $.parseJSON(response);
                console.log(data);
                if(data.error == true)
                {
                    var bundleNames = data.error_bundles;
                    var errorMsg  = "";
                        errorMsg += "<h4>Activation failed for the following bundles,</h4>";
                        var j= 1;
                        errorMsg += '<div class="text-left" style="width: 300px;margin: 0 auto;font-size: 16px;">';
                        for(var i=0;i<bundleNames.length;i++){
                            errorMsg += '<p>'+j+' . '+ bundleNames[i]['bundle_name'] +'</p>';
                            j++;
                        }
                        errorMsg += '</div>';
                        errorMsg += '<div class="text-center bundleenroll-notes">';
                        errorMsg += '<p class="bundleenroll-title">Note : following maybe the reason for failure</p>';
                        errorMsg += '<p>* Bundle image missing </p>';
                        errorMsg += '<p>* Bundle description missing</p>';
                        errorMsg += '<p>* Bundle category missing</p>';
                        errorMsg += '<p>* Courses not included in bundle</p>';
                        errorMsg += '</div>';
                       
                    var messageObject = {
                        'body': errorMsg,
                        'button_yes': 'OK',
                        'prevent_button_no': true
                    };
                    callback_danger_modal(messageObject);
                } 
                else 
                {
                    var message = (status == '1') ? 'public' : 'private';
                    var messageObject = {
                        'body': 'Bundle made '+ message +' successfully',
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                }
                if (data.bundles.length > 0) {
                    for (var i in data.bundles) {
                        if (__filter_dropdown == 'all') {
                            $('#course_row_' + data.bundles[i].id).html(renderBundleRow(data.bundles[i]));
                        } else {
                            $('#course_row_' + data.bundles[i].id).remove();
                            __totalUsers = __totalUsers - 1;
                            __shownUsers = __shownUsers - 1;
                            if (__totalUsers <= 0 && __shownUsers <= 0) {
                                var totalUsersHtml = 'No Bundles';
                            } else {
                                var totalUsersHtml = __shownUsers +' / '+__totalUsers + ' Bundles';
                            }
                            $('.course-count').html(totalUsersHtml);
                        }
                    }
                }
                $("#selected_course_count").html('');    
                course_selected = new Array(); 
                $('.course-checkbox-parent').prop('checked',false);
                $('.course-checkbox').prop('checked', false);   
                clearCacheBundle();   
            }
        });
    }
    function restoreBundle(bundle_id, bundle_name)
    {
        bundle_name = decodeURI(bundle_name);  
        var header_text     = 'Are you sure to Restore Bundle named '+bundle_name+' ? ';
        var messageObject = {
            'body': header_text,
            'button_yes': 'RESTORE',
            'button_no': 'CANCEL',
            'continue_params': {
                'bundle_id': bundle_id
            },
        };
        callback_warning_modal(messageObject, restoreBundleConfirmed);    
    }

    function restoreBundleConfirmed(params){
        
        var bundle_id   = params.data.bundle_id;
        $.ajax({
            url: admin_url+'bundle/restore',
            type: "POST",
            data:{"bundle_id":bundle_id, "is_ajax":true},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data.error == false)
                {
                    if(__filter_dropdown == 'all') {
                        $('#course_row_' + bundle_id).html(renderBundleRow(data.bundle));
                    } else {
                        $('#course_row_' + bundle_id).remove();
                        __totalUsers = __totalUsers - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalUsers == 0 && __shownUsers == 0) {
                            var totalUsersHtml = 'No Bundles';
                        } else {
                            var totalUsersHtml = __shownUsers +' / '+__totalUsers + ' Bundles';
                        }
                        $('.course-count').html(totalUsersHtml);
                    }
                    var messageObject = {
                        'body': 'Bundle restored successfully',
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                }
                else
                {
                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                    };
                    callback_danger_modal(messageObject);
                }
                clearCacheBundle();
            }
        });
    }
    function clearCacheBundle(){

       var bundle_list_length = $('.bundle-list:visible').length;
        if(bundle_list_length<=0){
            $('#course_row_wrapper').html(renderPopUpMessage('error', 'No Bundles found.'));
            $('#popUpMessage > .close').remove();
        }
        
    }
/*=========== Bundle Edit section ===========*/ 

function readImageData(imgData) {
    if (imgData.files && imgData.files[0]) {
        var readerObj = new FileReader();

        readerObj.onload = function(element) {
            $('#site_logo').attr('src', element.target.result);
            
            var img = new Image;

            img.onload = function() {
                if(img.width < 739 || img.height < 417){
                    lauch_common_message('Image Size', 'The image you have choosen is too small and cannot be uploaded.');
                    $('#site_logo').attr('src',__course_loaded_img);
                    $('#site_logo_btn').val('');
                    return false;
                }
            };

            img.src = element.target.result;
        }
        readerObj.readAsDataURL(imgData.files[0]);
    }
}


   
    