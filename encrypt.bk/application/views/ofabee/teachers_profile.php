<?php include_once 'header.php'; ?>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/chrome-css.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />

<section>
    <?php //echo '<pre>'; print_r($faculty); die; ?>
<div class="fundamentals fundamentals-alter teacher-profile-wrapper">
    	<div class="container">
        	<div class="container-reduce-width">
             	 <div class="row">
                 	<div class="col-lg-2 col-md-2 col-sm-2">
                    	<div class="teacher-pic-wraper">
                        <?php $user_img     = (($faculty['us_image'] == 'default.jpg')?default_user_path():user_path()); ?>
                        	<img src="<?php echo $user_img.$faculty['us_image']?>" alt="teacher" class="img-responsive img-circle responsive-img-small">
                        </div><!--teacher-pic-wraper-->
                    </div><!--columns-->
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                    	<div class="teacher-name-details">
                        	<h3 class="teacher-full-name"><?php echo $faculty['us_name']; ?></h3>
                            <span class="svg-badge-teacher">
                            <?php if($faculty['us_badge'] == 1 ){ ?>

                           		<img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/img/award-brouns.svg" alt="reputation-badge" class="reputation-badge">
                           
                           <?php  }  ?>
                            </span><!--teacher-name-details-->
                            <span class="department"><?php echo ($faculty['us_degree'])?$faculty['us_degree']:$faculty['rl_name']; ?></span>
                            <span class="star-rating-and-deatails">
                              <?php $rate = "width:0%";
                                    // if($faculty['rating'] != 0){
                                    //       $percentage = 20*$faculty['rating'];
                                    //       $rate = 'width:'.$percentage.'%';
                                    //   } 
                              ?>
                            	<?php /* ?><div class="star-ratings-sprite margin-right"><span style="<?php echo $rate; ?>" class="star-ratings-sprite-rating"></span></div><?php */ ?>
                                <?php /* ?><span class="star-num-rating"><?php echo ($faculty['rating'] != 0)? round($faculty['rating'],1): 0; ?> /5 &nbsp; | &nbsp; <?php echo (isset($reviews_count)? $reviews_count : 0 ); ?>  Reviews</span><?php */ ?>
                                <?php 
                                //$decimal = explode('.', $faculty['rating']);
                                //echo sizeof($decimal) ;die;
                                // if(sizeof($decimal) > 1 )
                                // {
                                //     $faculty_rating = floatval($faculty['rating']);   
                                //     $faculty_rating =  round($faculty_rating, 1);
                                // }
                                // else
                                // {                                    
                                //     $faculty_rating = intval($faculty['rating']).'.0';                                
                                // }
                                //echo is_float($faculty_rating);die;
                                //$faculty_rating = (is_float($faculty['rating']))?round($faculty['rating'],2):(($faculty['rating']>0)?$faculty['rating'].'.0':'0');
                                ?>
                                <?php /* ?><span class="star-num-rating"><?php echo $faculty_rating ?> &nbsp; | &nbsp; <?php echo (isset($rate_rowcount)&&$rate_rowcount>0) ? $rate_rowcount.(($rate_rowcount>1)? ' Ratings':' Rating') : 'No Rating' ; ?> </span><?php */ ?>
                                <?php $experianceHtml  = ''; 
                                      if($faculty['us_experiance'] != '' && $faculty['us_experiance'] > 0 ){ 
                                        $experianceHtml  = '';
                                        $years           = floor($faculty['us_experiance'] / 12); // 1
                                        $remainingMonths = ($faculty['us_experiance'] % 12); // 6
                                        //if($years > 0 )
                                        {
                                            $experianceHtml .= $years.'';
                                        }
                                        if($remainingMonths > 0 )
                                        {
                                            $experianceHtml .= '.'.$remainingMonths;
                                        }
                                ?>
                                <span>Teaching Experience &nbsp;&nbsp;<span class="year-num"><?php echo $years>1?$experianceHtml.' years':$experianceHtml.' year'; ?></span><?php } ?>                          
                            </span><!--star-rating-and-deatails-->
                        </div><!--teacher-name-details-->
                    </div><!--columns-->
                    <?php if (isset($session['id'])): ?>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    	<div class="button-vertical-center">
                    		<input class="btn  btn-orange2 send-msg-btn" value="Send Message" type="button" onclick="redirect_to_message('<?php echo $faculty["id"] ?> ', '<?php echo $faculty["us_name"] ?>')"> 
                        </div><!--button-vertical-center-->
                    </div><!--columns-->
                    <?php endif;?>
                 </div><!--row-->          
            </div><!--container-reduce-width-->
        </div><!--container-->    
