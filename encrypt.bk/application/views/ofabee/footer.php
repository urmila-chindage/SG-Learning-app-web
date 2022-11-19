<script> var showHidInformationPopUp = false;  </script>
<?php if(!empty($information_bars)):?>
<style>
.information-modal strong{word-break: break-word;}
</style>
<!-- Notification modal starts here -->
<div class="information-modal modal fade " id="information-modal" role="dialog" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div class="">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="text-center information-content ">
                <div id="myCarousel" class="carousel slide" data-ride="carousel">
               
                <!-- Wrapper for slides -->
                <div class="carousel-inner">
                    <?php $active = 'active'; $informatin_popup = 0; foreach($information_bars as $information_bar): ?> 
                        <?php if($information_bar['n_notification_bar_type'] == 1) : $informatin_popup++;?>
                            <div class="item <?php echo $active; $active = '';?>">
                                <div class="slide text-center">
                                    <?php echo $information_bar['n_content']; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?> 
                </div>
               
            </div>
          
            <div class="close-btn-holder">
                <button type="button" class="btn close-btn-orange info-close" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
<!-- Notification modal ends here -->
<script> showHidInformationPopUp = ('<?php echo $informatin_popup; ?>' > '0')?true:false;  </script>
<?php endif;?>
<!--footer section starts here-->

