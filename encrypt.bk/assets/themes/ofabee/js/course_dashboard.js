    var __main_flag = 0;
    var __lecture_count = 0;
    var __quiz_count = 0;
    $(document).ready(function () {
        __ratings_object      = $.parseJSON(__ratings_object);
        //console.log(renderRatingsHtml(__ratings_object));
        $('.profile-list').html(renderRatingsHtml(__ratings_object));
        __start_discussions = true;
        __discussions_object      = $.parseJSON(__discussions_object);
        //console.log(__discussions_object);
        plot_rank_chart();
        var count = $('#discussions_total_count').html();
        count = parseInt(count);
        if(count != 0){
            if(__child_count[0]>__discussion_limit){
                $('.question-post-ul').html(renderDiscussionsHtml(__discussions_object)+'<li id="show_more_discussions"><span onClick="loadMoreDiscussions()" class="show-more-question">Show more questions</span></li>');
            }else{
                $('.question-post-ul').html(renderDiscussionsHtml(__discussions_object));
            }
        }else{
            $('.question-post-ul').html('No questions to display.');
        }
        plot_category_p_chart();
        get_assessments();
        //refresh_delete(33);
        if(__main_flag >= 3){
            $('#my_reports').remove();
            $('#report').html('<div class="container container-res-chnger-frorm-page" style=""><div class="changed-container-for-forum"><h3 class="formpage-heading graph-heading">No reports available.</h3></div></div>');
        }
        
    });
    function searchQuestion(e){
        //console.log($(e).val());
        __start_discussions = true;
        __offset_discussion = 2;
        //clear_filter();
        __filter_discussion['keyword'] = $('#quest_search').val();
        if($('#show-ans').is(':checked')){
            __filter_discussion['user_id'] = __user_id;
        }else{
            __filter_discussion['user_id'] = '';
        }
        //console.log(__filter_discussion);
        $.ajax({
            url: __site_url+'material/get_discussions_json',
            type: "POST",
            data:{"is_ajax":true,'c_id':__course_id,'limit':__discussion_limit,'offset':'0','keyword':__filter_discussion['keyword'],'user_id':__filter_discussion['user_id']},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    $("#discussions_show_count").html(Object.size(data.discussions));
                    //console.log(data['ratings']);
                    $('.question-post-ul').html('');
                    if(Object.size(data.discussions) == 0){
                        $('.question-post-ul').html('No questions to display.');
                    }else if(Object.size(data.discussions) < __discussion_limit){
                        $('.question-post-ul').html(renderDiscussionsHtml(data['discussions']));
                    }else{
                        $('.question-post-ul').html(renderDiscussionsHtml(data['discussions'])+'<li id="show_more_discussions"><span onClick="loadMoreDiscussions()" class="show-more-question">Show more questions</span></li>');
                    }
                    __start_discussions = false;
                }
            }
        });
    }

    $("#show-ans").click(function(e){
        __start_discussions = true;
        __offset_discussion = 2;
        //clear_filter();

        if($('#show-ans').is(':checked')){
            __filter_discussion['user_id'] = __user_id;
        }else{
            __filter_discussion['user_id'] = '';
        }
        __filter_discussion['keyword'] = $('#quest_search').val();
        //console.log(__filter_discussion);
        $.ajax({
            url: __site_url+'material/get_discussions_json',
            type: "POST",
            data:{"is_ajax":true,'c_id':__course_id,'limit':__discussion_limit,'offset':'0','keyword':__filter_discussion['keyword'],'user_id':__filter_discussion['user_id']},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    $("#discussions_show_count").html(Object.size(data.discussions));
                    //console.log(data['ratings']);
                    $('.question-post-ul').html('');
                    if(Object.size(data.discussions)>=__discussion_limit){
                        $('.question-post-ul').html(renderDiscussionsHtml(data['discussions'])+'<li id="show_more_discussions"><span onClick="loadMoreDiscussions()" class="show-more-question">Show more questions</span></li>');
                    }else if(Object.size(data.discussions) == 0){
                        $('.question-post-ul').html('No questions to display.');
                    }else{
                        $('.question-post-ul').html(renderDiscussionsHtml(data['discussions']));
                    }
                    __start_discussions = false;
                }
            }
        });

    });

    function renderRatingsHtml(ratings)
    { 
        $('#Show-more-reviews-two').html('Show more reviews').hide();
        var rendershtml  = '';
        var imgurl = '';
        var date_new = '';
        var day_obj = new Object();
        if(Object.keys(ratings).length > 0 )
        {
            $.each(ratings, function(ratingkey, rating )
            {
                if(rating.cc_user_image=='default.jpg'){
                    imgurl = __default_user_path+'default.jpg';
                }else{
                    imgurl = __user_path+rating.cc_user_image;
                }
                day_obj = relative_time_ax(rating.created_date);

                if(rating.cc_reviews != ''){
                    
                    rendershtml += '<li class="profilelist-childs"><div class="profile-list-photo">';
                    rendershtml += '<img src="'+imgurl+'" class="img-responsive olp-img-rounded svg-common profile-pic">';
                    rendershtml += '<span class="profile-name-text rating-alter-text">'+rating.cc_user_name+'</span>';
                    rendershtml += '<div class="star-ratings-sprite star-rating-vertical-top-super starr-vertical-top">';
                    rendershtml += '<span style="width:'+rating.cc_rating*20+'%" class="star-ratings-sprite-rating"></span>';
                    rendershtml += '</div><span class="sub-profile-text">';
                    rendershtml += day_obj.day+'</span></div>';
                    rendershtml += '<p class="profil-des">'+rating.cc_reviews+'</p>';
                    rendershtml += '</li>';
                }

            });
            if( Object.keys(ratings).length == __perPage)
            {
                $('#Show-more-reviews-two').css('display', 'block');                
            }
        }
        return rendershtml;
    }
    
    
    function getRatings()
    {
        $.ajax({
            url: __site_url+'material/get_rating_json',
            type: "POST",
            data:{"is_ajax":true,'c_id':__course_id,'offset':__offset_rating},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    if(__start_ratings == true)
                    {
                        $('.profile-list').html('');
                        $('.profile-list').html(renderRatingsHtml(data['ratings']));
                    }
                    else
                    {
                        $('.profile-list').append(renderRatingsHtml(data['ratings']));
                    }
                    __start_ratings = false;
                    __offset_rating++;
                }
            }
        });
    }

    function loadMoreRatings()
    {
        $('#Show-more-reviews-two').html('Loading...');
        getRatings();
    }
    
    function renderDiscussionsHtml(discussions)
    {   
        if(__start_discussions==true){
            $("#discussions_show_count").html(Object.size(discussions));
            __start_discussions = false;
        }
        $('#show_more_discussions span').html('Show more replies').hide();
        var rendershtml  = '';
        var imgurl = '';
        var date_new = '';
        delete_link = '';
        report_link = '';
        if(Object.keys(discussions).length > 0 )
        {
            $.each(discussions, function(discussionkey, discussion )
            {   //console.log(discussion);
                if(jQuery.isEmptyObject(discussion.children)){
                    //console.log(1);
                    rendershtml += renderDiscussionsWithoutChildHtml(discussion);
                }else{
                    rendershtml += renderDiscussionsWithChildHtml(discussion);
                    //console.log(renderDiscussionsWithChildHtml(discussion));
                    //console.log(0);
                }
            });
            if( Object.keys(discussions).length == __discussion_limit)
            {
                $('#show_more_discussions span').css('display', 'block'); 
            }else{
                //clear_filter();
            }
            
        }
        return rendershtml;
    }

    function renderDiscussionsWithoutChildHtml(discussion)
    {   
        //$('#Show-more-reviews-two').html('Show more reviews').hide();
        var rendershtml  = '';
        var imgurl = '';
        var date_new = '';
        var options = '';
        __offset_discussion_child[discussion.id] = 2;

        if(discussion.us_image=='default.jpg'){
            imgurl = __default_user_path+'default.jpg';
        }else{
            imgurl = __user_path+discussion.us_image;
        }
        options = render_options(discussion.user_id,discussion.id,discussion.report_stat,discussion.parent_id);

        rendershtml += '<li id="comment_'+discussion.id+'"><span class="question-profile-picwrap"><img class="question-post-pic" src="'+imgurl+'" alt="Profile pic"></span>';
        rendershtml += '<span class="question-from-user"><span class="user-question">'+discussion.comment+'</span><span class="replay-and-details-wrap">';
        rendershtml += '<span class="question-user-name">'+discussion.us_name+'</span><span class="question-posted-date">'+relative_time(discussion.created_date)+'</span><span class="question-posted-date">'+Object.size(discussion.children)+' replies</span></span>';
        rendershtml += '<div class="dropdown drop-ellips">';
        rendershtml += '<button class="btn btn-ellipse dropdown-toggle" type="button" data-toggle="dropdown"><i class="icon-ellipsis-vert"></i></button>';
        rendershtml += '<ul class="dropdown-menu dropdown-menue-ellips">'+options+'</ul>';
        rendershtml += '</div></span></li>';
            
        return rendershtml;
    }

    function refresh_delete(parent){
        $.ajax({
            url: __site_url+'material/get_discussions_json',
            type: "POST",
            data:{"is_ajax":true,'c_id':__course_id,'limit':__discussion_limit,'offset':'0','parent':parent},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    //console.log(data['discussions'].first());
                    $.each(data['discussions'], function(datakey, datas ){
                        render_refresh(datas,parent);
                    });
                    //render_refresh(data.discussions[0]);
                }
            }
        });
    }

    function render_refresh(comment,parent){
        __child_count[parent]--;
        var return_html = '';
        if(comment.children_count == 0){
            return_html = render_refresh_without_child(comment);
        }else{
            return_html = render_refresh_with_child(comment);
        }

        //console.log(return_html);
        $('#comment_'+parent).html(return_html);
    }

    function render_refresh_without_child(comment){

        var renderHtml = '';
        var imgurl     = '';

        if(comment.us_image=='default.jpg'){
            imgurl = __default_user_path+'default.jpg';
        }else{
            imgurl = __user_path+comment.us_image;
        }
        options = render_options(comment.user_id,comment.id,comment.report_stat,comment.parent_id);

        renderHtml += '<span class="question-profile-picwrap"><img class="question-post-pic" src="'+imgurl+'" alt="Profile pic"></span>';
        renderHtml += '<span class="question-from-user"><span class="user-question">'+comment.comment+'</span><span class="replay-and-details-wrap">';
        renderHtml += '<span class="question-user-name">'+comment.us_name+'</span><span class="question-posted-date">'+relative_time(comment.created_date)+'</span><span class="question-posted-date">'+Object.size(comment.children)+' replies</span></span>';
        renderHtml += '<div class="dropdown drop-ellips">';
        renderHtml += '<button class="btn btn-ellipse dropdown-toggle" type="button" data-toggle="dropdown"><i class="icon-ellipsis-vert"></i></button>';
        renderHtml += '<ul class="dropdown-menu dropdown-menue-ellips">'+options+'</ul>';
        renderHtml += '</div></span>';
        

        return renderHtml;
    }

    function render_refresh_with_child(comment){

        var renderHtml = '';
        var imgurl     = '';
        var temp       = '';
        var i          = 0;
        if(comment.us_image=='default.jpg'){
            imgurl = __default_user_path+'default.jpg';
        }else{
            imgurl = __user_path+comment.us_image;
        }
        options = render_options(comment.user_id,comment.id,comment.report_stat,comment.parent_id);

        renderHtml += '<span class="question-profile-picwrap">';
        renderHtml += '<img class="question-post-pic" src="'+imgurl+'" alt="Profile pic">';
        renderHtml += '</span><span class="question-from-user"><span class="question-subs-wrap"><span class="user-question"><p>';
        renderHtml += comment.comment+'</p></span><span class="replay-and-details-wrap"><span class="question-user-name">';
        renderHtml += comment.us_name+'</span><span class="question-posted-date">';
        renderHtml += relative_time(comment.created_date)+'</span></span><div class="dropdown drop-ellips"><button class="btn btn-ellipse dropdown-toggle" type="button" data-toggle="dropdown">';
        renderHtml += '<i class="icon-ellipsis-vert"></i></button><ul class="dropdown-menu dropdown-menue-ellips">';
        renderHtml += options+'</ul></span>';
        renderHtml += '</div></span><ul class="sub-question-post">';
        //console.log(comment.children);
        $.each(comment.children, function(discussionkey,childrens)
        {

            temp += renderDiscussionsChild(childrens,i,comment.children_count);
            i++;
        });
        //console.log(temp);
        renderHtml += temp;
        if(comment.children_count>__child_limit){
            renderHtml += '<li id="show_more_discussions'+comment.id+'"><span onClick="loadMoreDiscussionsChild(this)" data-internal-id="'+comment.id+'" class="show-more-question">Show more replies</span></li>';
        }
        renderHtml += '</ul></span></li>';

        return renderHtml;
    }

    function renderDiscussionsWithChildHtml(discussion)
    {  
        //$('#Show-more-reviews-two').html('Show more reviews').hide();
        var rendershtml  = '';
        var imgurl = '';
        var date_new = '';
        var i = 0;
        var options = '';
        var temp = '';
        __recieved_child[discussion.id] = Object.size(discussion.children);
        __child_count[discussion.id] = discussion.children_count;
        options = render_options(discussion.user_id,discussion.id,discussion.report_stat,discussion.parent_id);

        __offset_discussion_child[discussion.id] = 2;
        
        if(discussion.us_image=='default.jpg'){
            imgurl = __default_user_path+'default.jpg';
        }else{
            imgurl = __user_path+discussion.us_image;
        }
        if(__user_id == discussion.user_id)
        {
            delete_link = '<li><a href="#">Delete</a></li>';
            report_link = '<li><a href="#">Report</a></li>';
        }

        rendershtml += '<li id="comment_'+discussion.id+'"><span class="question-profile-picwrap">';
        rendershtml += '<img class="question-post-pic" src="'+imgurl+'" alt="Profile pic"></span>';
        rendershtml += '<span class="question-from-user"><span class="question-subs-wrap">';
        rendershtml += '<span class="user-question">'+discussion.comment+'</span>';
        rendershtml += '<span class="replay-and-details-wrap"><span class="question-user-name">'+discussion.us_name+'</span>';
        rendershtml += '<span class="question-posted-date">'+relative_time(discussion.created_date)+'</span></span>';
        rendershtml += '<div class="dropdown drop-ellips"><button class="btn btn-ellipse dropdown-toggle" type="button" data-toggle="dropdown"><i class="icon-ellipsis-vert"></i></button>';
        rendershtml += '<ul class="dropdown-menu dropdown-menue-ellips">'+options+'</ul>';
        rendershtml += '</div></span><ul class="sub-question-post">';

        $.each(discussion.children, function(discussionkey,childrens)
        {

            temp += renderDiscussionsChild(childrens,i,discussion.children_count);
            i++;
        });
        //console.log(temp);
        rendershtml += temp;
        if(discussion.children_count>__child_limit){
            rendershtml += '<li id="show_more_discussions'+discussion.id+'"><span onClick="loadMoreDiscussionsChild(this)" data-internal-id="'+discussion.id+'" class="show-more-question">Show more replies</span></li>';
        }
        rendershtml += '</ul></span></li>';

        //console.log(rendershtml);
    
        return rendershtml;
    }
    function renderDiscussionsChild(children,discussionkey,count)
    { 
        //$('#Show-more-reviews-two').html('Show more reviews').hide();
        var renderschildhtml  = '';
        var imgurl = '';
        var date_new = '';
        var options = '';
        var temp = 'Reply';

        //console.log(count);
        if(count>1){
            temp = 'Replies';
        }
        
        if(children.us_image=='default.jpg'){
            imgurl = __default_user_path+'default.jpg';
        }else{
            imgurl = __user_path+children.us_image;
        }
        options = render_options(children.user_id,children.id,children.report_stat,children.parent_id);

        renderschildhtml += '<li id="comment_'+children.id+'" class="clearfix subans-revl">';
        if(discussionkey==0){
            renderschildhtml += '<span class="sub-question-reply-label">'+count+' '+temp+'</span>';
        }
        renderschildhtml += '<span class="sub-quest-profile-picwrap">';
        renderschildhtml += '<img class="question-post-pic" src="'+imgurl+'" alt="Profile pic"></span>';
        renderschildhtml += '<span class="question-subs-wrap clearfix"><span class="sub-question-from-user">';
        renderschildhtml += '<span class="replay-and-details-wrap sub-name-margin-bottom"><span class="question-user-name">'+children.us_name+'</span>';
        renderschildhtml += '<span class="question-posted-date">'+relative_time(children.created_date)+'</span></span>';
        renderschildhtml += '<p>'+children.comment+'</p></span>';
        renderschildhtml += '<div class="dropdown drop-ellips"><button class="btn btn-ellipse dropdown-toggle" type="button" data-toggle="dropdown"><i class="icon-ellipsis-vert"></i></button>';
        renderschildhtml += '<ul class="dropdown-menu dropdown-menue-ellips">'+options+'</ul>';
        renderschildhtml += '</div></span></li>';
        
        //console.log(renderschildhtml);
        return renderschildhtml;
    }
    
    
    function getDiscussions()
    {
        $.ajax({
            url: __site_url+'material/get_discussions_json',
            type: "POST",
            data:{"is_ajax":true,'c_id':__course_id,'offset':__offset_discussion,'keyword':__filter_discussion['keyword'],'user_id':__filter_discussion['user_id']},
            success: function(response) {
                var data = $.parseJSON(response);
                //console.log(data);
                $("#discussions_show_count").html(parseInt($("#discussions_show_count").html())+Object.size(data.discussions));
                if(data['error']==false)
                {
                    if(__start_discussions == true)
                    {
                        $('.question-post-ul').html('');
                        $('.question-post-ul').html(renderDiscussionsHtml(data['discussions']));
                    }
                    else
                    {
                        $('#show_more_discussions').before(renderDiscussionsHtml(data['discussions']));
                    }
                    __start_discussions = false;
                    __offset_discussion++;
                }
            }
        });
    }

    function renderDiscussionsChildUnit(children)
    {   //console.log(children);
        $(__offset_discussion_child[0]+' span').html('Show more replies').hide();
        //$('#Show-more-reviews-two').html('Show more reviews').hide();
        var renderschildhtml  = '';
        var imgurl = '';
        var date_new = '';
        var options = '';

        $.each(children, function(childrenskey,childrens)
        {
            
            if(childrens.us_image=='default.jpg'){
                imgurl = __default_user_path+'default.jpg';
            }else{
                imgurl = __user_path+childrens.us_image;
            }
            
            options = render_options(childrens.user_id,childrens.id,childrens.report_stat,childrens.parent_id);
            //console.log(options);
            renderschildhtml += '<li id="comment_'+childrens.id+'" class="clearfix subans-revl">';
            renderschildhtml += '<span class="sub-quest-profile-picwrap">';
            renderschildhtml += '<img class="question-post-pic" src="'+imgurl+'" alt="Profile pic"></span>';
            renderschildhtml += '<span class="question-subs-wrap clearfix"><span class="sub-question-from-user">';
            renderschildhtml += '<span class="replay-and-details-wrap sub-name-margin-bottom"><span class="question-user-name">'+childrens.us_name+'</span>';
            renderschildhtml += '<span class="question-posted-date">'+relative_time(childrens.created_date)+'</span></span>';
            renderschildhtml += '<p>'+childrens.comment+'</p></span>';
            renderschildhtml += '<div class="dropdown drop-ellips"><button class="btn btn-ellipse dropdown-toggle" type="button" data-toggle="dropdown"><i class="icon-ellipsis-vert"></i></button>';
            renderschildhtml += '<ul class="dropdown-menu dropdown-menue-ellips">'+options+'</ul>';
            renderschildhtml += '</div></span></li>';
            
        });
        console.log(__recieved_child[children[0]['parent_id']]);console.log(__child_count[children[0]['parent_id']]);
        if( __recieved_child[children[0]['parent_id']] < __child_count[children[0]['parent_id']])
        {
            $(__offset_discussion_child[0]+' span').css('display', 'block'); 
        }
        return renderschildhtml;
    }

    function render_options(user_id,comment_id,stat,parent_id){
        //console.log(user_id);
        var renderHtml = '';
        if(user_id == __user_id){
            renderHtml = '<li><a href="javascript:void(0)" onClick="delete_comment('+comment_id+','+parent_id+')">Delete</a></li>';
        }else{
            if(stat==0){
            renderHtml = '<li><a href="javascript:void(0)" id="report_btn_'+comment_id+'" onClick="report_comment('+comment_id+')">Report</a></li>';
            }else{
                renderHtml = '<li><a href="javascript:void(0)" id="report_btn_'+comment_id+'">Reported</a></li>';
            }
        }
        return renderHtml;
    }

    function getDiscussionsChildUnit(parent)
    {
        $.ajax({
            url: __site_url+'material/get_discussions_child_json',
            type: "POST",
            data:{"is_ajax":true,'c_id':__course_id,'p_id':parent,'offset':__offset_discussion_child[parent]},
            success: function(response) {
                var data = $.parseJSON(response);
                __recieved_child[parent] += data['discussions_child'].length;
                //console.log(response);
                if(data['error']==false)
                {
                    if(__start_discussions == true)
                    {
                        //$('.question-post-ul').html('');
                        //$('.question-post-ul').html(renderDiscussionsChildUnit(data['discussions_child']));
                    }
                    else
                    {
                        //console.log(renderDiscussionsChildUnit(data['discussions_child']));
                        $(__offset_discussion_child[0]).before(renderDiscussionsChildUnit(data['discussions_child']));
                    }
                    __start_discussions = false;
                    __offset_discussion_child[parent]++;
                }
            }
        });
    }

    function loadMoreDiscussions()
    {
        //console.log('Loadmore');
        $('#show_more_discussions span').html('Loading...');
        getDiscussions();
    }

    function loadMoreDiscussionsChild(e)
    {
        __offset_discussion_child[0] = '#show_more_discussions'+$(e).attr('data-internal-id');
        //console.log(__offset_discussion_child[0]);
        $(__offset_discussion_child[0]+' span').html('Loading...');
        getDiscussionsChildUnit($(e).attr('data-internal-id'));
    }

    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };

    function clear_filter(){
        __filter_discussion['keyword'] = '';
        __filter_discussion['user_id'] = '';
    }

    function delete_comment(comment_id,parent_id){
        var count = 0;
        if(parent_id != 0){
            __offset_discussion_child[parent_id] = parseInt(__child_limit);
            __recieved_child[parent_id] = parseInt(__child_limit);
        }
        $.ajax({
            url: __site_url+'material/delete_comment_ajax',
            type: "POST",
            data:{"is_ajax":true,'comment_id':comment_id},
            success: function(response) {
                var data = $.parseJSON(response);
                //console.log(get_curriciulum(data));
                if(data.error==false){
                    if(parent_id == 0){
                        count = $('#discussions_total_count').html();
                        count = parseInt(count);
                        count--;
                        $('#discussions_total_count').html(count)
                    }
                    $('#comment_'+comment_id).remove();
                    toastr["success"](data.message);
                }else{
                    toastr["error"](data.message);
                }
                if(parent_id != 0){
                    refresh_delete(parent_id);
                }
            }
        });
    }

    function report_comment(comment_id){
        $('#report_modal').attr('data-internal-id',comment_id);
        $('#report_modal').modal('show');
        /*$.ajax({
            url: __site_url+'material/delete_comment_ajax',
            type: "POST",
            data:{"is_ajax":true,'comment_id':comment_id},
            success: function(response) {
                var data = $.parseJSON(response);
                console.log(get_curriciulum(data));
                if(data.error==false){
                    $('#comment_'+comment_id).remove();
                    toastr["success"](data.message);
                }else{
                    toastr["error"](data.message);
                }
            }
        });*/
    }
    function report_comment_confirm(){
        var comment_id = $('#report_modal').attr('data-internal-id');
        var reason     = $('#report_reason').val();
        if(reason == ''){
            //toastr["error"]('Please provide your reason.');
            $('#report_reason').css('border','1px solid red');
            $('#report_reason').focus();
        }else{
            $('#report_reason').val('')
            $('#report_reason').css('border','1px');
            $.ajax({
                url: __site_url+'material/report_comment_ajax',
                type: "POST",
                data:{"is_ajax":true,'comment_id':comment_id,'reason':reason},
                success: function(response) {
                    var data = $.parseJSON(response);
                    //console.log(get_curriciulum(data));
                    if(data.error==false){
                        $('#report_btn_'+comment_id).attr('onclick','').unbind('click');
                        $('#report_btn_'+comment_id).html('Reported');
                        $('#report_modal').modal('toggle');
                        toastr["success"](data.message);
                    }else{
                        $('#report_modal').modal('toggle');
                        toastr["error"](data.message);
                    }
                }
            });
        }
    }

    $('.cancel_reporting').click(function(){
        $('#report_reason').val('')
        $('#report_reason').css('border','1px');
        $('#report_modal').modal('toggle');
    });

    function full_curriculum(e){
        $('#loadMore').html('Loading...');
        $.ajax({
            url: __site_url+'material/get_full_curriculum_json',
            type: "POST",
            data:{"is_ajax":true,'c_id':__course_id},
            success: function(response) {
                var data = $.parseJSON(response);
                //console.log(data);
                $('#curriculum_div').html('<h3 class="formpage-heading">Curriculum</h3>'+get_curriciulum(data));
            }
        });
    }

    function get_curriciulum(curriculums){
        var rendersHtml = '';
        var i = 1;
        $.each(curriculums.sections, function(curriculum_key,curriculum)
        {   if(curriculum.lectures.length != 0){
                rendersHtml += '<ul class="solution-list solution-list-for-curriculam">';
                rendersHtml += '<li class="solution-child-head"><p class="solution-para"><span class="solution-section">Section ';
                rendersHtml += i+':</span><span class="solution-intro">';
                rendersHtml += curriculum.s_name+'</span></p></li>';
                rendersHtml += get_curriciulum_lectures(curriculum.lectures);
                rendersHtml += '</ul>';
                i++;
            }

        });

        return rendersHtml;
    }
    function get_curriciulum_lectures(lectures) {
        var rendersHtml = '';
        var last_item = 'no-bottom-border';
        $.each(lectures, function(lecture_key,lecture)
        {   if(Object.size(lectures) == lecture_key+1){
                rendersHtml += '<li onclick="location.href=\''+__site_url+'/materials/course/'+lecture.cl_course_id+'#'+lecture.id+'\'" style="cursor:pointer;" class="soulution-childs '+last_item+'"><span class="solution-child-l-r-margin solution-child-table-cell">'+get_lecture_type(lecture.cl_lecture_type)+'</span>';
            }else{
                rendersHtml += '<li onclick="location.href=\''+__site_url+'/materials/course/'+lecture.cl_course_id+'#'+lecture.id+'\'" style="cursor:pointer;" class="soulution-childs"><span class="solution-child-l-r-margin solution-child-table-cell">'+get_lecture_type(lecture.cl_lecture_type)+'</span>';
            }
            switch(parseInt(lecture.cl_lecture_type)){
                case 1: __lecture_count++;
                break;
                case 2: __lecture_count++;
                break;
                case 3: __quiz_count++;
                break;
                case 4: __lecture_count++;
                break;
                case 5: __lecture_count++;
                break;
                case 6: __lecture_count++;
                break;
                case 7: __lecture_count++;
                break;
                case 8: __quiz_count++;
                break;
                case 11: __lecture_count++;
                break;
                case 12: __lecture_count++;
                break;
            }
            rendersHtml += '<span class="solution-child-l-r-margin solution-child-table-cell min-width-list">';
            switch(parseInt(lecture.cl_lecture_type)){
                
                case 1: rendersHtml += 'Lecture '+__lecture_count;
                break;
                case 2: rendersHtml += 'Lecture '+__lecture_count;
                break;
                case 3: rendersHtml += 'Quiz '+__quiz_count;
                break;
                case 4: rendersHtml += 'Lecture '+__lecture_count;
                break;
                case 5: rendersHtml += 'Lecture '+__lecture_count;
                break;
                case 6: rendersHtml += 'Lecture '+__lecture_count;
                break;
                case 7: rendersHtml += 'Lecture '+__lecture_count;
                break;
                case 8: rendersHtml += 'Quiz '+__quiz_count;
                break;
                case 11: rendersHtml += 'Lecture '+__lecture_count;
                break;
                case 12: rendersHtml += 'Lecture '+__lecture_count;
                break;
            }
            rendersHtml += '</span>';
            rendersHtml += '<span class="solution-child-l-r-margin solution-child-table-cell lecture-des">'+lecture.cl_lecture_name;
            rendersHtml += '</span><span class="solution-child-l-r-margin pull-right solution-time-align time-hide ">';
            rendersHtml += get_lecture_attr(parseInt(lecture.cl_lecture_type),lecture.unique);
            rendersHtml += '</span></li>';
        });

        return rendersHtml;
    }

    function get_lecture_attr(type,lval){
        var renderHtml = '';
        switch(parseInt(type)){
            case 1:
                renderHtml = lval;
            break;
            case 12:
                renderHtml = lval;
            break;
            case 4:
                renderHtml = '';
            break;
            case 9:
                renderHtml = '';
            break;
            case 2:
                if(parseInt(lval)>1){
                    renderHtml = lval+' Pages';
                }else{
                    renderHtml = lval+' Page';
                }
            break;
            case 5:
                renderHtml = '';
            break;
            case 6:
                renderHtml = '';
            break;
            case 7:
                renderHtml = lval;
            break;
            case 8:
                if(parseInt(lval)>1){
                    renderHtml = lval+' Pages';
                }else{
                    renderHtml = lval+' Page';
                }
            break;
            case 3:
                if(parseInt(lval)>1){
                    renderHtml = lval+' Questions';
                }else{
                    renderHtml = lval+' Question';
                }
            break;
        }
        return renderHtml;
    }

    function get_lecture_type(type){
        var renderHtml = '';
        switch(parseInt(type)){
            case 1:
                renderHtml = '<svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>';
            break;
            case 4:
                renderHtml = '<svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>';
            break;
            case 11:
                renderHtml = '<svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>';
            break;
            case 12:
                renderHtml = '<svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>';
            break;
            case 2:
                renderHtml = '<svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>';
            break;
            case 5:
                renderHtml = '<svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>';
            break;
            case 6:
                renderHtml = '<svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>';
            break;
            case 7:
                renderHtml = '<svg version="1.1" class="svg-common" .333="" x="0px" y="0px" width="19px" height="21px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve"><g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M20.5,4.5h-7.6l3.3-3.3l-0.7-0.7l-4,4l-4-4L6.8,1.2l3.3,3.3H2.5c-1.1,0-2,0.9-2,2v12c0,1.1,0.9,2,2,2h18c1.1,0,2-0.9,2-2v-12C22.5,5.4,21.6,4.5,20.5,4.5z M20.5,18.5h-18v-12h18V18.5z M8.5,8.5v8l7-4L8.5,8.5z"></path></g></svg>';
            break;
            case 8:
                renderHtml = '<svg version="1.1" x="0px" y="0px" class="svg-common" width="19px" height="17px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve"><g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g><g><path fill="#808080" d="M19.7,0H3.3C2.1,0,1,1.1,1,2.3v16.3C1,20,2.1,21,3.3,21h16.3c1.3,0,2.3-1,2.3-2.3V2.3C22,1.1,21,0,19.7,0zM8,16.3H5.7V8.2H8V16.3z M12.7,16.3h-2.3V4.7h2.3V16.3z M17.3,16.3H15v-4.7h2.3V16.3z"></path><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></svg>';
            break;
            case 3:
                renderHtml = '<svg version="1.1" x="0px" y="0px" class="svg-common" width="19px" height="17px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve"><g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g><g><path fill="#808080" d="M19.7,0H3.3C2.1,0,1,1.1,1,2.3v16.3C1,20,2.1,21,3.3,21h16.3c1.3,0,2.3-1,2.3-2.3V2.3C22,1.1,21,0,19.7,0zM8,16.3H5.7V8.2H8V16.3z M12.7,16.3h-2.3V4.7h2.3V16.3z M17.3,16.3H15v-4.7h2.3V16.3z"></path><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></svg>';
            break;
            default:
                renderHtml = '<svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>';
            break;
        }

        return renderHtml;
    }
    function plot_rank_chart(){

            var ranking = [];
            var rank_sub= [];
            var max_rank =[];
            var i = 0;
            var rank_flag = false;
            //console.log(__rank_object);
            __rank_object      = $.parseJSON(__rank_object);
            if(Object.size(__rank_object) == 0){
                __main_flag++;
                $('#db_rank').hide();
            }
            $.each(__rank_object, function(rankkey,rank)
            {
                if(rank['my_rank']>0){
                    rank_flag = true;
                    ranking[i] = rank['my_rank'];
                    rank_sub[i]= rank['lecture']['cl_lecture_name'];
                    max_rank[i]= rank['attempts'];
                    i++;
                }
            });

        Highcharts.chart('chartgreen', {
            chart: {
                type: 'line'
            },
            credits: {
                    enabled: false
            },
            exporting: {
               enabled: false 
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: rank_sub
            },
            yAxis: {
                allowDecimals: false,
                min: 1,
                max: Math.max.apply(Math,max_rank),
                reversed: true,          
                title: {
                    text: ''
                },
                labels: {
                    formatter: function () {
                        return this.value + '';
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true,
                useHTML: true      
            },
            legend: {
                enabled: false
            },      
            plotOptions: {
                line: {
                    marker: {
                        radius: 5,
                        lineColor: '#00c853',
                        fillColor:'#ffffff',
                        lineWidth: 2,
                        states: {
                            hover: {
                                fillColor: '#ffffff',
                                lineColor: '#00c853',
                                lineWidth: 2
                            }
                        }
                    }               
                },
                series: {
                    states: {
                        hover: {
                            enabled: true,
                            halo: {
                            size: 20
                            }
                        }
                    }
                }
            },
            series: [{
                name: ' ',
                lineColor: '#00c853',
                marker: {
                    symbol: 'circle'
                },data: ranking
            }]      
        });
        if(Object.size(__rank_object) != 0 && rank_flag == false){
            __main_flag++;
            $('#db_rank').hide();
        }
    }
__main_attended_flag = false;
    function plot_category_p_chart(){
        __topic_wise      = $.parseJSON(__topic_wise);
        var chart_div = '';
        var classes = ['violet','skyblue','maroon'];
        var colors  = [];
        colors[0] = {'lineColor':'#7753e5','fillColor':'#ffffff'};
        colors[1] = {'lineColor':'#00a1e5','fillColor':'#ffffff'};
        colors[2] = {'lineColor':'#cc53e5','fillColor':'#ffffff'};
        var i = 0,j = 0;
        var xaxis = [];
        var yaxis = [];
        var percentage = 0;
        var bar_chart = '';
        var average_percentage = 0;
        var flag = true;
        $.each(__topic_wise, function(topickey,topic){
            //console.log(topic);
            var attended_flag = false;
            chart_div = render_chart_place(classes[i],topic['id'],topic['qc_category_name']);
            //console.log(chart_div);
            average_percentage = 0;
            $.each(topic['assessment'], function(askey,assessment){
                flag = false;
                if(assessment['attended'] == 1){
                    attended_flag = true;
                    __main_attended_flag
                    if(assessment['total_mark']>=0){
                        xaxis[j] = assessment['assessment_name'];
                        if(assessment['total_mark']==0){
                            percentage = 0;
                        }else{
                            if(assessment['scored_mark']<=0){
                                percentage = 0;
                            }else{
                                assessment['total_mark']  = parseFloat(assessment['total_mark']);
                                assessment['scored_mark'] = parseFloat(assessment['scored_mark']);
                                percentage = (assessment['scored_mark']/assessment['total_mark'])*100;
                                percentage = percentage.toFixed(2);
                                percentage = parseFloat(percentage);
                            }
                        }
                        yaxis[j] = percentage;
                        j++;
                        average_percentage += percentage;
                    }
                }
            });
            average_percentage = average_percentage/j;
            j = 0;
            bar_chart += render_bar_chart(average_percentage,topic['qc_category_name']);
            average_percentage = 0;
            if(yaxis.length>0){
                i++;
                $('#assessments').before(chart_div);
                if(attended_flag == true){
                    plot_chart_single(xaxis,yaxis,topic['id'],colors[i-1]);
                }else{

                }
            }
            xaxis = [];
            yaxis = [];
            if(i>=classes.length){
                i=0;
            }
        });
        if(__main_attended_flag == false){
            $('#topic_wise_progress').remove();
        }
        if(bar_chart == ''){
            $('#db_bar_chart').hide();
            __main_flag++;
        }else{
            $('#topic_average').html(bar_chart);
        }
        if(flag){
            __main_flag++;
            $('#db_topic').hide();
        }
    }
    function render_chart_place(color,id,name){
        var renderHtml = '';
        renderHtml += '<div class="progress-graph bar-wrap-margin-top">';
        renderHtml += '<div class="container container-res-chnger-frorm-page">';
        renderHtml += '<div class="changed-container-for-forum">';
        renderHtml += '<div class="ling-graph-wrap">';
        renderHtml += '<div class="parent-bar-details parent-bar-violet-graph">';
        renderHtml += '<span class="bar-tunnel bar-tunnel-inside-violet bar-'+color+'"></span>';
        renderHtml += '<span class="bar-text bar-text-'+color+'">'+name+'</span>';
        renderHtml += '</div><div id="chart'+id+'" class="chart'+color+'"></div>';
        renderHtml += '</div></div></div></div>';

        return renderHtml;
    }    

    function render_bar_chart(percentage,name){
        //console.log(percentage);
        var renderHtml = '';
        if(percentage>0&&percentage!=''){
            renderHtml = '<div class="bar-wrap"><div class="leftprogressTexr">'+name+'</div><div class="progressBar-wrap"><span class="prgressBarchild bar bar-'+percentage_class(Math.round(percentage))+' cf" data-percent="'+Math.round(percentage)+'%" style="width: '+Math.round(percentage)+'%;"><span class="count">'+Math.round(percentage)+'%</span></span></div></div>';
        }else{
            renderHtml = '';
        }
        return renderHtml;
    }
    function percentage_class(percentage){
        if(percentage>90){
            return 'green';
        }
        if(percentage>80&&percentage<=90){
            return 'blue';
        }
        if(percentage>=60&&percentage<=80){
            return 'violet';
        }
        if(percentage<60){
            return 'peach';
        }
    }
    function plot_chart_single(xaxis,yaxis,id,color){

        Highcharts.chart('chart'+id, {
            chart: {
                type: 'line'        

            },
             credits: {
                enabled: false
            },

            exporting: {
               enabled: false 
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: xaxis
            },
            yAxis: {
                min: 0,
                max: 100,
                reversed: false,             
                title: {
                    text: ''
                },
                labels: {
                    formatter: function () {
                        return this.value + ' %';
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true,
                 useHTML: true      
            },
            
            legend: {
                enabled: false
            },      
            plotOptions: {
                line: {
                    marker: {
                        radius: 5,
                        lineColor: color['lineColor'],
                        fillColor:color['fillColor'],
                        lineWidth: 2,
                        states: {
                                hover: {
                                    fillColor: color['fillColor'],
                                    lineColor: color['lineColor'],
                                    lineWidth: 2
                                }
                        }
                    }               
                },
                series: {
                    states: {
                        hover: {
                            enabled: true,
                                halo: {
                                    size: 20
                                }
                            }
                        }
                    }
                },
                series: [{
                    name: ' ',
                    lineColor: color['lineColor'],
                    marker: {
                        symbol: 'circle'
                    },
                    data: yaxis

                }]
                    
        });
    }

    function get_assessments(){
        $('#assessment_load').html('Loading...');
        var limit = 5;
        $.ajax({
            url: __site_url+'material/get_ajax_assesments',
            type: "POST",
            data:{"is_ajax":true,'course_id':__course_id,'offset':__assessment_offset,'limit':limit},
            success: function(response) {
                $('#assessment_load').parent().hide();
                var data = $.parseJSON(response);
                render_assesments(data['assessments'])
                __assessment_offset ++;
                if(data['assessments'].length == limit){
                    $('#assessment_load').html('Load More');
                    $('#assessment_load').parent().show();
                }
            }
        });
    }

    function render_assesments(assessments){
        var renderHtml = '';
        $.each(assessments, function(assesmentkey,assessment){
            if(assessment['attempt']){
                renderHtml += render_assesment_attended(assessment);
            }else{
                renderHtml += render_assesment_unattended(assessment);
            }
        });
        $('#assessments_ul').append(renderHtml);
        if($("#assessments_ul").children().length == 0){
            __main_flag++;
            $('#db_assessments').hide();
        }
    }
    function render_assesment_unattended(assessment){
        var rendersHtml = '';
        rendersHtml += '<li class="discussion-forum-white-lists  mt10 clearfix">';
        rendersHtml += '<span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index result-title-wrap-alter">';
        rendersHtml += '<span class="serail-no">'+__assessment_count+'</span>';
        rendersHtml += '<span class="forum-des result-des">'+assessment['name']+'</span>';
        rendersHtml += '</span><span class="discussion-right-wrap">';
        rendersHtml += '<span class="not-attended">Not attended yet</span>';
        rendersHtml += '<a href="'+__site_url+'materials/course/'+__course_id+'#'+assessment['lecture_id']+'" class="attend-btn">Attend now</a>';
        rendersHtml += '</span></li>';
        __assessment_count++;
        return rendersHtml;
    }
    function render_assesment_attended(assessment){
        var rendersHtml = '';
        var mark_scored = assessment['attempt']['marks_obtained']==null?0:assessment['attempt']['marks_obtained'];
        rendersHtml += '<li class="discussion-forum-white-lists  mt10"><span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index result-title-wrap-alter">';
        rendersHtml += '<span class="serail-no">'+__assessment_count+'</span>';
        rendersHtml += '<span class="forum-des result-des">'+assessment['name']+'</span>';
        rendersHtml += '</span><span class="topic-xs result-mid-box">';
        rendersHtml += '<span class="topic-form-text quarter-result date-time-hide"><strong>'+assessment['attempt']['attented_date']+'</strong> <br>Date Attended</span>';
        rendersHtml += '<span class="topic-form-text quarter-result date-time-hide"><strong>'+assessment['attempt']['time_taken']+' m</strong> <br>Time Taken</span>';
        rendersHtml += '<span class="topic-form-text quarter-result"><strong>'+mark_scored+'</strong><br>Marks Scored</span>';
        rendersHtml += '<span class="topic-form-text quarter-result"><strong>'+assessment['attempt']['rank']+'</strong><br>Your Rank</span>';
        rendersHtml += '</span><span class="last-post-forum-text last-col-result t-dash-details">';
        rendersHtml += '<span class="by-name"><a href="'+__site_url+'material/assesment_report_item/'+assessment['attempt']['attempt_id']+'" class="name-orange dash-result-link">View Details</a></span>';
        rendersHtml += '</span></li>';
        __assessment_count++;
        return rendersHtml;
    }

    function relative_time_ax(date_str){
        var date_time = new Object();
        var d = new Date(date_str);
        var month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        var date = d.getDate() + " " + month[d.getMonth()] + " " + d.getFullYear();
        var time = d.toLocaleTimeString().toLowerCase();
        date_time.day = date;
        date_time.time = time;
        return date_time;
    
    };

    function relative_time(date_str) {
        if (!date_str) {return;}
        date_str = $.trim(date_str);
        date_str = date_str.replace(/\.\d\d\d+/,""); // remove the milliseconds
        date_str = date_str.replace(/-/,"/").replace(/-/,"/"); //substitute - with /
        date_str = date_str.replace(/T/," ").replace(/Z/," UTC"); //remove T and substitute Z with UTC
        date_str = date_str.replace(/([\+\-]\d\d)\:?(\d\d)/," $1$2"); // +08:00 -> +0800
        var parsed_date = new Date(date_str);
        var relative_to = (arguments.length > 1) ? arguments[1] : new Date(); //defines relative to what ..default is now
        var delta = parseInt((relative_to.getTime()-parsed_date)/1000);
        delta=(delta<2)?2:delta;
        var r = '';
        if (delta < 60) {
        r = delta + 'few seconds ago';
        } else if(delta < 120) {
        r = 'one minute ago';
        } else if(delta < (45*60)) {
        r = (parseInt(delta / 60, 10)).toString() + ' minutes ago';
        } else if(delta < (2*60*60)) {
        r = 'an hour ago';
        } else if(delta < (24*60*60)) {
        r = '' + (parseInt(delta / 3600, 10)).toString() + ' hours ago';
        } else if(delta < (48*60*60)) {
        r = 'one day ago';
        } else {
        r = (parseInt(delta / 86400, 10)).toString() + ' days ago';
        }
        return r;
    };