<?php include_once 'header.php'; ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/chrome-css.css">
<section id="nav-group">
    <div class="nav-group pad0">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <div class="col-xs-4 col-sm-3 col-md-3 sdfw">
                    <h2 class="funda-head teacher-head">Our Teachers</h2>
                </div>
                <div class="col-xs-8 col-sm-9 col-md-9 sdfw">
                    <button type="button" class="filter-options visible-xs">
                        <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/img/filter.png" width="32" height="32">                      
                    </button> 
                    <div class="custom-search-input">
                        <div class="input-group col-md-12">
                            <input type="text" class="form-control teacher-box input-lg padtb50" value="<?php echo (($keyword)?$keyword:'') ?>" id="teacher_keyword" placeholder="Search by name, keyword or subject" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search by name, keyword or subject'" />
                            <span class="input-group-btn">
                                <button class="btn btn-search btn-lg" type="button">
                                    <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/img/search.png" width="36" height="36">
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--teacher listing section starts here-->
<section class="teacher-list">
    <div class="container container-altr">
        <div class="container-reduce-width">
            <div class="col-sm-3 col-md-3 teacher-list-sidebar">
                <div class="side-box">
                    <h3>Category</h3>
                    <?php if (isset($categories) && !empty($categories)): ?>
                        <ul>
                            <?php foreach ($categories as $category): ?>
                                <li>                
                                    <span class="chkbox-span">
                                        <input type="checkbox" id="category_<?php echo $category['id'] ?>" value="<?php echo $category['id'] ?>" class="category-selector" />
                                        <label class="label-narrow" for="category_<?php echo $category['id'] ?>"><span></span><?php echo $category['ct_name'] ?></label>
                                    </span><!--chkbox-span-->
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="side-box">
                    <h3>Location</h3>
                    <ul>
                        <?php /* ?><li><input data-role="tagsinput" type="text" placeholder="Enter a city" id="city_keyword" class="form-control"></li><?php */ ?>
                        <li>
                            <?php if(isset($teacher_locations) && !empty($teacher_locations)): ?>
                            <select id="city_keyword" class="form-control">
                                <option value="">All Location</option>
                            <?php foreach ($teacher_locations as $t_location): ?>
                                <option value="<?php echo $t_location['id'] ?>"><?php echo $t_location['city_name'] ?></option>
                            <?php endforeach; ?>
                            </select>
                            <?php endif; ?>
                        </li>
                        <li id="liting_city"></li>
                    </ul>
                </div>

                <div class="side-box">
                    <h3>Language Speaks</h3>
                    <?php if (isset($languages) && !empty($languages)): ?>
                        <ul>
                            <?php foreach ($languages as $language): ?>
                                <li>                
                                    <span class="chkbox-span">
                                        <input type="checkbox" id="language_<?php echo $language['id'] ?>" value="<?php echo $language['id'] ?>" class="language-selector" />
                                        <label class="label-narrow" for="language_<?php echo $language['id'] ?>"><span></span><?php echo $language['cl_lang_name'] ?></label>
                                    </span><!--chkbox-span-->
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>            


                <div class="side-box">
                    <h3>Rating</h3>
                    <ul>
                        <li>                
                            <span class="chkbox-span">
                                <input type="checkbox" id="5star" name="rate_selector" value="5" class="rate-selector" />
                                <label class="label-narrow" for="5star"><span></span>
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                            
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                         
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                              
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                                                            
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                              
                                </label>
                            </span><!--chkbox-span-->
                        </li>

                        <li>                
                            <span class="chkbox-span">
                                <input type="checkbox" id="4star" name="rate_selector" value="4" class="rate-selector" />
                                <label class="label-narrow" for="4star"><span></span>
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                         
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                              
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                                                            
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                                                            
                                </label>
                            </span><!--chkbox-span-->
                        </li>

                        <li>                
                            <span class="chkbox-span">
                                <input type="checkbox" id="3star" name="rate_selector" value="3" class="rate-selector" />
                                <label class="label-narrow" for="3star"><span></span>
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                         
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                              
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                                                            

                                </label>
                            </span><!--chkbox-span-->
                        </li>

                        <li>                
                            <span class="chkbox-span">
                                <input type="checkbox" id="2star" name="rate_selector" value="2" class="rate-selector" />
                                <label class="label-narrow" for="2star"><span></span>
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                         
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                              

                                </label>
                            </span><!--chkbox-span-->
                        </li>

                        <li>                
                            <span class="chkbox-span">
                                <input type="checkbox" id="1star" name="rate_selector" value="1" class="rate-selector" />
                                <label class="label-narrow" for="1star"><span></span>
                                    <svg fill="#808080" height="16" viewBox="0 0 18 18" class="teacher-star"  width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9,13.5l5.5,4l-2.1-6.4l5.5-3.9h-6.7L9,0.5L6.9,7.2H0.1l5.5,3.9l-2.1,6.4L9,13.5z"/>
                                    <path class="st0" d="M0,0h18v18H0V0z"/>
                                    </svg>                         
                                </label>
                            </span><!--chkbox-span-->
                        </li>                                        

                    </ul>
                </div> 


            </div>

            <div class="col-xs-12 col-sm-9 col-md-9 teacher-list-content">
                <ul id="teachers_wrapper"></ul>
                <div class="load-more pull-left full-width" id="load_more_btn"></div>
            </div>
        </div>
    </div>    