<!-- Footer -->
<?php $CI = & get_instance(); ?>
<?php $user_id = $CI->session->userdata('user'); //print_r($user_id); //exit;?>
<style>
.copyright-footer-row{
    display: flex;
    justify-content: space-between;
    padding: 15px 0px 0px 0px;
}
.copyright-footer-row a{color: #fff;text-decoration: none;}
.copyright-footer-row .support-link{margin-right: 20px;}
.copyright-footer-row .footer-widget h3{margin-bottom: 10px;}
.footer-widget .contact-link{
    display: flex;
    align-items: center;
}
.footer-widget .footer-links li {
    padding: 5px 0px;
}

.page-footer{
    bottom: 0 !important;  
    width: 100% !important; 
}
.error-border {border: 1px solid #d43525 !important;}
.footer-widget .form-control.error-border{margin-bottom: 5px;}
.footer-widget .validation-msg{margin-bottom: 6px;}
.footer-widget #success-msg{
    padding: 0px 0px 15px 0px;
    color: #8fb548;
    text-transform: capitalize;
}
.information-content .item .slide{
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}
</style>

<?php if(!isset($_GET['privacy'])): ?>
<footer class="page-footer font-small blue pt-4 footer-group">
    <section id="footer-group">
    <div class="footer-group">
    <div class="container footer-large">
        <div class="">
            <div class="col-md-4 col-sm-12 col-xs-12 mobile-full-width p-0">
                <h3 class="quicklink-title">Contact Us</h3>
                <div class="footer-widget">
                    <!-- <h3 class="quicklink-title">About Us</h3> -->
                    <!-- <p><?php // echo config_item('meta_description'); ?></p> -->
                    <!-- <div class="col-xs-6 col-sm-4 col-md-3 mobile-full-width"> -->
                        <div class="footer-widget">
                            <?php 
                                $site_contact_settings  = $this->settings->setting('website');
                                $site_contact_details   = $site_contact_settings['as_setting_value'];
                                $contacts               = $site_contact_details['setting_value'];
                            ?>
                            <ul class="footer-links">
                                <li>
                                    <div class="footer-address-column">
                                        <div class="map-icon">
                                            <img src="<?php echo assets_url() ?>images/footer-icons/map.svg">
                                        </div>
                                        <div class="contact-info-col"><?php echo $contacts->site_address;?></div>
                                    </div>
                                </li>
                                <li class="contact-link">
                                    <div class="foot-icons">
                                        <img src="<?php echo assets_url() ?>images/footer-icons/mail.png">
                                    </div>
                                    <span class="contact-info-col"> <a href="mailto:<?php echo $contacts->site_email;?>" target="_top"><?php echo $contacts->site_email;?></a></span>
                                </li>
                                <li class="contact-link">
                                    <div class="foot-icons">
                                        <img src="<?php echo assets_url() ?>images/footer-icons/phone.png">
                                    </div>
                                    <span class="contact-info-col"> <a href="tel:<?php echo $contacts->site_phone;?>" target="_blank"><?php echo $contacts->site_phone;?></a></span>
                                </li>
                            </ul>
                        </div>
                    <!-- </div>  -->
                </div>
            </div>

            <?php $footer_pages_array = menu_pages(array('type' => 'footer')); 

            ?>
            <div class="col-xs-12 col-sm-12 col-md-4 mobile-full-width p-0 <?php if(empty($footer_pages_array)):?> dynamic-link-hidden-xs <?php endif; ?>" style="padding-left:0px">
                <?php if(!empty($footer_pages_array)):?>
                    <?php $footer_pages = array_chunk($footer_pages_array, ceil(count($footer_pages_array) / 2));?>
                        <?php if(!empty($footer_pages_array)): ?>
                        <h3 class="quicklink-title">Quick Links</h3>
                        <div class="footer-widget col-xs-12 col-sm-12 col-md-6 no-padding">
                            <ul class="footer-links">
                                <?php foreach($footer_pages[0] as $footer_page): ?>
                                    <?php 
                                        $showLinkC  = (($footer_page['mm_connected_as_external'] == '1' ) ? $footer_page['mm_external_url']: $footer_page['mm_item_connected_slug']);
                                        $page_url   = site_url($footer_page['mm_item_connected_slug']);
                                        $attributes = ((!empty($footer_page['mm_new_window'])) ? 'target="_blank"':'');
                                        $page_url   = (!empty($footer_page['mm_connected_as_external']) && $footer_page['mm_connected_as_external'] == '1' ) ? $footer_page['mm_external_url']: $page_url;
                                    ?>
                                    <?php if($showLinkC):?>
                                        <li>
                                            <img class="green-chevron" src="<?php echo assets_url() ?>images/chevron-green.svg">
                                            <a href="<?php echo $page_url; ?>" <?php echo $attributes;?>><?php echo ucfirst($footer_page['mm_name']);?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>   
                        <div class="footer-widget col-xs-12 col-sm-12 col-md-6 no-padding">
                            <ul class="footer-links">
                            <?php if(isset($footer_pages[1]) && !empty($footer_pages[1])):?>
                                <?php foreach($footer_pages[1] as $footer_page): ?>
                                    <?php 
                                        $showLinkC  = (($footer_page['mm_connected_as_external'] == '1' ) ? $footer_page['mm_external_url']: $footer_page['mm_item_connected_slug']);
                                        $page_url   = site_url($footer_page['mm_item_connected_slug']);
                                        $attributes = ((!empty($footer_page['mm_new_window'])) ? 'target="_blank"':'');
                                        $page_url   = (!empty($footer_page['mm_connected_as_external']) && $footer_page['mm_connected_as_external'] == '1' ) ? $footer_page['mm_external_url']: $page_url;
                                    ?>
                                    <?php if($showLinkC): ?>
                                        <li>
                                            <img class="green-chevron" src="<?php echo assets_url() ?>images/chevron-green.svg">
                                            <a href="<?php echo $page_url; ?>" <?php echo $attributes;?>><?php echo ucfirst($footer_page['mm_name']);?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </ul>
                        </div>        
                           
                        <?php endif; ?>
                    <?php endif; ?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4 mobile-full-width p-0">                       
                <div class="footer-widget">
                    <h3 class="quicklink-title" id="price-table-trigger">Get in Touch</h3>
                    <div class="form-group">
                        <div class="footer-widget-input">
                            <input class="form-control" placeholder="Name" type="text" name="" value="" id="contact_name">
                            <div id="conract-error-name" class="validation-msg"></div>
                        </div>
                        <div class="footer-widget-input">
                            <input class="form-control" placeholder="Email" type="email" name="" value="" id="contact_email">
                            <div id="conract-error-email" class="validation-msg"></div>
                        </div>
                        <div class="footer-widget-textarea">
                            <textarea class="form-control" placeholder="Message" rows="4" name="" id="contact_msg"></textarea>
                            <div id="conract-error-message" class="validation-msg"></div>
                        </div>
                        <div class="form-group text-right d-flex align-center justify-between">
                            <div class="col-md-6 pad0"><div id="success-msg"></div></div>
                            <div class="col-md-6 pad0"><button onclick="getInTouch()" id="contact_submit" class="btn send-btn" type="">Send</button></div>
                        </div>
                    </div>
                </div>
            </div>   
        </div>
    </div>
    <?php 
        $site_social_settings   = $this->settings->setting('social_links');
        $site_social_links      = $site_social_settings['as_setting_value'];
        $social_links           = $site_social_links['setting_value'];
    ?>
    <!-- Bottom footer starts here -->
    <div class="bottom-line"></div>
        <div class="col-xs-12 col-sm-12 copyright-footer">
            <div class="scrolltop-icon">
                <img src="<?php echo assets_url() ?>images/footer-icons/scroll-top.png" alt="">
            </div>
            <div class="copyright-footer-row container">
                <div class="footer-info-left-column col-xs-12 col-md-6">
                    <div class="social-links-wrapper">
                    <?php $site_whatsapp_number =  str_replace(array('+','-'), '', $contacts->site_whatsapp_number); ?>
                        <a href="https://api.whatsapp.com/send?phone=<?php echo $site_whatsapp_number;?>" target="_blank">
                            <div class="whatsapp-link">
                                <img src="<?php echo assets_url() ?>images/footer-icons/whatsapp.png">
                                <span>Chat with us</span>
                            </div>
                        </a>
                        <ul>
                            <?php if($social_links->facebook):?>
                                <li class="footer-social-links">
                                    <a href="<?php echo $social_links->facebook;?>" target="_blank">
                                        <div class="fs-icon footer-social-facebook"></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($social_links->twitter):?>
                                <li class="footer-social-links">
                                    <a href="<?php echo $social_links->twitter;?>" target="_blank">
                                        <div class="fs-icon footer-social-twitter"></div>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="footer-copyright-info"><a href="javascript:void(0)">&copy; <?php echo date('Y') ?> <?php echo config_item('site_name'); ?></a></div>
                </div>

                <div class="footer-info-right-column col-xs-12 col-md-6">
                    <div class="secure-seal">
                        <img src="<?php echo assets_url() ?>images/footer-icons/secure-seal.png" class="img-responsive" alt="">
                    </div>
                    <div class="secure-payment-info">
                        <h4>Secure Payment with:</h4>
                        <img src="<?php echo assets_url() ?>images/footer-icons/payment-icons.png" class="img-responsive" alt="">
                    </div>

                    <?php // if (isset($session['id'])): ?>
                        <!-- <a href="https://SGlearningapphelpdesk.freshdesk.com/support/tickets/new" target="_blank" class="support-link">Customer Care</a> -->
                    <?php // endif; ?>
                        <!-- <a href="https://SGlearningapphelpdesk.freshdesk.com/support/home" target="_blank">FAQ</a> -->
                </div>
            </div>
        </div>
    </div>
    <!-- Bottom footer ends here -->
</section>
<div id="verification_success_modal" class="modal info-modal info-modal-container" style="display: none;">
    <div class="modal-content">
        <span class="close" data-dismiss="modal">×</span>
        <div id="enroll_modal_img" class="icon-holder text-center">
            
        </div>
        <p id="enroll_modal_content" class="text-center discussion-info-content">
        The verification email has been sent. Check your inbox and verify.​
        </p>
        <div class="text-center">
            <!-- <button data-dismiss="modal" type="" id="enroll_modal_cancel" style="text-transform:uppercase" class="custom-btn">CANCEL</button> -->
            <button  data-dismiss="modal" class="custom-btn">Ok</button>
        </div>
    </div>
</div>
</footer>
<?php endif; ?>
<!-- Footer -->

<!--footer section ends here-->

<!-- Invitation popup by Alex -->
<?php //include_once('invite.php'); ?>

<!-- End invitation popup By Alex -->

<?php 
    switch ($this->router->fetch_class()) {
        case 'dashboard':
            include_once 'dashboard_modals.php';
            break;
        case 'material':
            include_once 'material_modals.php';
            break;
        }    
?>

<?php
//convert to mins

function convert_to_minutes($seconds)
{
    $duration_in_minutes     = $seconds/60;
    $cut_duration_in_minutes = substr($duration_in_minutes, 0, 4);
    
    return $cut_duration_in_minutes;
}
?>

<?php /* ?>
//usleep(5000000);
$survey_status = check_survey_status();
$today         = date('d-m-Y');
if (strtotime($today) >= strtotime($survey_status['s_start_date']) && strtotime($today) <= strtotime($survey_status['s_end_date']) && $this->router->fetch_class()!='survey') {
    if(!isset($_COOKIE['surveycookie']) && !isset($_COOKIE['surveytakencookie'])){
        echo '<script>setTimeout(function(){  var surveyModal = $("#survey_modal").modal({backdrop: "static", keyboard: false});  }, 10000);</script>';
    }
}
else{
    //echo 'Passed';
}
?><?php */ ?>
<style type="text/css">
.callus .right a.site-mail {
    color: #fff;
    text-decoration: none;
}
</style>

<script type="text/javascript">
    // Owl Slider
    //Tabs
    var __ntheme_url    = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
    var __notifications = [];    
    var siteUrl         = '<?php echo site_url() ?>';
    var __userId        = "<?php echo $user_id['id']; ?>";
    var __userToken     = btoa("<?php echo fetch_user_token('user') ?>");
    var __classUrl      = '<?php echo $this->uri->segment("1")?>';
    //alert(__userId);

    var myCookie = getCookie("messageCount");
    function checkMessageCount(){
        console.log('checkMessageCount');
        if (myCookie != "") {
            $('#message_count_wrapper').show();
            $('#message_count').html(myCookie);
            $('#mobilemessage').show();
            $('#mobilemessage').html(myCookie);
            console.log('myCookie exists');
        }else{
            console.log('myCookie not exists fetching...');
            //var user = {"userId":__userId};
            $.ajax({
                type    : "POST",
                url     : "<?php echo config_item('message_api_url').'getmessagecount' ?>",
                headers : {"Authorization": 'Bearer '+atob(__userToken)},
                data    : {"userId":__userId}, //{action:'x',params:['a','b','c']},
                success : function (response) {
                    // var data = $.parseJSON(response);
                    console.log(response,'response');
                    if(response.success == true){
                        if(response.count>0){
                            var msgCount = response.count;
                            console.log(msgCount, 'msgCount');
                            setcookie('messageCount',msgCount,'1');
                            $('#message_count_wrapper').show();
                            $('#message_count').html(msgCount);
                            $('#mobilemessage').show();
                            $('#mobilemessage').html(msgCount);
                        }
                    }
                
                }
            });
        }                         
    }
    
    function setcookie(name, value, days)
    {
        if (days)
        {
            var date = new Date();
            date.setTime(date.getTime()+days*60*1000); // ) removed
            var expires = "; expires=" + date.toGMTString(); // + added
        }
        else{
            var expires = "";
            
        }
        document.cookie = name+"=" + value+expires + ";path=/"; // + and " added
    }

    (function() {
        var nElement = document.getElementById('site_notification_count_wrapper');
        if(nElement)
        {
            nElement.style.display = "none";
        }
        <?php if(isset($session) && !empty($session)): ?>
        setTimeout(function(){
            $.ajax({
                type: "GET",
                url: '<?php echo site_url('dashboard/notification_count'); ?>',
                success: function (response) {
                    var data = $.parseJSON(response);
                    if(+data['count']>0){
                        $('#site_notification_count_wrapper').show();
                        $('#site_notification_count').html(data['count']);
                        $('#mobilenotify').show();
                        $('#mobilenotify').html(data['count']);
                    }
                }
            });
            if(__classUrl != 'messages'){
                checkMessageCount();
            }
        },1000);
        <?php endif; ?>
    })();

    $('body').click(function(evt){    
        if(evt.target.id == "notifications_ul" || evt.target.id == 'notification_main')
            return;
        if($(evt.target).closest('#notifications_ul').length || $(evt.target).closest('#notification_main').length)
            return;             
        $('#notifications_ul').css('display','none');
    });

    //checkMessageCount();
    
    function msgRedirect(){
        eraseCookie('messageCount');
        window.location = "<?php echo site_url('/messages'); ?>";
    }
    

    function getNotifications()
    {
        if($('#notifications_ul').css('display') == 'block'){
            $('#notifications_ul').css('display','none');
        }else{
            notification_loader();
            $('#notifications_ul').css('display','block');
            
            $.ajax({
                type: "GET",
                url: '<?php echo site_url('dashboard/notifications'); ?>',
                success: function (response) {
                    var data                = $.parseJSON(response);
                    $('#site_notification_count_wrapper').remove();
                    if(data['notifications'] && data['notifications'].length != 0){
                        __notifications     = data['notifications'];
                        $('#notifications_area').html(renderNotifications(__notifications));
                    }else{
                        notification_empty();
                    }
                }
            });

        }
    }

    function renderNotifications(notifications)
    {
        var html = '';
        $.each(notifications,function(n_key,notification){
            html += `   <li ${ +notification['seen']!=0?'class="active-notification"':''} id="${n_key}">
                            <a onclick="markAsRead('${n_key}')" href="javascript:void(0)" class="notification general-notification">
                                <span class="noti-text">${notification['message']}<span class="notification-time">${notification['time']}</span></span>
                            </a>
                        </li>`;
        });

        return html;
    }

    function markAsRead(message)
    {
        $.ajax({
            type: "POST",
            url: '<?php echo site_url('dashboard/read_notification'); ?>',
            data : {notification : message},
            success: function (response) {
                var data = $.parseJSON(response);
                if(data['success']){
                    $(message).removeClass('active-notification');
                    window.location = siteUrl+__notifications[message]['link'];
                }
            }
        });
    }

    function notification_empty()
    {
        var loadingHtml = `
            <ul class="my-notifications no-overflow">
                <div class="empty-notifications"><img src="${__ntheme_url}/images/No_Notification_illustration.svg" width="100" height="100">
                <p>No notifications to show..!</p>
                </div>
            </ul>
        `;
        $('#notifications_area').html(loadingHtml);
    }

    function notification_loader(show = true)
    {
        var loadingHtml = `
            <ul class="my-notifications no-overflow">
                <div class="empty-notifications"><img src="${__ntheme_url}/images/notification_loading.gif" width="120" height="120">
                <p>Loading...</p>
                </div>
            </ul>
        `;
        $('#notifications_area').html(loadingHtml);
    }

    $('#myTab a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
    
    //Get in touch 
    function getInTouch() {
        $('#contact_name,#contact_email,#contact_msg').removeClass('error-border');
        $('#conract-error-name,#conract-error-email,#conract-error-message').html('');
        $('#success-msg').html('');
        $('#conract-error').html('');
        $('#contact_submit').html('Sending...');
        $('#success-msg').fadeIn();

        var contactName             = $('#contact_name').val();
        var contactEmail            = $('#contact_email').val();
        var contactMsg              = $('#contact_msg').val();
        var error                   = 0;
        var errorMsg                = '';

        if(contactName == '') {
            $('#conract-error-name').html('Please fill the Name');
            $('#contact_name').addClass('error-border');
            error++;
        }

        if(contactEmail == '') {
            $('#conract-error-email').html('Please fill the Email');
            $('#contact_email').addClass('error-border');
            error++;
        }

        if(contactMsg == '') {
            errorMsg    += '<br> Please fill the Message';
            $('#conract-error-message').html('Please fill the Message');
            $('#contact_msg').addClass('error-border');
            error++;
        }

        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

        if ( contactEmail != '' && reg.test(contactEmail) == false ) {
            errorMsg    += '<br> Please fill Correct Email';
            $('#contact_email').addClass('error-border');
            error++;
        }
        
        if(error > 0) {
            $('#contact_submit').html('Send');
            return false;
        }

        var postData    = { 'name' : contactName, 'email' : contactEmail, 'message' : btoa( contactMsg ) };
        $('#contact_submit').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: '<?php echo site_url('homepage/get_in_touch'); ?>',
            data : postData,
            success: function (response) {
                var data        = $.parseJSON(response);
                var message     = data['message'];
                $('#conract-error').html(message);
                $('#contact_submit').html('Send');
                if(data['error'] == false){
                    $('#contact_name').val('');
                    $('#contact_email').val('');
                    $('#contact_msg').val('');
                    $('#contact_submit').prop('disabled', false);
                    $('#success-msg').html(data['message']);
                    $('#success-msg').fadeOut(3000);
                }
            }
        });
    }
    
    $(".scrolltop-icon").click(function() {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
    });
   
