<?php include 'header.php';?>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/bootstrap-multiselect.js"></script>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/bootstrap-multiselect.css" rel="stylesheet">
<style>
    .dropdown-menu>li>a{white-space: normal !important;}
    /* .courser-bottom-half a label {
        cursor: pointer;
        display: inline;
    } */
    @media (max-width:560px){
        .no-course-container {padding: 50px 10px;}
        .no-course-container .noquestion-btn{margin: 0 auto;width: 60%;}
    }
    /*bundle label*/
    .bundle-label{
    background: #ff327a;
        width: 36px;
        height: 42px;
        border-radius: 0px 6px 6px 6px;
        position: absolute;
        top: -5px;
        right: 25px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .bundle-label:before{
        content: ' ';
        position: absolute;
        left: -4px;
        top: 0px;
        border-style: solid;
        border-width: 0 0 6px 4px;
        border-color: transparent transparent #aa28ac transparent;
    }
    .bundle-label .bundle-icon{height: 20px;margin: 0 auto;}
    .bundle-label .bundle-count{
        font-size: 12px;
        color: #fff;
        text-align: center;
        line-height: 15px;
    }
    .bundle-label .bundle-count span{font-weight: 600;}
    .bundle-label .bundle-count span.in{
        font-size: 10px;
        display: inline-block;
    }
    .course-block-1 a {
        text-decoration: none;
        display: contents;
    }
    
</style>

<section>
    <div class="nav-group pad0">
        <div class="container container-altr pr-10-xs pl-10-xs">
            <div class="explore-course-row">
                <div class="col-xs-12 col-sm-6 col-md-6 sdfw">
                    <h2 class="funda-head expl-course"><?php echo lang('explore_courses') ?></h2>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 sdfw">
                    <div class="custom-search-input custom-search-input-alter">
                        <div class="input-group col-md-12">
                            <input type="text" id="course_listing_keyword" class="form-control teacher-box input-lg  padd-alter" placeholder="<?php echo lang('search_courses') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php echo lang('search_courses') ?>'" autocomplete="off" value="<?php echo implode(" ", $keyword_arr); ?>">
                            <span class="input-group-btn" id="search_clear" style="display:none">
                                <button type="button" class="close" aria-label="Close"  >
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </span>
                            <span class="input-group-btn" id="search_icon">
                                <button type="submit" class="btn btn-search btn-lg searchbtn-align-fixer" type="button" id="search_btn">
                                    <img src="<?php echo assets_url() . 'themes/' . $this->config->item('theme') . '/img/search.png' ?>" width="36" height="36">
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="course-listing-wrapper xs-minheight-vh">
    <div class="ex-course">
        <div class="container-reduce-width">
            <div class="container container-res-chnger-frorm-page no-padding-xs">
                <div class="explorer">
                    <div class="drop-down-wrap">
                        <span class="all-questions-wrapper olp-library-page-drop-wrap olp-drop-alterd-sm all-categor-drop">
                            <div class="form-group mul-alter all_cata">
                                <span class="multiselect-native-select">
                                    <?php if(!empty($categories)):?>
                                        <select id="course_listing_category" multiple="multiple" class="form-control category-sel" name="course_listing_category[]">
                                            <?php foreach($categories as $category): ?>
                                                <?php if($category['ct_status'] && $category['ct_deleted']== 0): ?>
                                                    <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id']?'selected':''; ?>><?php echo $category['ct_name'] ?></option>    
                                                <?php endif; ?>
                                            <?php endforeach; ?>    
                                        </select>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </span>
                    </div>
                    <div class="ex-course-container" id="course_list_wrapper">
                    </div>
                </div>
            </div>

            <div class="text-center text-center-btn" id="load_more_courses_list">
                <span class="noquestion-btn-wrap">
                    <a href="javascript:void(0)" onclick="loadMoreCourse()" class="orange-flat-btn noquestion-btn">Load more courses</a>
                </span>
            </div><!--text-center-->
        </div><!--container-reduce-width-->
    </div> <!--container container-altr-->
</section>

<script type="text/javascript">
    var __site_url              = '<?php echo site_url() ?>';
    var __default_course_path   = '<?php echo default_course_path() ?>';
    var __default_catalog_path  = '<?php echo default_catalog_path() ?>';
    var __catalog_path          = '<?php echo catalog_path() ?>';
    var __course_path           = '<?php echo course_path() ?>';
    var __admin_name            = '<?php echo $admin; ?>';
    var by_text                 = '<?php echo lang('by') ?>';
    var __theme_img             = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
    var __coursesObject         = atob('<?php echo base64_encode(json_encode($course_list)) ?>');
    let __itemsActive           = [];
    var __perPage               = +'<?php echo $limit ?>';
    let __page                  = 1;
    let __search                = true;
    let __categoryFilters       = [];
    $(document).ready(function () {
        let search          = getQueryStringValue("search");
        let category        = getQueryStringValue("categoryids");
        category            = category != ''?category:'';
        __categoryFilters   = category.split(',');
        __categoryFilters   = __categoryFilters.map(function(category){
            return +category;
        });
        search = search.split('-').join(' ');
        __coursesObject     = $.parseJSON(__coursesObject); 
        //console.table(__coursesObject);
        if(search != '' || category != 0){
            var results = [];
            var searchCategory = '';
            var searchTitle = '';
            for(var i=0; i<__coursesObject.length; i++) {
                if(__coursesObject[i]['item_type'] == 'course') {
                    searchCategory = __coursesObject[i].cb_category;
                    searchTitle = __coursesObject[i].cb_title;
                } else {
                    searchCategory = __coursesObject[i].c_category;
                    searchTitle = __coursesObject[i].c_title;
                }
                
                if(searchCategory){
                    var course_categories   = searchCategory.split(',');
                    for(var j=0; j<course_categories.length;j++) course_categories[j] = +course_categories[j];
                    if(search != '' && __categoryFilters.length != 0){
                        if(containsAll(course_categories,__categoryFilters) && (searchTitle.toLowerCase().indexOf(search.toLowerCase())!=-1)){
                            results.push(__coursesObject[i]); 
                        }
                    }else{
                        if(search != '' && (searchTitle.toLowerCase().indexOf(search.toLowerCase())!=-1)){
                            results.push(__coursesObject[i]);
                        }
                        if((__categoryFilters.length != 0 && containsAll(course_categories,__categoryFilters))){
                            results.push(__coursesObject[i]);
                        }
                    }
                }
            }
            __itemsActive   = results;
        }else{
            __itemsActive   = __coursesObject;
        }
        $("#course_listing_category").val(__categoryFilters);
        $('#course_listing_category').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            dropUp: false,
            nonSelectedText: "All Categories"
        });
        //console.log(__itemsActive);
        if(__itemsActive.length != 0){
            $('#load_more_courses_list a').hide();
            paginate(__itemsActive,__perPage,__page);
        }else{
            $('#load_more_courses_list a').hide();
            $('#course_list_wrapper').html(
                `<div class="col-sm-12 dashboard-no-course">
                    <div class="no-course-container">
                        <img class="no-questions-svg" src="${__theme_img}/img/no-courses.svg">
                        <span class="no-discussion no-content-text"><span>Oops! </span>No result found.</span>
                    </div>                 
                </div>`
            );
        }

        applyFilter();
    });
    function paginate (array, page_size, page_number) {
        
        if(page_number != 1){
            __search = false;
        }
        --page_number;
        let courses     = array.slice(page_number * page_size, (page_number + 1) * page_size);
        //console.log(array,'123');
        if(__search){
            $('#course_list_wrapper').html(renderCoursesHtml(array.slice(page_number * page_size, (page_number + 1) * page_size)));
        }else{
            $('#course_list_wrapper').append(renderCoursesHtml(array.slice(page_number * page_size, (page_number + 1) * page_size)));
        }
        if((__itemsActive.length - ((page_number+1) * page_size)) > 0){
            $('#load_more_courses_list a').html('Load more courses').show();
        }else{
            $('#load_more_courses_list a').html('Load more courses').hide();
        }
    }
    $(document).on('keyup','#course_listing_keyword',function(event){
        setTimeout(function(){
            $('#course_listing_category').val('');
            //$('#course_listing_category').multiselect('refresh');
            //__categoryFilters.length = 0;
            $("#search_btn").click();
        },400);
        
        if($('#course_listing_keyword').val()) {
            $('#search_clear').show();
            $('#search_icon').hide();
        } else {
            $('#search_clear').hide();
            $('#search_icon').show();
        }
    });

    if($('#course_listing_keyword').val()) {
        $('#search_clear').show();
        $('#search_icon').hide();
    } else {
        $('#search_clear').hide();
        $('#search_icon').show();
    }

    $(document).on('click', '#search_clear', function(){
        __offset = 1;
        $('#course_listing_keyword').val('');
        $('#search_clear').hide();
        $('#search_icon').show();
        applyFilter();
    });

    $(document).on("click","#search_btn",function(){
        applyFilter();
    });
    $(document).on("click",".block-load-in",function(event){
        event.preventDefault();
    });
    function renderCoursesHtml(courses)
    {
        //console.log(courses);
        $('#load_more_courses_list a').html('Load more courses').hide();
        var coursesHtml  = '';
        if(Object.keys(courses).length > 0 )
        {
            var count_course = 1;
            $.each(courses, function(courseKey, course )
            {
                coursesHtml     += __courseCard(course);
                count_course++;
            });
            if( Object.keys(courses).length == __perPage)
            {
                $('#load_more_courses_list a').css('display', 'inline-block');
            }
        }
        return coursesHtml;
    }



