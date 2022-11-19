var __event_selected    = new Array();
var __filter_dropdown   = '';
__grades                = jQuery.parseJSON(__grades);

$( function() {
    $('#event_row_wrapper').html(renderGrades(__grades));
} );

function renderGrades(grades){
    var eventHtml = '';
    var actions   = [];
    var options   = [];
    $.each(grades,function(grade_key,grade){
        eventHtml += renderGrade(grade);
    });

    return eventHtml;
}

function renderGrade(grade){
    var eventHtml = '';
    var actions   = [];
    var options   = [];
        eventHtml   += '<div class="rTableRow" id="event_row_'+grade['id']+'" data-name="Event">';
        eventHtml   += '<div class="rTableCell">';
        eventHtml   += '<span class="icon-wrap-round">';
        eventHtml   += '<small class="icon-custom">G</small>';
        eventHtml   += '</span>';
        eventHtml   += '<span class="wrap-mail ellipsis-hidden">';
        eventHtml   += '<div class="ellipsis-style">';
        eventHtml   += '<a href="javascript:void(0)">'+grade['gr_name']+' ('+grade['gr_range_from']+' > '+grade['gr_range_to']+')</a> <br>';
        eventHtml   += '</div>';
        eventHtml   += '</span>';
        eventHtml   += '</div>';
        actions['action']           = grade['action'];
        actions['action_by']        = grade['action_by'];
        actions['action_author']    = grade['us_name'];
        actions['date']             = grade['updated'];
        actions['status']           = grade['gr_status'];
        eventHtml   += renderActionLabel(actions);
        eventHtml   += '<div class="td-dropdown rTableCell">';
        eventHtml   += '<div class="btn-group lecture-control">';
        eventHtml   += '<span class="dropdown-tigger" data-toggle="dropdown">';
        eventHtml   += '<span class="label-text">';
        eventHtml   += '<i class="icon icon-down-arrow"></i>';
        eventHtml   += '</span>';
        eventHtml   += '<span class="tilder"></span>';
        eventHtml   += '</span>';
        eventHtml   += '<ul class="dropdown-menu pull-right" role="menu" id="notification_action_1">';
        options['id']       = grade['id'];
        eventHtml   += renderOptions(options);
        eventHtml   += '</ul>';
        eventHtml   += '</div>';
        eventHtml   += '</div>';
        eventHtml   += '</div>';

    return eventHtml;
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
                    if(actions['status'] == '1'){
                        actionHtml      += '<span class="pull-right spn-active">'+__actions[action]['label']+' by- '+author+' on '+format_date(actions['date'])+'</span>';
                    }else{
                        actionHtml      += '<span class="pull-right spn-inactive">'+__actions[action]['label']+' by- '+author+' on '+format_date(actions['date'])+'</span>';
                    }
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
    optionsHtml     += '<li><a href="'+__admin_url+'grade/basic/'+btoa(options['id'])+'">Settings</a></li>';
    return optionsHtml;
}



function format_date(date_str){
    var d = new Date(Date.parse(date_str.replace(/-/g, "/")));
    var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    var date = d.getDate() + " " + month[d.getMonth()]+" "+d.getFullYear();
    var time = d.toLocaleTimeString().toLowerCase();

    return date;
}