</script>
<script>
    var today           = "<?php echo strtotime(date('d-m-Y')) ?>";
    var this_controller = "<?php echo $this->router->fetch_class() ?>";
</script>


<?php
$analytics_settings = $this->settings->setting('has_google_analytics');

if($analytics_settings['as_superadmin_value'] == '1' && $analytics_settings['as_siteadmin_value'] == '1' && $analytics_settings['as_setting_value']['setting_value']->script != ''){
    echo base64_decode($analytics_settings['as_setting_value']['setting_value']->script);
}
?>

<script>

function createCookie(name,value,days) {
    if (days) {
        var date        = new Date();
        date.setTime(date.getTime()+(days*1000));
        var expires     = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

</script>
<script type="text/javascript">
    /*$(document).ready(function () {
        //Disable cut copy paste
        $('body').bind('cut copy paste', function (e) {
            e.preventDefault();
        });
        //Disable mouse right click
        $("body").on("contextmenu",function(e){
            return false;
        });
    });*/
</script>
<?php /* ?><script src="<?php echo assets_url() ?>pwa/upup.min.js"></script>
  <script>
  UpUp.start({
    'content-url': '<?php echo site_url() ?>/404',
  });
  </script>
<?php */ ?>

<?php 
    $chat_details = get_support_chat();
    if ($support_chat_enabled) 
    {
        ?>
        <script>
        // To set unique user id in your system when it is available
        <?php if(!isset($session['id'])): ?>
            const __senderName  = "Guest";
            const __senderId    = "guest";
            const __senderEmail = "no-email";
        <?php else: ?>
            const __senderName  = "<?php echo $session['us_name'] ?>";
            const __senderId    = "<?php echo $session['id'] ?>";
            const __senderEmail = "<?php echo $session['us_email'] ?>";
        <?php endif; ?>

        </script>
        <?php
        echo base64_decode($chat_details['support_chat_script']);
    } 
?>

<script>
    $(document).on('click','#verify_user',function(){
        $('#verification_success_modal').modal('show');
        var btn = $(this);
        btn.prop('disabled', 'disabled');
        $.ajax({
            type: "POST",
            url: '<?php echo site_url('register/sendVerificationMail'); ?>',
            success: function (response) {
                btn.prop('disabled', false);
            }
        });
    });
    
</script>
</body>
</html>