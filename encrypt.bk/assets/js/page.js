var __page_selected = new Array();
var __filter_dropdown = '';
var __position_filter = '';
__pages = $.parseJSON(__pages);
const __previlage = new Access();
$(function() {
    var filter = getQueryStringValue('filter');
    var keyword = getQueryStringValue('keyword');
    var showin = getQueryStringValue('showin');

    showin = (showin == '') ? 'anywhere' : showin;
    filter = (filter == '') ? 'active' : filter;
    //console.log(filter);
    __filter_dropdown = filter;
    __position_filter = showin;
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    $('#show_page_in_dropdown_text').html($('#filer_dropdown_position_' + showin).text() + '<span class="caret"></span>');

    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#page_keyword').val(keyword);
    }
    //console.log(__totalPages, 'd');
    $('#page_row_wrapper').html(renderPageHtml(__pages, __totalPages, __filter_dropdown));
    renderPagination(__offset, __totalPages);
    setBulkAction();
})

function getPages() {
    var keyword = $('#page_keyword').val().trim();
    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__filter_dropdown != '' || keyword != '' || __position_filter != '') {
            link += '?';
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + __filter_dropdown;
        }
        if (__position_filter != '') {
            link += '&showin=' + __position_filter;
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        console.log(__offset);
        __offset = (typeof __offset == 'undefined') ? 1 : __offset;
        link += '&offset=' + __offset;
        window.history.pushState({
            path: link
        }, '', link);
    }
    
    $.ajax({
        url: admin_url + 'page/filter_pages',
        type: "POST",
        data: {
            "filter"    : __filter_dropdown,
            "limit"     : __limit,
            "keyword"   : keyword,
            "offset"    : __offset,
            "showin"    : __position_filter
        },
        success: function(response){
            var filteredPages = JSON.parse(response);
            //console.log(filteredPages);
            if (filteredPages.pages.length == '0') {
                $('#total-pages').text('0 / 0 pages');
                $('#page_row_wrapper').html('<div id="popUpMessagePage" class="alert alert-danger">No Pages are found.</div>');
            } else {
                
                $('#page_row_wrapper').html(renderPageHtml(filteredPages.pages, filteredPages.total_pages, __filter_dropdown));
                __page_selected = new Array();
                $('.page-checkbox-parent').prop('checked', false);
                renderPagination(__offset, filteredPages.total_pages);
            }
        }
    });
}


