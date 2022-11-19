var __hidden_id = $('#hidden_lecture_id').val();
function isUrl(s) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return regexp.test(s);
}

$("#cmtbox").keyup(function(e){
	var cmnt = $(this).val();
	var str  = '';
	var now = new Date();
        
        //configuring coment
        var nonEscapedValue = cmnt;
        // added line below to escape typed text
        var value = nonEscapedValue;
        var words = value.split(" ");
        for (var i=0;i<words.length;i++)
        {
            var n = isUrl(words[i]);
            if (n)  {
                var deadLink = '<a href="'+words[i]+'" target="_blank" rel="nofollow">'+words[i]+'</a>';
                // changed line below to assign replace()'s result
                words[i] = words[i].replace(words[i], deadLink);
            }
        }
        // added line below to put the result in to the div #myResultDiv
        cmnt = words.join(" ");
        //End
        
        
    if(e.keyCode == 13)
    {
        $.ajax({
        	method: "POST",
        	url: admin_url+'coursebuilder/savecomment',
        	data: {
        		cmnt: cmnt,
        		attempt_id: attempt_id,
                        user_id: user_id,
                        lecture_id: lecture_id
        	},
            dataType: 'JSON',
        	success: function(response){
                    var user_img = ((response.user_img == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
                    str = '<div id="comment_id_'+response.comment_id+'" class="right-group-wrap old-chat clearfix">';
                    str = str + '<span class="">';
                    str = str + '<img class="box-style" src="'+user_img+response.user_img+'">';
                    str = str + '</span>';
                    str = str + '<span class="user-date">';
                    str = str + '<a>';
                    str = str + '<span class="user">'+admin_name+'</span>';
                    str = str + '<span class="pull-right comment-delete-btn" onclick="deleteComment('+response.comment_id+')">X</span>';
                    str = str + '<span class="pull-right date">';
                    str = str + $.format.date(new Date(), 'MMM dd yyyy');
                    str = str + '</span>';
                    str = str + '</a>';
                    str = str + '<div class="content-text">';
                    str = str + cmnt;
                    str = str + '</div>';
                    str = str + '</span>';
                    str = str + '</div>';                        

                    $("#cmtsection").append(str);
                    $("#cmtbox").val(""); 

        	}
        });

    }
});

function deleteComment(comment_id, file)
{
    file = file ? file : '';
    $.ajax({
            url: admin_url+'coursebuilder/delete_comment',
            type: "POST",
            data:{"is_ajax":true, 'comment_id':comment_id, 'file':file},
            success: function(response) {
                $('#comment_id_'+comment_id).remove();
            }
        });  
}

$("#saveandexit").click(function (e){
	var mark = $("#txtmrk").val();
        var max_mark = $("#descriptive_max_mark").val();
   
    if(parseInt(mark) > parseInt(max_mark)){
        lauch_common_message('Invalid Marks', 'Marks should not be above max marks');    
        return false;
    }
	if(mark != ''){
		$.ajax({
        	method: "POST",
        	url: admin_url+'coursebuilder/savemark',
        	data: {
        		mark: mark,
        		lecture_id: lecture_id,
        		user_id: user_id
        	},
        	success: function(response){
        		window.location = admin_url+'coursebuilder/report/'+lecture_id;
        	}
        });
	}
	else{
		window.location = admin_url+'coursebuilder/report/'+lecture_id;
	}
	
});

$("#saveandcontinue").click(function (e){
    var mark = $("#txtmrk").val();
    var max_mark = $("#descriptive_max_mark").val();
   
    if(parseInt(mark) > parseInt(max_mark)){
        lauch_common_message('Invalid Marks', 'Marks should not be above max marks');    
        return false;
    }
    
    if(mark != ''){

            if(next_id != ''){
                $.ajax({
                    method: "POST",
                    url: admin_url+'coursebuilder/savemark',
                    data: {
                        mark: mark,
                        lecture_id: lecture_id,
                        user_id: user_id
                    },
                    success: function(response){
                        window.location = admin_url+'coursebuilder/evaluate_descriptive/'+lecture_id+'/'+next_id;
                    }
                });
        }
        else{
            $.ajax({
                    method: "POST",
                    url: admin_url+'coursebuilder/savemark',
                    data: {
                        mark: mark,
                        lecture_id: lecture_id,
                        user_id: user_id
                    },
                    success: function(response){
                        location.reload();
                    }
                });
        }
    }
    else{

        if(next_id != ''){
            window.location = admin_url+'coursebuilder/evaluate_descriptive/'+lecture_id+'/'+next_id;
        }
        else{
            location.reload();
        }
        
    }
});

function select_test(lecture_id, obj = ''){
    __hidden_id = $("#hidden_lecture_id").val(lecture_id);
    if(obj != ''){
        $("#test_name").text($(obj).text());
    }
    
    $.ajax({
        method: "POST",
        url: admin_url+'coursebuilder/select_descriptive_test',
        data: {
            lecture_id: lecture_id,
        },
        success: function(response){
            $("#tblcontent").html(response);
            var export_url = admin_url+'coursebuilder/export_descriptive_test/'+lecture_id;
            console.log(export_url);
            $('#export_results').attr('href',export_url);
        }
    });
}

function select_course(course_id, obj){

    $("#course_name").text($(obj).text());
    $.ajax({
        method: "POST",
        url: admin_url+'coursebuilder/select_descriptive_test_course',
        data: {
            course_id: course_id
        },
        success: function(response){
            data = $.parseJSON(response);
            $("#test_list").html(data.str+'<span class="caret"></span>');
            $("#test_name").html(data.current_name+'<span class="caret"></span>');

            if(data.current_id == ''){
                $("#tblcontent").html('');
            }
            else{
                select_test(data.current_id);
            }
        }
    });
}

$(document).on('click','#export_results',function(){
    if(__hidden_id)
    {
        window.location.href = admin_url+'coursebuilder/export_descriptive_test/'+__hidden_id;
    }
});