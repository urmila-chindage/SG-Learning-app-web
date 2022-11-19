var __filter_dropdown   = '';
__promocodes            = $.parseJSON(__promocodes);
const __previlage       = new Access();
$(function() {
    var filter  = getQueryStringValue('filter');
    var keyword = getQueryStringValue('keyword');

    filter  = (filter == '')?'active':filter;
    __filter_dropdown = filter;
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    
    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#promocode_keyword').val(keyword);
    }
    $('#promocode_row_wrapper').html(renderPromocodes(__promocodes));
    renderPagination(__offset, __totalPromocodes);
    setBulkAction();
});

$(document).ready(function() {
    $('input:radio[name="promocode_user_permission"]').change(function(){
        if ($(this).val() == '1') {
            //$('#user_limit_section').show();
            $("#promocode_user_limit").prop('disabled', false);
        }else{
            //$('#user_limit_section').hide();
            $("#promocode_user_limit").prop('disabled', true);
            
        }
    });
    $('input:radio[name="promocode_discount_type"]').change(function() {
        //$('#promocode_discount_section').show();
        $("#promocode_discount_rate").prop('disabled', false);
        if ($(this).val() == '1') {
            $('#promocode_discount_label').html('Discount Rate*:');
            $('#promocode_discount_rate').val('');
            //document.getElementById("promocode_discount_rate").removeAttribute("max");
            document.getElementById("promocode_discount_rate").removeAttribute("maxLength");
        }else{
            $('#promocode_discount_label').html('Percentage Rate*:');
            $('#promocode_discount_rate').val('');
            document.getElementById("promocode_discount_rate").maxLength = "2";
            //document.getElementById("promocode_discount_rate").max = "99";
        }
    });
});

function isNumber( evt ) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

$(function() {
    var today = new Date();
    $("#promocode_expiry_date").datepicker({
        language: 'en',
        minDate: today,
        dateFormat: 'dd-mm-yy',
        autoClose: true
    });
});

$(document).on('click','#create_new_promocode',function() {
    createPromocode();
});

$(document).on('click','#generate_new_promocode',function() {
    generatePromocode();
});

function createPromocode() {
    $('#pc_count_section').hide();
    $('#pc_user_permission_section').show();
    $('#create_box_title').show();
    $('#generate_box_title').hide();
    $('#promocode_creation_type').val(0);
    emptyPopupfields();
}

function generatePromocode() {
    $('#pc_count_section').show();
    $('#pc_user_permission_section').hide();
    $('#create_box_title').hide();
    $('#generate_box_title').show();
    $('#promocode_creation_type').val(1);
    emptyPopupfields();
}

function emptyPopupfields() {
    $('#promocode_name').val('');
    $('#promocode_description').val('');
    $('#promocode_user_limit').val('');
    $('#promocode_discount_rate').val('');
    $('#promocode_count').val('');
    $('#promocode_expiry_date').val('');
    $("input[name=promocode_user_permission]").prop("checked", false);
    $("input[name=promocode_discount_type]").prop("checked", false);
    //$('#user_limit_section').hide();
    $("#promocode_user_limit").prop('disabled', true);
    //$('#promocode_discount_section').hide();
    $("#promocode_discount_rate").prop('disabled', true);
    $('#popUpMessage').hide();
}

