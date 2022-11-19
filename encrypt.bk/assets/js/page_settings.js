$(document).ready(function(e) {
    var page_top_offset = $(".header").height() + $(".breadcrumb").height() + $(".courses-tab").height();
    $('#p_content').redactor({
        toolbarFixedTopOffset: page_top_offset,
        maxHeight: '320px',
        minHeight: '320px',
        imageUpload: admin_url + 'configuration/redactore_image_upload',
        plugins: ['table', 'alignment', 'source'],
        callbacks: {
            image: {
                uploadError: function(response)
                {
                    console.log(response.message);
                    $('#redactor_image_upload_response').remove();
                    $('.redactor-modal-body').append(`<div id="redactor_image_upload_response" class="alert alert-danger alert-dismissible" style="margin-top: 20px;">
                    <a href="#" class="close" aria-label="close" onclick="$('.alert').remove()">&times;</a>
                    <strong>${response.message}</strong>
                  </div>`);
                }
            },
            file: {
                uploadError: function(response)
                {
                    console.log(response.message);
                }
            }
        }
    });
    
});

function validURL(Url) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(Url);
}

if ($('#p_goto_external_url').is(':checked') == true) {
    //$('#p_external_url').attr("required", true);
}

//$(document).on('keyup', '#p_external_url', function(){
$("#p_external_url").bind("paste", function(e){
    var iframeUrl = e.originalEvent.clipboardData.getData('text');
    
    if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(iframeUrl)){
        if(!iframeUrl.match(/https/g)){
            $('#externaliframe').html(`<center style="margin-top: 180px;">Only secured sites are allowed</center>`);
            return; 
        }
        $('#externaliframe').html(`<center style="margin-top: 180px;">Page preview is loading...</center>`);
    } else {
        $('#externaliframe').html(`<center style="margin-top: 180px;">NO PREVIEW</center>`);
        return;
    }
    
    var iframe = document.createElement('iframe');
        iframe.src = iframeUrl; 
        iframe.id  = 'externaliframelink';
        $('#externaliframe').html(iframe);
        $('#p_external_url').focus();
});

$(document).on('keyup', '#p_external_url', function(){
    var iframeUrl = $('#p_external_url').val();
    if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(iframeUrl)){
        if(!iframeUrl.match(/https/g)){
            $('#externaliframe').html(`<center style="margin-top: 180px;">Only secured sites are allowed</center>`);
            return; 
        }
        $('#externaliframe').html(`<center style="margin-top: 180px;">Page preview is loading...</center>`);
    } else {
        $('#externaliframe').html(`<center style="margin-top: 180px;">NO PREVIEW</center>`);
        return;
    }
    
    var iframe = document.createElement('iframe');
        iframe.src = iframeUrl; 
        iframe.id  = 'externaliframelink';
        $('#externaliframe').html(iframe);
        $('#p_external_url').focus();
});

$(document).on('click', '#p_goto_external_url', function() {
    if ($(this).is(':checked') == true) {
        $('.external_url_wrapper').show();
        $('#externaliframe').show();
        var iframeUrl = $('#p_external_url').val();
        //$('#p_external_url').attr("required", true);
        $('.internal_page_wrapper').hide();
        if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(iframeUrl)){
            if(!iframeUrl.match(/https/g)){
                $('#externaliframe').html(`<center style="margin-top: 180px;">Only secured sites are allowed</center>`);
                return; 
            }
            $('#externaliframe').html(`<center style="margin-top: 180px;">Page preview is loading...</center>`);
        } else {
            $('#externaliframe').html(`<center style="margin-top: 180px;">NO PREVIEW</center>`);
            return;
        }
        var iframe = document.createElement('iframe');
       // iframe.onload = function() { iframes = iframe;console.log('myframe is loaded'); }; // before setting 'src'
        iframe.src = iframeUrl; 
        iframe.id  = 'externaliframelink';
        $('#externaliframe').html(iframe);
        $('#p_external_url').focus();
    } else {
        $('.internal_page_wrapper').show();
        $('.external_url_wrapper').hide();
        $('#externaliframe').hide();
        //$('#p_external_url').val($('#p_external_url').val()?$('#p_external_url').val():'');
        //$('#p_external_url').attr("required", false);
    }

});

function canShowCategory(option_id) {
    $('.external_category_wrapper').hide();
    $('#p_category').val(0);
    switch (option_id) {
        case "0":
            /*$.ajax({
                url: admin_url+'page/get_all_pages',
                type: "POST",
                data:{"is_ajax":true},
                success: function(response) {
                    console.log(response);
                    var data = $.parseJSON(response);
                    $('#p_parent_id').html('<option value="" >Choose Page</option>'+parsePageTree(data['pages'], ""));
                }
            });*/
            $('#page_parent_id').hide();
            $('#p_parent_id').html('');
            break;
        case "5":
            $('#page_parent_id').hide();
            $('#p_parent_id').html('');
            break;
        case "1":
            // $('#page_parent_id').show();
            // $.ajax({
            //     url: admin_url+'page/get_header_pages',
            //     type: "POST",
            //     data:{"is_ajax":true},
            //     success: function(response) {
            //         var data = $.parseJSON(response);
            //         $('#p_parent_id').html('<option value="" >Choose Parent Menu</option>'+parsePageTree(data['pages'], ""));
            //     }
            // });
            break;
        case "2":
            // $('#page_parent_id').show();
            // $.ajax({
            //     url: admin_url+'page/get_footer_pages',
            //     type: "POST",
            //     data:{"is_ajax":true},
            //     success: function(response) {
            //         var data = $.parseJSON(response);
            //         $('#p_parent_id').html('<option value="" >Choose Parent Menu</option>'+parsePageTree(data['pages'], ""));
            //     }
            // });
            break;
        case "4":
            $('#page_parent_id').show();
            $('.external_category_wrapper').show();
            $('#p_parent_id').html('');
            break;

    }
}

$(document).on('change', '#p_category', function() {
    var category_id = $('#p_category').val();
    if (category_id > 0) {
        $.ajax({
            url: admin_url + 'page/get_category_pages',
            type: "POST",
            data: { "is_ajax": true, 'category_id': category_id },
            success: function(response) {
                console.log(response);
                var data = $.parseJSON(response);
                $('#p_parent_id').html('<option value="" >Choose Page</option>' + parsePageTree(data['pages'], ""));
            }
        });
    } else {
        $('#p_parent_id').html('');
    }
});

function parsePageTree(pages, dash) {
    var pageHtml = '';
    if (Object.keys(pages).length > 0) {
        $.each(pages, function(index, page) {
            if (__page_id != page['id']) {
                pageHtml += '<option value="' + page['id'] + '" >' + dash + page['p_title'] + '</option>';
            }
            if (Object.keys(page['children']).length > 0) {
                pageHtml += parsePageTree(page['children'], dash + '-');
            }
        });
    }

    return pageHtml;
}