$(document).on('click', '.page-checkbox', function() {
    var page_id = $(this).val();
    if ($('.page-checkbox:checked').length == $('.page-checkbox').length) {
        $('.page-checkbox-parent').prop('checked', true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __page_selected.push(page_id);
    } else {
        $('.page-checkbox-parent').prop('checked', false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__page_selected, page_id);
    }
    if (__page_selected.length > 0) {
        $("#selected_page_count").html(' (' + __page_selected.length + ')');
    } else {
        $("#selected_page_count").html('');
    }

    if (__page_selected.length > 1) {
        $("#page_bulk").css('display', 'block');
    } else {
        $("#page_bulk").css('display', 'none');
    }
});

$(document).on('click', '.page-checkbox-parent', function() {
    var parent_check_box = this;
    __page_selected = new Array();
    $('.page-checkbox').prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $('.page-checkbox').each(function(index) {
            __page_selected.push($(this).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if (__page_selected.length > 0) {
        $("#selected_page_count").html(' (' + __page_selected.length + ')');
    } else {
        $("#selected_page_count").html('');
    }

    if (__page_selected.length > 1) {
        $("#page_bulk").css('display', 'block');
        var filter = getQueryStringValue('filter');
        if (filter == 'all') {
            $("#bulkRestorePage").hide();
        } else {
            $("#bulkRestorePage").show();
        }
    } else {
        $("#page_bulk").css('display', 'none');
    }
});

$(document).on('click', '#searchclear', function() {
    __offset = 1;
    $("#page_keyword").val('');
    getPages();
});

function setBulkAction() {
    var page_bulk = '';
    if (__previlage.hasEdit() == true || __previlage.hasDelete() == true) {
        page_bulk += '<span class="dropdown-tigger" data-toggle="dropdown">';
        page_bulk += ' <span class="label-text">';
        page_bulk += '     Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->';
        page_bulk += ' </span>';
        page_bulk += ' <span class="tilder"></span>';
        page_bulk += '</span>';
        page_bulk += '<ul class="dropdown-menu pull-right" role="menu">';
        if (__previlage.hasDelete() == true) {
            if (__filter_dropdown != "deleted") {
                page_bulk += ' <li>';
                page_bulk += '     <a href="javascript:void(0)" onclick="deletePageBulk()" > Delete </a>';
                page_bulk += ' </li>';
            }
        }
        if (__previlage.hasEdit() == true) {
            if (__filter_dropdown != "active" && __filter_dropdown != "deleted") {
                page_bulk += '<li>';
                page_bulk += ' <a href="javascript:void(0)" onclick="changePageStatusBulk(\'Are you sure to make Public the selected pages ?\', \'1\')" > Make Public </a>';
                page_bulk += '</li>';
            }
            if (__filter_dropdown != "inactive" && __filter_dropdown != "deleted") {
                page_bulk += '<li>';
                page_bulk += ' <a href="javascript:void(0)" onclick="changePageStatusBulk(\'Are you sure to make Private the selected pages ?\', \'0\')" > Make Private </a>';
                page_bulk += '</li>';
            }
            if (__filter_dropdown == "deleted" /*|| __filter_dropdown == "all"*/) {
                page_bulk += '<li id="bulkRestorePage">';
                page_bulk += ' <a href="javascript:void(0)" onclick="restorePageBulk()" > Restore </a>';
                page_bulk += '</li>';
            }
        }
        page_bulk += '</ul>';
    }
    //console.log(__filter_dropdown);
    //return;
    $('#page_bulk').html(page_bulk);
}

function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

function deletePage(page_id) {
    var page_name = $('#page_row_'+page_id).attr('data-title');
    // page_name = atob(page_name);
    var headerText = 'Are you sure to delete the Page named <b>' + page_name + '</b> ?';
    var messageObject = {
        'body': headerText,
        'button_yes': 'DELETE',
        'button_no': `<span onclick="changePageStatus(${page_id},'make_private','1')">MAKE PRIVATE, INSTEAD<span>`,
        'continue_params': {
            'page_id': page_id
        },
    };
    callback_warning_modal(messageObject, deletePageConfirmed);
}

function deletePageConfirmed(params) {
    var page_id = params.data.page_id;
    $.ajax({
        url: admin_url + 'page/delete',
        type: "POST",
        data: {
            "page_id": page_id
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                if (__filter_dropdown != 'all') {
                    $('#page_row_' + page_id).remove();
                }
                var messageObject = {
                    'body': 'Page Deleted successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': 'Error to Delete the page',
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function restorePage(page_id) {
    var page_name = $('#page_row_'+page_id).attr('data-title');
    var headerText = 'Are you sure to restore the Page named <b>' + page_name + '</b> ?';
    var messageObject = {
        'body': headerText,
        'button_yes': 'RESTORE',
        'button_no': 'CANCEL',
        'continue_params': {
            'page_id': page_id
        },
    };
    callback_warning_modal(messageObject, restorePageConfirmed);
}

function restorePageConfirmed(params) {
    var page_id = params.data.page_id;
    $.ajax({
        url: admin_url + 'page/restore',
        type: "POST",
        data: {
            "page_id": page_id
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                if (__filter_dropdown != 'all') {
                    $('#page_row_' + page_id).remove();
                }
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function restorePageBulk() {
    var header_text = 'Are you sure to restore the selected Pages ?';

    var messageObject = {
        'body': header_text,
        'button_yes': 'RESTORE',
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status
        },
    };
    callback_warning_modal(messageObject, restorePageBulkConfirmed);
}

function restorePageBulkConfirmed(params) {
    $.ajax({
        url: admin_url + 'page/restore_page_bulk',
        type: "POST",
        data: {
            "pages": JSON.stringify(__page_selected)
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                for (var i in __page_selected) {
                    if (__filter_dropdown != 'all') {
                        $('#page_row_' + __page_selected[i]).remove();
                    }
                }
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
            __page_selected = new Array();
            $("#page_bulk").css('display', 'none');
            $("#selected_page_count").html('');
            $('.page-checkbox-parent, .page-checkbox').prop('checked', false);
        }
    });
}

function deletePageBulk() {
    var header_text = 'Are you sure to delete the selected Pages ?';

    var messageObject = {
        'body': header_text,
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status
        },
    };
    callback_warning_modal(messageObject, deletePageBulkConfirmed);
}

function deletePageBulkConfirmed(params) {
    $.ajax({
        url: admin_url + 'page/delete_page_bulk',
        type: "POST",
        data: {
            "pages": JSON.stringify(__page_selected)
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                for (var i in __page_selected) {
                    if (__filter_dropdown != 'all') {
                        $('#page_row_' + __page_selected[i]).remove();
                    }
                }
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
            __page_selected = new Array();
            $("#page_bulk").css('display', 'none');
            $("#selected_page_count").html('');
            $('.page-checkbox-parent, .page-checkbox').prop('checked', false);
        }
    });
}

function renderPageHtml(pages, total_pages='', filter = '') {
    $("#selected_page_count").html('');
    var pageHtml = '';
    var totalPages = pages.length;
    console.log(pages, 'renderPageHtml');
    var pagesShown = ((__limit*(__offset-1))+totalPages);
    $('#total-pages').text(pagesShown + ' / ' + total_pages + ' pages');
    if (totalPages > 0) {
        for (var i = 0; i < totalPages; i++) {
            var action          = 'Deleted';
            var action_class    = 'label-danger';
            var item_deleted    = filter == 'deleted'? 'page-checkbox': ' item-deleted';
            var item_desabled   = '';
            var pageSettingUrl  = 'javascript:void(0)';
            if (pages[i]['p_deleted'] == 0) {
                item_deleted    = 'page-checkbox ';
                pageSettingUrl  = admin_url+'page/basics/'+pages[i]['id'];
                if (pages[i]['action_id'] == 1) {
                    action_class = 'label-warning';
                    action = lang('private');
                } else {
                    if (pages[i]['p_status'] == 1) {
                        action_class = 'label-success';
                        action = lang('public');
                    } else {
                        action_class = 'label-warning';
                        action = lang('private');
                    }
                }
            }else{

                if(filter!='deleted'){
                    item_desabled = 'disabled="disabled"';
                }
            }

            var pageShowsIn = '';
            switch (pages[i]['p_show_page_in']){
                case '1':
                    pageShowsIn = 'header'; 
                    break;
                case '2':
                    pageShowsIn = 'footer'; 
                    break;
                case '3':
                    pageShowsIn = 'headerfooter'; 
                    break;
                case '0':
                    pageShowsIn = 'nowhere'; 
                    break;
                default:
                    break;
            }

            pageHtml += `
                <div class="dragging rTableRow page-listing-row" id="page_row_${pages[i]['id']}" data-title="${pages[i]['p_title']}">
                    <div class="rTableCell cours-fix ellipsis-hidden cms-mange-list"> 
                    <!--<div class="drager">
                        <img src="http://api.SGlearningapp.com/assets//images/drager.png">
                    </div>-->
                        <input type="checkbox" class="${item_deleted}" ${item_desabled} value="${pages[i]['id']}" id="page_details_${pages[i]['id']}">
                        <a href="${pageSettingUrl}" class="cust-sm-6 padd0">
                            <div class="ellipsis-style display-initial" title="${pages[i]['p_title']}">  
                                    <span class="icon-wrap-round color-box" data-name="${pages[i]['p_title']}">`;
            var iconLetter = pages[i]['p_title'].split(' ').join('').substr(0, 1);
            pageHtml += `
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="438.533px" height="438.533px" viewBox="0 0 438.533 438.533" style="enable-background:new 0 0 438.533 438.533;width: 20px;height: 18px;fill: #65367d;" xml:space="preserve">
                                    <g>
                                        <g>
                                            <path d="M396.283,130.188c-3.806-9.135-8.371-16.365-13.703-21.695l-89.078-89.081c-5.332-5.325-12.56-9.895-21.697-13.704    C262.672,1.903,254.297,0,246.687,0H63.953C56.341,0,49.869,2.663,44.54,7.993c-5.33,5.327-7.994,11.799-7.994,19.414v383.719    c0,7.617,2.664,14.089,7.994,19.417c5.33,5.325,11.801,7.991,19.414,7.991h310.633c7.611,0,14.079-2.666,19.407-7.991    c5.328-5.332,7.994-11.8,7.994-19.417V155.313C401.991,147.699,400.088,139.323,396.283,130.188z M255.816,38.826    c5.517,1.903,9.418,3.999,11.704,6.28l89.366,89.366c2.279,2.286,4.374,6.186,6.276,11.706H255.816V38.826z M365.449,401.991    H73.089V36.545h146.178v118.771c0,7.614,2.662,14.084,7.992,19.414c5.332,5.327,11.8,7.994,19.417,7.994h118.773V401.991z"></path>
                                            <path d="M319.77,292.355h-201c-2.663,0-4.853,0.855-6.567,2.566c-1.709,1.711-2.568,3.901-2.568,6.563v18.274    c0,2.67,0.856,4.859,2.568,6.57c1.715,1.711,3.905,2.567,6.567,2.567h201c2.663,0,4.854-0.856,6.564-2.567s2.566-3.9,2.566-6.57    v-18.274c0-2.662-0.855-4.853-2.566-6.563C324.619,293.214,322.429,292.355,319.77,292.355z"></path>
                                            <path d="M112.202,221.831c-1.709,1.712-2.568,3.901-2.568,6.571v18.271c0,2.666,0.856,4.856,2.568,6.567    c1.715,1.711,3.905,2.566,6.567,2.566h201c2.663,0,4.854-0.855,6.564-2.566s2.566-3.901,2.566-6.567v-18.271    c0-2.663-0.855-4.854-2.566-6.571c-1.715-1.709-3.905-2.564-6.564-2.564h-201C116.107,219.267,113.917,220.122,112.202,221.831z"></path>
                                        </g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                    </svg>
                                    </span>
                                    ${pages[i]['p_title']}
                            </div>
                        </a>
                    </div>
                    <div class="rTableCell pad0 cours-fix width70 ondrag-hide"> 
                        <div class="col-sm-12 pad0">
                            <label>
                                <a style="color:#007480" href="${pageSettingUrl}" class="cust-sm-6 padd0">
                                    ${lang(pageShowsIn)}
                                </a>
                            </label>
                            <label class="pull-right label ${action_class}" id="action_class_${pages[i]['id']}">${action}</label>
                        </div>
                    </div>`;
            if (__previlage.hasEdit() == true || __previlage.hasDelete() == true) {
                pageHtml += `
                    <div class="td-dropdown rTableCell ondrag-hide">
                        <div class="btn-group lecture-control">
                            <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class="label-text">
                                    <i class="icon icon-down-arrow"></i>
                                </span>
                            <span class="tilder"></span>

                            </span>
                            <ul class="dropdown-menu pull-right" role="menu" id="course_action_${pages[i]['id']}">`;

                if (pages[i]['p_deleted'] == 0) {
                    var cb_status = (pages[i]['p_status'] == 1) ? 'make_private' : 'make_public';
                    var cb_action = cb_status;
                    if (__previlage.hasEdit() == true) {
                        //console.log(pages[i].p_goto_external_url,pages[i].p_slug);
                        var previewUrl = pages[i].p_goto_external_url == '1' ? pages[i].p_external_url : __site_url+pages[i].p_slug+'?preview=1';
                        pageHtml += `
                                <li id="status_btn_${pages[i]['id']}">
                                    <a href="javascript:void(0);" onclick="changePageStatus('${pages[i]['id']}', '${cb_action}' )">${lang(cb_status)}</a>
                                </li>
                                <li>
                                    <a href="${admin_url}page/basics/${pages[i]['id']}">${lang('settings')}</a>
                                </li>
                                <li>
                                    <a target="_blank" href="${previewUrl}">${lang('page_preview')}</a>
                                </li>`;
                    }
                    if (__previlage.hasDelete() == true) {
                        pageHtml += `
                                <li>
                                    <a href="javascript:void(0);" id="delete_btn_${pages[i]['id']}" data-toggle="modal" onclick="deletePage('${pages[i]['id']}')">${lang('delete_page')}</a>
                                </li>`;
                    }
                } else {
                    if (__previlage.hasEdit() == true) {
                        pageHtml += `
                                <li>
                                    <a href="javascript:void(0);" id="restore_btn_${pages[i]['id']}" data-toggle="modal" onclick="restorePage('${pages[i]['id']}')">${lang('restore')}</a>
                                </li>`;
                    }
                }
                pageHtml += `
                            </ul>
                        </div>
                    </div>`;
            }
            pageHtml += `
                </div>`;
        }
    }
    return pageHtml;
}

var timeOut = null;
$(document).on('keyup', '#page_keyword', function() {
    clearTimeout(timeOut);
    timeOut = setTimeout(function() {
        __offset = 1;
        getPages();
        $("#selected_page_count").html('');
        __page_selected = new Array();
        $("#page_bulk").css('display', 'none');
    }, 600);
});

function filter_page_by(filter) {
    __offset = 1;
    __filter_dropdown = filter;
    if (filter == 'all') {
        //$('#page_keyword').val('');
        $('#dropdown_text').html(lang('pages') + ' <span class="caret"></span>');
    }
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    getPages();
    $("#selected_page_count").html('');
    __page_selected = new Array();
    $("#page_bulk").css('display', 'none');
    setBulkAction();
}

function filter_page_position_by(position_filter) {
    __offset = 1;
    __position_filter = position_filter;
    if (position_filter == 'anywhere') {
        //$('#page_keyword').val('');
        $('#dropdown_text').html(lang('pages') + ' <span class="caret"></span>');
    }
    $('#show_page_in_dropdown_text').html($('#filer_dropdown_position_' + position_filter).text() + '<span class="caret"></span>');
    getPages();
    $("#selected_page_count").html('');
    __page_selected = new Array();
    $("#page_bulk").css('display', 'none');
    setBulkAction();
}

function renderPagination(offset, totalPages) {
    offset = Number(offset);
    totalPages = Number(totalPages);
    var totalPage = Math.ceil(totalPages / __limit);
    if (offset <= totalPage && totalPage > 1) {
        var paginationHtml = '';
        paginationHtml += '<ul class="pagination pagination-wrapper"  style="left:65px;">';
        paginationHtml += generatePagination(offset, totalPage);
        paginationHtml += '</ul>';
        $('#pagination_wrapper').html(paginationHtml);
        scrollToTopOfPage();
    } else {
        $('#pagination_wrapper').html('');
    }
}

$(document).on('click', '.locate-page', function() {
    __offset = $(this).attr('data-page');
    
    getPages();
    $("#selected_page_count").html('');
    __page_selected = new Array();
    $("#page_bulk").css('display', 'none');
    setBulkAction();
});

function refreshListing() { 
    if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
        if ($('.page-listing-row').length != 0) {
            __offset = $('.pagination li.active a').attr('data-page');
        } else {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = __offset - 1;
            if (__offset == 0) {
                __offset = 1;
            }
        }
        getPages();
    } else {
        if ($('.page-listing-row').length != 0) {
            __offset = $('.pagination li.active a').attr('data-page');
        }
        getPages();
    }
}

function changePageStatus(page_id, action, instead = '') {
    var page_name = $('#page_row_'+page_id).attr('data-title');
    var status = (action == 'make_public') ? '1' : '0';
    console.log(action);
    // page_name = atob(page_name);
    //alert(action.split("_")[0]);
    var header_text = 'Are you sure to ' + (lang(action).split(" ")[0]) + ' the page named <b>' + page_name + '</b> '+(lang(action).split(" ")[1]+' ?');
    var messageObject = {
        'body': header_text,
        'button_yes': lang(action).toUpperCase(),
        'button_no': 'CANCEL',
        'continue_params': {
            'page_id': page_id,
            'status': status
        },
    };

    if(instead){
        changeStatusConfirmed({'data':{'page_id': page_id,'status': status}});
    }else{
        callback_warning_modal(messageObject, changeStatusConfirmed);
    }
}

function changeStatusConfirmed(params) {
    var page_id = params.data.page_id;
    var status = params.data.status;
    $.ajax({
        url: admin_url + 'page/change_status',
        type: "POST",
        data: {
            "page_id": page_id,
            "status": status
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                if (__filter_dropdown != 'all') {
                    $('#page_row_' + page_id).remove();
                }

                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
                refreshListing();
            }
        }
    });
}

function changePageStatusBulk(header_text, status) {
    var action = 'make Public';
    var ok_button_text = 'MAKE PUBLIC';

    if (status == 0) {
        action = 'make Private';
        ok_button_text = 'MAKE PRIVATE';
    }
    if (header_text == '') {
        header_text = 'Are you sure to <b>' + action + '</b> the selected pages ?';
    }

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status,
        },
    };
    callback_warning_modal(messageObject, ChangePageBulkConfirmed);
}

function ChangePageBulkConfirmed(params) {
    var status = params.data.status;
    $.ajax({
        url: admin_url + 'page/change_status_bulk',
        type: "POST",
        data: {
            "pages": JSON.stringify(__page_selected),
            "status": status
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                for (var i in __page_selected) {
                    if (__filter_dropdown != 'all') {
                        $('#page_row_' + __page_selected[i]).remove();
                    }
                }
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
                refreshListing();
            }
            __page_selected = new Array();
            $("#page_bulk").css('display', 'none');
            $("#selected_page_count").html('');
            $('.page-checkbox-parent, .page-checkbox').prop('checked', false);

        }
    });
}

function createPage(header_text, label) {
    $('#page_name').val('');
    $('#popUpMessage').hide();
    $('#create_box_title').html(header_text);
    $('#create_box_label').html(label);
    $('#create_box_ok').unbind();
    $('#create_box_ok').click(function () {
        $('#create_box_ok').prop('disabled', true);
        setTimeout(function(){
            createPageConfirmed();
        }, 1500);
    });
}

function createPageConfirmed() {
    
    var page_name = $('#page_name').val();
    page_name = page_name.replace(/["<>{}]/g, '');
    page_name = page_name.trim();
    var errorCount = 0;
    var errorMessage = '';
    if (page_name == '') {
        errorCount++;
        errorMessage += 'please enter page name <br />';
        $('#create_box_ok').prop('disabled', false);
    }
    cleanPopUpMessage();
    if (errorCount == 0) {
        $.ajax({
            url: admin_url + 'page/create_page',
            type: "POST",
            // async : true,
            data: {
                'page_name': page_name
            },
            success: function(response) {
                $('#create_box_ok').prop('disabled', false);
                var data = $.parseJSON(response);
                if (data['error'] == false) {
                    window.location = admin_url + 'page/basics/' + data['id'];
                } else {
                    $('#create_page .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
            }
        });
    } else {
        $('#create_page .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    }
}