function savePromocode() {
    var promocodeCreationType     = $.trim($('#promocode_creation_type').val());
    var promocodeName             = $.trim($('#promocode_name').val());
    var promocodeDescription      = $.trim($('#promocode_description').val());
    var promocodeUserPermission   = $.trim($('input[name="promocode_user_permission"]:checked').val());
    var promocodeUserLimit        = $.trim($('#promocode_user_limit').val());
    var promocodeDiscountType     = $.trim($('input[name="promocode_discount_type"]:checked').val());
    var promocodeDiscountRate     = $.trim($('#promocode_discount_rate').val());
    var promocodeCount            = $.trim($('#promocode_count').val());
    var promocodeExpiryDate       = $.trim($('#promocode_expiry_date').val());
 

    var message         = [];

    if(promocodeName == '') {
        message.push('Enter Discount Coupon Name.');
    }
    // if(promocodeDescription == '') {
    //     message.push('Enter Discount Coupon Description.');
    // }
    if(promocodeCreationType == 0) {
        if(promocodeUserPermission == '') {
            message.push('Choose Usage Type.');
        }
        if(promocodeUserPermission == 1){
            if(promocodeUserLimit == '' || promocodeUserLimit <= '0') {
                message.push('Enter Number of Users.');
            }
        }
    }
    if(promocodeDiscountType == '') {
        message.push('Choose Discount Type.');
    }
//alert(promocodeDiscountRate);
    if(promocodeDiscountType == 0) {
        if(promocodeDiscountRate == '' || promocodeDiscountRate <= '0') {
            message.push('Enter Percentage Rate.');
        }
        
        if(Number(promocodeDiscountRate) >= 100) {
            message.push('Enter Valid Percentage Rate, Restricted to less than 100%. Eg:- 99.99');
        }

        if(!$.isNumeric( promocodeDiscountRate )){
            message.push('Enter Valid Percentage Rate.');
        }
    }
    if(promocodeDiscountType == 1) {
        if(promocodeDiscountRate == '' || promocodeDiscountRate <= '0') {
            message.push('Enter Discount Rate.');
        }
        if(!$.isNumeric( promocodeDiscountRate )){
            message.push('Enter Valid Discount Rate.');
        }
    }
    if(promocodeCreationType == 1) {
        if(promocodeCount == '') {
            message.push('Enter Discount Coupons Count.');
        }
        var promocodeLength         = promocodeName.length + promocodeCount.length;
        if(promocodeLength > 10) {
            message.push('Generated Discount Coupons Should be Maximum Ten Characters');
        }
    }
    if(promocodeExpiryDate == '') {
        message.push('Enter Discount Coupon Expiry Date.');
    }

    if(message.length > 0) {
        $('#new_promocode .modal-body').prepend(renderPopUpMessage('error', message.join('<br />')));
    } else {
        $.ajax({
            url: admin_url+'promo_code/save_promocode',
            type: "POST",
            data:{ 
                    "promocode_creation_type"    : promocodeCreationType,
                    "promocode_name"             : promocodeName,
                    "promocode_description"      : promocodeDescription,
                    "promocode_user_permission"  : promocodeUserPermission,
                    "promocode_user_limit"       : promocodeUserLimit,
                    "promocode_discount_type"    : promocodeDiscountType,
                    "promocode_discount_rate"    : promocodeDiscountRate,
                    "promocode_count"            : promocodeCount,
                    "promocode_expiry_date"      : promocodeExpiryDate 
                },
            success: function(response) {
                var promocode = $.parseJSON(response);
                if(promocode['header']['success'] == true) {
                    $('#new_promocode').modal('hide');
                    if(promocode['header']['type'] == 'Generated') {
                        setTimeout(location.reload.bind(location), 3000);
                    } else {
                        __totalPromocodes    = parseInt(__totalPromocodes)+1;
                        var total_promocodes = (__totalPromocodes>1)?__totalPromocodes+' Discount Coupons':__totalPromocodes+' Discount Coupon';
                        $('#total-promocodes').html(total_promocodes);

                    var promocodeHtml    = '';
                        promocodeHtml   += '<div class="rTableRow promocode-listing-row" id="promocode_row_'+promocode['body']['promocode']['id']+'" >';
                        promocodeHtml   +=      renderPromocode(promocode['body']['promocode']);
                        promocodeHtml   += '</div>';   
                    $('#promocode_row_wrapper').prepend(promocodeHtml);
                    $('#popUpMessagePage.alert-danger').hide();
                    }
                } 
                else {
                     $('#new_promocode .modal-body').prepend(renderPopUpMessage('error',promocode['header']['message']));
                 }
            }
        });
    }
}

