function showInputDisc(id) {
    $('#input_' + id).show();
}

$(document).ready(function() {
    $('#reply_disc_admin').redactor({
        minHeight: 250,
        maxHeight: 250,
        imageUpload: admin_url + 'configuration/redactore_image_upload',
        plugins: ['table', 'alignment', 'source'],
        callbacks: {
            imageUploadError: function(json, xhr) {
                return false;
            }
        }
    });
});


$(document).on('keyup', '#reply_disc_admin', function(e) {
    var text_id_new = $(this).data('id'); //getter
    if (e.keyCode == '13') {
        if (this.value != "") {
            sendDiscReply(text_id_new, this.value);
            $(this).val('');
            $('#input_' + text_id_new).hide();
        }
    }
});

/* Function for posting admin comments as reply */
function sendDiscReply(comment_id, value) {
    $.ajax({
        url: admin_url + 'course/reply_admin_discussion',
        type: "POST",
        data: {
            "is_ajax": true,
            'course_id': __course_id,
            'comment_id': comment_id,
            'comment': value
        },
        success: function(response) {
            //var data           = $.parseJSON(response);
        }
    });
}

function changeCourseStatus(course_id, header_text, message) {
    $('#confirm_box_title').html(atob(header_text));
    $('#confirm_box_content').html(message);
    $('#confirm_box_ok').unbind();
    $('#confirm_box_ok').click({
        "course_id": course_id
    }, changeStatusConfirmed);
}

function changeStatusConfirmed(params) {
    $.ajax({
        url: admin_url + 'course/change_status',
        type: "POST",
        data: {
            "course_id": params.data.course_id,
            "is_ajax": true
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data['error'] == false) {
                var statusClass = 'bg-red';
                if (data['actions']['deleted'] == 0) {
                    if (data['actions']['status'] == 1) {
                        statusClass = 'bg-green';
                    } else {
                        statusClass = 'bg-yellow';
                    }
                }
                $('#status_badge').removeClass('bg-yellow').removeClass('bg-green').removeClass('bg-red').html(data['actions']['action']).addClass(statusClass);
                $('#action_class_' + params.data.course_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass(data['actions']['action_class']).html(data['actions']['action']);
                $('#label_class_' + params.data.course_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass(data['actions']['label_class']).html(data['actions']['label_text']);
                $('#status_btn_' + params.data.course_id).html(data['action_list']);
                $('#publish-course').modal('hide');
            } else {
                //$('#confirm_box_title').html(data['message']);
                //$('#confirm_box_content').html('');
                lauch_common_message('Error occured', data['message']);
                $('#publish-course').modal('hide');
            }
        }
    });
}


function restoreCourse(course_id, header_text) {
    $('#confirm_box_title').html(atob(header_text));
    $('#confirm_box_content').html('');
    $('#confirm_box_ok').unbind();
    $('#confirm_box_ok').click({
        "course_id": course_id
    }, restoreCourseConfirmed);
}

function restoreCourseConfirmed(params) {
    $.ajax({
        url: admin_url + 'course/restore',
        type: "POST",
        data: {
            "course_id": params.data.course_id,
            "is_ajax": true
        },
        success: function(response) {
            console.log(response);
            var data = $.parseJSON(response);
            if (data['error'] == false) {
                $('#status_badge').removeClass('bg-yellow').removeClass('bg-green').removeClass('bg-red').html(data['actions']['action']).addClass('bg-yellow');
                $('#course_action_' + params.data.course_id).html(data['action_list']);
                $('#action_class_' + params.data.course_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass(data['actions']['action_class']).html(data['actions']['action']);
                $('#label_class_' + params.data.course_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass(data['actions']['label_class']).html(data['actions']['label_text']);
                $('#status_btn_' + params.data.course_id).html(data['actions']['button_text']);
                $('#publish-course').modal('hide');
            } else {
                $('#confirm_box_title').html(data['message']);
                $('#confirm_box_content').html('');
            }
        }
    });
}