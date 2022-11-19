<!DOCTYPE HTML>
<html>
   <head>
      <meta charset="utf-8">
      <title>Ofabee</title>
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
      <link rel="shortcut icon" type="image/png" href=""/>
      <meta name="title" content=" ">
      <meta name="description" content="">
      <meta property="og:image" content="">
      <meta property="og:image:width" content="400" />
      <meta property="og:image:height" content="300" />
      <link href="<?php echo assets_url(); ?>themes/onboarding/assets/css/bootstrap.min.css" rel="stylesheet">
      <link href="<?php echo assets_url(); ?>themes/onboarding/assets/css/style.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <link rel="stylesheet" href="<?php echo site_url('onboarding/css/circle.css')?>">
      <style>
      .custom-alert{
      border-radius: 6px;
      flex-direction: row-reverse;
      }
      .error-msg {
      font-size: 11px;
      font-weight: 600;
      color: #e43838;
      position: absolute;
      bottom: -17px;
      left: 0px;
      right: 0px;
      }
      .profile-image-error-msg {
      font-size: 11px;
      font-weight: 600;
      color: #e43838;
      }
      </style>
   </head>
<body >
   <section>
      <div class="container-fluid d-flex p-0 justify-content-between theme-section category-section">
         <div class="register-left-wrapper">
            <div class="ofabee-logo">
               <img class="logo-img" src="<?php echo assets_url(); ?>themes/onboarding/assets/img/ofabee.png" alt="">
            </div>
            <div class="infographic-choose-theme">
               <img class="infographic-img" src="<?php echo assets_url(); ?>themes/onboarding/assets/img/choose-theme.jpg" alt="">
            </div>
      </div>
      <div class="register-right-wrapper d-flex flex-column" id="domain_creation_section" style="display:none;">
         <input type="hidden" name="user_name" id="user_name" value="<?php echo isset($user_name)?$user_name:'' ?>" />
         <input type="hidden" name="user_email" id="user_email" value="<?php echo isset($user_email)?$user_email:'' ?>" />
         <input type="hidden" name="institute_name" id="institute_name" value="<?php echo isset($institute_name)?$institute_name:'' ?>" />
         <input type="hidden" name="password" id="password" value="<?php echo isset($password)?$password:'' ?>" />
         <div class="domain-switcher d-flex align-items-center">
            <div class="avatar-uploader">
               <label class="file-upload">
                  <input type="hidden" name="image_type" id="image_type" value="" />
                  <input class="file-input" type="file" name="file" value="" onchange="showPreviewImage(this)">
                  <img src="<?php echo assets_url(); ?>themes/onboarding/assets/img/avatar.png" class="img-fluid" alt="" id="profile_image">
                  <span class="camera-icon">
                     <svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" style="width: 15px;height: 15px;" viewBox="0 0 512 512"><g>
                        <title>background</title><rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"/></g><g><title>Layer 1</title><path fill="#cacaca" stroke="#cacaca" id="svg_2" d="m430.4,147l-67.5,0l-40.4,-40.8c0,0 -0.2,-0.2 -0.3,-0.2l-0.2,-0.2l0,0c-6,-6 -14.1,-9.8 -23.3,-9.8l-84,0c-9.8,0 -18.5,4.2 -24.6,10.9l0,0.1l-39.5,40l-69,0c-18.6,0 -33.6,14.6 -33.6,33.2l0,202.1c0,18.6 15,33.7 33.6,33.7l348.8,0c18.5,0 33.6,-15.1 33.6,-33.7l0,-202.1c0,-18.6 -15.1,-33.2 -33.6,-33.2zm-174.4,218.5c-50.9,0 -92.4,-41.6 -92.4,-92.6c0,-51.1 41.5,-92.6 92.4,-92.6c51,0 92.4,41.5 92.4,92.6c0,51 -41.4,92.6 -92.4,92.6zm168.1,-165c-7.7,0 -14,-6.3 -14,-14.1s6.3,-14.1 14,-14.1c7.7,0 14,6.3 14,14.1s-6.3,14.1 -14,14.1z"/>
                        <path fill="#cacaca" stroke="#cacaca" id="svg_3" d="m256,202.9c-38.6,0 -69.8,31.3 -69.8,70c0,38.6 31.2,70 69.8,70c38.5,0 69.8,-31.3 69.8,-70c0,-38.7 -31.3,-70 -69.8,-70z"/>
                        </g>
                     </svg>
                  </span>
               </label>
            </div>
            <div class="avatar-name">
               <div class="input-group custom-input-group">
                  <input type="text" class="form-control" id="domainname" name="domainname" placeholder="domain">
                  <label class="input-label" for="inputlabel">Website Address</label>
                  <div class="error-msg" id="error_message_domain_name"></div>
                     <div class="input-group-append">
                        <span class="input-group-text" for="inputlabel" id="basic-addon2">ofabee.com</span>
                     </div>
                  </div>
               </div>
            </div>

            <div class="profile-image-error-msg" id="error_message_profile_image"></div>

            <div class="theme-switcher">
      
               <h4 class="navyblue-heading">Choose Theme</h4>
               <div class="theme-wrapper d-flex justify-content-between">
                  <div class="theme">
                     <div class="theme-frame">
                        <img src="<?php echo assets_url(); ?>themes/onboarding/assets/img/theme-screen.jpg" alt="">
                        <div class="preview-btn-holder">
                           <button class="btn preview-btn">Preview</button>
                        </div>
                     </div>
                     <div class="theme-selector">
                        <label class="custom-radio-btn d-flex align-center mt-3 justify-content-center">
                           <input type="radio" checked="checked" name="radio">
                           <span class="theme-name ml-2">Theme 01</span>
                        </label>
                     </div>
                  </div>
                  <div class="theme">
                     <div class="theme-frame">
                        <img src="<?php echo assets_url(); ?>themes/onboarding/assets/img/theme-screen.jpg" alt="">
                        <div class="preview-btn-holder">
                           <button class="btn preview-btn">Preview</button>
                        </div>
                     </div>
                     <div class="theme-selector">
                        <label class="custom-radio-btn d-flex align-center mt-3 justify-content-center">
                           <input type="radio" name="radio">
                           <span class="theme-name ml-2">Theme 02</span>
                        </label>
                     </div>
                  </div>
                  <div class="theme">
                     <div class="theme-frame">
                        <img src="<?php echo assets_url(); ?>themes/onboarding/assets/img/theme-screen.jpg" alt="">
                        <div class="preview-btn-holder">
                           <button class="btn preview-btn">Preview</button>
                        </div>
                     </div>
                     <div class="theme-selector">
                        <label class="custom-radio-btn d-flex align-center mt-3 justify-content-center">
                           <input type="radio" name="radio">
                           <span class="theme-name ml-2">Theme 03</span>
                        </label>
                     </div>
                  </div>
               </div>
            </div>

            <div class="color-switcher">
               <h4 class="navyblue-heading">Choose Color</h4>
               <div class="d-flex justify-content-between">
               <label class="d-flex align-items-center color-label">
                  <span>Heading 1</span>
                  <div class="color-picker-btn" style="background-color: #008000;"></div>
                  <div class="color-picker">
                     <div class="color-pallete">
                        <div class="color" style="background:#008000" onclick="document.getElementById('headingcolor').jscolor.fromString('008000')"></div>

                        <div class="color" style="background:#ff0000" onclick="document.getElementById('headingcolor').jscolor.fromString('ff0000')"></div>

                        <div class="color" style="background:#ffa500" onclick="document.getElementById('headingcolor').jscolor.fromString('ffa500')"></div>

                        <div class="color" style="background:#54c6d2" onclick="document.getElementById('headingcolor')
                        .jscolor.fromString('54c6d2')"></div>

                        <div class="color" style="background:#F44336" onclick="document.getElementById('headingcolor')
                        .jscolor.fromString('F44336')"></div>

                        <div class="color" style="background:#673AB7" onclick="document.getElementById('headingcolor')
                        .jscolor.fromString('673AB7')"></div>

                        <div class="color" style="background:#8BC34A" onclick="document.getElementById('headingcolor')
                        .jscolor.fromString('8BC34A')"></div>

                        <div class="color" style="background:#4c4c4c" onclick="document.getElementById('headingcolor')
                        .jscolor.fromString('4c4c4c')"></div>
                     </div>
                     <div class="color-input">
                        <input class="jscolor" name="domain_heading1colour" id="domain_heading1colour">
                     </div>
                  </div>
               </label>
               <label class="d-flex align-items-center color-label">
                  <span>Heading 2</span>
                  <div class="color-picker-btn" style="background-color: #ff0000;"></div>
                  <div class="color-picker">
                     <div class="color-pallete">
                     <div class="color" style="background:#008000" onclick="document.getElementById('headingcolo2')
                     .jscolor.fromString('008000')"></div>

                     <div class="color" style="background:#ff0000" onclick="document.getElementById('headingcolo2')
                     .jscolor.fromString('ff0000')"></div>

                     <div class="color" style="background:#ffa500" onclick="document.getElementById('headingcolo2')
                     .jscolor.fromString('ffa500')"></div>

                     <div class="color" style="background:#54c6d2" onclick="document.getElementById('headingcolo2')
                     .jscolor.fromString('54c6d2')"></div>

                     <div class="color" style="background:#F44336" onclick="document.getElementById('headingcolo2')
                     .jscolor.fromString('F44336')"></div>

                     <div class="color" style="background:#673AB7" onclick="document.getElementById('headingcolo2')
                     .jscolor.fromString('673AB7')"></div>

                     <div class="color" style="background:#8BC34A" onclick="document.getElementById('headingcolo2')
                     .jscolor.fromString('8BC34A')"></div>

                     <div class="color" style="background:#4c4c4c" onclick="document.getElementById('headingcolo2')
                     .jscolor.fromString('4c4c4c')"></div>
                  </div>
                  <div class="color-input">
                     <input class="jscolor" name="domain_heading2colour" id="domain_heading2colour">
                  </div>
               </div>
            </label>
            <label class="d-flex align-items-center color-label">
               <span>Background</span>
               <div class="color-picker-btn" style="background-color: #ffa500;"></div>
               <div class="color-picker">
                  <div class="color-pallete">
                     <div class="color" style="background:#008000" onclick="document.getElementById('backgroundcolor')
                     .jscolor.fromString('008000')"></div>

                     <div class="color" style="background:#ff0000" onclick="document.getElementById('backgroundcolor')
                     .jscolor.fromString('ff0000')"></div>

                     <div class="color" style="background:#ffa500" onclick="document.getElementById('backgroundcolor')
                     .jscolor.fromString('ffa500')"></div>

                     <div class="color" style="background:#54c6d2" onclick="document.getElementById('backgroundcolor')
                     .jscolor.fromString('54c6d2')"></div>

                     <div class="color" style="background:#F44336" onclick="document.getElementById('backgroundcolor')
                     .jscolor.fromString('F44336')"></div>

                     <div class="color" style="background:#673AB7" onclick="document.getElementById('backgroundcolor')
                     .jscolor.fromString('673AB7')"></div>

                     <div class="color" style="background:#8BC34A" onclick="document.getElementById('backgroundcolor')
                     .jscolor.fromString('8BC34A')"></div>

                     <div class="color" style="background:#4c4c4c" onclick="document.getElementById('backgroundcolor')
                     .jscolor.fromString('4c4c4c')"></div>
                  </div>
                  <div class="color-input">
                     <input class="jscolor" name="domain_backgroundcolour" id="domain_backgroundcolour">
                  </div>
               </div>
            </label>
            <label class="d-flex align-items-center color-label">
               <span>Footer</span>
               <div class="color-picker-btn" style="background-color: #0000ff;"></div>
                  <div class="color-picker">
                     <div class="color-pallete">
                     <div class="color" style="background:#008000" onclick="document.getElementById('footer')
                     .jscolor.fromString('008000')"></div>

                     <div class="color" style="background:#ff0000" onclick="document.getElementById('footer')
                     .jscolor.fromString('ff0000')"></div>

                     <div class="color" style="background:#ffa500" onclick="document.getElementById('footer')
                     .jscolor.fromString('ffa500')"></div>

                     <div class="color" style="background:#54c6d2" onclick="document.getElementById('footer')
                     .jscolor.fromString('54c6d2')"></div>

                     <div class="color" style="background:#F44336" onclick="document.getElementById('footer')
                     .jscolor.fromString('F44336')"></div>

                     <div class="color" style="background:#673AB7" onclick="document.getElementById('footer')
                     .jscolor.fromString('673AB7')"></div>

                     <div class="color" style="background:#8BC34A" onclick="document.getElementById('footer')
                     .jscolor.fromString('8BC34A')"></div>

                     <div class="color" style="background:#4c4c4c" onclick="document.getElementById('footer')
                     .jscolor.fromString('4c4c4c')"></div>
                  </div>
                  <div class="color-input">
                     <input class="jscolor" name="domain_footercolour" id="domain_footercolour">
                  </div>
               </div>
            </label>
         </div>
      </div>

      <div class="d-flex align-items-center">
            <div class="w-100">
               <div class="error-msg" id="error_message_domain_colour" style="position:unset;">
               </div>
            </div>
            <button onclick="registerDomain()" id="registerDomainButton" class="btn green-btn">NEXT</button>
         </div>
      </div>
      <div class="category-picker-wrapper d-flex flex-column" id="domain_category_section" style="display:none;">
         <div class="category-picker">
            <h4>Pick Your Category</h4>
            <p>What type of an online course do you want to create?</p>
         </div>

         <div class="default-category">
            <div class="input-group custom-input-group">
               <label class="input-label" for="inputlabel">Choose Categories</label>
               <div class="error-msg" id="error_message_category_name"></div>
               <input type="hidden" name="new_domain_id" id="new_domain_id" value="" />
            <div class="category-selector">
            <div class="category-holder" id="category_list">
               <?php foreach ($categories as $category): ?> 
                  <label class="custom-checkbox">
                     <span class="category-name"><?php echo $category; ?></span>
                     <input type="checkbox" name="c_category[]" value="<?php echo $category; ?>" class="select_Category">
                     <span class="checkmark"></span>
                  </label> 
               <?php endforeach;?>
            </div>
         </div>
      </div>
   </div>

   <div class="or-linebreak"></div>

   <div class="add-category">
      <div class="input-group custom-input-group">
         <input type="text" class="form-control" id="categoryname" name="categoryname" placeholder="">
         <label class="input-label" for="inputlabel">Add Category</label>
         <div class="input-group-append">
            <button type="submit" class="input-group-text" for="inputlabel" id="add_category">ADD</button>
         </div>
      </div>
   </div>

      <div class="text-right d-flex justify-content-between">
         <button  id="skip_category" onclick="skipAndContinue()" class="btn green-btn-inverted">SKIP</button> 
         <button onclick="configureCategories()" id="configureCategoriesButton" class="btn green-btn">NEXT</button>
      </div>

   </div>
      <div id="domain_install_section" class="d-flex flex-column" style="">
         <div class="clearfix">
            <div class="c100 p1 big" id="progress">
               <span class="progressCirle" data-width="1" data-stop="100">1%</span>
               <div class="slice">
               <div class="bar"></div>
               <div class="fill"></div>
            </div>
            <p id="status-word"></p>
         </div>
      </div>

   </div> 
   </section>

 <script type="text/javascript" src="<?php echo assets_url(); ?>themes/onboarding/assets/js/jquery-3.3.1.min.js"></script>
 <script type="text/javascript" src="<?php echo assets_url(); ?>themes/onboarding/assets/js/bootstrap.min.js"></script>
 <script type="text/javascript" src="<?php echo assets_url(); ?>themes/onboarding/assets/js/popper.min.js"></script>
 <script type="text/javascript" src="<?php echo assets_url(); ?>themes/onboarding/assets/js/custom.js"></script>
 <script type="text/javascript" src="<?php echo assets_url(); ?>themes/onboarding/assets/js/jscolor.js"></script>