function renderPromocodes( promocodes ) {
    var promocodeHtml = '';
    if(Object.keys(promocodes).length > 0) {
        $.each(promocodes,function(promocode_key,promocode){
            promocodeHtml   += '<div class="rTableRow promocode-listing-row" id="promocode_row_'+promocode['id']+'">';
            promocodeHtml   +=      renderPromocode(promocode);
            promocodeHtml   += '</div>';
        }); 
        var total_promocodes = (__totalPromocodes>1)?__totalPromocodes+' Discount Coupons':__totalPromocodes+' Discount Coupon';
        $('#total-promocodes').html(total_promocodes);
    } else {
        promocodeHtml += '<div id="popUpMessagePage" class="alert alert-danger">No Discount Coupons are found.</div>';
        $('#total-promocodes').html('0 Discount Coupon');
    } 
    return promocodeHtml;
}

function copyPromocode(code, id) {
var textarea = document.createElement('textarea');
    textarea.textContent = code.toUpperCase();
    document.body.appendChild(textarea);
var selection = document.getSelection();
var range = document.createRange();
//  range.selectNodeContents(textarea);
  range.selectNode(textarea);
  selection.removeAllRanges();
  selection.addRange(range);
  console.log('copy success', document.execCommand('copy'));
  selection.removeAllRanges();
  document.body.removeChild(textarea);
  $('.custom-tooltip').html("click here to copy");
  $('#coupon_'+id).html("Copied to clipbord");
  }

