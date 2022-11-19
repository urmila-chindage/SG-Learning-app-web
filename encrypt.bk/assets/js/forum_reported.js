$(document).ready(function () {
    __topic_comments = $.parseJSON(__topic_comments);
    process_comment(__topic_comments);
    pagination(__num_pages);
    $(".redactor-hidden").hide();
    setTinyMce(__mentions);
    navigate(__report_pageNumber);
    setTimeout(function(){
        highlight(__report_id);
    },2000);
});

function process_comment(comments){
	var renderHtml = '';
    __current_page_count = Object.keys(comments).length;
	if(Object.keys(comments).length > 0){
		renderHtml += '<li><section><div class="replies-logo-text">';
		renderHtml += '<div class="container  container-res-chnger-frorm-page"><span class="reply-and-text">';
		renderHtml += '<img class="reply-parent-svg" src="'+__themes_url+'/img/reply.svg"><label class="reply-text">Replies</label>';
		renderHtml += '</span></div></div>';
		renderHtml += '</section></li>';
	}
	$.each(comments, function(comment_key, comment )
    {
    	if(comment['children_count']>0){
    		renderHtml += renderCommentWithChild(comment);
    	}else{
    		renderHtml += renderCommentWithoutChild(comment);
    	}
    });

	$('.post-comment-parent').html(renderHtml);
}

function highlight(comment_id){
    $('#myComment'+comment_id).css("border","1px solid");
    $('#myComment'+comment_id).css("border-color","red");
    $('html,body').animate({
        scrollTop: $('#myComment'+comment_id).offset().top},
        'slow');
    $('#loaamoreLi'+__report_parent).remove();
}

function renderCommentWithoutChild(comment){
	var renderHtml = '';
	renderHtml += '<li class="post-comment-main" id="parentComment'+comment['id']+'"><div id="myComment'+comment['id']+'" class="olp-post-holder clearfix">';
	if(comment['us_image'] == 'default.jpg'){
		renderHtml += '<span class="olp-post-image"><img src="'+__default_user_image_path+comment['us_image']+'" class="olp-prof-pic img-rounded">';
	}else{
		renderHtml += '<span class="olp-post-image"><img src="'+__user_image_path+comment['us_image']+'" class="olp-prof-pic img-rounded">';
	}
	renderHtml += '<span class="olp-profile-name-small">';
	renderHtml += '<span class="olp-user-name">'+comment['us_name']+'</span><span class="olp-site-admin">'+comment['author_role']+'</span>';
	renderHtml += '<span class="olp-posts-count">Posts: 60</span></span></span>';
	renderHtml += '<span class="post-content post-content-comment">';
	renderHtml += comment['topic_comment'];
	renderHtml += '<span class="reply-for-cmt-post"><a class="reply-link post-comment-common" href="javascript:void(0)" onclick="replyTo('+comment['id']+')">Reply</a></span></span><span class="cmt-post-time">';
	renderHtml += comment['comment_created']+'</span>';
	renderHtml += renderOptions(comment['id'],comment['parent_id'])+'</div></li>';
    renderHtml += renderReplyBox(comment['id']);
	
	return renderHtml;
}

function renderOptions(comment_id,parent_id){
    var renderHtml = '<div class="dropdown dropdown-dots dropdown-dots-alter">';
    renderHtml += '<button class="btn  dropdown-toggle alterd-drop" type="button" data-toggle="dropdown"><span class="transformed-dotes">...</span></button>';
    renderHtml += '<ul class="dropdown-menu dropdown-ul"><li><a href="javascript:void(0);" onclick="deleteComment('+comment_id+','+parent_id+')">Delete</a></li>';
    renderHtml += '</ul>';

    return renderHtml;
}

function deleteComment(comment_id,parent_id){
    var renderHtml = '';
    var child_already = false;
    $.ajax({
        url: __site_url+'forum/delete_comment',
        type: "POST",
        data:{"is_ajax":true,'topic_id':__topic_id,'forum_id':__forum_id,'user_id':atob(__user_id),'comment_id':comment_id,'child_limit':__child_limit,'parent_id':parent_id,'limit':__limit},
        success: function(response) {
            var data = $.parseJSON(response);
            if(data['success'] == true){
                if(parseInt(parent_id) == 0){
                    __num_pages = data['pages'];
                    __total_comments = data['reply_count'];
                    pagination(__num_pages);
                    if(__current_page == 1){
                        navigate(1);
                    }else{
                        if(__current_page_count>1){
                            navigate(__current_page);
                        }else{
                            navigate(__current_page-1);
                        }
                    }
                }else{
                    __offset_child[parent_id] = parseInt(__child_limit);
                    __recieved_childs[parent_id] = parseInt(__child_limit);
                    $.each(data['posts'], function(comment_key, comment )
                    {
                        if(comment['children_count']>0){
                            renderHtml += renderChildComments(comment['children'],comment['children_count'],parent_id);
                            __childs_total[parent_id] = parseInt(comment['children_count']);
                            if(comment['children_count']>0){
                                child_already = true;
                            }
                        }
                    });
                    if(child_already == true){
                        $('#childUl'+parent_id).replaceWith(renderHtml);
                    }else{
                        $('#childUl'+parent_id).remove();
                    }
                    setTinyMce(__mentions);
                }
            }else{
                console.log(data['message']);
            }
        }
    });
}

