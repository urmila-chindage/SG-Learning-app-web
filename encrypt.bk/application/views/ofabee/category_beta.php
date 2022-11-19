<?php include 'header.php'; ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
<script type="text/javascript">
    var rattings = $.parseJSON('<?php echo $rattings; ?>');
    var categoryid= '<?php echo $category_id; ?>';
    var user_id   = '<?php echo $session['id']; ?>';
</script>

<section>
    <div class="category-terms">
    	<div class="container container-altr">
            <div class="container-reduce-width">
            
                <div class="col-sm-9 col-md-9 col-lg-9 category-right col-lg-push-3 col-md-push-3 col-sm-push-3">
                    <div class="col-sm-12"><h3><?php echo lang('explore_course') ?></h3></div>
                    <div id="explore_category_course_wrapper">  
                    </div>

                    <div class="col-sm-12 full-width text-center mb30" id="load_more_category_course">
                        <a class="btn btn-black more-changes-btn-padding mt20 medbtn" onclick="loadMoreCourse()" href="javascript:void(0)">View More</a>
                    </div>
                </div>
                <?php include_once "sidebar_beta.php"; ?>
            </div>	<!--container-reduce-width-->
        </div><!--container-->       
    </div><!--category-terms-->
</section>


<script type="text/javascript">
    var __site_url              = '<?php echo site_url() ?>';
    var __default_course_path   = '<?php echo default_course_path() ?>';
    var __course_path           = '<?php echo course_path() ?>';
    var __admin_name            = '<?php echo $admin ?>';
    var by_text                 = '<?php echo lang('by') ?>';
    var __userId                = '<?php //echo $logged_in_student; ?>';
    var __theme_img             = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
    var __coursesObject         = atob('<?php echo base64_encode(json_encode($category_course)) ?>');        
    
    $(document).ready(function () {
        __coursesObject      = $.parseJSON(__coursesObject);
        $('#explore_category_course_wrapper').html(renderCoursesHtml(__coursesObject));
    });
    
    $(document).on("click",".block-load-in",function(event){
        event.preventDefault();
    });

    var __offset          = '2';
    var __perPage         = '<?php echo $per_page ?>';
    var __start           = false;
    var __progress        = false;
    var __requestTimeOut  = null;
    
    function renderCoursesHtml(courses)
    { 
        //alert(courses);
        $('#load_more_category_course a').html('View more').hide();
        var coursesHtml  = '';
        if(Object.keys(courses).length > 0 )
        {
            
            $.each(courses, function(courseKey, course )
            {
                
                var __img_path = ((course['cb_image'] == 'default.jpg')?__default_course_path:__course_path);
                var image_first_name = course['cb_image'];
                    image_first_name = image_first_name.slice(0,-4);
                var image_dimension = '_300x160.jpg';
                var __image_new_name  = image_first_name+''+image_dimension;
                
                var course_rate = "width:0%";
                if(course['ratting'] != 0){
                    var percentage = 20*course['ratting'];
                    var course_rate = 'width:'+percentage+'%';
                } 
                
                var tutor_names = new Array();
                $.each(course['assigned_tutors'], function(tutorKey, course_tutor ){
                    tutor_names.push(course_tutor['us_name']);
                });
                
                var by_tutor = (tutor_names.length == 0)?__admin_name:tutor_names.join(" , ");
                var price = ((course['cb_is_free'] == '1') || (course['cb_price'] == '0'))?'ENROLL NOW':((course['cb_discount'] != '0')?"RS. "+course['cb_discount']:"RS. "+course['cb_price']);
                var discount = ((course['cb_is_free'] == '1') || (course['cb_price'] == '0'))?'':((course['cb_discount'] != '0')?"RS. "+course['cb_price']:'');
                
                coursesHtml += '<div class="col-md-4 col-sm-4 xs-replacer">';
                coursesHtml += '    <a class="block-link" href="'+__site_url+course['cb_slug']+'">';
                coursesHtml += '        <div class="course-block-1">';
                coursesHtml += '            <div class="course-top-half course-top-sm-alter">';
                coursesHtml += '                <div class="block-load-in" id="whishdiv_'+course['id']+'">';
                coursesHtml += '                '+course['wish_stat']+'</div>';
                coursesHtml += '                <img src="'+__img_path+__image_new_name+'" class="card-img-fit">';
                coursesHtml += '            </div>';
                coursesHtml += '            <div class="courser-bottom-half">'; 
                coursesHtml += '                <label class="block-head">'+course['cb_title']+'</label>';
                coursesHtml += '                <p class="sub-head-des">'+by_text+by_tutor+'</p>';
                coursesHtml += '                <div class="star-ratings-sprite star-ratings-sprite-block"><span style="'+course_rate+'" class="star-ratings-sprite-rating"></span></div>';
                coursesHtml += '                <label class="amount">'+price+'</label>';
                coursesHtml += '                <label class="discount">'+discount+'</label>';
                coursesHtml += '            </div>';
                coursesHtml += '        </div>';
                coursesHtml += '    </a>';
                coursesHtml += '</div>';
                
            });
            
            
            if( Object.keys(courses).length == __perPage)
            {
                $('#load_more_category_course a').css('display', 'inline-block');                
            }
        }
        
        return coursesHtml; 
    }
    
    function getCourses()
    {
        AbortPreviousAjaxRequest();
        __requests.push($.ajax({
            url: __site_url+'category/category_courses_json',
            type: "POST",
            data:{"is_ajax":true, 'offset':__offset, 'category_id':categoryid},
            success: function(response) {
                //alert(response);
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    if(__start == true)
                    {
                        $('#explore_category_course_wrapper').html('');
                        $('#explore_category_course_wrapper').html(renderCoursesHtml(data['category_course']));
                    }
                    else
                    {
                        $('#explore_category_course_wrapper').append(renderCoursesHtml(data['category_course']));
                    }
                    __start = false;
                    __offset++;
                }
            }
        }));
    }
    
    function loadMoreCourse()
    {
        $('#load_more_category_course a').html('Loading...');
        getCourses();
    }
    
    var __requests = new Array();
    function AbortPreviousAjaxRequest()
    {
        for(var i = 0; i < __requests.length; i++)
        {
            __requests[i].abort();
        }
    }
    
    function add_wishlist(cid, uid, obj){
	key = $(obj).attr('data-key');
	if(uid != ''){
		if(!__progress){
			__progress = true;
			$.ajax({
				url: base_url+'course/change_whishlist',
				method: "POST",
				data: {
					cid: cid,
					uid: uid,
					stat: 1,
					page: 'search'
				},
				success: function(response){
					__progress = false;
					data = $.parseJSON(response);
					if(data.stat == '1'){
						$("#whishdiv_"+key).html(data.str);
						//$('#'+cid).addClass('wish-added');
						$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });
					}
					else{
						window.location = base_url+'login';
					}
					
				}
			});
		}else{
			//console.log('on progress');
		}
	}
	else{
		window.location = base_url+'login';
	}
	
}

function remove_wishlist(cid, uid, obj){
	key = $(obj).attr('data-key');
	if(uid != ''){
		if(!__progress){
			__progress = true;
			$.ajax({
				url: base_url+'course/change_whishlist',
				method: "POST",
				data: {
					cid: cid,
					uid: uid,
					stat: 0,
					page: 'search'
				},
				success: function(response){
					__progress = false;
					data = $.parseJSON(response);
					if(data.stat == '1'){
						//$('#'+cid).removeClass('wish-added');
						$("#whishdiv_"+key).html(data.str);
						$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });
					}
					else{
						window.location = base_url+'login';
					}
					
				}
			});
		}else{
			//console.log('on progress');
		}
	}
	else{
		window.location = base_url+'login';
	}
}
</script>

<style>
#load_more_category_course a{ display: none;}
</style>


<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/jquery.rateyo.min.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/category.js'; ?>" ></script>

<?php include('challenge_invite_modal.php'); ?>
<?php include 'footer.php'; ?>

<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/challenge_invite_modal.js'; ?>" ></script>