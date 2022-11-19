$(document).ready(function () {
    __forums = $.parseJSON(__forums);
    if(Object.keys(__forums).length > 0){
        pagination(__num_pages);
        forum_header = get_header();
        forum_body = render_forum_object(__forums);
        $('.discussion-forum-parent').html(forum_header+forum_body);
    }else{
        $('.discussion-forum-parent').html(renderEmptyObject());
        $('.view-unans-text').hide();
        $('.view-active-text').hide();
    }
});

function set_order_type(type){
    var renderHtml = '';
    __listing_type = type;
    switch(type){
        case 1:
            $('#filterLabel').html('Unanswered first');
            renderHtml += '<li><a href="javascript:void(0)" onclick="set_order_type(0)">Recent</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="set_order_type(2)">Active Topics</a></li>';
            $('.dropdown-filter').html(renderHtml);
        break;
        case 2:
            $('#filterLabel').html('Active Topics');
            renderHtml += '<li><a href="javascript:void(0)" onclick="set_order_type(0)">Recent</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="set_order_type(1)">Unanswered first</a></li>';
            $('.dropdown-filter').html(renderHtml);
        break;
        default:
            $('#filterLabel').html('Recent');
            renderHtml += '<li><a href="javascript:void(0)" onclick="set_order_type(2)">Active Topics</a></li>';
            renderHtml += '<li><a href="javascript:void(0)" onclick="set_order_type(1)">Unanswered first</a></li>';
            $('.dropdown-filter').html(renderHtml);
        break;
    }
    $('.forum-active-link').children().last().remove();
    paginate(1);
    pagination(__num_pages);
}

function renderEmptyObject(){
    var renderHtml = '<div class="empty-notifications">';
    renderHtml += '<img src="'+__themes_url+'/images/No_Notification_illustration.svg" width="100" height="100">';
    renderHtml += '<p>No forums to show</p></div>';

    return renderHtml;
}

function render_forum_object(forum_obj){
    var renderHtml = '';
    __current_page_count = Object.keys(forum_obj).length;
    $.each(forum_obj, function(forum_key, forum )
    {
        if(forum['total_topics'] == null){
            forum['total_topics'] = 0;
        }
        if(forum['total_comments'] == null){
            forum['total_comments'] = 0;
        }
        renderHtml += '<li class="discussion-forum-white-lists">';
        renderHtml += '<a href="'+__site_url+'forum/'+forum['forum_slug']+'" class="discussion-link">';
        renderHtml += '<img src="'+__themes_url+'/img/forum-ico.svg" class="forum-ion-res">';
        renderHtml += '<span class="forum-title-wrap forum-title-wrap-alter-index"><span class="forum-titile">'+forum['forum_name']+'</span>';
        renderHtml += '<span class="forum-des forum-des-for-index">'+forum['forum_description']+'</span></span><span class="topic-xs">';
        renderHtml += '<span class="topic-form-text">'+forum['total_topics']+'<span class="hidden-lg hidden-md hidden-sm topic-post-sm">Topic</span></span>';
        renderHtml += '<span class="topic-form-text">'+forum['total_comments']+'<span class="hidden-lg hidden-md hidden-sm topic-post-sm">Posts</span></span>';
        renderHtml += '</span><span class="last-post-forum-text">';
        if(forum.latest_topic['author'] != ''){
            renderHtml += '<span class="by-name">by <span class="name-orange">'+forum['latest_topic']['author'].us_name+'</span></span>';
            renderHtml += '<span class="forum-date-time">'+forum.latest_topic['topic_created']+'</span>';
        }else{
            renderHtml += '<span class="by-name"><span class="name-orange"></span></span>';
            renderHtml += '<span class="forum-date-time"></span>';
        }
        renderHtml += '</span></a>'+renderOptions(forum['id'])+'</li>';
    });
//Wed Apr 09, 2016 2:28 pm
    return renderHtml;
}
function renderOptions(forum_id){
    var renderHtml = '<div class="dropdown dropdown-dots">';
    renderHtml += '<button class="btn  dropdown-toggle alterd-drop" type="button" data-toggle="dropdown"><span class="transformed-dotes">...</span>';
    renderHtml += '</button><ul class="dropdown-menu dropdown-ul">';
    renderHtml += '<li><a href="javascript:void(0);" onclick="deleteForum('+forum_id+')">Delete</a></li></ul></div>';

    return renderHtml;
}
function  get_header() {
    var renderHtml = '<li class="discussion-header">';
    renderHtml += '<span class="discussion-head-text">Dicussion forums</span>';
    renderHtml += '<span class="topic-head-text">Topics</span>';
    renderHtml += '<span class="topic-head-text">Posts</span>';
    renderHtml += '<span class="last-post-head-text">Last post</span>';
    renderHtml += '</li>';

    return renderHtml;
}