<script>
   var __site_url                = '<?php echo site_url() ?>';
   var __uploading_file          = new Array();
   var __param                   = new Array();
   var domainId                  = '0';

   var CurrentStep               = '1';

   var loadingStausWord            = {};
    loadingStausWord[12]        = 'Processing your request..';
    loadingStausWord[36]        = 'Configuring your scription..';
    loadingStausWord[48]        = 'Deploying the module..';
    loadingStausWord[60]        = 'Correlating the panel and link..';
    loadingStausWord[68]        = 'Loading and integrating the data..';
    loadingStausWord[72]        = 'Loading and integrating the data..';
    loadingStausWord[92]        = 'Verifing the Check list..';
    loadingStausWord[96]        = 'Final minute reboot..';
    loadingStausWord[100]        = 'Go green with Ofabee..';

   window.onbeforeunload = function() {
      return "Are you sure you want to leave?";
   }

   history.pushState(null, null, location.href);
   window.onpopstate = function () {
   history.go(1);
   };

   $(document).on("keypress", function (e) {
   
      if (e.key == "F5" || e.key == "F11" ||
      (e.ctrlKey == true && (e.key == 'r' || e.key == 'R'))  || e.keyCode == 82 || e.keyCode == 8) {

         e.preventDefault();
      }
   });

   $(document).ready(function(){
      var hashTag          = getQueryStringValue('domain');
      var steps            = getQueryStringValue('steps');
      var domainIdString   = getQueryStringValue('doamin_id');
      domainId             = domainIdString;
      route(steps);
   
   });

   function route(steps){
   
      switch(steps)
      {
         case '1':
         {
            $('#domain_creation_section').attr('style', 'display: flex !important');
            $('#domain_category_section').attr('style', 'display: none !important');
            $('#domain_install_section').attr('style', 'display: none !important');
            break;
         }
         case '2':
         {
            $('#domain_creation_section').attr('style', 'display: none !important');
            $('#domain_category_section').attr('style', 'display: flex !important');
            $('#domain_install_section').attr('style', 'display: none !important');
           
           
            break;
         }
         
         default:
         {
            steps = parseInt(steps);

            if(steps > 2)
            {
            
               $('#domain_creation_section').attr('style', 'display: none !important');
               $('#domain_category_section').attr('style', 'display: none !important');
               $('#domain_install_section').attr('style', 'top: 50%;transform: translateY(-50%);position: fixed;right: 0;left: 0;align-items: center;display: flex !important');
               
               installApplication(steps);
            }
            else
            {
               $('#domain_creation_section').attr('style', 'display: flex !important');
               $('#domain_category_section').attr('style', 'display: none !important');
               $('#domain_install_section').attr('style', 'display: none !important');
            }
         
         break;
         }
      }
   }

