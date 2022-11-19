<style>    
    .logo img{ width: 36px ; height:33px;}
	.logo{padding:7px 15px;}
    .admin-name{
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .message-count{
        background: #FF3F3F;
        color: #fff;
        font-size: 10px;
        position: absolute;
        display: block;
        top: 7px;
        right: 9px;
        line-height: normal;
        padding: 2px 3px;
        border-radius: 20px;
    }
</style>
<header class="header">
    <a href="<?php echo admin_url(); ?>" class="logo">
        <img src="<?php echo assets_url() ?>images/logo.png" />
    </a>
        <ul class="headr-menu-rite">
            <?php 
                $faculty_management_link = '';
                $userLoddedIn = $this->auth->get_current_user_session('admin');

                $faculty_management_link = '<li><a href="'.admin_url('profile').'">Profile</a></li>';
                if(in_array($admin['id'], array(config_item('super_admin'))))
                {
                    $faculty_management_link .= '<li><a href="'.admin_url('environment').'">'.lang('settings').'</a></li>';
                    $faculty_management_link .= '<li><a href="'.admin_url('role').'">Manage Roles</a></li>';
                    $faculty_management_link .= '<li><a href="'.admin_url('email_template').'">Email Templates</a></li>';
                }
                if($userLoddedIn['role_id'] == '1' || $userLoddedIn['role_id'] == '8')
                {
                    $faculty_management_link .= '<li><a href="'.admin_url('orders').'">Sales Report</a></li>';
                    $faculty_management_link .= '<li><a href="'.admin_url('pending_order').'">Pre-sales report</a></li>';
                    $faculty_management_link .= '<li><a href="'.admin_url('sales_manager').'">Sales management</a></li>';
                }
                
                $discount_coupon_previlage = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'promo_code'));
                if(in_array(1, $discount_coupon_previlage))
                {
                    $faculty_management_link .= '<li><a href="'.admin_url('promo_code').'">Discount Coupons</a></li>';
                }

                $notification_previlage = $this->accesspermission->get_permission(array('role_id' => $admin['role_id'], 'module' => 'notification'));
               
                if(in_array(1, $notification_previlage))
                {
                    $faculty_management_link .= '<li><a href="'.admin_url('notification').'">Information Bars</a></li>';
                }
                $bundlespermission = $this->accesspermission->get_permission(array('role_id' => $userLoddedIn['role_id'], 'module' => 'bundle'));
                //print_r($bundles);echo $userLoddedIn['role_id'];die;
                if(isset($bundlespermission) && (in_array(1, $bundlespermission))):
                    $faculty_management_link .= '<li id="bundle-menu"><a href="'.admin_url('bundle').'">Bundles</a></li>';
                endif;
                /*$analytics_settings = $this->settings->setting('has_google_analytics');
                
                if($analytics_settings['as_superadmin_value'] == '1' && $analytics_settings['as_siteadmin_value'] == '1' && $analytics_settings['as_setting_value']['setting_value']->access_url != ''){
                    $access_url = base64_decode($analytics_settings['as_setting_value']['setting_value']->access_url);
                    $faculty_management_link .= '<li><a href="'.$access_url.'">Analytics Report</a></li>';
                }*/
                
            ?>
            <?php 
            //processing notification
            $site_notification = array();//$this->ofabeenotifier->user_notification(array('user_id'=>$admin['id']));
            //echo '<pre>'; print_r($site_notification);die;
            ?>
            <li >
                <a onclick="msgRedirect()" class="notify-icoset dropdown-toggle">
                    <svg style="width: 16px;height: 100%;" xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96" style="&#10;">
                        <g style="&#10;">
                        <rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"/>
                        </g>
                        <g style="&#10;">
                        <path fill="#ffffff" id="svg_1" d="m80,12l-64,0c-6.627,0 -12,5.373 -12,12l0,48c0,6.627 5.373,12 12,12l64,0c6.627,0 12,-5.373 12,-12l0,-48c0,-6.627 -5.373,-12 -12,-12zm0,8c0.459,0 0.893,0.093 1.303,0.235l-33.303,26.642l-33.303,-26.642c0.411,-0.142 0.844,-0.235 1.303,-0.235l64,0zm0,56l-64,0c-2.21,0 -4,-1.79 -4,-4l0,-43.677l33.501,26.8c0.73,0.585 1.615,0.877 2.499,0.877s1.769,-0.292 2.499,-0.877l33.501,-26.8l0,43.677c0,2.21 -1.79,4 -4,4z"/>
                        </g>
                    </svg> 
                    <span class="badge-orange message-count" id="message_count_wrapper" style="display:none;">
                        <span id="message_count" style="display: inline-block;min-width: 15px;text-align: center;"></span>
                    </span>               
                </a>
            </li>

            <li id="notification_main" onclick="getNotifications()">
                <a id="site_notification_count_wrapper" href="javascript:void(0)" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"class="notify-icoset dropdown-toggle">
                    <i class="icon icon-bell"></i><span class="buble-not" id="site_notification_count" style="display:none;"></span>
                </a>
                <ul class="dropdown-menu notification-wrapper-li" aria-labelledby="dLabel">
                    <div id="notifications_area"  style="overflow-y: auto;max-height: 290px;"></div>
                </ul>
            </li>

            <li>    
                <a href="javascript:void(0)" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" class="notify-icoset dropdown-toggle nav-bg-hover admin-name" title="<?php  echo $admin['us_name'] ?>">
                <?php  echo $admin['us_name'] ?></a>
                 <ul class="dropdown-menu" aria-labelledby="dLabel">
                     <?php echo $faculty_management_link;?>
                     <?php $module = $this->accesspermission->get_permission(array('role_id' => $userLoddedIn['role_id'], 'module' => 'page'));?>
                     <li><a href="<?php echo admin_url('question_manager') ?>">Category Manager</a></li>
                     <?php if(isset($module) && (in_array(1, $module))): ?>
                         <li><a href="<?php echo admin_url('Menu_sorted') ?>">Menu Manager</a></li>
                     <?php endif;?>
                     <li><a href="<?php echo site_url('logout') ?>"><?php echo lang('logout') ?></a></li>
                 </ul>
            </li>
        </ul>
        <script type="text/javascript">
            var __ntheme_url = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
            var __notifications = [];    
            var siteUrl = '<?php echo site_url() ?>';
            var __notificationFlag = 1;
            var __userId        = "<?php echo $admin['id']; ?>";
            var __userToken     = btoa("<?php echo fetch_user_token() ?>");
            var __classUrl      = '<?php echo $this->uri->segment("2")?>';

    function checkMessageCount(){
        
        var myCookie = getCookie("adminmessageCount");
        if (myCookie != null) {

            $('#message_count_wrapper').show();
            $('#message_count').html(myCookie);
        }else{
            var user = {"userId":__userId};
            $.ajax({
                type    : "POST",
                url     : "<?php echo config_item('message_api_url').'getmessagecount' ?>",
                headers : {"Authorization": 'Bearer '+atob(__userToken)},
                data    : user, //{action:'x',params:['a','b','c']},
                success : function (response) {
                    // var data = $.parseJSON(response);
                    if(response.success == true){
                        if(response.count>0){
                            var msgCount = response.count;
                            setcookie('adminmessageCount',msgCount,'1');
                            $('#message_count_wrapper').show();
                            $('#message_count').html(msgCount);
                        }else{
                            $('#message_count_wrapper').hide();
                            $('#message_count').html('');
                        }
                    }
                
                }
            });
        }                          
    }

            (function() {
                var nElement = document.getElementById('site_notification_count');
                if(nElement)
                {
                    nElement.style.display = "none";
                }
                setTimeout(function(){
                    $.ajax({
                        type: "GET",
                        url: '<?php echo admin_url('dashboard/notification_count'); ?>',
                        success: function (response) {
                            var data = $.parseJSON(response);
                            if(+data['count'] > 0){
                                $('#site_notification_count').html(data['count']);
                                $('#site_notification_count').show();
                            }
                        }
                    });
                    if(__classUrl != 'messages'){
                        checkMessageCount();
                    }
                },1000)
            })();

            function getNotifications()
            {
                if(__notificationFlag == 1){
                    
                    if(!$('#notification_main').hasClass('open')){
                    notification_loader();
                    $.ajax({
                        type: "GET",
                        url: '<?php echo admin_url('dashboard/notifications'); ?>',
                        success: function (response) {
                            var data = $.parseJSON(response);
                            $('#site_notification_count').hide();
                            if(data['notifications'] && data['notifications'].length != 0){
                                __notifications = data['notifications'];
                                $('#notifications_area').html(renderNotifications(__notifications));
                            }else{
                                notification_empty();
                            }
                            __notificationFlag = 2;
                        }
                    });
                }
                }
                
            }

            function renderNotifications(notifications)
            {
                var html = '';

                $.each(notifications,function(n_key,notification){
                    
                    html += '<li id="'+n_key+'">';
                    html += '   <a '+(notification.seen == 1?'class="readed"':'')+' onclick="markAsRead(\''+n_key+'\')" href="javascript:void(0)">'+notification['message']+' - '+notification['time']+'</a>';
                    html += '</li>';
                });
                
                return html;
            }

            function markAsRead(message)
            {
                
                $.ajax({
                    type: "POST",
                    url: '<?php echo admin_url('dashboard/read_notification'); ?>',
                    data : {notification : message},
                    success: function (response) {
                        var data = $.parseJSON(response);
                        //console.log(data);
                        if(data['success']){
                            $(message).removeClass('readed');
                            window.location = siteUrl+__notifications[message]['link'];
                        }
                    }
                });
            }

            function notification_empty()
            {
                var loadingHtml = '';
                    loadingHtml += '<ul class="my-notifications no-overflow">';
                    loadingHtml += '    <div class="empty-notifications text-center">';
                    loadingHtml += '        <img src="'+__ntheme_url+'/images/No_Notification_illustration.svg" width="100" height="100">';
                    loadingHtml += '        <p>No notifications to show..!</p>';
                    loadingHtml += '    </div>';
                    loadingHtml += '</ul>';
                $('#notifications_area').html(loadingHtml);
            }

            function notification_loader(showObj)
            {
                var show = typeof showObj != 'undefined' ? showObj : true;
                var loadingHtml = '';
                    loadingHtml += '<ul class="my-notifications no-overflow">';
                    loadingHtml += '    <div class="empty-notifications text-center">';
                    loadingHtml += '        <img src="'+__ntheme_url+'/images/notification_loading.gif" width="120" height="120">';
                    loadingHtml += '        <p>Loading...</p>';
                    loadingHtml += '    </div>';
                    loadingHtml += '</ul>';
                $('#notifications_area').html(loadingHtml);
            }

            function getCookie(name) {
            var dc      = document.cookie;
            var prefix  = name + "=";
            var begin   = dc.indexOf("; " + prefix);
            if (begin == -1) {
                begin   = dc.indexOf(prefix);
                if (begin != 0) return null;
            }
            else
            {
                begin += 2;
                var end = document.cookie.indexOf(";", begin);
                if (end == -1) {
                end = dc.length;
                }
            }
            //return unescape(dc.substring(begin + prefix.length, end));
            return decodeURI(dc.substring(begin + prefix.length, end));
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
        
        function createCookie(name,value,days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime()+(days*1000));
                var expires = "; expires="+date.toGMTString();
            }
            else var expires = "";
            document.cookie = name+"="+value+expires+"; path=/";
        }
        function eraseCookie(name) {
            createCookie(name,"",-1);
        }
        function msgRedirect(){
            eraseCookie('adminmessageCount');
            setTimeout(function(){
                window.location = "<?php echo site_url('admin/messages'); ?>";  
            }, 500);
                     
        }

        
        </script>
    </header>