//on scroll load more starts here

        $.fn.isOnScreen = function(){

        var win = $(window);

        var viewport = {
        top : win.scrollTop(),
        left : win.scrollLeft()
        };
        viewport.right = viewport.left + win.width();
        viewport.bottom = viewport.top + win.height();

        var bounds = this.offset();
        bounds.right = bounds.left + this.outerWidth();
        bounds.bottom = bounds.top + this.outerHeight();

        return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));

        };

        $(document).ready(function(){
            $(window).scroll(function(){
            if ($('#load_more_courses_list').isOnScreen()) {
                // The element is visible, do something

                if(Number(__itemsActive.length) > Number(__page)){
                __page++;
                $('#load_more_courses_list a').html('Loading...');
                paginate(__itemsActive,__perPage,__page);
                }
                //console.log(__itemsActive.length,__perPage,'limit=',__page);

            } //else {
                // The element is NOT visible, do something else
            //}
            });
        });

//on scroll load more ends here

    function loadMoreCourse()
    {
        __page++;
        $('#load_more_courses_list a').html('Loading...');
        setTimeout(function(){
            paginate(__itemsActive,__perPage,__page);
        },300);
        //getCourses();
    }
    function getCourses()
    {
        // var category = $('#course_listing_category').val();
        // var language = $('#course_listing_language').val();
        // var price    = $('#course_listing_price').val();
        var keyword  = $('#course_listing_keyword').val();
        AbortPreviousAjaxRequest();
        __requests.push($.ajax({
            url: __site_url+'course/courses_json',
            type: "POST",
            data:{"is_ajax":true, "keyword":keyword, 'offset':__offset},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    if(__start == true)
                    {
                        $('#course_list_wrapper').html('');
                        $('#course_list_wrapper').html(renderCoursesHtml(data['course_list']));
                    }
                    else
                    {
                        $('#course_list_wrapper').append(renderCoursesHtml(data['course_list']));
                    }
                    __start = false;
                    __offset++;
                }
            }
        }));
    }
    $(document).on('click', '.sort-by-filters-course a', function(e){
        var sort_id     = $(this).attr('data-sortby');
        var sort_name   = $(this).text();
        $('#sortby_selected_text').text(sort_name);
        __sortId        = sort_id;
        initGetCourse();
    });
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
            }
        }
        else{
            window.location = base_url+'login';
        }
    }
    $(document).on('change', '#course_listing_category', function(){
        var selectorValue = $(this).val();
        if(selectorValue != null && selectorValue.length != 0){
            __categoryFilters = selectorValue.map(function(cId){
                return +cId;
            });
        }else{
            __categoryFilters = [];
        }
        setTimeout(function(){
            applyFilter();
        },300);
    });
    function applyFilter(){
        
        __search = true;
        __page = 1;
        var search          = $('#course_listing_keyword').val();
        
        if (history.pushState) {
            var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
            //console.log(__categoryFilters[0]);
            if(search != '' && __categoryFilters.length != 0 && __categoryFilters[0]!='0'){
                var uSearch = search.split(' ').join('-');
                var uCategories = __categoryFilters.join(',');
                link = link+'?search='+uSearch+'&categoryids='+uCategories;
            }else{
                if(search != ''){
                    var uSearch = search.split(' ').join('-');
                    link = link+'?search='+uSearch;
                }
                if(__categoryFilters.length != 0 && __categoryFilters != '0'){
                    var uCategories = __categoryFilters.join(',');
                    link = link+'?categoryids='+uCategories;
                } else {
                    __categoryFilters.length = 0;
                }
            }
            window.history.pushState({path:link},'',link); 
        }
        
        if(search != '' || __categoryFilters.length != 0){
            var results = [];
            var searchCategory = '';
            var searchTitle = '';
            
            for(var i=0; i<__coursesObject.length; i++) {
                if(__coursesObject[i]['item_type'] == 'course') {
                    searchCategory = __coursesObject[i].cb_category;
                    searchTitle = __coursesObject[i].cb_title;
                } else {
                    searchCategory = __coursesObject[i].c_category;
                    searchTitle = __coursesObject[i].c_title;
                }
                
            if(searchCategory){
                var course_categories   = searchCategory.split(',');
                for(var j=0; j<course_categories.length;j++) course_categories[j] = +course_categories[j];
                if(search != '' && __categoryFilters.length != 0){
                    if(containsAll(course_categories,__categoryFilters) && (searchTitle.toLowerCase().indexOf(search.toLowerCase())!=-1)){
                        results.push(__coursesObject[i]); 
                    }
                }else{
                    if(search != '' && (searchTitle.toLowerCase().indexOf(search.toLowerCase())!=-1)){
                        results.push(__coursesObject[i]);
                    }
                    if((__categoryFilters.length != 0 && containsAll(course_categories,__categoryFilters))){
                        results.push(__coursesObject[i]);
                    }
                }
            }
            }
            __itemsActive   = results;
        }else{
            __itemsActive   = __coursesObject;
        }
        if(__itemsActive.length === 0){
            $('#load_more_courses_list a').hide();
            $('#course_list_wrapper').html(
                `<div class="col-sm-12 dashboard-no-course">
                    <div class="no-course-container">
                        <img class="no-questions-svg" src="${__theme_img}/img/no-courses.svg">
                        <span class="no-discussion no-content-text"><span>Oops! </span>No result found.</span>
                    </div>                 
                </div>`
            );
            return;
        }else{
            $('#load_more_courses_list a').show();
        }
        
        paginate(__itemsActive,__perPage,__page);
    }
    function containsAll(needles, haystack){ 
        for(var i = 0 , len = needles.length; i < len; i++){
            if($.inArray(+needles[i], haystack) != -1) return true;
        }
        return false;
    }
    function getQueryStringValue (key) {  
        return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));  
    }  
</script>
    
<style>
    #load_more_courses_list a{ display: none;}
</style>
<?php include_once 'modals.php'; ?>
<?php include 'footer.php'; ?>