/*function reportComment(comment_id){
    $('#reportConfirmButton').attr('onclick','sendReportConfirm('+comment_id+')');
    $('textarea#reportReasonText').val('');
    $('#reportConfirmButton').html('REPORT');
    $('#report_popup').modal('show');
}

function sendReportConfirm(comment_id){
    $('#reportConfirmButton').html('Reporting...');
    $.ajax({
        url: __site_url+'forum/report_comment',
        type: "POST",
        data:{"is_ajax":true,'topic_id':__topic_id,'user_id':atob(__user_id),'comment_id':comment_id,'reason':$('textarea#reportReasonText').val()},
        success: function(response) {
            var data = $.parseJSON(response);
            if(data['success'] == true){
                $('#reportLi'+comment_id+' a').attr('onclick','javascript:void(0)');
                $('#reportLi'+comment_id+' a').html('Reported');
                $('#reportConfirmButton').html('Reported');
                $('#reportCancelButton').trigger('click');
            }else if(data['code'] == 401){
                $('#reportConfirmButton').html('Aborted');
                window.location.replace(__site_url+'login');
            }
        }
    });
}*/

function renderCommentWithChild(comment){
	var renderHtml = '';
	__recieved_childs[comment['id']] = parseInt(__child_limit);
	__childs_total[comment['id']] = comment['children_count'];
	__offset_child[comment['id']] = parseInt(__child_limit);
	renderHtml += '<li class="post-comment-sub" id="parentComment'+comment['id']+'">';
	renderHtml += '<div id="myComment'+comment['id']+'" class="olp-post-holder olp-subComment-Border clearfix"><span class="olp-post-image">';
	if(comment['us_image'] == 'default.jpg'){
		renderHtml += '<img src="'+__default_user_image_path+comment['us_image']+'" class="olp-prof-pic img-rounded">';
	}else{
		renderHtml += '<img src="'+__user_image_path+comment['us_image']+'" class="olp-prof-pic img-rounded">';
	}
	renderHtml += '<span class="olp-profile-name-small"><span class="olp-user-name">'+comment['us_name']+'</span>';
	renderHtml += '<span class="olp-site-admin">'+comment['author_role']+'</span><span class="olp-posts-count">Posts: '+comment['author_post_count']+'</span>';
	renderHtml += '</span></span>';
	renderHtml += '<span class="post-content post-content-comment"><p class="olp-post-para">';
	renderHtml += comment['topic_comment'];
	renderHtml += '<span class="reply-for-cmt-post"><a class="reply-link post-comment-common" href="javascript:void(0)" onclick="replyTo('+comment['id']+')">Reply</a></span></span><span class="cmt-post-time">';
	renderHtml += comment['comment_created'];
    renderHtml += '</span>'+renderOptions(comment['id'],comment['parent_id'])+'</div></li>';
	renderHtml += renderChildComments(comment['children'],comment['children_count'],comment['id']);
	renderHtml += '</li>';
    renderHtml += renderReplyBox(comment['id']);

	return renderHtml;
}