function pagination(links){
    var renderHtml = '';
    var category_text = '';
    if(__total_forums == 1){
        category_text = 'forum';    
    }else{
        category_text = 'forums';
    }
    if(links>1){
        renderHtml = '<span class="pagination-prev pag-next first-link"><a href="javascript:void(0);" onclick="paginate(1)">First</a></span>';
        renderHtml += '<span class="pagination-prev pag-next previous-link"><a href="javascript:void(0);" onclick="paginate(1)">Previous</a></span>';
        renderHtml += '<span class="pagination-wraper">';
        renderHtml += '<ul class="pagination-black pagination-ul">';
        renderHtml += setPagination(1,__num_pages);
        renderHtml += '</ul></span></span>';
        renderHtml += '<span class="pagination-next-last">';
        renderHtml += '<span class="pagination-prev pag-next next-link"><a href="javascript:void(0);" onclick="paginate('+2+')">Next</a></span>';
        renderHtml += '<span class="pagination-prev pag-prev last-link"><a href="javascript:void(0);" onclick="paginate('+links+')">Last</a></span></span>';
        renderHtml += '<span class="forum-pagination-page" id="footer_page_details">'+__total_forums+' '+category_text+' | page <strong>'+1+'</strong> of <strong>'+links+'</strong></span>';

        $('.links-pagination').html(renderHtml);
        $('.first-link').hide();
        $('.previous-link').hide();
        $('.forum-active-link').children().last().after('<span class="forum-and-page">'+__total_forums+' '+category_text+' | page <strong>'+1+'</strong> of <strong>'+links+'</strong> </span>');
    }else{
        renderHtml += '<span class="forum-pagination-page" id="footer_page_details">'+__total_forums+' '+category_text+' | page <strong>'+1+'</strong> of <strong>'+links+'</strong></span>';
        $('.links-pagination').html(renderHtml);
        $('.forum-active-link').children().last().after('<span class="forum-and-page">'+__total_forums+' '+category_text+' | page <strong>'+1+'</strong> of <strong>'+links+'</strong> </span>');
    }
}

function paginate(index){
    var category_text = '';
    __current_page = index;
    if(__total_forums == 1){
        category_text = 'forum';    
    }else{
        category_text = 'forums';
    }
    $.ajax({
        url: __site_url+'forum/get_ajax_forum',
        type: "POST",
        data:{"is_ajax":true,'offset':index,'limit':__limit,'listing_type':__listing_type},
        success: function(response) {
            $('.first-link').show();
            $('.previous-link').show();
            $('.next-link').show();
            $('.last-link').show();
            var data = $.parseJSON(response);
            forum_header = get_header();
            forum_body = render_forum_object(data['forums']);
            $('.discussion-forum-parent').html(forum_header+forum_body);
            if(index == 1){
                $('.first-link').hide();
                $('.previous-link').hide();
            }else if(index == __num_pages){
                $('.next-link').hide();
                $('.last-link').hide();
            }
            $('.pagination-ul').html(setPagination(index,__num_pages));
            $('.previous-link a').attr('onclick',"paginate("+(parseInt(index)-1)+")");
            $('.next-link a').attr('onclick',"paginate("+(parseInt(index)+1)+")");
            $('.forum-active-link').children().last().remove();
            $('.forum-active-link').children().last().after('<span class="forum-and-page">'+__total_forums+' '+category_text+' | page <strong>'+index+'</strong> of <strong>'+__num_pages+'</strong> </span>');
            $('#footer_page_details').html(__total_forums+' '+category_text+' | page <strong>'+index+'</strong> of <strong>'+__num_pages+'</strong>');
        }
    });
}

function deleteForum(forum_id){
    $('#delete_main').attr('onclick',"delete_forum_confirmed("+forum_id+")");
    $('#DeleteModal').modal('show');
}

function delete_forum_confirmed(forum_id){
    $('#DeleteModal').modal('hide');
    $.ajax({
        url: __site_url+'forum/delete_ajax_forum',
        type: "POST",
        data:{"is_ajax":true,'forum_id':forum_id,'admin_id':0,'per_page':__limit},
        success: function(response) {
            var data = $.parseJSON(response);
            if(data['success'] == true){
                __num_pages = data['pages'];
                __total_forums = data['forum_count'];
                $('.forum-active-link').children().last().remove();
                if(__current_page == 1){
                    paginate(1);
                }else{
                    if(__current_page_count<2){
                        paginate(__current_page-1);
                    }else{
                        paginate(__current_page);
                    }
                }
                pagination(__num_pages);
            }else{
                //console.log(data['message']);
                toastr.options.timeOut = 300;
                toastr["error"](data['message']);
            }
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