</div><!--fundamentals-->
</section>
<div class="tutor-profile-container">
    <section>
        <div class="biography">
            <div class="container">
                <div class="change-size-of-bottom-container">
                    <h3 class="biography-text">Biography</h3>
                    <p class="biography-para" style="white-space: pre-line;"><?php echo $faculty['us_about'] ?></p>
                </div><!--container-reduce-width-->
            </div><!--container-->    
        </div><!--biography-->
    </section>

    <section>
        <div class="expertise-section">
            <div class="container">
                <div class="change-size-of-bottom-container">
                    <div class="expertise-white-canvas">
                        <span class="expertise-text-wraper">
                                <?php if(isset($faculty['expertise']) && !empty($faculty['expertise'])): ?>
                                    <span class="expertise-text">Expertise</span>
                                    <span class="expertise-lists">
                                        <ul class="expertise-ul">
                                        <?php foreach($faculty['expertise'] as $expertise):?>
                                            <li><?php echo $expertise['fe_title']; ?></li>
                                        <?php endforeach; ?>
                                        </ul>
                                    </span><!--expertise-lists-->
                                <?php endif; ?>
                            </span><!--expertise-text-wraper-->
                        
                        <span class="expertise-text-wraper">
                            <span class="expertise-text">From</span>
                            <span class="expertise-lists">
                                <?php echo ($faculty_city['city_name'] && $faculty_state['state_name'])? $faculty_city['city_name'].', '.$faculty_state['state_name']:''; ?>                          
                            </span><!--expertise-lists-->
                        </span><!--expertise-text-wraper-->
                        
                        <span class="expertise-text-wraper">
                            <span class="expertise-text">Speaks</span>
                            <span class="expertise-lists">
                                <?php echo $faculty['us_language_speaks'] ?>   
                            </span><!--expertise-lists-->
                        </span><!--expertise-text-wraper-->
                        
                    </div><!--expertise-white-canvas-->
                </div><!--container-reduce-width-->
            </div><!--container-->
        </div><!--expertise-section-->
    </section>
    <?php if(!empty($faculty['us_youtube_url'])){ if(count(array_filter($faculty['us_youtube_url'])) > 0){ 
            $video_url_count =count(array_filter($faculty['us_youtube_url']));
    if($video_url_count == 1)
            $video_ul_class = 'video-ul-1';
    else if($video_url_count == 2)
            $video_ul_class = 'video-ul-2';
    else
            $video_ul_class = 'video-ul-3-more';
    ?>
    <?php 
    function generate_youtube_url($url=false)
    {
        $pattern = 
            '%^# Match any youtube URL
            (?:https?://)?  # Optional scheme. Either http or https
            (?:www\.)?      # Optional www subdomain
            (?:             # Group host alternatives
            youtu\.be/    # Either youtu.be,
            | youtube\.com  # or youtube.com
            (?:           # Group path alternatives
                /embed/     # Either /embed/
            | /v/         # or /v/
            | /watch\?v=  # or /watch\?v=
            )             # End path alternatives.
            )               # End host alternatives.
            ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
            $%x'
            ;
        $result = preg_match($pattern, $url, $matches);
        if ($result) {
            //return $matches[1];
            return 'https://www.youtube.com/embed/'.$matches[1];
        }
        return false;
    }
    ?> 
    <section>
    <div class="video-section">
        <div class="container">
            <div class="change-size-of-bottom-container">
                <h3 class="biography-text">Demo videos</h3>
                    <div class="row">
                    <ul class="<?php echo $video_ul_class; ?> clearfix"> 

                    <?php for($var=0;$var<3;$var++) { ?>
                        <?php if($faculty['us_youtube_url'][$var] != ''){ ?>
                        <li>
                            <div class="embed-responsive embed-responsive-16by9">
                            <iframe width="auto" height="auto" src="<?php echo generate_youtube_url($faculty['us_youtube_url'][$var]); ?>" frameborder="0" allowfullscreen></iframe>        
                            </div>
                        </li>
                        <?php }} ?>    

                    </ul>
                    </div><!--row-->
                </div><!--change-size-of-bottom-container-->
            </div> <!--container-->   
        </div><!--video-section-->
    </section>
    <?php  } } ?>
</div>
<!-- Modal pop up contents:: Edit Profile Popup-->
<div class="modal fade" id="enquiry-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
                <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true" style="color:#444;">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>

            <form role="form" method="post">
                <!-- Modal Body -->
                <div class="modal-body">
                <div id="edit_enquiry_message" class="edit-profile-message alert alert-success" ></div>
                    <div class="form-group">
                        <?php $user = $this->auth->get_current_user_session('user'); ?>
                        <label for="exampleInputPassword21">Your Name</label>
                        <!-- <input class="form-control" id="user_name" value="<?php // echo (isset($user['us_name'])?$user['us_name']:'') ?>" /> -->
                        <input class="form-control" id="user_name" value="" />
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Your Message</label>
                        <textarea class="form-control" id="user_message" name="user_message" ></textarea>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Upload attachment</label>
                        <input type="file" id="upload" class="form-control" name="attachment">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-green" id="send_enquiry_mail">SEND</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pop up contents:: Edit Profile Popup-->
<div class="modal fade" id="message-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
                <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true" style="color:#444;line-height: 26px;">&times;</span>
                </button>
                <div><h4 class="modal-title" id="myModalLabel">Send Message</h4></div>
            </div>
                <!-- Modal Body -->
                <div class="modal-body">
                <div id="send_message_to_faculty" class="edit-profile-message alert alert-success" ></div>
                    <div class="form-group">
                        <?php $user = $this->auth->get_current_user_session('user'); ?>
                        <label for="exampleInputPassword21">Your Subject*</label>
                        <input class="form-control" type="hidden" id="message_faculty_id" value="" />
                        <input class="form-control" type="text" id="message_subject" value="" />
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Your Message*</label>
                        <textarea class="form-control" id="message_body" name="message_body" ></textarea>
                    </div>
                   
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-red sendbtn-orange" data-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-green sendbtn-green" id="send_message_faculty">SEND</button>
                </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function () {
    $(".profilelist-childs").slice(0, 5).show();
    $("#Show-more-reviews-two").on('click', function (e) {
        e.preventDefault();   
        $(".profilelist-childs:hidden").slideDown();
        if ($(".profilelist-childs:hidden").length == 0) {
             $("#load").fadeOut('slow');
      
        }
//        $('html,body').animate({
//            scrollTop: $(this).offset().top
//        }, 1500);
    $("#Show-more-reviews-two").remove();
    });
});
/*
* Implementtion of enquiry functionality in teacher's profile form
* Modified by Neethu KP
* Modified at 10/01/2017
*/
var __site_url          = '<?php echo site_url() ?>';
var __userId            = '<?php echo $UserId; ?>';
function enquiryPopUp(teacherId, teacherName)
{
  
        if(__userId > 0)
        {
          $('#user_message, #user_name').removeClass('form-error');
          $('#edit_enquiry_message').html('').hide();
          $('#myModalLabel').html('Send message to "'+teacherName+'"');
          $('#user_message').val('');
          $('#send_enquiry_mail').unbind('click');
          $('#send_enquiry_mail').click({'teacherId':teacherId, 'teacherName':teacherName}, sendEnquiryMail);
          $('#enquiry-popup').modal();
          
        }else{
          window.location.href = __site_url+'login';
        }
        
}