function renderPromocode( promocode ) {  
    //expired-coupon
    var expiryDateArray     = promocode['pc_expiry_date'].split(' ');
    var lastDate            = expiryDateArray[0].split('-');
    var expiryDate          = lastDate[2] + '-' + lastDate[1] + '-' + lastDate[0];
    var thisDay             = new Date();
    var thisMonth           = thisDay.getMonth()+1;
    var thisDate            = thisDay.getDate();
    var todaysDate          = thisDay.getFullYear() + '-' + (thisMonth<10 ? '0' : '') + thisMonth + '-' + (thisDate<10 ? '0' : '') + thisDate;
    
    var isValid         = 'valid';
    if(promocode['pc_user_limit'] != '0') {
        if(promocode['pc_user_count'] >= promocode['pc_user_limit']) {
            var isValid = 'invalid';
        }
    }
    var expiredCoupon = '';
    if(expiryDateArray[0] < todaysDate || isValid == 'invalid') {
        expiredCoupon = 'expired-coupon';
    }
    var promocodeHtml    = '';
        promocodeHtml   += '    <div class="rTableCell promo-align">';
        promocodeHtml   += '        <input type="checkbox" class="promocode-checkbox c-pointer" value="' + promocode['id'] + '" id="promocode_details_' + promocode['id'] + '"> ';
        promocodeHtml   += '        <span class="wrap-mail ellipsis-hidden" style="vertical-align: super;">';
        promocodeHtml   += '            <div class="ellipsis-style coupon '+expiredCoupon+'">';
        promocodeHtml   += `                <span class="custom-tooltip" id="coupon_${promocode.id}">click here to copy</span>`;
        promocodeHtml   += '                <a class="promocodecopy" href="javascript:void(0)" onclick="copyPromocode(\''+promocode['pc_promo_code_name']+'\','+promocode['id']+')">'+promocode['pc_promo_code_name'].toUpperCase()+'</a> ';
        promocodeHtml   += '            </div>';
        promocodeHtml   += '        </span>';
        promocodeHtml   += '    </div>';
        //promocodeHtml   +=``;

        var discountRate = '<b>Flat ₹'+promocode['pc_discount_rate']+' Off</b>';
        if(promocode['pc_discount_type'] == '0') {
            discountRate = promocode['pc_discount_rate']+'% Off';
        }
        promocodeHtml   += '    <div class="rTableCell">';
        promocodeHtml   += '        <span class="wrap-mail ellipsis-hidden" style="vertical-align: middle;">';
        promocodeHtml   += '            <div class="ellipsis-style ">';
        promocodeHtml   += '                <a href="javascript:void(0)">'+discountRate+'</a> ';
        promocodeHtml   += '            </div>';
        promocodeHtml   += '        </span>';
        promocodeHtml   += '    </div>';
        
        
        //var todaysDate          = (thisDate<10 ? '0' : '') + thisDate + '-' + (thisMonth<10 ? '0' : '') + thisMonth + '-' + thisDay.getFullYear();
        
        promocodeHtml   += '    <div class="rTableCell">';
        promocodeHtml   += '        <span class="wrap-mail ellipsis-hidden" style="vertical-align: middle;">';
        promocodeHtml   += '            <div class="ellipsis-style">';
        promocodeHtml   += '                <a href="javascript:void(0)">'+expiryDate+'</a> ';
        promocodeHtml   += '            </div>';
        promocodeHtml   += '        </span>';
        promocodeHtml   += '    </div>';
        
        var user_url     = admin_url+'promo_code/users/'+promocode['id'];
        if(promocode['pc_user_count'] == '0') {
            user_url     = 'javascript:void(0)';
        }
        var user_limit   = promocode['pc_user_limit'];
        if(user_limit == '0') {
            user_limit   = 'Unlimited';
        }
        promocodeHtml   += '    <div class="rTableCell">';
        promocodeHtml   += '        <span class="wrap-mail ellipsis-hidden" style="vertical-align: middle;">';
        promocodeHtml   += '            <div class="ellipsis-style">';
        promocodeHtml   += '                <a href="'+user_url+'" class="text-green">'+promocode['pc_user_count']+' used'+' / '+user_limit+'</a> ';
        promocodeHtml   += '            </div>';
        promocodeHtml   += '        </span>';
        promocodeHtml   += '    </div>';
        
       
        var actionClass  = 'label-warning';
        var actionLabel  = 'Inactive';
        var actionText   = 'Activate';
        if((promocode['pc_status'] == '1')) {
            actionClass  = 'label-success';
            actionLabel  = 'Active';
            actionText   = 'Deactivate';
        }
        if(expiryDateArray[0] < todaysDate || isValid == 'invalid') {
            actionClass  = 'label-danger';
            actionLabel  = 'Expired';
        }
        promocodeHtml   += '    <div class="rTableCell pad0" style="vertical-align: bottom;">';
        promocodeHtml   += '        <div class="col-sm-12 pad0">';
        promocodeHtml   += '            <label style="margin-right:15px;" class="pull-right label '+actionClass+'" id="action_class_'+promocode['id']+'">'+actionLabel+'</label>';
        promocodeHtml   += '        </div>';
        promocodeHtml   += '        <div class="col-sm-12 pad0 pad-vert5 pos-inhrt"></div>';
        promocodeHtml   += '    </div>';
        
        if(__previlage.hasEdit() == true || __previlage.hasDelete() == true) {
            promocodeHtml   += '    <div class="td-dropdown rTableCell">';
            promocodeHtml   += '        <div class="btn-group lecture-control">';
            promocodeHtml   += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            promocodeHtml   += '                <span class="label-text"><i class="icon icon-down-arrow"></i></span>';
            promocodeHtml   += '                <span class="tilder"></span>';
            promocodeHtml   += '            </span>';
            promocodeHtml   += '            <ul class="dropdown-menu pull-right" role="menu" id="promocode_action_'+promocode['id']+'">';
            if(__previlage.hasEdit() == true && expiryDateArray[0] >= todaysDate && isValid == 'valid') {
                promocodeHtml   += '                    <li><a href="javascript:void(0)" class="c-pointer" onclick="changePromocodeStatus(\''+promocode['id']+'\', \''+promocode['pc_status']+'\', \''+btoa(unescape(encodeURIComponent(promocode['pc_promo_code_name'])))+'\')">'+actionText+'</a></li>'; 
            }
            if(__previlage.hasDelete() == true) {
                promocodeHtml   += '                    <li><a href="javascript:void(0)" class="c-pointer" onclick="deletePromocode(\''+promocode['id']+'\', \''+btoa(unescape(encodeURIComponent(promocode['pc_promo_code_name'])))+'\')">Delete</a></li>';
            }
            promocodeHtml   += '            </ul>';
            promocodeHtml   += '        </div>';
            promocodeHtml   += '    </div>';
        }

    return promocodeHtml;
}

function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

var timeOut = null;
$(document).on('keyup', '#promocode_keyword', function()  {
    clearTimeout(timeOut);
    timeOut = setTimeout(function () {
        __offset = 1;
        getPromocodes();
        $("#selected_promocode_count").html('');
        __promocode_selected = new Array();
        $("#promocode_bulk").css('display', 'none');
    }, 600);
});

