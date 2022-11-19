function dropBoxFileUpload() {
    options = {
        // Required. Called when a user selects an item in the Chooser.
        success: function (files) {
            // console.log(decodeURIComponent(files[0].link));
            __uploading_file = files;
            $('#percentage_bar').hide();
            $('#attached_file_name').html('<label>' + lang('attached') + ' : ' + files[0]['name'] + '</label>')
            //saveLecture();
            $('#section_id').html(getSectionsOptionHtml());
            $("#upload-lecture").modal();
            $('#save_lecture').unbind();
            $('#save_lecture').click({}, saveLectureFromDropbox);
        },

        // Optional. Called when the user closes the dialog without selecting a file
        // and does not include any parameters.
        cancel: function () {

        },

        // Optional. "preview" (default) is a preview link to the document for sharing,
        // "direct" is an expiring link to download the contents of the file. For more
        // information about link types, see Link types below.
        linkType: "direct", // or "direct" preview

        // Optional. A value of false (default) limits selection to a single file, while
        // true enables multiple file selection.
        multiselect: false, // or true

        // Optional. This is a list of file extensions. If specified, the user will
        // only be able to select files with these extensions. You may also specify
        // file types, such as "video" or "images" in the list. For more information,
        // see File types below. By default, all extensions are allowed.
        extensions: __allowed_files,
    };
    Dropbox.choose(options);
}

function saveLectureFromDropbox() {
    //getting file details
    var section_id = $('#section_id').val();
    var section_name = $('#section_name').val();
    var lecture_name = $('#lecture_name').val();
    var lecture_description = $('#lecture_description').val();
    var sent_mail_on_lecture_creation = $('#sent_mail_on_lecture_creation').prop('checked');
    sent_mail_on_lecture_creation = (sent_mail_on_lecture_creation == true) ? '1' : '0';
    var errorCount = 0;
    var errorMessage = '';
    var lecture_image = $('#lecture_image_add').attr('image_name');

    //end

    //validation process
    if (lecture_name == '') {
        errorCount++;
        errorMessage += 'please enter lecture name <br />';
    }
    if ((cb_has_lecture_image == 1) && lecture_image == 'default-lecture.jpg' && ($('#lecture_logo_btn')[0].files[0] == undefined)) {
        errorCount++;
        errorMessage += 'Please upload lecture image.<br>';
    }

    if (__create_section_as_new == true && section_name == '') {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if (__create_section_as_new == false && section_id == '') {
        errorCount++;
        errorMessage += 'please choose section <br />';
    }

    if (lecture_description == '') {
        errorCount++;
        errorMessage += 'please enter lecture description<br />';
    }
    cleanPopUpMessage();
    if (errorCount > 0) {
        $('#upload-lecture .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    //End of validation

    var i = 0;
    var uploadURL = admin_url + "coursebuilder/upload_from_dropbox";
    var fileObj = new processFileName(__uploading_file[i]['name']);

    var __video_format = new Array('mp4', 'flv', 'avi', 'f4v');
    var __document_format = new Array('doc', 'docx', 'pdf', 'ppt', 'pptx');





    var param = new Array;
    param["file_name"] = fileObj.uniqueFileName();
    param["extension"] = fileObj.fileExtension();
    param["file"] = JSON.stringify(__uploading_file[i]);





    //setting lecture details in post array
    if (__create_section_as_new == true) {
        param["section_name"] = section_name;
        param["section_id"] = '';
    } else {
        param["section_name"] = '';
        param["section_id"] = section_id;
    }
    param["lecture_name"] = lecture_name;
    param["course_id"] = __course_id;
    param["lecture_description"] = lecture_description;
    param["sent_mail_on_lecture_creation"] = sent_mail_on_lecture_creation;

    if (inArray(param["extension"], __video_format)) {
        param["engine"] = 'video';
    }

    if (inArray(param["extension"], __document_format)) {
        param["engine"] = 'document';
    }
    //end
    console.log(param);
    uploadFiles(uploadURL, param, uploadFileFromDropboxCompleted);
}

function uploadFileFromDropboxCompleted(response) {
    //console.log(response);
    var data = $.parseJSON(response);
    if (data['status'] == 'true') {
        window.location = admin_url + 'lecture/' + data['lecture_id'];
    } else {
        alert(data['message']);
    }
}