</section>
<!--teacher listing section ends here-->


<!-- Modal pop up contents:: Edit Profile Popup-->
<div class="modal fade ofabee-modal" id="enquiry-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ofabee-modal-content">
                <!-- Modal Header -->
            <div class="modal-header ofabee-modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title ofabee-modal-title" id="myModalLabel"></h4>
                
            </div>
            <form role="form" method="post">
                <!-- Modal Body -->
                <div class="modal-body ofabee-modal-body">
                    <div id="edit_enquiry_message" class="edit-profile-message alert alert-success"></div>
                    <div class="form-group" style="display:none;">
                        <?php $user = $this->auth->get_current_user_session('user'); ?>
                        <label for="exampleInputPassword21">Your Name</label>
                        <input class="form-control" id="user_name" value="<?php echo (isset($user['us_name'])?$user['us_name']:'') ?>" />
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Your Message</label>
                        <textarea class="ofabee-textarea" id="user_message" name="user_message" ></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer ofabee-modal-footer">
                    <button type="button" class="btn ofabee-dark" data-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn ofabee-orange" id="send_enquiry_mail">SEND</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="enquiry_send_success" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content ofabee-modal-content">
      <div class="modal-header ofabee-modal-header">
        <button type="button" class="close" data-dismiss="modal">
          &times;
        </button>
      </div>
      <div class="modal-body ofabee-modal-body textarea-top">
        <img src="<?php echo assets_url(); ?>themes/ofabee/img/Successful_icon.svg" class="blocked-image">
        <span class="your_review">Your enquiry has been
            <br />
            send</span>
      </div>
      <div class="modal-footer ofabee-modal-footer modal-footer-text-center">
        <button type="button" class="btn ofabee-dark" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>


