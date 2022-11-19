var __event_selected    = new Array();
var __filter_dropdown   = '';

$( function() {
    getTemplates();
    // $('#event_row_wrapper').html(renderEmails(__emails));
    $('#event_time').timepicker({ timeFormat: 'h:i A' });
    var today = new Date();
    $("#event_date").datepicker({
        language: 'en',
        minDate: today,
        dateFormat: 'dd-mm-yyyy',
        autoClose: true
    });
} );

function getTemplates(){
       
    var keyword  = $('#email_keyword').val().trim();
    
    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if ( keyword != '') {
            link += '?';
        }
        
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        
        window.history.pushState({ path: link }, '', link);
    }

    $.ajax({
        url: admin_url+'email_template/get_template',
        type: "POST",
        data:{
            "is_ajax"   :true, 
            "keyword"   :keyword
        },
        success: function(response) {
            
            __emails = jQuery.parseJSON(response);
            if(__emails['success'] == true){
                $('#event_row_wrapper').html(renderEmails(__emails['email_templates']));
            }else{
                $('#event_row_wrapper').html(renderPopUpMessage('error', 'No Templates Found.'));
                $('#popUpMessage > .close').remove();
            }
        }
    });
}

function renderEmails(emails){
    var emailHtml = '';
    var actions   = [];
    var options   = [];
    $.each(emails,function(emails_key,emails){
        emailHtml += renderEmail(emails);
    });

    return emailHtml;
}


function renderEmail(emails){
    var emailHtml = '';
    var actions   = [];
    var options   = [];
        emailHtml   += '<div class="rTableRow" id="event_row_'+emails['id']+'" data-name="Emails">';
        emailHtml   += '<div class="rTableCell">';
        emailHtml   += '<span class="icon-wrap-round">';
        emailHtml   += '<small class="icon-custom">T</small>';
        emailHtml   += '</span>';
        emailHtml   += '<span class="wrap-mail ellipsis-hidden">';
        emailHtml   += '<div class="ellipsis-style">';
        emailHtml   += '<a href="javascript:void(0)">'+emails['em_name']+'</a> <br>';
        emailHtml   += '</div>';
        emailHtml   += '</span>';
        emailHtml   += '</div>';
        actions['action']           = emails['action'];
        actions['action_by']        = emails['action_by'];
        actions['action_author']    = emails['us_name'];
        actions['date']             = emails['updated'];
        actions['status']           = emails['em_status'];
        emailHtml   += renderActionLabel(actions);
        emailHtml   += '<div class="td-dropdown rTableCell">';
        emailHtml   += '<div class="btn-group lecture-control">';
        emailHtml   += '<span class="dropdown-tigger" data-toggle="dropdown">';
        emailHtml   += '<span class="label-text">';
        emailHtml   += '<i class="icon icon-down-arrow"></i>';
        emailHtml   += '</span>';
        emailHtml   += '<span class="tilder"></span>';
        emailHtml   += '</span>';
        emailHtml   += '<ul class="dropdown-menu pull-right" role="menu" id="notification_action_1">';
        options['deleted']  = emails['em_deleted'];
        options['status']   = emails['em_status'];
        options['id']       = emails['id'];
        emailHtml   += renderOptions(options);
        emailHtml   += '</ul>';
        emailHtml   += '</div>';
        emailHtml   += '</div>';
        emailHtml   += '</div>';

    return emailHtml;
}

function renderActionLabel(actions){
    var actionHtml      = '';
    var action_author   = actions['action_author'];
    var action          = actions['action'];
    var date            = actions['date'];
    actionHtml      += '<div class="rTableCell pad0">';
    actionHtml      += '<div class="col-sm-12 pad0">';
    actionHtml      += '</div>';
    actionHtml      += '<div class="col-sm-12 pad0 pad-vert5 pos-inhrt">';
    actionHtml      += generateLabel(2,actions);
    actionHtml      += '</div>';
    actionHtml      += '</div>';
    return actionHtml;
}

function generateLabel(type,actions){
    var action      = parseInt(actions['action']);
    var author      = actions['action_author'];
    var actionHtml  = '';
    switch(type){

        case 2:
            switch(action){
                case 1:
                    actionHtml      += '<span class="pull-right spn-inactive">'+__actions[action]['label']+' by- '+author+' on '+format_date(actions['date'])+'</span>';
                break;

                case 2:
                    // if(actions['status'] == '1'){
                    //     actionHtml      += '<span class="pull-right spn-active">'+__actions[action]['label']+' by- '+author+' on '+format_date(actions['date'])+'</span>';
                    // }else{
                    //     actionHtml      += '<span class="pull-right spn-inactive">'+__actions[action]['label']+' by- '+author+' on '+format_date(actions['date'])+'</span>';
                    // }
                break;

                case 3:
                    actionHtml      += '<span class="pull-right spn-active">'+__actions[action]['label']+' by- '+author+' on '+format_date(actions['date'])+'</span>';
                break;

                case 4:
                    actionHtml      += '<span class="pull-right spn-inactive">'+__actions[action]['label']+' by- '+author+' on '+format_date(actions['date'])+'</span>';
                break;

                case 5:
                    actionHtml      += '<span class="pull-right spn-delete">'+__actions[action]['label']+' by- '+author+' on '+format_date(actions['date'])+'</span>';
                break;

                case 8:
                    actionHtml      += '<span class="pull-right spn-inactive">'+__actions[action]['label']+' by- '+author+' on '+format_date(actions['date'])+'</span>';
                break;
            }
        break;
    }

    return actionHtml;
}

function renderOptions(options){  
    var optionsHtml     = '';
    optionsHtml     += '<li><a href="'+__admin_url+'email_template/basic/'+btoa(options['id'])+'">Settings</a></li>';
    return optionsHtml;
}


function format_date(date_str){
    var d = new Date(Date.parse(date_str.replace(/-/g, "/")));
    var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    var date = d.getDate() + " " + month[d.getMonth()]+" "+d.getFullYear();
    var time = d.toLocaleTimeString().toLowerCase();

    return date;
}