function renderChildComments(childs,child_count,parent_id){
	var renderHtml = '<ul class="post-comment-child" id="childUl'+parent_id+'"><li><span class="reply-and-text padd-adjust-top-btm">';
	renderHtml += '<img class="reply-parent-svg reply-child-svg" src="'+__themes_url+'/img/reply-sub.svg">';
	renderHtml += '<label class="reply-text-and-num">'+child_count+' Replies</label></span></li>';
	$.each(childs, function(child_key, child )
    {
    	renderHtml += '<li class="post-comment-sub"><div id="myComment'+child['id']+'" class="olp-post-holder olp-subComment-Border clearfix"><span class="olp-post-image">';
    	if(child['us_image'] == 'default.jpg'){
			renderHtml += '<img src="'+__default_user_image_path+child['us_image']+'" class="olp-prof-pic img-rounded">';
		}else{
			renderHtml += '<img src="'+__user_image_path+child['us_image']+'" class="olp-prof-pic img-rounded">';
		}
    	renderHtml += '<span class="olp-profile-name-small">';
    	renderHtml += '<span class="olp-user-name">'+child['us_name']+'</span><span class="olp-site-admin">'+child['author_role']+'</span><span class="olp-posts-count">Posts: '+child['author_post_count']+'</span>';
    	renderHtml += '</span></span><span class="post-content post-content-comment">';
    	renderHtml += child['topic_comment'];
    	renderHtml += '</span><span class="cmt-post-time">';
        renderHtml += child['comment_created'];
    	renderHtml += '</span>'+renderOptions(child['id'],child['parent_id'])+'</div></li>';
    });
    if(__child_limit<child_count){
    	renderHtml += '<li class="post-comment-viw-more" id="loaamoreLi'+parent_id+'">';
		renderHtml += '<a href="javascript:void(0);" onclick="loadChild('+parent_id+')">View more replies - <span id="spanChildCount'+parent_id+'">'+(child_count-__child_limit)+'</span></a>';
		renderHtml += '<img src="'+__themes_url+'/img/ajax-loader.gif" class="ajax-loader hidden" id="loadingChild'+parent_id+'">';
		renderHtml += '</li>';
    }
	renderHtml += '</ul>';

	return renderHtml;
}

function pagination(links){
    var renderHtml = '';
    var comment_text = '';
    if(__total_comments == 1){
        comment_text = 'reply';
    }else{
        comment_text = 'replies';
    }

    if(links>1){
        renderHtml = '<span class="pagination-prev pag-next first-link"><a href="javascript:void(0);" onclick="navigate(1)">First</a></span>';
        renderHtml += '<span class="pagination-prev pag-next previous-link"><a href="javascript:void(0);" onclick="navigate(1)">Previous</a></span>';
        renderHtml += '<span class="pagination-wraper">';
        renderHtml += '<ul class="pagination-black pagination-ul">';
        renderHtml += setPagination(1,links);
        renderHtml += '</ul></span></span>';
        renderHtml += '<span class="pagination-next-last">';
        renderHtml += '<span class="pagination-prev pag-next next-link"><a href="javascript:void(0);" onclick="navigate('+2+')">Next</a></span>';
        renderHtml += '<span class="pagination-prev pag-prev last-link"><a href="javascript:void(0);" onclick="navigate('+links+')">Last</a></span></span>';
        renderHtml += '<span class="forum-pagination-page" id="footer_page_details">'+__total_comments+' '+comment_text+' | page <strong>'+1+'</strong> of <strong>'+links+'</strong></span>';

        $('.links-pagination').html(renderHtml);
        $('.first-link').hide();
        $('.previous-link').hide();
        $('.forum-and-page').html(__total_comments+' '+comment_text+' | page <strong>1</strong> of <strong>'+links+'</strong>');
    }else{
        renderHtml += '<span class="forum-pagination-page" id="footer_page_details">'+__total_comments+' '+comment_text+' | page <strong>'+1+'</strong> of <strong>'+links+'</strong></span>';
        $('.links-pagination').html(renderHtml);
        $('.forum-and-page').html(__total_comments+' '+comment_text+' | page <strong>1</strong> of <strong>'+links+'</strong>');
    }
}

function loadChild(parent_id){
	var childHtml = '';
	$('#loadingChild'+parent_id).removeClass('hidden');
	$.ajax({
        url: __site_url+'forum/get_ajax_childs',
        type: "POST",
        data:{"is_ajax":true,'parent':parent_id,'offset':__offset_child[parent_id],'limit':__limit},
        success: function(response) {
            var data = $.parseJSON(response);
            __recieved_childs[parent_id] += Object.keys(data['childs']).length;
            $('#spanChildCount'+parent_id).html(__childs_total[parent_id] - __recieved_childs[parent_id]);
            __offset_child[parent_id]++;
            childHtml += renderChildCommentsLi(data['childs'],__recieved_childs[parent_id],parent_id);
            $('#childUl'+parent_id).children().last().before(childHtml);
            $('#loadingChild'+parent_id).addClass('hidden');
            if(__recieved_childs[parent_id]==__childs_total[parent_id]){
            	$('#loaamoreLi'+parent_id).remove();
            }
        }
    });
}