$(document).on('click', '#searchclear', function(){
    __offset = 1;
    getPromocodes();
    $("#selected_promocode_count").html(''); 
});

function filter_promocodes_by(filter) {
    __offset            = 1;
    __filter_dropdown   = filter;
    if (filter == 'all') {
        $('#promocode_keyword').val('');
    }
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    getPromocodes();
    $("#selected_promocode_count").html('');
    __promocode_selected = new Array();
    $("#promocode_bulk").css('display', 'none');
    setBulkAction();
}

var __promocode_selected = new Array();
$(document).on('click', '.promocode-checkbox', function()  {
    var promocode_id = $(this).val();
    if ($('.promocode-checkbox:checked').length == $('.promocode-checkbox').length) {
        $('.promocode-checkbox-parent').prop('checked', true);
    }
    if ($(this).is(':checked')) {
        __promocode_selected.push(promocode_id);
    } else {
        $('.promocode-checkbox-parent').prop('checked', false);
        removeArrayIndex(__promocode_selected, promocode_id);
    }
    if (__promocode_selected.length > 1) {
        $("#selected_promocode_count").html(' (' + __promocode_selected.length + ')');
        $("#promocode_bulk").css('display', 'block');
    } else {
        $("#selected_promocode_count").html('');
        $("#promocode_bulk").css('display', 'none');
    }
});

$(document).on('click', '.promocode-checkbox-parent', function()  {
    var parent_check_box = this;
    __promocode_selected = new Array();
    $('.promocode-checkbox').not(':disabled').prop('checked', $(parent_check_box).is(':checked'));
    if ($(parent_check_box).is(':checked') == true) {
        $('.promocode-checkbox').not(':disabled').each(function (index) {
            __promocode_selected.push($(this).val());
        });
    }
    if (__promocode_selected.length > 1) {
        $("#selected_promocode_count").html(' (' + __promocode_selected.length + ')');
        $("#promocode_bulk").css('display', 'block');
    } else {
        $("#selected_promocode_count").html('');
        $("#promocode_bulk").css('display', 'none');
    }
});

function getPromocodes() {
    var keyword = $('#promocode_keyword').val().trim();
    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__filter_dropdown != '' || keyword != '') {
            link += '?';
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + __filter_dropdown;
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        __offset = (typeof __offset == 'undefined') ? 1 : __offset;
        link += '&offset=' + __offset;
        window.history.pushState({
            path: link
        }, '', link);
    }
    $.ajax({
        url: admin_url + 'promo_code/filter_promocodes',
        type: "POST",
        data:{     
                "filter" : __filter_dropdown,
                "limit"  : __limit,        
                "keyword": keyword,
                "offset" : __offset
             },
        success: function(response) {
            $('.promocode-checkbox-parent').prop('checked', false);
            var filteredPromocodes  = $.parseJSON(response);
            if(filteredPromocodes['header']['success'] == true) {
                $('#promocode_row_wrapper').html(renderPromocodes(filteredPromocodes['body']['promocodes']));
            } else {
                $('#promocode_row_wrapper').html('<div id="popUpMessagePage" class="alert alert-danger">No Discount Coupons are found.</div>');
            }
            renderPagination(__offset, filteredPromocodes['body']['total_promocodes']);
            __totalPromocodes    = filteredPromocodes['body']['total_promocodes'];
            var total_promocodes = (__totalPromocodes>1)?__totalPromocodes+' Discount Coupons':__totalPromocodes+' Discount Coupon';
            $('#total-promocodes').html(total_promocodes);
        }
    });
}