function skipAndContinue()
{
   $('#skip_category').attr('disabled','true');
   $('#skip_category').html('loading..');
   window.onbeforeunload = null;
   location.href = window.location.protocol + "//" + window.location.host +'/onboarding/index?steps=3&doamin_id='+domainId;

}

   $('#domainname').focusout(function(){
      var domainName       = $(this).val();
      if(domainName != '') {
         if(domainName.length > 10) {
            $('#error_message_domain_name').html('Sorry! Domain name should be less than 10 characters.').css('color', '#e43838');
         } else if(/^[a-zA-Z0-9- ]*$/.test(domainName) == false) {
            $('#error_message_domain_name').html('Sorry! Special Characters not allowed.').css('color', '#e43838');
         } else if(domainName.match(/^\d+$/)) {
            $('#error_message_domain_name').html('Sorry! Only numerics not allowed.').css('color', '#e43838');
         } else {
         checkDomain(domainName);
         }
      }
   });

function getQueryStringValue(key) {
 return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

function registerDomain() {

   var domainname                = $.trim($('#domainname').val());
   var domaintheme               = $("input[name='domaintheme']:checked").val();
   var domain_heading1colour     = $.trim($('#domain_heading1colour').val());
   var domain_heading2colour     = $.trim($('#domain_heading2colour').val());
   var domain_backgroundcolour   = $.trim($('#domain_backgroundcolour').val());
   var domain_footercolour       = $.trim($('#domain_footercolour').val());
   var user_name                 = $.trim($('#user_name').val());
   var user_email                = $.trim($('#user_email').val());
   var institute_name            = $.trim($('#institute_name').val());
   var password                  = $.trim($('#password').val());
   var image_type                = $.trim($('#image_type').val());
   var errorCount                = 0;

   $('#error_message_profile_image').html('');
   if(image_type == ''){
      $('#error_message_profile_image').html('Please choose the Logo');
      errorCount++;
   } else if($.inArray(image_type, ['gif','png','jpg','jpeg']) == -1) {
      $('#error_message_profile_image').html('Invalid file format.');
      errorCount++;
   }
   if(domainname == ''){
      $('#error_message_domain_name').html('Domain Name cannot be empty');
      errorCount++;
   } else if(domainname.length > 10) {
      $('#error_message_domain_name').html('Sorry! Domain name should be less than 10 characters.');
      errorCount++;
   } else if(/^[a-zA-Z0-9- ]*$/.test(domainname) == false) {
      $('#error_message_domain_name').html('Sorry! Special Characters not allowed.');
      errorCount++;
   } else if(domainname.match(/^\d+$/)) {
      $('#error_message_domain_name').html('Sorry! Only numerics not allowed.');
      errorCount++;
   }
  
   if(domain_heading1colour == '' || domain_heading2colour == '' || domain_backgroundcolour == '' || domain_footercolour == ''){
      $('#error_message_domain_colour').html('Domain Colour cannot be empty');
      errorCount++;
   }
   if(errorCount == 0) {
      $('#error_message_domain_name').html('Domain Registering...').css('color', '#2d5379');
      $('#registerDomainButton').attr('disabled','true');
      $('#registerDomainButton').html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>Loading');
      
      __param["is_ajax" ]                          = true;
      __param["step"]                              = '1';
      __param["domainname"]                        = domainname;
      __param["domain_heading1colour"]             = domain_heading1colour;
      __param["domain_heading2colour"]             = domain_heading2colour;
      __param["domain_backgroundcolour"]           = domain_backgroundcolour;
      __param["domain_footercolour"]               = domain_footercolour;
      __param["user_name"]                         = user_name;
      __param["user_email"]                        = user_email;
      __param["institute_name"]                    = institute_name;
      __param["password"]                          = password;
      __param["image_type"]                        = image_type
      
      var uploadURL                                = __site_url+'onboarding/domain_create';
      uploadFiles(uploadURL, __param , domainRegisterCompleted); 
   }
}

function uploadFiles(uploadURL, param, callback, __dataType) {
   var formDataType                 = (typeof __dataType == 'undefined') ? 'json' : __dataType;
   var formData                     = new FormData();
   for (key in param) {
      formData.append(key, param[key]);

   }
   var processingId                 = (typeof param['processing'] != 'undefined') ? param['processing'] : 'percentage_count';
   var jqXHR                        = $.ajax({
   xhr   : function() {
   var xhrobj     = $.ajaxSettings.xhr();
   return xhrobj;
   },
   url               : uploadURL,
   type              : "POST",
   datatype          : formDataType,
   contentType       : false,
   processData       : false,
   cache             : false,
   data              : formData,
   async             : true,
   success           : function(responseData) {
                           var data = responseData;
   
                           callback(data);
                        }
   });
}

function domainRegisterCompleted(response) {
   var data             = $.parseJSON(response);
   if(data['error'] == true){
      $('#error_message_domain_name').html(data['message']).css('color', '#e43838');
   } else {
      $('#domain_creation_section').attr('style', 'display: none !important');
      $('#domain_category_section').attr('style', 'display: flex !important');
      $('#new_domain_id').val(data['domain_id']); 
      $('#skip_category').attr("href", window.location.protocol + "//" + window.location.host +'/onboarding/locate/'+data['domain_id']);
      var link             = window.location.protocol + "//" + window.location.host + window.location.pathname;
      link                 = link + '?domain=created&steps=2&doamin_id='+data['domain_id'];
      domainId             = data['domain_id'];
      window.history.pushState({
      path: link
      }, '', link);
   }
}

function processFileName(fileName) {
   var __fileNameTemp                  = fileName;
   var __explodedFileName              = '';
   this.trimFileName = function() {
      __fileNameTemp = trimFileName(__fileNameTemp);
   }

   this.explodeFileName = function() {
         return __explodedFileName = __fileNameTemp.split('.');
   }

   this.fileExtension = function() {
         return __explodedFileName[(__explodedFileName.length) - 1].toLowerCase();
   }

   this.uniqueFileName = function() {
         this.trimFileName();
   this.explodeFileName();
   var currentdate      = new Date();
   var datetime         = currentdate.getDate() + '-' + (currentdate.getMonth() + 1) + '-' + currentdate.getFullYear() + '-' + currentdate.getHours() + '-' + currentdate.getMinutes() + '-' + currentdate.getSeconds();
   var uniqueFileName   = __explodedFileName[0].slice(0, 30) + datetime + "." + this.fileExtension();
   return uniqueFileName.replace(/\\/g, "");
   }
}

function trimFileName(file_name) {
   var trimed_filename        = file_name.split(' ').join('-');
   trimed_filename            = trimed_filename.split('&').join('-');
   trimed_filename            = trimed_filename.split(';').join('-');
   trimed_filename            = trimed_filename.split(':').join('-');
   trimed_filename            = trimed_filename.split('/').join('-');
   trimed_filename            = trimed_filename.split('{').join('-');
   trimed_filename            = trimed_filename.split('}').join('-');
   trimed_filename            = trimed_filename.split('(').join('-');
   trimed_filename            = trimed_filename.split(')').join('-');
   trimed_filename            = trimed_filename.split('\'').join('-');
   trimed_filename            = trimed_filename.split('"').join('-');
   return trimed_filename;
}

function showPreviewImage(input) {
   $('#error_message_profile_image').html('');
   var i                   = 0;
   var allowedTypes        = ['jpg','jpeg','png','gif'];
   __uploading_file        = input.files;
   var fileObj             = new processFileName(__uploading_file[i]['name']);
   __param["file_name"]    = fileObj.uniqueFileName(); 
   __param["extension"]    = fileObj.fileExtension();
   __param["file"]         = __uploading_file[i];
   if (input.files && input.files[i]) {
   var reader              = new FileReader();
   reader.onload           = function(e) {
   $('#profile_image').attr('src', e.target.result);
   }
   reader.readAsDataURL(input.files[i]);

   if($.inArray(__param["extension"], ['gif','png','jpg','jpeg']) == -1) {
   $('#error_message_profile_image').html('Invalid file format.');
   }
   $('#image_type').val(__param["extension"]);
   }
}


function checkDomain(domainName){
   $('#error_message_domain_name').html('Domain Checking...').css('color', '#2d5379');
   $.ajax({
            url            : __site_url+'onboarding/check_domain',
            type           : "POST",
            data           : {"is_ajax":true, "domain_name":domainName},
            success        : function(response) {
                                 var data = $.parseJSON(response);
                                 if(data['error'] == true){
                                    $('#error_message_domain_name').html('Sorry! Domain Name already exists.').css('color', '#e43838');
                                 } else {
                                    $('#error_message_domain_name').html('Congrats! Valid Domain Name.').css('color', 'green');
                                 }     
                              }
   });
}

$(document).on('click', '#add_category', function() {
   var category_name = $('#categoryname').val();
   if(category_name != ''){
      $('#error_message_category_name').html('').css('visibility', 'hidden');
      $('#category_list').append('<label class="custom-checkbox"><span class="category-name">'+category_name+'</span><input type="checkbox" name="c_category[]" value="'+category_name+'" class="select_Category" checked><span class="checkmark"></span></label>');
      $('#categoryname').val('');
   }
});

function configureCategories(){
   
   var selected_categories = [];
   $('input.select_Category:checkbox:checked').each(function () {
      selected_categories.push($(this).val());
   });
   if(selected_categories.length === 0){
      $('#error_message_category_name').html('Please Select atleast one category.').css('visibility', 'visible');
   } else {
      $('#configureCategoriesButton').attr('disabled','true');
      $('#configureCategoriesButton').html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>Loading');
      domainId = getQueryStringValue('doamin_id');
      $.ajax({
         url: __site_url+'onboarding/domain_create',
         type: "POST",
         data:{
               "is_ajax" :true,
               "domain_id" :domainId,
               "selected_categories" :selected_categories,
               "step" :'2'
               },
         success: function(response) {
               var data = $.parseJSON(response);
               if(data['error'] == true){
                  $('#error_message_category_name').html(data['message']);
               } else {
                  var link          = window.location.protocol + "//" + window.location.host + window.location.pathname;
                  link              = link + '?steps=3&doamin_id='+data['domain_id'];  

                  window.history.pushState({ path: link}, '', link);

                  var steps         = getQueryStringValue('steps');
                  
                  route(steps); 
               }
         }
      });
   }
}

function installApplication(cStep){
   var domainIdString   = getQueryStringValue('doamin_id');
   domainId             = domainIdString;

   $.ajax({
      url: __site_url+'onboarding/domain_create',
      type: "POST",
      data:{
            "is_ajax" :true,
            "domain_id" :domainId,
            
            "step" :cStep
            },
      success: function(response) {
         var data = $.parseJSON(response);
         if(data['error'] == true){
            $('#error_message_category_name').html(data['message']);
         } else {
           
            if(data['completed'] == '29'){
               // final steps here
               window.onbeforeunload = null;
               checkIt(100); 
               window.location.href = window.location.protocol + "//" + window.location.host +'/onboarding/locate/'+data['domain_id']; 
            } else {
               CurrentStep    = parseInt(data['completed']) + 1;
               var link       = window.location.protocol + "//" + window.location.host + window.location.pathname;
               link           = link + '?steps='+CurrentStep+'&doamin_id='+data['domain_id'];

               window.history.pushState({ path: link}, '', link);
               var steps = getQueryStringValue('steps');
                     
               route(steps); 
               checkIt(parseInt(data['completed']) * 4); 
            }

         }
      }
   });
}

function loading()
{
   var infinload = setInterval(checkIt,90); 
}
function checkIt(compltedLevel){
   var progress      = $('.progressCirle').attr('data-width');
   var stopProgress  = $('.progressCirle').attr('data-stop');// $('.progress-bar').width() / $('.progress-bar').parent().width() * 100;
   progress          = compltedLevel;

   // if( progress in loadingStausWord)
   // {
   //    $('#status-word').html(loadingStausWord[progress])
   // }

   if(progress >= stopProgress)
   {
   // clearInterval(infinload); 
   }
   else
   {
      $('.progressCirle').html(progress+'%');
      $('#progress')[0].className = $('#progress')[0].className.replace(/\bp.*?\b/g, 'p'+progress);
      $('.progressCirle').attr('data-width',progress);
      
   }
 
}

$('#categoryname').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
      $('#add_category').click();
    }
});
</script>
</body>
</html>