function renderChildCommentsLi(childs,child_count,parent_id){
	var renderHtml = '';
	$.each(childs, function(child_key, child )
    {
    	renderHtml += '<li class="post-comment-sub"><div id="myComment'+child['id']+'" class="olp-post-holder olp-subComment-Border clearfix"><span class="olp-post-image">';
    	if(child['us_image'] == 'default.jpg'){
			renderHtml += '<img src="'+__default_user_image_path+child['us_image']+'" class="olp-prof-pic img-rounded">';
		}else{
			renderHtml += '<img src="'+__user_image_path+child['us_image']+'" class="olp-prof-pic img-rounded">';
		}
    	renderHtml += '<span class="olp-profile-name-small">';
    	renderHtml += '<span class="olp-user-name">'+child['us_name']+'</span><span class="olp-site-admin">'+child['author_role']+'</span><span class="olp-posts-count">Posts: '+child['author_post_count']+'</span>';
    	renderHtml += '</span></span><span class="post-content post-content-comment">';
    	renderHtml += child['topic_comment'];
    	renderHtml += '</span><span class="cmt-post-time">';
    	renderHtml += child['comment_created'];
    	renderHtml += '</span>'+renderOptions(child['id'],child['parent_id'])+'</div></li>';
    });

	return renderHtml;
}

function navigate(page_number){
    var comment_text = '';
    __current_page = page_number;
    if(__total_comments == 1){
        comment_text = 'reply';
    }else{
        comment_text = 'replies';
    }
	$.ajax({
        url: __site_url+'forum/get_ajax_parents',
        type: "POST",
        data:{"is_ajax":true,'topic':__topic_id,'offset':page_number,'limit':__limit,'child_limit':__child_limit,'report_parent':__report_parent},
        success: function(response) {
        	$('.first-link').show();
        	$('.previous-link').show();
        	$('.next-link').show();
            $('.last-link').show();
            var data = $.parseJSON(response);
            process_comment(data['comments']);
            $('.pagination-ul').html(setPagination(page_number,__num_pages));
            if(page_number == __num_pages){
            	$('.next-link').hide();
            	$('.last-link').hide();
            }
            if(page_number == 1){
            	$('.first-link').hide();
        		$('.previous-link').hide();
            }
            $('.previous-link a').attr('onclick',"navigate('"+(parseInt(page_number)-1)+"')");
            $('.next-link a').attr('onclick',"navigate('"+(parseInt(page_number)+1)+"')");
            $('.forum-and-page').html(__total_comments+' '+comment_text+' | page <strong>'+page_number+'</strong> of <strong>'+__num_pages+'</strong>');
            $('#footer_page_details').html(__total_comments+' '+comment_text+' | page <strong>'+page_number+'</strong> of <strong>'+__num_pages+'</strong>');
            $(".redactor-hidden").hide();
            setTinyMce(__mentions);
        }
    });
}

function renderReplyBox(parent_id){
    var renderHtml = '<li class="post-comment-main redactor-hidden"   id="childReplyBox'+parent_id+'">';
    renderHtml += '<div class="olp-post-holder clearfix">';
    renderHtml += '<textarea id="tinyChild'+parent_id+'" class="redactor-sub TinyMceEditor"></textarea>';
    renderHtml += '<span class="redactor-bootom-btns clearfix">';
    renderHtml += '<span class="cancel-post-btns">';
    renderHtml += '<a href="javascript:void(0)" onclick="postChildReply('+parent_id+')" id="posttButton'+parent_id+'" class="btn btn-post post-comment-common">Post</a>';
    renderHtml += '<a href="javascript:void(0)" onclick="cancelChildReply('+parent_id+')" class="btn btn-cancel post-comment-common">Cancel</a>';
    renderHtml += '</span></span>';
    renderHtml += '</div></li>';

    return renderHtml;
}

function replyTo(parent_id){
    $('#posttButton'+parent_id).html('POST');
    if(atob(__user_id) != 0){
        $('#childReplyBox'+parent_id).slideToggle("slow");
        $('html,body').animate({
        scrollTop: $('#childReplyBox'+parent_id).offset().top},
        'slow');
    }else{
        window.location.replace(__site_url+'login');
    }

}

function cancelChildReply(parent_id){
    $('#childReplyBox'+parent_id).slideToggle("slow");
    $('html,body').animate({
        scrollTop: $('#parentComment'+parent_id).offset().top},
        'slow');
    tinyMCE.activeEditor.setContent('');
}

