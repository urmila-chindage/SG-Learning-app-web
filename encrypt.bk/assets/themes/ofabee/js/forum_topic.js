$(document).ready(function () {
    __forum_topics = $.parseJSON(__forum_topics);
    if(Object.keys(__forum_topics).length > 0){
        pagination(__num_pages);
        forum_header = get_header();
        forum_body = render_forum_object(__forum_topics);
        $('.discussion-forum-parent').html(forum_header+forum_body);
    }else{
        $('.dropdown-full-width').hide();
        $('#term').prop('disabled', true);
        $('.discussion-forum-parent').html(renderEmptyObject());
    }
});

function filter_post(filter_type){
    var renderHtml = '';
    switch(filter_type){
        case 0:
            $('#filter_label').html('Recent Posts');
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(3)">Date Created</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(1)">Most Viewed</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(2)">Most Replied</a></li>';
            $('.dropdown-values').html(renderHtml);
            __listing_type = filter_type;
            paginate(1);
            pagination(__num_pages_search);
        break;

        case 1:
            $('#filter_label').html('Most Viewed');
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(3)">Date Created</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(0)">Recent Posts</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(2)">Most Replied</a></li>';
            $('.dropdown-values').html(renderHtml);
            __listing_type = filter_type;
            paginate(1);
            pagination(__num_pages_search);
        break;

        case 2:
            $('#filter_label').html('Most Replied');
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(3)">Date Created</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(0)">Recent Posts</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(1)">Most Viewed</a></li>';
            $('.dropdown-values').html(renderHtml);
            __listing_type = filter_type;
            paginate(1);
            pagination(__num_pages_search);
        break;

        case 3:
            $('#filter_label').html('Date Created');
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(0)">Recent Posts</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(1)">Most Viewed</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(2)">Most Replied</a></li>';
            $('.dropdown-values').html(renderHtml);
            __listing_type = filter_type;
            paginate(1);
            pagination(__num_pages_search);
        break;

        default:
            $('#filter_label').html('Date Created');
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(0)">Recent Posts</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(1)">Most Viewed</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="filter_post(2)">Most Replied</a></li>';
            $('.dropdown-values').html(renderHtml);
            __listing_type = 3;
            paginate(1);
            pagination(__num_pages_search);
        break;
    }
}

function renderEmptyObject(){
    var renderHtml = '<div class="empty-notifications">';
    renderHtml += '<img src="'+__themes_url+'/images/No_Notification_illustration.svg" width="100" height="100">';
    renderHtml += '<span class="no-discussion no-content-text"><span>Oops! </span>No topics to show.</span></div>';

    return renderHtml;
}

$('#term').keyup(function(){
    clearTimeout(__initForumSearchBuffer);
    __initForumSearch();
});

var __initForumSearchBuffer;
function __initForumSearch()
{
    __initForumSearchBuffer = setTimeout(function(){ 
        __keyword = $('#term').val();
        paginate(1);
        setTimeout(function(){
            pagination(__num_pages_search);
        },300);
    }, 600);    
}



function render_forum_object(topic_obj){
    var renderHtml = '';
    $.each(topic_obj, function(topic_key, topic )
    {
        if(topic['total_topics'] == null){
            topic['total_topics'] = 0;
        }
        if(topic['total_comments'] == null){
            topic['total_comments'] = 0;
        }
        renderHtml += '<li class="discussion-forum-white-lists">';
        renderHtml += '<a href="'+__site_url+'forum/'+topic['permalink']+'" class="discussion-link">';
        renderHtml += '<img src="'+__themes_url+'/img/forum-ico.svg" class="forum-ion-res">';
        renderHtml += '<span class="forum-title-wrap">';
        renderHtml += '<span class="forum-titile">'+topic['topic_name']+'</span>';
        renderHtml += '<span class="forum-des">by <span class="usr-name-orange">'+topic['user_id']+'</span> <span class="discussion-list-date">'+topic['topic_created']+'</span></span>';
        renderHtml += '</span><span class="topic-xs">';
        renderHtml += '<span class="topic-form-text">'+topic['total_comments']+'<span class="hidden-lg hidden-md hidden-sm topic-post-sm">Topic</span></span>';
        renderHtml += '<span class="topic-form-text">'+topic['views']+'<span class="hidden-lg hidden-md hidden-sm topic-post-sm">Posts</span></span>';
        renderHtml += '</span><span class="last-post-forum-text">';
        if(topic['comments_user_id'] != ''){
            renderHtml += '<span class="by-name">by <span class="name-orange">'+topic['comments_user_id']+'</span></span>';
            renderHtml += '<span class="forum-date-time">'+topic['comment_created']+'</span>';
        }else{
            renderHtml += '<span class="by-name"><span class="name-orange"></span></span>';
            renderHtml += '<span class="forum-date-time"></span>';
        }
        renderHtml += '</span></a></li>';
        
    });
    return renderHtml;
}

function  get_header() {
    var renderHtml = '<li class="discussion-header">';
    renderHtml += '<span class="discussion-head-text">Topic</span>';
    renderHtml += '<span class="topic-head-text">Replies</span>';
    renderHtml += '<span class="topic-head-text">Views</span>';
    renderHtml += '<span class="last-post-head-text">Last post</span>';
    renderHtml += '</li>';

    return renderHtml;
}