<?php /* ?><link href="<?php echo assets_url() ?>css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<script src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js" type="text/javascript"></script><?php */ ?>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery-ui.min.js"></script>
<script>
   <?php $logged_in_student = isset($session['id'])?$session['id']:0; ?>
    var __site_url          = '<?php echo site_url() ?>';
    var __default_user_path = '<?php echo default_user_path() ?>';
    var __user_path         = '<?php echo user_path() ?>';
    var __userId            = '<?php echo $logged_in_student; ?>';
    var __theme_img         = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
    var __teachersObject    = atob('<?php echo base64_encode(json_encode($teachers)) ?>');
    var __facultylanguages  = atob('<?php echo base64_encode(json_encode($languages)) ?>');      
        __facultylanguages  = $.parseJSON(__facultylanguages);
    var __totalTeacher      = '<?php echo $total_teachers ?>';
    $(document).ready(function () {
        __teachersObject      = $.parseJSON(__teachersObject);
        $('#load_more_btn').html('<a href="javascript:void(0)" onclick="loadMoreTeacher()" class="btn dark-round-btn center-block noborder">Load More</a>');
        $('#teachers_wrapper').html(renderTeachersHtml(__teachersObject));
        __offset++;
        $(".filter-options").click(function () {
            // Set the effect type
            var effect = 'slide';
            // Set the options for the effect type chosen
            var options = {direction: 'left'};
            // Set the duration (default: 400 milliseconds)
            var duration = 500;
            $('.teacher-list-sidebar').toggle(effect, options, duration);
        });
    });
    
    function renderTeachersHtml(teachers)
    { 
        $('#load_more_btn a').html('Load More').hide();
        var teachersHtml  = '';
        if(Object.keys(teachers).length > 0 )
        {
            $.each(teachers, function(teacherKey, teacher )
            {
                var uri=__site_url+'teachers/view/'+teacher['id'];
                teachersHtml += '<li data-location="'+uri+'" class="teacher-card" id="teacher_'+teacher['id']+'">';

                teachersHtml += renderTeacherHtml(teacher,uri);
                teachersHtml += '</li>';
            });
            if( /*Object.keys(teachers).length*/ __totalTeacher > (__offset*__perPage))
            {
                $('#load_more_btn a').css('display', 'block');                
            }
        }
        return teachersHtml;
    }
    
    $(document).on('click', '.teacher-lt img, .teacher-rt h3', function(){
        var hrefLocation = $(this).parent().parent().parent().attr('data-location');
        //alert(hrefLocation);
         $('<a/>', {
                'id':'teacher_details_page_btn',
                'href':hrefLocation,
            }).on('click', function(){
            }).appendTo('body');
        $('#teacher_details_page_btn').trigger('click');
    });
    
    function renderTeacherHtml(teacher, uri)
    {
        var teachersHtml = '';
        var userImg      = '';    
            userImg      = ((teacher['us_image'] == 'default.jpg')?__default_user_path:__user_path); 
            teachersHtml += '';
            teachersHtml += '    <a href="'+uri+'"><div class="col-xs-8 col-sm-8 br teacher-card-left usdfw">';
            teachersHtml += '        <div class="teacher-lt">';
            teachersHtml += '            <img  src="'+userImg+''+teacher['us_image']+'" class="img-responsive img-circle" width="66">';
            teachersHtml += '        </div>';
            teachersHtml += '        <div class="teacher-rt">';
            teachersHtml += '            <h3 class="listed-teacher-nm">'+teacher['us_name']+'</h3>';
            if(teacher['us_badge'] > 0 )
            {
            teachersHtml += '            <span class="svg-badge-teacher teacher-badge">';
            teachersHtml += '                <img src="'+__theme_img+'/img/award-brouns.svg" alt="reputation-badge" class="reputation-badge">';
            teachersHtml += '            </span>';
            }
            if(teacher['us_degree'])
            {
            teachersHtml += '            <span class="department">'+teacher['us_degree']+'</span>';
            }
            teachersHtml += '        </div>';
            if(typeof teacher['expertise'] != 'undefined' && Object.keys(teacher['expertise']).length > 0 )
            {
            teachersHtml += '        <span class="expertise-text-wraper pull-left teacher-row">';
            teachersHtml += '            <span class="expertise-text smallblock">Expertise</span>';
            teachersHtml += '            <span class="expertise-lists">';
            teachersHtml += '                <ul class="expertise-ul">';
                $.each(teacher['expertise'], function(expertiseKey, expertise )
                {
            teachersHtml += '                    <li>'+expertise['fe_title']+'</li>';
                });
            teachersHtml += '                </ul>';
            teachersHtml += '            </span>';
            teachersHtml += '        </span>';
            }
            if(teacher['us_native'] != null && teacher['us_native'] != '' )
            {
            teachersHtml += '        <span class="expertise-text-wraper pull-left teacher-row">';
            teachersHtml += '            <span class="expertise-text smallblock">From</span>';
            teachersHtml += '            <span class="expertise-lists">'+teacher['us_native']+'</span>';
            teachersHtml += '        </span>';
            }
            if(teacher['us_language_speaks'] != null && teacher['us_language_speaks'] != '' )
            {
                var facultyLanguages    = teacher['us_language_speaks'].split(',');
                var languagePieces      = new Array;
                if(facultyLanguages.length > 0 )
                {
                    for(var l=0; l<facultyLanguages.length; l++)
                    {
                        languagePieces[l] = __facultylanguages[facultyLanguages[l]]['cl_lang_name'];
                    }
                }
            teachersHtml += '        <span class="expertise-text-wraper pull-left teacher-row">';
            teachersHtml += '            <span class="expertise-text smallblock">Speaks</span>';
            teachersHtml += '            <span class="expertise-lists">'+(languagePieces.join(', '))+'</span>';
            teachersHtml += '        </span>';
            }
            teachersHtml += '    </div></a>';
            teachersHtml += '    <div class="col-xs-4 col-sm-4 teacher-card-right usdfw">';
            teachersHtml += '        <span class="star-rating-and-deatails padtb15">';
            var ratingPercentage = (teacher['rating']*20);
            /*var clearence        = ratingPercentage%10;
                if(clearence > 5)
                {
                    ratingPercentage = ratingPercentage+(10-clearence);
                }
                else
                {
                    ratingPercentage = ratingPercentage-clearence;    
                }*/
            teachersHtml += '            <div class="star-ratings-sprite-two margin-right custom-star"><span style="width:'+ratingPercentage+'%" class="star-ratings-sprite-rating-two"></span></div>';
            teachersHtml += '        </span>';
            if(teacher['us_experiance'] != null && teacher['us_experiance'] != '' && teacher['us_experiance'] > 0 )
            {
                var experianceHtml  = '0';
                var years           = Math.floor(teacher['us_experiance'] / 12); // 1
                var remainingMonths = Math.floor(teacher['us_experiance'] % 12); // 6
                if(years > 0 )
                {
                    experianceHtml = years+'';
                }
                if(remainingMonths > 0 )
                {
                    experianceHtml += '.'+remainingMonths;
                }
                years         = (years>1)?'<b>'+years+'</b>Years':'<b>'+years+'</b>Year';
                teachersHtml += '        <span class="center-block">';
                teachersHtml += '            <span class="teacher-exp">'+years+'</span>';
                teachersHtml += '            <span class="center-block text-center">Teaching Experience</span>';
                teachersHtml += '        </span>';
            }
                teachersHtml += '        <span class="center-block mt30">';
            if(__userId > 0)
            {
                teachersHtml += '            <a href="javascript:void(0)" onclick="enquiryPopUp('+teacher['id']+', \''+btoa(teacher['us_name'])+'\')" class="btn orange-round-btn center-block">Send Message</a>';
            }else{
                teachersHtml += '            <a href="'+__site_url+'login"  class="btn orange-round-btn center-block">Send Message</a>';
            }
                teachersHtml += '        </span>';

            teachersHtml += '    </div>';
        return teachersHtml;
    }
    
    var __categoryFilters = new Object;
    var __locationFilters = new Object;
    var __languageFilters = new Object;
    var __ratingFilters   = new Object;
    var __offset          = 1;
    var __perPage         = '<?php echo $per_page ?>';
    var __start           = false;
    var __requestTimeOut  = null;
    function getTeachers()
    {
        renderLocationInput();
        var keyword  = $('#teacher_keyword').val();
        AbortPreviousAjaxRequest();
        __requests.push($.ajax({
            url: __site_url+'teachers/teachers_json',
            type: "POST",
            data:{"is_ajax":true, "category_filters":JSON.stringify(__categoryFilters), "location_filters":JSON.stringify(__locationFilters), "language_filters":JSON.stringify(__languageFilters), "rating_filters":JSON.stringify(__ratingFilters), "keyword":keyword, 'offset':__offset},
            success: function(response) {
                var data = $.parseJSON(response);
                    __totalTeacher = data['total_teachers'];
                if(data['error']==false)
                {
                    if(__start == true)
                    {
                        $('#teachers_wrapper').html('');
                        $('#teachers_wrapper').html(renderTeachersHtml(data['teachers']));
                    }
                    else
                    {
                        $('#teachers_wrapper').append(renderTeachersHtml(data['teachers']));
                    }
                    __start = false;
                    __offset++;
                }
            }
        }));
    }
    
    $(document).on('change', '.category-selector', function(){
        var selectorValue = $(this).val();
        //alert(__categoryFilters);
        if($(this).prop('checked') == true)
        {
            __categoryFilters[selectorValue] = selectorValue;
        }
        else
        {
            removeArrayIndex(__categoryFilters, selectorValue)
        }
        initGetTeacher();
    });
    $(document).on('change', '.language-selector', function(){
        var selectorValue = $(this).val();
        if($(this).prop('checked') == true)
        {
            __languageFilters[selectorValue] = selectorValue;
        }
        else
        {
            removeArrayIndex(__languageFilters, selectorValue)
        }
        initGetTeacher();
    });
    $(document).on('change', '.rate-selector', function(){
        var selectorValue = $(this).val();
        if($(this).prop('checked') == true)
        {
            __ratingFilters[selectorValue] = selectorValue;
        }
        else
        {
            removeArrayIndex(__ratingFilters, selectorValue)
        }
        initGetTeacher();
    });
    
    $(document).on('keyup', '#teacher_keyword', function(){
        initGetTeacher();
    });
    $(document).on('change', '#city_keyword', function(){
        initGetTeacher();
    });
    
    /*$(document).on('itemAdded', '#city_keyword', function(event){
        initGetTeacher();
    });
    $(document).on('itemRemoved', '#city_keyword', function(event){
        initGetTeacher();
    });
   
    $(document).on('click', '.auto-search-lister-city li', function(){
        $('#city_keyword').tagsinput('removeAll');
        $('#city_keyword').tagsinput('add', $(this).text());
        $('.bootstrap-tagsinput input').val('');
        $(this).parent().html('').hide();
    });
    $('#city_keyword').on('beforeItemAdd', function(event) {
        $('#city_keyword').tagsinput('removeAll');
    });*/

    var __timeOutLanguage   = null;
    var __currentLocation   = null;
    $(document).on('keyup', '.bootstrap-tagsinput input', function(){
        var keyword         = $(this).val();
        __currentLocation   = this;
        clearTimeout(__timeOutLanguage);
        __timeOutLanguage = setTimeout(function(){ 
            var url 	= __site_url+'teachers/cities';
            var tagHTML	= '';
            if( keyword )
            {
                $("#liting_city").html('<ul class="auto-search-lister-city"><li>Loading...</li></ul>').show();
                $.ajax({
                    url: url,
                    type: "POST",
                    data:{ 'city_name':keyword, 'is_ajax':true},
                    success: function (response){
                        var data    = $.parseJSON(response);
                        if( data['tags'].length > 0 ){
                            tagHTML += '<ul class="auto-search-lister-city">';
                            for( var i = 0; i < data['tags'].length; i++){
                                tagHTML += '<li id="'+data['tags'][i]['id']+'">'+data['tags'][i]['city_name']+'</li>';
                            }
                            tagHTML += '</ul>';
                        }
                        $("#liting_city").html(tagHTML).show();       
                    },
                });
            }
        }, 300);
    });

    
    function renderLocationInput()
    {
        __locationFilters = new Object;
        var location      = $('#city_keyword').val();
        if(location!='')
        {
            location = location.split(",");
            for(var l=0;l<location.length;l++)
            {
                __locationFilters[location[l]] = location[l];
            }
        }
    }
    
    function enquiryPopUp(teacherId, teacherName)
    {
        $('#user_message, #user_name').removeClass('form-error');
        $('#edit_enquiry_message').html('').hide();
        $('#myModalLabel').html('Send message to "'+atob(teacherName)+'"');
        $('#user_message').val('');
        $('#send_enquiry_mail').unbind('click');
        $('#send_enquiry_mail').click({'teacherId':teacherId, 'teacherName':teacherName}, sendEnquiryMail);
        $('#enquiry-popup').modal();
    }
    
    
    
    function removeArrayIndex(array, index) {
        if(typeof array == 'object')
        {
            delete array[index];
        }
        else
        {
          for(var i = array.length; i--;) {
              if(array[i] === index) {
                  array.splice(i, 1);
              }
          }        
        }
    }
    
    function initGetTeacher()
    {
        __offset = 1;
        __start  = true;
        clearTimeout(__requestTimeOut);
        //__requestTimeOut = setTimeout(function(){
            getTeachers(); 
        //}, 100);
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
                /*$('#edit_enquiry_message').html('Your message is send to '+atob(param.data.teacherName)+' <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a> ').show();
                $('#send_enquiry_mail').html('SEND');
                setTimeout(function(){
                    $('#enquiry-popup').modal('hide');
                }, 1000)*/
                $('#send_enquiry_mail').html('SEND');
                $('#enquiry-popup').modal('hide');
                $('#enquiry_send_success').modal('show');
            },
        });
    }
    
    function loadMoreTeacher()
    {
        $('#load_more_btn a').html('Loading...');
        getTeachers();
    }
    
    var __requests = new Array();
    function AbortPreviousAjaxRequest()
    {
        for(var i = 0; i < __requests.length; i++)
        {
            __requests[i].abort();
        }
    }

</script>
<style>
.form-error{ border: 1px solid #ff7a7a;}
.edit-profile-message{display: none;}
#load_more_btn a{ display: none;}
</style>
<?php include_once 'footer.php'; ?>