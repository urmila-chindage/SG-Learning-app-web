<?php include 'header.php'; ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
<script type="text/javascript">
	var rattings = $.parseJSON('<?php echo $rattings; ?>');
</script>
<?php //echo "<pre>";print_r($courses);die; ?>

<section>
    <div class="nav-group pad0">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <div class="sdfw">
                    <div class="custom-search-input custom-search-input-alter mobile-search">
                        <div class="input-group col-md-12">
                            <input type="text" id="searchid" value="<?php echo $query; ?>" class="form-control teacher-box input-lg  padd-alter search-rslt" placeholder="Find a Course" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Find a Course'">
                            <span class="input-group-btn">
                                <button id="searchbtn" class="btn btn-search btn-lg searchbtn-align-fixer" type="button">
                                    <img src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/img/search.png' ?>" width="36" height="36" class="search-img">
                                    <img src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/img/close-search.svg' ?>" width="22" height="22" class="closeimg">
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section>
    <div class="container container-altr">
        <div class="container-reduce-width">
            <div class="drop-down-wrap">
                <div class="row">
                    <!--columns-->
                    <div class="col-md-3 col-sm-3">
                        <span class="all-questions-wrapper olp-library-page-drop-wrap olp-drop-alterd-sm">
                            <div class="form-group mul-alter all_price">
                                <select id="course_search_price" multiple="multiple" class="form-control category-sel" name="course_search_price[]">
                                    <option value="1">Free</option>
                                    <option value="2">Paid</option>
                                </select>
                            </div>
                        </span>
                    </div><!--columns--> 
                    
                    <div class="col-md-3 col-sm-3">
                        <span class="all-questions-wrapper olp-library-page-drop-wrap olp-drop-alterd-sm">
                            <div class="form-group mul-alter all_language">
                                <?php if (isset($languages) && !empty($languages)): ?> 
                                <select id="course_search_language" multiple="multiple" class="form-control category-sel" name="course_search_language[]">
                                    <?php foreach ($languages as $language): ?>
                                    <option value="<?php echo $language['id']; ?>"><?php echo $language['cl_lang_name']; ?></option>    
                                    <?php endforeach; ?>
                                </select>
                                <?php endif; ?>
                            </div>
                        </span>
                    </div><!--columns--> 
                    
                    <div class="col-md-3 col-sm-3">
                        <span class="all-questions-wrapper olp-library-page-drop-wrap olp-drop-alterd-sm">
                            <div class="dropdown">
                                <button id="sortby_selected_text" class="btn btn-outline dropdown-toggle btn-library-width sort-by-padding" type="button" data-toggle="dropdown">Sort by
                                    <span class="dropdown-arrow-down dropdown-right-align">
                                        <b class="caret caret-alter"></b>
                                    </span>
                                </button>
                                <ul class="dropdown-menu sort-by-filters-course1  drop-olp-width">
                                    <li class="sort-by-filters-search"><a href="javascript:void(0)" data-sortby="1">Relevance</a></li>
                                    <li class="sort-by-filters-search"><a href="javascript:void(0)" data-sortby="2">Price: Low to High</a></li>
                                    <li class="sort-by-filters-search"><a href="javascript:void(0)" data-sortby="3">Price: High to Low</a></li>
                                    <li class="sort-by-filters-search"><a href="javascript:void(0)" data-sortby="4">Top rated</a></li>
                                </ul>
                            </div>
                        </span>
                    </div><!--columns-->                                               
                </div><!--row-->
            </div><!--drop-down-wrap-->
                
            <div id="search_result_wrapper">
                <?php if(!empty($courses)) { 
                    $count_course = 1;?>
	        <?php foreach($courses as $key => $course){ ?>
                <?php if($count_course%4 == 1){ ?>
                <div class="row">
                <?php } ?>
                    <div class="col-md-3 col-sm-3 xs-replacer">
                        <a class="block-link" href="<?php echo site_url().''.$course['cb_slug']; ?>">
                            <div class="course-block-1">
                                <div class="course-top-half course-top-sm-alter"> 
                                    <div class="block-load-in" id="whishdiv_<?php echo $course['id'] ?>">
                                    <?php echo $course['wish_stat']; ?> 
                                    </div>
                                    <?php 
                                    $image_first_name   = substr($course['cb_image'],0,-4);
                                    $image_dimension    = '_300x160.jpg';
                                    $image_new_name     = $image_first_name.$image_dimension;
                                    ?>
                                    <img src="<?php echo (($course['cb_image'] == 'default.jpg')?default_course_path():course_path(array('course_id' => $course['id']))).$image_new_name?>" class="card-img-fit"> 
                                </div>
                      <!--course-top-half-->
                                <div class="courser-bottom-half">
                                    <label class="block-head"><?php echo $course['cb_title']; ?></label>
                                    <?php
                                        $tutor_names = array();
                                        foreach ($course['course_tutors'] as $val) {
                                            $tutor_names[] = $val['us_name'];
                                        }
                                        $course_rate = "width:0%";
                                        if($course['ratting'] != 0){
                                            $percentage = 20*$course['ratting'];
                                            $course_rate = 'width:'.$percentage.'%';
                                        }
                                    ?>
                                    <p class="sub-head-des">By <?php echo (empty($tutor_names))?$admin:implode(', ',$tutor_names); ?></p>
                                    <div class="star-ratings-sprite star-ratings-sprite-block"><span style="<?php echo $course_rate; ?>" class="star-ratings-sprite-rating"></span></div>                              

                                    <label class="amount"><?php echo (($course['cb_is_free'] == '1') || ($course['cb_price'] == '0'))?'FREE':(($course['cb_discount'] != '0')?"RS. ".$course['cb_discount']:"RS. ".$course['cb_price']) ?></label>
                                    <label class="discount"><?php echo (($course['cb_is_free'] == '1') || ($course['cb_price'] == '0'))?'':(($course['cb_discount'] != '0')?"RS. ".$course['cb_price']:'') ?></label>
                                </div>
                      <!--courser-bottom-half--> 
                            </div>
                  <!--course-block-1--> 
                        </a>
                    </div><!--columns-->
                <?php if($count_course%4 == 0){?>
                </div><!--row-->
                <?php }
                $count_course++;
                } ?>
                <?php if ($count_course%4 != 1){echo '</div>';}  ?>
                                <?php } else{ ?>
                                    <?php echo "<h3 style='text-align:center'>No results found</h3>";?>
                                <?php } ?>
            </div>
        </div><!--container-reduce-width-->
    </div> <!--container container-altr-->               