function renderPagination(offset, totalPromocodes) {
    offset          = Number(offset);
    totalPromocodes = Number(totalPromocodes);
    var totalPage   = Math.ceil(totalPromocodes / __limit);
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

$(document).on('click', '.locate-page', function()  {
    __offset = $(this).attr('data-page');
    getPromocodes();
    $("#selected_promocode_count").html('');
    __promocode_selected = new Array();
    $("#promocode_bulk").css('display', 'none');
    setBulkAction();
});

function setBulkAction() {
    var promocode_bulk = '';
    if(__previlage.hasEdit() == true || __previlage.hasDelete() == true) {
        promocode_bulk += '<span class="dropdown-tigger" data-toggle="dropdown">';
        promocode_bulk += ' <span class="label-text">';
        promocode_bulk += '     Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->';
        promocode_bulk += ' </span>';
        promocode_bulk += ' <span class="tilder"></span>';
        promocode_bulk += '</span>';
        promocode_bulk += '<ul class="dropdown-menu pull-right" role="menu">';
    
        if(__previlage.hasDelete() == true) {
            promocode_bulk += ' <li>';
            promocode_bulk += '     <a href="javascript:void(0)" class="c-pointer" onclick="deletePromocodeBulk()" > Delete </a>';
            promocode_bulk += ' </li>';
        }
        if(__previlage.hasEdit() == true) {
            if (__filter_dropdown != "active" && __filter_dropdown != "expired") {
                promocode_bulk += '<li>';
                promocode_bulk += ' <a href="javascript:void(0)" class="c-pointer" onclick="changePromocodeStatusBulk(\'Are you sure to Activate the selected Discount Coupons\', \'1\')" > Activate </a>';
                promocode_bulk += '</li>';
            }
            if (__filter_dropdown != "inactive" && __filter_dropdown != "expired") {
                promocode_bulk += '<li>';
                promocode_bulk += ' <a href="javascript:void(0)" class="c-pointer" onclick="changePromocodeStatusBulk(\'Are you sure to Deactivate the selected Discount Coupons\', \'0\')" > Deactivate </a>';
                promocode_bulk += '</li>';
            }
        }
        promocode_bulk += '</ul>';
    }
    $('#promocode_bulk').html(promocode_bulk);
}

function changePromocodeStatusBulk(header_text, status) {
    var action = 'Activate';
    var ok_button_text = 'ACTIVATE';

    if (status == 0) {
        action = 'Deactivate';
        ok_button_text = 'DEACTIVATE';
    }
    if (header_text == '') {
        header_text = 'Are you sure to ' + action + ' the selected Discount Coupons ?';
    }

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status,
        },
    };
    callback_warning_modal(messageObject, changePromocodeStatusBulkConfirmed);
}

function changePromocodeStatusBulkConfirmed( params ) {
    var status = params.data.status;
    $.ajax({
        url: admin_url + 'promo_code/change_promocode_status_bulk',
        type: "POST",
        data: {
            "promocodes" : JSON.stringify(__promocode_selected),
            "status"     : params.data.status
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data['header']['error'] == false) {
                for (var i in __promocode_selected) {
                    if (__filter_dropdown != 'all') {
                        $('#promocode_row_' + __promocode_selected[i]).remove();
                        __totalPromocodes = __totalPromocodes - 1;
                    }
                }
                var messageObject = {
                    'body': 'Discount Coupons status changed successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data['header']['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
                }
            __promocode_selected = new Array();
            $("#promocode_bulk").css('display', 'none');
            $("#selected_promocode_count").html('');
            $('.promocode-checkbox-parent, .promocode-checkbox').prop('checked', false);
            
        }
    });
}

function deletePromocodeBulk() {
    var header_text = 'Are you sure to delete the selected Discount Coupons ?';

    var messageObject = {
        'body': header_text,
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status
        },
    };
    callback_warning_modal(messageObject, deletePromocodeBulkConfirmed);
}

function deletePromocodeBulkConfirmed() {
    $.ajax({
        url: admin_url + 'promo_code/delete_promocode_bulk',
        type: "POST",
        data: {
            "promocodes" : JSON.stringify(__promocode_selected),
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data['header']['error'] == false) {
                for (var i in __promocode_selected) {
                    if (__filter_dropdown != 'all') {
                        $('#promocode_row_' + __promocode_selected[i]).remove();
                        __totalPromocodes = __totalPromocodes - 1;
                    }
                }
                var messageObject = {
                    'body': 'Discount Coupons Deleted successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data['header']['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
                }
            __promocode_selected = new Array();
            $("#promocode_bulk").css('display', 'none');
            $("#selected_promocode_count").html('');
            $('.promocode-checkbox-parent, .promocode-checkbox').prop('checked', false);
            
        }
    });

}