function postChildReply(parent_id){
    var renderHtml = '';
    var comment = $(tinyMCE.activeEditor.getContent());
    var IDs = [];
    var child_already = false;
    comment.find("span").each(function(){ IDs.push(this); });
    for(var i=0;i<IDs.length;i++){
        IDs[i] = $(IDs[i]).attr('data-internalid');
    }
    IDs = jQuery.unique(IDs);
    //alert(JSON.stringify(IDs));
    if(tinyMCE.activeEditor.getContent()==''){
        $(textarea_id).focus();
    }else{
        $('#posttButton'+parent_id).html('Posting...');
        $.ajax({
            url: __site_url+'forum/ajax_comment',
            type: "POST",
            data:{"is_ajax":true,'forum_id':__forum_id,'topic_id':__topic_id,'user_id':atob(__user_id),'mention_ids':IDs,'child_limit':__child_limit,'comment':tinyMCE.activeEditor.getContent(),'parent_id':parent_id,'url':$(location).attr('href')},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['success'] == true){
                    $('#posttButton'+parent_id).html('Posted');
                    __offset_child[parent_id] = parseInt(__child_limit);
                    __recieved_childs[parent_id] = parseInt(__child_limit);
                    $.each(data['posts'], function(comment_key, comment )
                    {
                        if(comment['children_count']>0){
                            renderHtml += renderChildComments(comment['children'],comment['children_count'],parent_id);
                            __childs_total[parent_id] = parseInt(comment['children_count']);
                            if(comment['children_count']>1){
                                child_already = true;
                            }
                        }
                    });
                    if(child_already == true){
                        $('#childUl'+parent_id).replaceWith(renderHtml);
                    }else{
                        $('#parentComment'+parent_id).after(renderHtml);
                    }
                    cancelChildReply(parent_id);
                }else if(data['code'] == 401){
                    window.location.replace(__site_url+'login');
                }
                setTinyMce(__mentions);
            }
        });
    }
}

function setTinyMce(mentions){
    //mentions = atob(mentions)
    //mentions = jQuery.parseJSON(mentions);
    tinyMCE.remove();
    mentions = $.parseJSON(mentions);
    tinyMCE.init({
        paste_data_images: true,
        mode : "exact",
        selector: '.TinyMceEditor',
        plugins : 'mention',
        mentions: {
            source:mentions
        },
        init_instance_callback : function(ed) {
         //   QUnit.start();
        }
    });
}

$('#mainTopicReply').click(function(){
    $('#mainReply').slideToggle("slow");
    $('html,body').animate({
        scrollTop: $('#mainReply').offset().top},
        'slow');
    $('#mainTopicReplyBoxSend').html('POST')
});

$('#mainTopicReplyBoxCancel').click(function(){
    $('#mainReply').slideToggle("slow");
    $('html,body').animate({
        scrollTop: $('#topicSectionMain').offset().top},
        'slow');
    tinyMCE.activeEditor.setContent('');
});

$('#mainTopicReplyBoxSend').click(function(){
    $('#mainTopicReplyBoxSend').html('Posting...');
    var comment = $(tinyMCE.activeEditor.getContent());
    var IDs = [];
    comment.find("span").each(function(){ IDs.push(this); });
    for(var i=0;i<IDs.length;i++){
        IDs[i] = $(IDs[i]).attr('data-internalid');
    }
    IDs = jQuery.unique(IDs);
    $.ajax({
        url: __site_url+'forum/ajax_comment',
        type: "POST",
        data:{"is_ajax":true,'forum_id':__forum_id,'topic_id':__topic_id,'user_id':'0','limit':__limit,'mention_ids':IDs,'comment':tinyMCE.activeEditor.getContent(),'parent_id':0,'url':$(location).attr('href')},
        success: function(response) {
            var data = $.parseJSON(response);
            if(data['success'] == true){
                tinyMCE.activeEditor.setContent('');
                __num_pages = data['pages'];
                __total_comments = data['total_replies'];
                pagination(__num_pages);
                navigate(1);
                $('#mainTopicReplyBoxSend').html('Posted');
            }else if(data['code'] == 401){
                window.location.replace(__site_url+'login');
            }
        }
    });
});

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
                rangeWithDots += '<li><a href="javascript:void(0);" onclick="navigate('+(l + 1)+')">'+(l + 1)+'</a></li>';
            } else if (i - l !== 1) {
                rangeWithDots += '<li><a href="javascript:void(0);">...</a></li>';
            }
        }
        if(i==c){
            rangeWithDots += '<li><a href="javascript:void(0);" class="pagination-active" onclick="navigate('+i+')">'+i+'</a></li>';
        }else{
            rangeWithDots += '<li><a href="javascript:void(0);" onclick="navigate('+i+')">'+i+'</a></li>';
        }
        l = i;
    }

    return rangeWithDots;
}