</section>


<script type="text/javascript">
 
    var __site_url              = '<?php echo site_url() ?>';
    var __default_course_path   = '<?php echo default_course_path() ?>';
    var __course_path           = '<?php echo course_path() ?>';
    var __admin_name            = '<?php echo $admin ?>';
    var by_text                 = '<?php echo 'By ' ?>';
    var __userId                = '<?php //echo $logged_in_student; ?>';
    var __theme_img             = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
    //var __coursesObject         = atob('<?php //echo base64_encode(json_encode($course_list)) ?>');
    var __facultylanguages      = atob('<?php echo base64_encode(json_encode($languages)) ?>');      
        __facultylanguages      = $.parseJSON(__facultylanguages);        
    
    $(document).on("click",".block-load-in",function(event){
        event.preventDefault();
    });
    
    var __categoryFilters = new Object;
    var __languageFilters = new Object;
    var __priceFilters    = new Object;
    var __sortId          = 0;
    var __start           = false;
    var __progress        = false;
    var __requestTimeOut  = null;
    
    function renderCoursesHtml(courses)
    { 
        //alert(courses);
        
        var coursesHtml  = '';
        if(Object.keys(courses).length > 0 )
        {
            var count_course = 1;
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
                
                var by_tutor = (tutor_names.length == 0)?__admin_name:tutor_names.join();
                var price = ((course['cb_is_free'] == '1') || (course['cb_price'] == '0'))?'FREE':((course['cb_discount'] != '0')?"RS. "+course['cb_discount']:"RS. "+course['cb_price']);
                var discount = ((course['cb_is_free'] == '1') || (course['cb_price'] == '0'))?'':((course['cb_discount'] != '0')?"RS. "+course['cb_price']:'');
                if(count_course%4 == 1){
                coursesHtml += '<div class="row">';
                }
                coursesHtml += '    <div class="col-md-3 col-sm-3 xs-replacer">';
                coursesHtml += '        <a class="block-link" href="'+__site_url+course['cb_slug']+'">';
                coursesHtml += '            <div class="course-block-1">';
                coursesHtml += '                <div class="course-top-half course-top-sm-alter">';
                coursesHtml += '                    <div class="block-load-in" id="whishdiv_'+course['id']+'">';
                coursesHtml += '                    '+course['wish_stat']+'</div>';
                coursesHtml += '                    <img src="'+__img_path+__image_new_name+'" class="card-img-fit">';
                coursesHtml += '                </div>';
                coursesHtml += '                <div class="courser-bottom-half">'; 
                coursesHtml += '                    <label class="block-head">'+course['cb_title']+'</label>';
                coursesHtml += '                    <p class="sub-head-des">'+by_text+by_tutor+'</p>';
                coursesHtml += '                    <div class="star-ratings-sprite star-ratings-sprite-block"><span style="'+course_rate+'" class="star-ratings-sprite-rating"></span></div>';
                coursesHtml += '                    <label class="amount">'+price+'</label>';
                coursesHtml += '                    <label class="discount">'+discount+'</label>';
                coursesHtml += '                </div>';
                coursesHtml += '            </div>';
                coursesHtml += '        </a>';
                coursesHtml += '    </div>';
                if(count_course%4 == 0){
                coursesHtml += '</div>';
                }
                count_course++;
                
            });
            
            if (count_course%4 != 1){coursesHtml += '</div>';} 
            
        }
        
        return coursesHtml; 
    }
    
    function getCourses()
    {
        //alert(JSON.stringify(__categoryFilters));
        var language = $('#course_search_language').val();
        var price    = $('#course_search_price').val();
        var keyword  = $('#searchid').val();
        AbortPreviousAjaxRequest();
        __requests.push($.ajax({
            url: __site_url+'course/courses_json',
            type: "POST",
            data:{"is_ajax":true, "language_filters":JSON.stringify(language), "price_filters":JSON.stringify(price), "sort_filters":__sortId, "keyword":keyword, 'offset':__offset},
            success: function(response) {
                //alert(response);
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    if(__start == true)
                    {
                        $('#search_result_wrapper').html('');
                        $('#search_result_wrapper').html(renderCoursesHtml(data['course_list']));
                    }
                    else
                    {
                        $('#search_result_wrapper').append(renderCoursesHtml(data['course_list']));
                    }
                    __start = false;
                    __offset++;
                }
            }
        }));
    } 
    
    $(document).on('change', '#course_search_language', function(){
        initGetCourse();
    });
    $(document).on('change', '#course_search_price', function(){
        initGetCourse();
    });
    
    $(document).on('click', '.sort-by-filters-search a', function(e){
        var sort_id     = $(this).attr('data-sortby');
        var sort_name   = $(this).text();
        $('#sortby_selected_text').text(sort_name);
        __sortId        = sort_id;
        //alert(__sortId);
        initGetCourse();
    });
    
    function initGetCourse()
    {
        __offset = 1;
        __start  = true;
        clearTimeout(__requestTimeOut);
        __requestTimeOut = setTimeout(function(){
            getCourses();
        }, 600);
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

<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/jquery.rateyo.min.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/search.js'; ?>" ></script>
<?php include 'footer.php'; ?>