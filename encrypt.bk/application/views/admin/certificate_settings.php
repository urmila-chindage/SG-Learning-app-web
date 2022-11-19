<style>
.innercontent ul.banner-list li a.certificate-item{ height: 100px;}    
</style>
<h3 class="text-center social-heading">Certificate Settings</h3>
<div id="certificate_message"></div>
<div class="col-sm-12">
    <ul class="banner-list" id="certificates_list">
        <?php if (!empty($certificates)): ?>
            <?php foreach ($certificates as $certificate): ?>
                <li class="col-sm-3">
                    <a href="javascript:void(0)" class="banner-thumb certificate-item <?php echo (($certificate['cm_is_active'] == '1') ? 'active-banner' : '') ?>"  data-id="<?php echo $certificate['id']; ?>">
                        <img src="<?php echo certificate_path().$certificate['cm_image'] ?>" width="100%">
                        <span class="triangle"><i class="icon icon-ok-circled"></i></span>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>
<div class="col-sm-12">
    <div class="banner-setting"> 
        <div class="text-center">
            <input name="file" class="logo-image-upload-btn" id="site_certificate_btn" accept=".docx" type="file">
            <button class="btn btn-green pos-abs">UPLOAD CERTIFICATE</button>
        </div>
    </div>
    <div class="banner-upload upload-info">
        Supported File Format : DOCX <br />
        For course name use <b>{Course_name}</b><br />
        For student name use <b>{Name}</b><br />
        For date use <b>{dd-mm-yyyy}</b>
    </div>
    <div class="clearfix progress-custom" id="certificate_progress_div" style="display:none">
        <div class="progress width100">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
                <span class="sr-only">0% Complete</span>
            </div>
        </div>
        <span id="certification_status_wrapper"><b id="certificate_percentage_count">Uploading...</b><b class="percentage-text"></b></span>
    </div>        
</div>                    

<script>
/* Banner Image Upload */
    var __certificateUrl = '<?php echo certificate_path() ?>';
    var __uploading_file_certificate = new Array();
    $(document).on('change', '#site_certificate_btn', function(e){
    	$('#popUpMessage').remove();
    	$('#certificate_progress_div').css('display','block');
        __uploading_file_certificate = e.currentTarget.files;
        //console.log(e.currentTarget.files);
        if( __uploading_file_certificate.length > 1 )
        {
            lauch_common_message('Error Occured', 'You are not allowed to upload more that one file.');
            return false;
        }
        var i                           = 0;
        var uploadURL                   = admin_url+"environment/upload_certificate" ; 
        var fileObj                     = new processFileName(__uploading_file_certificate[i]['name']);
        var param                       = new Array;
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
            param["file"]               = __uploading_file_certificate[i];
            param['processing']         = 'certificate_percentage_count';
            if(param["extension"] == 'docx' )
            {
                $('#certification_status_wrapper').html('<b id="certificate_percentage_count">Uploading...</b><b class="percentage-text"></b>');
                uploadFiles(uploadURL, param, uploadCertificateProcessCompleted);
            }
            else
            {
                __uploading_file_certificate = new Array();
                $('#certificate_message').prepend(renderPopUpMessage('error', 'Invalid file type'));
                $('#site_certificate_btn').val('');
                scrollToTopOfPage();
            }
    });

    function uploadCertificateProcessCompleted(response)
    {
       $('#certificate_progress_div').css('display','none');
       var data             = $.parseJSON(response);
       var  certificateHtml  = '';
            certificateHtml+= '<li class="col-sm-3">'; 
            certificateHtml+= '     <a href="javascript:void(0)" class="banner-thumb certificate-item" data-id="'+data['certificate']['id']+'">';
            certificateHtml+= '         <img src="'+__certificateUrl+data['certificate']['cm_image']+'" width="100%">';
            certificateHtml+= '         <span class="triangle"><i class="icon icon-ok-circled"></i></span>';
            certificateHtml+= '     </a>';
            certificateHtml+= '</li>';
       	$('#certificates_list').prepend(certificateHtml);
       	$('#certificate_message').prepend(renderPopUpMessage('success', data['message']));
        $('#site_certificate_btn').val('');
        scrollToTopOfPage();
        $('.progress-bar').css('width','0');
    }
    
    $(document).on('click','.certificate-item',function(){
        var certificate_id = $(this).attr('data-id');
        $('a.certificate-item').removeClass('active-banner');
        $(this).addClass('active-banner');
        $.ajax({
                url: admin_url+'environment/change_certificate_status',
                type: "POST",
                data:{"is_ajax":true,'certificate_id':certificate_id},
                success: function(response) {
                    var data  = $.parseJSON(response);
                    if(data['error'] != false)
                    {
                        $('#certificate_message').prepend(renderPopUpMessage('error', 'Error occured'));
                        scrollToTopOfPage();
                    }
                }
        });
    });
    
</script>