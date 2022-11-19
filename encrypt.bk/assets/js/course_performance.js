function getSubscribers() {
    // $("#selected_user_count").html('');
    // $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
    // $("#course_bulk").css('display', 'none');
    var keyword = $('#user_keyword').val().trim();

    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__filter_dropdown != '' || __institute_id != '' || __branch_id != '' || __batch_id != '' || __offset != '' || keyword != '') {
            link += '?';
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + __filter_dropdown;
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        if (__institute_id != '') {
            link += '&institute_id=' + __institute_id;
        }
        if (__branch_id != '') {
            link += '&branch_id=' + __branch_id;
        }
        if (__batch_id != '') {
            link += '&batch_id=' + __batch_id;
        }
        alert(__offset);
        __offset = (typeof __offset == 'undefined') ? 1 : __offset;
        link += '&offset=' + __offset;
        window.history.pushState({
            path: link
        }, '', link);
    }
    $.ajax({
        url: admin_url + 'course/enrolled_json',
        type: "POST",
        data: {
            "is_ajax": true,
            "filter": __filter_dropdown,
            'course_id': __course_id,
            'keyword': keyword,
            'institute_id': __institute_id,
            'branch_id': __branch_id,
            'batch_id': __batch_id,
            'limit': __limit,
            'offset': __offset
        },
        success: function (response) {

            var data_user = $.parseJSON(response);
            //$('#loadmorebutton').hide();

            renderPagination(__offset, data_user['total_enrolled']);
            if (data_user['enrolled_users'].length > 0) {
                __offset++;
                if (__offset == 2) {
                    __totalUsers = data_user['total_enrolled'];
                    __shownUsers = data_user['enrolled_users'].length;
                    //remainingUser = (data_user['total_enrolled'] - data_user['enrolled_users'].length);
                    var totalUsersHtml = data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                    scrollToTopOfPage();
                    $('.user-count').html(totalUsersHtml);
                    $('#user_row_wrapper').html(renderSubscribersHtml(response));
                } else {
                    __totalUsers = data_user['total_enrolled'];
                    __shownUsers = ((__offset - 2) * data_user['limit']) + data_user['enrolled_users'].length;
                    //remainingUser = (data_user['total_enrolled'] - (((__offset - 2) * data_user['limit']) + data_user['enrolled_users'].length));
                    var totalUsersHtml = data_user['total_enrolled'] + ' Students'; //(((__offset - 2) * data_user['limit']) + data_user['enrolled_users'].length) + ' / ' + data_user['total_enrolled'] + ' Students';
                    $('.user-count').html(totalUsersHtml);
                    $('.user-checkbox-parent').prop('checked', false);
                    $('#user_row_wrapper').html(renderSubscribersHtml(response));
                }
                // scrollToTopOfPage();

                if (data_user['batches'].length > 0) {
                    $('#filter_batch_div').attr('style', '');
                    var batchHtml = '<li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch(\'all\')">All batches </a></li>';
                    for (var i in data_user['batches']) {
                        var batchNameToolTip = '';
                        if (data_user['batches'][i]['batch_name'].length > 15) {
                            batchNameToolTip = 'data-toggle="tooltip" title="' + data_user['batches'][i]['batch_name'] + '"';
                        }
                        batchHtml += '<li><a href="javascript:void(0)" id="filter_batch_' + data_user['batches'][i]['id'] + '" onclick="filter_batch(' + data_user['batches'][i]['id'] + ')" ' + batchNameToolTip + '>' + data_user['batches'][i]['batch_name'] + '</a></li>';
                    }
                    $('#batch_filter_list').html(batchHtml);
                    if (__batch_id == '') {
                        $('#filter_batch').html('All Batches <span class="caret"></span>');
                    }
                } else {
                    $('#filter_batch_div').css('display', 'none');
                }

            } else {
                $('.user-count').html('No Students');
                scrollToTopOfPage();
                $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                clearUserCache();

            }
            if (data_user['show_load_button'] == true) {
                //$('#loadmorebutton').show();
            }
            //remainingUser = (remainingUser > 0) ? '(' + remainingUser + ')' : '';
            //$('#loadmorebutton').html('Load More ' + remainingUser + '<ripples></ripples>');
            clearUserCache();
        }
    });
}