function redirect_to_message(teacherId, teacherName)
{
    var tutor_id=teacherId;
    $R('#message_body');
    $('#message_faculty_id').val(tutor_id);
    $('#send_message_to_faculty').removeClass('alert-success alert-warning alert');
    $('#send_message_to_faculty').html('');
    $("#message-popup").modal();
}
$("#send_message_faculty").on('click', function (e) {
    var message_subject = $('#message_subject').val();
    var message_body    = btoa($('#message_body').val());
    var faculty_id      = $('#message_faculty_id').val();
    var errorcount      =0;
    var errormessage    = '';
    if(message_subject=='')
    {
        errormessage +='Subject should not be empty!</br>';
        errorcount++;
    }

    if(message_body=='')
    {
        errormessage +='Message should not be empty!</br>';
        errorcount++;
    }

    if(errorcount>0)
    {
        $('#send_message_to_faculty').removeClass('alert-success');
        $('#send_message_to_faculty').addClass('alert alert-warning');
        $('#send_message_to_faculty').html(errormessage+' <a class="close" style="margin-top:-0.9em;" onclick="closePopup();" id="dismiss_message_error">×</a> ').show();
    }
    else 
    {
        $('#send_message_faculty').html('Sending...');
        $.ajax({
            url: __site_url+'teachers/send_message',
            type: "POST",
            data:{ 'faculty_id':faculty_id, 'message_subject':message_subject, 'message_body':message_body, 'is_ajax':true},
            success: function (response)
            {
                var data  = $.parseJSON(response);
                if(data['success'] == true)
                {
                    $('#message_subject').val('');
                    $('#message_body').val('');
                    $('#message_body').redactor('insertion.set','');
                    $('#send_message_to_faculty').removeClass('alert-warning');
                    $('#send_message_to_faculty').addClass('alert alert-success');
                    $('#send_message_to_faculty').html(data['message']+' <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a> ').show();
                    //$("#message-popup").hide();
                    //setTimeout(function() {$('#message-popup').modal('hide');}, 2000);
                }
                else 
                {
                    $('#send_message_to_faculty').removeClass('alert-success');
                    $('#send_message_to_faculty').addClass('alert alert-warning');
                    $('#send_message_to_faculty').html(data['message']+' <a class="close" onclick="closePopup();"  id="dismiss_message_error">×</a> ').show();
                }
                
                $('#send_message_faculty').html('SEND'); 
            },
        });
    }
    
});
function closePopup()
{
    $("#send_message_to_faculty").hide();
}
function sendEnquiryMail(param)
{
    var enquiryMessage  = $('#user_message').val();
    var userName        = $('#user_name').val();
    var errorCnt        = 0;
    $('#user_message, #user_name').removeClass('form-error');
    if(enquiryMessage=='')
    {              
        $('#user_message').addClass('form-error');
        errorCnt++;
    }
    if(userName=='')
    {              
        $('#user_name').addClass('form-error');
        errorCnt++;
    }
    if(errorCnt>0)
    {
        return false;
    }
    $('#send_enquiry_mail').html('SENDING...');
    $.ajax({
        url: __site_url+'teachers/send_enquiry_mail',
        type: "POST",
        data:{ 'tutor_id':param.data.teacherId, 'user_name':userName, 'user_message':enquiryMessage, 'is_ajax':true},
        success: function (response){
            $('#edit_enquiry_message').html('Your message is send to '+param.data.teacherName+' <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a> ').show();
            $('#send_enquiry_mail').html('SEND');
            /*setTimeout(function(){
              $('#enquiry-popup').modal('hide');
            }, 1000)*/
        },
    });
}
</script>
<style>
.form-error{ border: 1px solid #ff7a7a;}
.edit-profile-message{display: none;}
#load_more_btn a{ display: none;}
html last-child { border-bottom: 10px; }
</style>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<?php include_once 'footer.php'; ?>