function deletePromocode( promocode_id, promocode_name ) {
    promocode_name      = atob(promocode_name);
    var headerText      = 'Are you sure to delete the Discount Coupon named "' + promocode_name.toUpperCase() + '" ?';

    var messageObject   = {
                            'body'            : headerText,
                            'button_yes'      : 'DELETE',
                            'button_no'       : 'CANCEL',
                            'continue_params' : {
                                                   'promocode_id'   : promocode_id
                                                },
                          };
    callback_warning_modal(messageObject, deletePromocodeConfirmed);
}

function deletePromocodeConfirmed( params ) {
    var promocode_id    = params.data.promocode_id;
    $.ajax({
        url: admin_url + 'promo_code/delete_promocode',
        type: "POST",
        data: {
                "promocode_id": promocode_id
            },
        success: function (response) {
            var removePromocode = $.parseJSON(response);
            if(removePromocode['header']['success'] == true) {
            $('#promocode_row_' + promocode_id).remove();
            __totalPromocodes    = __totalPromocodes - 1;
            refreshListing();
            var messageObject = {
                'body': removePromocode['header']['message'],
                'button_yes': 'OK',
            };
            $("#selected_promocode_count").html('');
            // if($('.promocode-checkbox-parent').prop('checked', true)){
            //     $('.promocode-checkbox-parent').prop('checked', true);
            //     if(__promocode_selected.length >= 1){
            //         $("#selected_promocode_count").html(' (' + (__promocode_selected.length-1) + ')');
            //     }
            // }
            callback_success_modal(messageObject);
            } else {
            var messageObject = {
                'body': removePromocode['header']['message'],
                'button_yes': 'OK',
            };
            callback_danger_modal(messageObject);
            }
        }
    });
}

function changePromocodeStatus(promocode_id, action, promocode_name) {
    var ok_button_text  = (action == '0')?'ACTIVATE':'DEACTIVATE';
    promocode_name      = atob(promocode_name);
    var header_text     = 'Are you sure to ' + (ok_button_text.toLowerCase()) + ' the Discount Coupon named "' + promocode_name.toUpperCase() + '" ?';

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'promocode_id': promocode_id,
            'status': ((action != '0')?'0':'1')
        },
    };
    callback_warning_modal(messageObject, changePromocodeStatusConfirmed);
}

function changePromocodeStatusConfirmed(params) {
    var promocode_id    = params.data.promocode_id;
    var status          = params.data.status;
    $.ajax({
        url: admin_url + 'promo_code/change_promocode_status',
        type: "POST",
        data: {
            "promocode_id": promocode_id,
            "status": status,
        },
        success: function (response) {
            var PromocodeStatus = $.parseJSON(response);
            if (PromocodeStatus['header']['error'] == false) {
                if (__filter_dropdown != 'all') {
                    $('#promocode_row_' + promocode_id).remove();
                    __totalPromocodes = __totalPromocodes - 1;
                    }

                var messageObject = {
                    'body': 'Discount Coupon status changed successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': PromocodeStatus['header']['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function refreshListing() {
    if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
        if ($('.promocode-listing-row').length != 0) {
            __offset = $('.pagination li.active a').attr('data-page');
        } else {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = __offset - 1;
            if (__offset == 0) {
                __offset = 1;
            }
        }
        getPromocodes();
    } else {
        if ($('.promocode-listing-row').length != 0) {
            __offset = $('.pagination li.active a').attr('data-page');
        }
        getPromocodes();
    }
}

function exportPromocodeReport() {
    var keyword         = $('#promocode_keyword').val().trim();
    var filter_by       = __filter_dropdown;
    var param           = {
                            "keyword"   : keyword,
                            "filter"    : filter_by
                            };
    param               = JSON.stringify(param);
    var pathname        = '/admin/promo_code/export_promocode_report';
    var link            = window.location.protocol + "//" + window.location.host + pathname;
    window.location     = link + '/' + btoa(param);
}