function pagination(links){
    var renderHtml = '';
    var topic_html = '';
    if(__total_forums == 1){
        topic_html = 'topic';
    }else{
        topic_html = 'topics';
    }

    if(links>1){
        renderHtml = '<span class="pagination-prev pag-next first-link"><a href="javascript:void(0);" onclick="paginate(1)">First</a></span>';
        renderHtml += '<span class="pagination-prev pag-next previous-link"><a href="javascript:void(0);" onclick="paginate(1)">Previous</a></span>';
        renderHtml += '<span class="pagination-wraper">';
        renderHtml += '<ul class="pagination-black pagination-ul">';
        renderHtml += setPagination(1,links);
        renderHtml += '</ul></span></span>';
        renderHtml += '<span class="pagination-next-last">';
        renderHtml += '<span class="pagination-prev pag-next next-link"><a href="javascript:void(0);" onclick="paginate('+2+')">Next</a></span>';
        renderHtml += '<span class="pagination-prev pag-prev last-link"><a href="javascript:void(0);" onclick="paginate('+links+')">Last</a></span></span>';
        renderHtml += '<span class="forum-pagination-page" id="footer_page_details">'+__total_forums+' '+topic_html+' | page <strong>'+1+'</strong> of <strong>'+links+'</strong></span>';

        $('.links-pagination').html(renderHtml);
        $('.first-link').hide();
        $('.previous-link').hide();
        $('.forum-and-page').html(__total_forums+' '+topic_html+' | page <strong>1</strong> of <strong>'+links+'</strong>');
    }else{
        renderHtml += '<span class="forum-pagination-page" id="footer_page_details">'+__total_forums+' '+topic_html+' | page <strong>'+1+'</strong> of <strong>'+links+'</strong></span>';
        $('.links-pagination').html(renderHtml);
        $('.forum-and-page').html(__total_forums+' '+topic_html+' | page <strong>1</strong> of <strong>'+links+'</strong>');
    }
}

function paginate(index){
    var topic_text = '';
    if(__total_forums == 1){
        topic_text = 'topic';
    }else{
        topic_text = 'topics';
    }
    $.ajax({
        url: __site_url+'forum/get_ajax_topic',
        type: "POST",
        data:{"is_ajax":true,'forum_id':__forum_id,'offset':index,'limit':__limit,'listing_type':__listing_type,'keyword':__keyword},
        success: function(response) {
            $('.first-link').show();
            $('.previous-link').show();
            $('.next-link').show();
            $('.last-link').show();
            var data = $.parseJSON(response);
            //console.log(data['topics']);
            __num_pages_search = data['pages'];
            __total_forums     = data['total_topics'];
            forum_header = get_header();
            forum_body = render_forum_object(data['topics']);
            if(__total_forums<1){
                $('.discussion-forum-parent').html(renderEmptyObject());
            }else{
                $('.discussion-forum-parent').html(forum_header+forum_body);
            }
            $('.pagination-ul').html(setPagination(index, __num_pages_search));
            if(index == 1){
                $('.first-link').hide();
                $('.previous-link').hide();
            }else if(index == __num_pages){
                $('.next-link').hide();
                $('.last-link').hide();
            }
            $('.previous-link a').attr('onclick',"paginate("+(parseInt(index)-1)+")");
            $('.next-link a').attr('onclick',"paginate("+(parseInt(index)+1)+")");
            $('.forum-and-page').html(__total_forums+' '+topic_text+' | page <strong>'+index+'</strong> of <strong>'+__num_pages_search+'</strong>');
            //$('.forum-active-link').children().last().after('<span class="forum-and-page">'+__total_forums+' forums | page <strong>'+index+'</strong> of <strong>'+__num_pages+'</strong> </span>');
            $('#footer_page_details').html(__total_forums+' '+topic_text+' | page <strong>'+index+'</strong> of <strong>'+__num_pages_search+'</strong>');
        }
    });
}


function setPagination(c, m) {
    var current = c,
        last = m,
        delta = 2,
        left = current - delta,
        right = current + delta + 1,
        range = [],
        rangeWithDots = '',
        l;
  
    range.push(1)  
    for (let i = c - delta; i <= c + delta; i++) {
        if (i >= left && i < right && i < m && i > 1) {
            range.push(i);
        }
    }  
    range.push(m);

    for (let i of range) {
        if (l) {
            if (i - l === 2) {
                rangeWithDots += '<li><a href="javascript:void(0);" onclick="paginate('+(l + 1)+')">'+(l + 1)+'</a></li>';
            } else if (i - l !== 1) {
                rangeWithDots += '<li><a href="javascript:void(0);">...</a></li>';
            }
        }
        if(i==c){
            rangeWithDots += '<li><a href="javascript:void(0);" class="pagination-active" onclick="paginate('+i+')">'+i+'</a></li>';
        }else{
            rangeWithDots += '<li><a href="javascript:void(0);" onclick="paginate('+i+')">'+i+'</a></li>';
        }
        l = i;
    }

    return rangeWithDots;
}