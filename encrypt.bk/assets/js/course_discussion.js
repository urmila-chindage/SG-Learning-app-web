/* Created by Yadu Chandran */
var __questionCommentsSearchTimeOut = null;
var __currentDiscussionId = 0;

$(document).ready(function() {
    questionTimeOutSearch();
    // Add new question discussion
    $('#add_discussion_input').redactor({
        imageUpload: admin_url+'configuration/redactore_image_upload',
        callbacks: {
            imageUploadError: function(json, xhr)
            {
                 // var erorFileMsg = "This file type is not allowed. upload a valid image.";
                 // $('#course_form').prepend(renderPopUpMessage('error', erorFileMsg));
                 // scrollToTopOfPage();
                 // return false;
            }
        }  
    });

    comment_id_hash      = window.location.href;
    comment_id_hash      = comment_id_hash.substring(comment_id_hash.lastIndexOf('/') + 1);
    children_id_hash     = window.location.hash.substr(1);
    comment_id_hash      = comment_id_hash.replace('#'+children_id_hash,'');
    if(children_id_hash){
        $('#parent_hidden').val(comment_id_hash);
        $('#child_hidden').val(children_id_hash);
        $('div#'+children_id_hash).trigger('click');
        showDiscDetails(children_id_hash);  
        scrollToTopOfPage();
    }else{
        //Trigger click - first div of discussion.
        setTimeout(function(){ $('div.ques-list:first').trigger('click');
         }, 500);
        //Trigger click - first div of discussion closed.
    }
    

     $(document).on('click','.ques-list',function(){      
             $('#show_discussion_div').html('');     
             showDiscDetails(this.id);  
    }); 

    /* show discussion details starts */        
    function showDiscDetails(id)        
    {
        __currentDiscussionId = id;
        $.ajax({        
            url: admin_url+'course/load_previous_comments',        
            type: "POST",       
            data:{"is_ajax":true,'course_id' : __course_id,'discussion_id' : id},       
            success: function(response) {       
                var data = $.parseJSON(response);       
                if(data.course_comments.length > 0){        
                    $('#show_discussion_div').html(renderPreviousComments(response));       
                }       
            }       
        });     
    }       
    /* Show discussion details ends */
    
    __questionCommentsSearchTimeOut = setInterval(function(){
        if(__currentDiscussionId > 0)
        {
            $.ajax({        
                url: admin_url+'course/load_previous_comments',        
                type: "POST",       
                data:{"is_ajax":true,'course_id' : __course_id,'discussion_id' : __currentDiscussionId},       
                success: function(response) {       
                    var data = $.parseJSON(response);       
                    if(data.course_comments.length > 0){        
                        $('#append_answer').html(renderPreviousQuestionComments(response));       
                    }       
                }       
            });     
        }
    }, 4000);  


    /* Render comment details if on click parent comment starts */        
    function renderPreviousQuestionComments(response){      
        var data               = $.parseJSON(response);     
        var prevcommentHtml    = '';        
        if(data.course_comments.length > 0 )        
        {       
            for (var i=0; i<data.course_comments.length; i++)       
            {       
                var parentDate  = data.course_comments[i].created_date;     

                //var parentNew   = parentDate.replace(" ","T");   

                var date        = new Date(parentDate);      

                var newDate     = timeSince(date);      
                        
                data.children_comments[data.course_comments[i].id] = data.children_comments[data.course_comments[i].id].reverse();      
                        

                //if(data.children_comments[data.course_comments[i].id].length > 0 )        
                //{     
                
                   for (var j=0; j<data.children_comments[data.course_comments[i].id].length; j++)      
                    {       
    
                        var childDate        = data.children_comments[data.course_comments[i].id][j].created_date;      
                        //var childNew         = childDate.replace(" ","T");      
                        var childDate        = new Date(childDate);      

                        var childnewDate     = timeSince(childDate);        

                            
                        prevcommentHtml+= '<li class="single-answer" id="'+data.children_comments[data.course_comments[i].id][j].id+'">';       
                        prevcommentHtml+= '<span class="question-avatar thumb-avatar"><img src="'+((data.children_comments[data.course_comments[i].id][j].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.children_comments[data.course_comments[i].id][j].us_image+'" alt="'+data.children_comments[data.course_comments[i].id][j].us_name+'"/></span>';      
                        prevcommentHtml+= '<span class="answer-detailed-desc">';        
                        prevcommentHtml+= '<span class="col-sm-12 nopad">';        
                        prevcommentHtml+= '<span class="question-author">'+data.children_comments[data.course_comments[i].id][j].us_name+' '+'</span>';     
                        if(childnewDate.charAt(0) == '-')
                        {
                            childnewDate = 'just now';
                        }
                        prevcommentHtml+= '<span class="posted-on"> '+childnewDate+'</span>';      
                        prevcommentHtml+= '</span>'+data.children_comments[data.course_comments[i].id][j].comment+'</span>';        



                            prevcommentHtml+= '<span class="answer-close">';        
                            prevcommentHtml+= '    <span class="dropdown drop-down">';        

                            prevcommentHtml+= '     <a href="#" class="delete_comment" onclick="setID('+data.children_comments[data.course_comments[i].id][j].parent_id+','+data.children_comments[data.course_comments[i].id][j].id+')"><span class="icon icon-cancel-1"></span></a>';       
                            prevcommentHtml+= '        </span>';        
                            prevcommentHtml+= '    </span>';        
                        
                        prevcommentHtml+= '</li>';      

                    }       
     
                //}     
            }       
            return prevcommentHtml;     
        }       
    } 
    
    function renderPreviousComments(response){      
        var data               = $.parseJSON(response);     
        var prevcommentHtml    = '';        
        if(data.course_comments.length > 0 )        
        {       
            for (var i=0; i<data.course_comments.length; i++)       
            {       
                var parentDate  = data.course_comments[i].created_date;     

                //var parentNew   = parentDate.replace(" ","T");   

                var date        = new Date(parentDate);      

                var newDate     = timeSince(date);      
                        
                prevcommentHtml+= '<li class="single-question" id="single-question'+data.course_comments[i].id+'">';       

                prevcommentHtml+= '<span class="question-avatar thumb-avatar"><img src="'+((data.course_comments[i].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.course_comments[i].us_image+'" alt="'+data.course_comments[i].us_name+'"/></span>';        
                        
                prevcommentHtml+= '<span class="question-detailed-desc">';      



                prevcommentHtml+= '<span class="col-sm-12 btxt nopad">'+data.course_comments[i].comment_title;     
                prevcommentHtml+= '</span>';        

                prevcommentHtml+= '<span class="col-sm-12 nopad">';        
                prevcommentHtml+= '            <span class="question-author">'+data.course_comments[i].us_name+'</span>';           
                if(newDate.charAt(0) == '-')
                {
                    newDate = 'just now';
                }
                prevcommentHtml+= '            <span class="posted-on"> '+newDate+'</span>';           
                prevcommentHtml+= '</span>';        

                if(data.course_comments[i].comment)     
                {       
                    prevcommentHtml+= '<span class="col-sm-12 nopad">'+data.course_comments[i].comment;        
                    prevcommentHtml+= '</span>';        
                }       
                        
                prevcommentHtml+= '</span>';        


                
                    prevcommentHtml+= '<span class="major-close">';                     
                    prevcommentHtml+=    '<span class="dropdown drop-down">';       
                       
                    prevcommentHtml+=     '<a class="delete_discussion" href="#" onclick="setID('+data.course_comments[i].id+',0)"><span class="icon icon-cancel-1"></span></a>';       
                   
                    prevcommentHtml+=     '</span>';        
                    prevcommentHtml+= '</span>';       
       
  
                        
                data.children_comments[data.course_comments[i].id] = data.children_comments[data.course_comments[i].id].reverse();      
                        

                //if(data.children_comments[data.course_comments[i].id].length > 0 )        
                //{     

                prevcommentHtml+= '<ul class="all-answers">';       
                    prevcommentHtml+= '<span id="append_answer">';      
                   for (var j=0; j<data.children_comments[data.course_comments[i].id].length; j++)      
                    {       
    
                        var childDate        = data.children_comments[data.course_comments[i].id][j].created_date;      
                        //var childNew         = childDate.replace(" ","T");      
                        var childDate        = new Date(childDate);      

                        var childnewDate     = timeSince(childDate);        

                            
                        prevcommentHtml+= '<li class="single-answer" id="'+data.children_comments[data.course_comments[i].id][j].id+'">';       
                        prevcommentHtml+= '<span class="question-avatar thumb-avatar"><img src="'+((data.children_comments[data.course_comments[i].id][j].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.children_comments[data.course_comments[i].id][j].us_image+'" alt="'+data.children_comments[data.course_comments[i].id][j].us_name+'"/></span>';      
                        prevcommentHtml+= '<span class="answer-detailed-desc">';        
                        prevcommentHtml+= '<span class="col-sm-12 nopad">';        
                        prevcommentHtml+= '<span class="question-author">'+data.children_comments[data.course_comments[i].id][j].us_name+' '+'</span>';     
                        if(childnewDate.charAt(0) == '-')
                        {
                            childnewDate = 'just now';
                        }
                        prevcommentHtml+= '<span class="posted-on"> '+childnewDate+'</span>';      
                        prevcommentHtml+= '</span>'+data.children_comments[data.course_comments[i].id][j].comment+'</span>';        



                            prevcommentHtml+= '<span class="answer-close">';        
                            prevcommentHtml+= '    <span class="dropdown drop-down">';        

                            prevcommentHtml+= '     <a href="#" class="delete_comment" onclick="setID('+data.children_comments[data.course_comments[i].id][j].parent_id+','+data.children_comments[data.course_comments[i].id][j].id+')"><span class="icon icon-cancel-1"></span></a>';       
                            prevcommentHtml+= '        </span>';        
                            prevcommentHtml+= '    </span>';        
                        
                        prevcommentHtml+= '</li>';      

                    }       

                    prevcommentHtml+= '</span>';        

                        prevcommentHtml+= '<li class="single-answer">';     
                        prevcommentHtml+= '<span class="question-avatar thumb-avatar"><img class="imag-res" src="'+((data.admin_details.us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.admin_details.us_image+'" alt="'+data.admin_details.us_name+'"/></span>';     
                        prevcommentHtml+= '<span class="answer-detailed-desc">';        
                        prevcommentHtml+= '<textarea placeholder="Add an answer" class="add-answer" id="comment_textarea" maxlength="1000"></textarea>';        
                        prevcommentHtml+= '<button class="green-btn lefty add-btn sbtm add_answer" style="display:none;" id="'+data.course_comments[i].id+'">Add an answer</button>';       
                        prevcommentHtml+= '</span>';        
                        prevcommentHtml+= '</span>';        
                        prevcommentHtml+= '</li>';      

                prevcommentHtml+= '</ul>';      
                //}     
            }       
            return prevcommentHtml;     
        }       
    } 
    /* Render comment details ends here */

});

    /* Function for posting admin comments */
    $(document).on('click','.add_answer',function(){
        var text_id = this.id; //getter
        var comment_textarea  = $('#comment_textarea').val();
            comment_textarea  = comment_textarea.trim();
            comment_textarea  = $.trim( $('#comment_textarea').val() );
        if(comment_textarea!=""){
            postUserComments(text_id,comment_textarea);
            $("#comment_textarea").redactor('code.set', '');
            $('#comment_textarea').val('');
            $('#comment_textarea').redactor('core.destroy');
            $('#comment_textarea').attr('placeholder','Add an answer');
        }
    });

    /* Function for posting user comments */
    function postUserComments(comment_id,value){

        $.ajax({
                url: admin_url+'course/post_user_comment',
                type: "POST",
                data:{"is_ajax":true,'course_id' : __course_id, 'comment_id':comment_id,'comment':value},
                success: function(response) {
                    var appendHtml = '';
                    var data       = $.parseJSON(response); 
                    
                    var parentDate      = data.posted_user[0].created_date;

                    //var parentNew       = parentDate.replace(" ","T");
                    var date            = new Date(parentDate);

                    var childstrTime     = 'just now';//timeSince(date);


                    appendHtml+= '<li class="single-answer" id="'+data.inserted_id+'">';
                    appendHtml+= '<span class="question-avatar thumb-avatar"><img src="'+((data.user_details.us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.user_details.us_image+'" alt="'+data.user_details.us_name+'"/></span>';
                    appendHtml+= '<span class="answer-detailed-desc">';
                    appendHtml+= '<span class="col-sm-12 nopad">';
                    appendHtml+= '<span class="question-author">'+data.user_details.us_name+' '+'</span>';
                    appendHtml+= '<span class="posted-on"> '+childstrTime+'</span>';
                    appendHtml+= '</span>'+value+'</span>';

                
                    appendHtml+= '<span class="answer-close">';
                    appendHtml+= '    <span class="dropdown drop-down">';
                    appendHtml+= '     <a href="#" class="delete_comment" onclick="setID('+comment_id+','+data.inserted_id+')"><span class="icon icon-cancel-1"></span></a>';
                    appendHtml+= '        </span>';
                    appendHtml+= '    </span>';
                    appendHtml+= '</li>';

                   $('#append_answer').append(appendHtml);
                }
        });
    }

    /* Discussion js starts here */
    /* Created by Yadu Chandran */

    $(document).on('click','#add_discussion',function(){

        var discussion_title  = $('#add_discussion_title').val();
            discussion_title  = discussion_title.trim();
            discussion_title  = $.trim( $('#add_discussion_title').val() );

        var discussion_topic  = $('#add_discussion_input').val();
            discussion_topic  = discussion_topic.trim();
            discussion_topic  = $.trim( $('#add_discussion_input').val() );
         if(discussion_title!=''){
             $.ajax({
                url: admin_url+'course/post_new_discussion',
                type: "POST",
                data:{"is_ajax":true,'course_id' : __course_id,'discussion_title':discussion_title, 'discussion_comment':discussion_topic},
                success: function(response) {
                    $('#ask_question').modal('hide');
                    var  appendNewDisc =  '';
                    var data           = $.parseJSON(response);  
                    $('#add_discussion_title').val('');
                    $('#add_discussion_input').val('');
                    $("#add_discussion_input").redactor('code.set', ''); 
                    $('#add_discussion').prop('disabled', true);
                    $("#add_discussion").css('cursor','not-allowed');
                    
                    appendNewDisc+= '<div class="rTableRow ques-list" id="'+data.inserted_id+'">';
                    appendNewDisc+= '  <div class="rTableCell individual-question"><span class="question-avatar nopad">';
                    appendNewDisc+= '<img src="'+((data.user_details.us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.user_details.us_image+'" alt="'+data.user_details.us_name+'" width="40" height="40"></span>';
                   
                    appendNewDisc+= '   <span class="question-description">';

                    appendNewDisc+= '<div class="archive-question">'+discussion_title+'</div>';
                     if(discussion_topic!=''){
                        appendNewDisc+= '       <div class="archive-answer">'+discussion_topic+'</div>';
                    }
                    appendNewDisc+= '   </span>'

                    appendNewDisc+= '</div>';
                    appendNewDisc+= '<div class="rTableCell pos-rel">';
                    appendNewDisc+=    '<span class="active-arrow"></span>';
                    appendNewDisc+= '</div>'
                    appendNewDisc+= '</div>';


                    //if($('.fx-c').length == 0)
                    //{
                        $('#left_discussion_content').prepend(appendNewDisc);    
                    //}
                   // // else
                   //  {
                   //      $('#left_discussion_content').html(appendNewDisc);    
                   //  }
                    $("html, body").animate({ scrollTop: 0 }, "slow");  
                    $('#'+data.inserted_id).trigger('click');
                }
            });
         }
    });


     /* Delete Discussion onclick */      
    $(document).on('click','.delete_discussion',function(){     
        $('#delete_comment').modal('show');     
        $('#confirm_box_content_1').html('Are you sure you want to delete this discussion ?');      
    });     
                
    $(document).on('click','.delete_comment',function(){        
        $('#delete_comment').modal('show');     
        $('#confirm_box_content_1').html('Are you sure you want to delete this comment ?');     
    });  

    function setID(parent_id,child_id)      
    {       
        $('#modal_parent_id').val(parent_id);       
        $('#modal_child_id').val(child_id);     
    }

    /* Delete section STARTS */
    function deleteCommentUser()
    {
        var parent_id = $('#modal_parent_id').val();
        var child_id  = $('#modal_child_id').val();

        $.ajax({
                url: admin_url+'course/delete_comments_admin',
                type: "POST",
                data:{"is_ajax":true,'course_id' : __course_id, 'parent_id':parent_id,'child_id':child_id},
                success: function(response) {
                    if(child_id>0){
                        $('#'+child_id).remove();               
                    }
                    if(child_id==0){
                        $('[id="'+parent_id+'"]').remove();
                        setTimeout(function(){ $('div.ques-list:first').trigger('click');
                        }, 500);
                    }
                    // if($('#q_count').html()=="0")
                    // {
                    //     $('.discussions').fadeIn('slow');
                    //     $('.question-detail').hide();
                    //     $('#show_parent').html(renderNullHtml());
                    // }

                    // if($('ul .single-question').length == 0)
                    // {
                    //     $('.discussions').fadeIn('slow');
                    //     $('.question-detail').hide();
                    // }

                    $('#delete_comment').modal('hide');

                }
       });
    }
    /* Delete section ends */

        /* Search textbox keyup function */
        var __questionSearchTimeOut = null;
        $(document).on('click','#basic-addon2',function(){
            clearInterval(__questionSearchTimeOut);
            __questionSearchTimeOut = null;
            proceedToQuestionSearch();
        });
        
        function questionTimeOutSearch()
        {
            __questionSearchTimeOut = setInterval(function(){
                proceedToQuestionSearch();
            }, 2000);                    
        }


        function proceedToQuestionSearch()
        {
            var keyword = $('#search_user').val();
            $.ajax({
            url: admin_url+'course/commented_users_json',
            type: "POST",
            data:{"is_ajax":true,'course_id' : __course_id, 'keyword':keyword},
                success: function(response) {
                    var data           = $.parseJSON(response);
                    if(data.course_comments.length > 0){
                        $('#left_discussion_content').html(renderNewHtml(response));
                        if(__questionSearchTimeOut == null)
                        {
                            $('#show_discussion_div').html('');
                            setTimeout(function(){ 
                                $('div.ques-list:first').trigger('click');
                            }, 500);
                        }
                    }
                    else
                    {
                        //$('#show_parent').html(renderNullHtml());
                    }
                    if(__questionSearchTimeOut == null)
                    {
                        questionTimeOutSearch();
                    }
                }
            });
        }
        
        
        function renderNewHtml(response)
        {
            var data    = $.parseJSON(response);
            var newHtml = '';
            if(data.course_comments.length > 0 )
            {
                for(var i=0; i<data.course_comments.length; i++)
                {
                    newHtml+= '<div class="rTableRow ques-list" id="'+data.course_comments[i].id+'">';
                    newHtml+= '<div class="rTableCell individual-question">'; 
                    newHtml+= '     <span class="question-avatar nopad"><img src="'+((data.course_comments[i].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.course_comments[i].us_image+'" alt="'+data.course_comments[i].us_name+'" width="40" height="40"></span>';
                    newHtml+= '    <span class="question-description">';
                    newHtml+= '<div class="archive-question">'+data.course_comments[i].comment_title+'</div>';
                    newHtml+= ' <div class="archive-answer">'+data.course_comments[i].comment+'</div>';
                    newHtml+= '   </span>';
                    newHtml+= '</div>';
                    newHtml+= '<div class="rTableCell pos-rel">';
                    newHtml+= '<span class="active-arrow"></span>';
                    newHtml+= '</div></div>';
                }
            }
            return newHtml;
        }

        function renderNullHtml()
        {
            var Html = '';
                Html+= '<div class="fx-c p20" style="">';
                Html+= '                <div class="fx tac">';
                Html+= '                    <div class="bold pt10">';
                Html+= '                        <span>No related questions found for this search</span>';
                Html+= '                    </div>';
                Html+= '               <div translate="">';
                Html+= '                     <span>Be the first to ask your question! You’ll be able to add details in the next step.</span>';
                Html+= '               </div>';
                Html+= '             <img src="'+theme_url+'/images/raise_your_hand.png" alt="img">';
                Html+= '        </div> </div>';
                return Html;
        }
    /* search function ends here */

     /* Formatting  time */      
        function timeSince(date) {      

            var seconds = Math.floor((new Date() - date) / 1000);       
            if(seconds=='0')        
            {       
                seconds = 1;        
            }       
            var interval = Math.floor(seconds / 31536000);      
            if (interval >= 1) {        
                return interval + " years ago";     
            }       
            interval = Math.floor(seconds / 2592000);       
            if (interval >= 1) {        
                return interval + " months ago";        
            }       
            interval = Math.floor(seconds / 86400);     
            if (interval >= 1) {        
                return interval + " days ago";      
            }       
            interval = Math.floor(seconds / 3600);      
            if (interval >= 1) {        
                return interval + " hours ago";     
            }       
            interval = Math.floor(seconds / 60);        
            if (interval >= 1) {        
                return interval + " minutes ago";       
            }       
            
            return Math.floor(seconds) + " seconds ago";        
        }       
        /* formatting time ends*/

















$(document).ready(function() {
     var limit               = 5;
});
$(document).ready(function(){

        // comment_id_hash      = window.location.href;
        // comment_id_hash      = comment_id_hash.substring(comment_id_hash.lastIndexOf('/') + 1);
        // children_id_hash     = window.location.hash.substr(1);
        // comment_id_hash      = comment_id_hash.replace('#'+children_id_hash,'');
        // if(children_id_hash){
        //     $('#parent_hidden').val(comment_id_hash);
        //     $('#child_hidden').val(children_id_hash);
        //     alert(1)
        //     $('div#'+children_id_hash).trigger('click');
            // $.ajax({
            //     url: admin_url+'course/get_discussion_hash',
            //     type: "POST",
            //     data:{"is_ajax":true,'course_id' : __course_id, 'parent_id':comment_id_hash, 'child_id':children_id_hash},
            //     success: function(response) {
            //         var data           = $.parseJSON(response);
            //         //console.log(response);
            //         limit        = data.size_children_comments[comment_id_hash].length;
            //         var numClick = Math.ceil(data.size_children_comments[comment_id_hash].length/5);
            //         var  i;
                   
            //             for (i = 1; i <= numClick; i++) { 
            //                  setTimeout(function(){ 
            //                 $("#view_previous_"+comment_id_hash).trigger('click'); }, 3000);
            //             }
            //     }
            // });
       // }
    //window.location = webConfigs('user_path');

    //$("html, body").animate({ scrollTop: total_height }, "slow");
    
});
/* Function for search comments */

/* Function for search comments */

/* Function for search comments */
function getCommentedUsers()
{
    var keyword  = $('#search_user').val();
    $.ajax({
            url: admin_url+'course/commented_users_json',
            type: "POST",
            data:{"is_ajax":true,'course_id' : __course_id, 'keyword':keyword},
            success: function(response) {
                var data           = $.parseJSON(response);
                //console.log(response);
                if(data.course_comments.length > 0){
                    $('#dis_commented_users').html(renderSubscribersHtml(response));
                    scrollToTopOfPage();
                }
                else{
                    //$('#dis_commented_users').html(renderPopUpMessage('error', 'No Discussions found.'));
                    $('#popUpMessage').remove();
                    var template     = 'error';
                    var errorClass   = (template=='error')?'danger':'success';
                    var messageHtml  = '';
                    messageHtml += '<div id="popUpMessage" class="alert alert-'+errorClass+'">';
                    messageHtml += '<a data-dismiss="alert" class="close">×</a>';
                    messageHtml += 'No Discussions found for this search';
                    messageHtml += '</div>';
                    //return messageHtml;
                    $('#dis_commented_users').html(messageHtml);
                    scrollToTopOfPage();
                }
            }
    });
}
/* Function for rendering comments */
function renderSubscribersHtml(response)
{
    var data           = $.parseJSON(response);
    var commentHtml    = '';
    if(data.course_comments.length > 0 )
    {
        for (var i=0; i<data.course_comments.length; i++)
        {
            var monthNames = [
              "January", "February", "March",
              "April", "May", "June", "July",
              "August", "September", "October",
              "November", "December"
            ];
            var date        = new Date(data.course_comments[i].created_date);
            var day         = date.getDate();
            var monthIndex  = date.getMonth();
            var year        = date.getFullYear();
            var hours       = date.getHours();
            var minutes     = date.getMinutes();
            var ampm        = hours >= 12 ? 'pm' : 'am';
            hours           = hours % 12;
            hours           = hours ? hours : 12; // the hour '0' should be '12'
            minutes         = minutes < 10 ? '0'+minutes : minutes;
            var strTime     = hours + ':' + minutes + ' ' + ampm;
            var dateOnly    = monthNames[monthIndex]+' '+' '+day+','+' '+year;
            var newDate     = monthNames[monthIndex]+' '+' '+day+','+' '+year+' '+strTime;

            
            var todayDate        = new Date();
            var yesDate          = new Date(new Date().setDate(new Date().getDate()-1));

            var yesday           = yesDate.getDate();
            var yesmonthIndex    = yesDate.getMonth();
            var yesyear          = yesDate.getFullYear();

            var todayday         = todayDate.getDate();
            var todaymonthIndex  = todayDate.getMonth();
            var todayyear        = todayDate.getFullYear();
            var todayFullDate    = monthNames[todaymonthIndex]+' '+' '+todayday+','+' '+todayyear;
            var yestFullDate     = monthNames[yesmonthIndex]+' '+' '+yesday+','+' '+yesyear;
            //console.log(yesDate);
            if(dateOnly==todayFullDate){
                newDate = 'Today'+ ' '+strTime;
            }
            if(yestFullDate==dateOnly){
                newDate = 'Yesterday'+ ' '+strTime;
            }
            commentHtml+= '<div class="row discussion-container listing-discuss course-cont-wrap pad0" id="'+data.course_comments[i].id+'">';
            commentHtml+= '		<div class="col-sm-12 bg-white">';
            commentHtml+= '			<div class="right-group-wrap full-thread clearfix">';
            commentHtml+= '			<span class="closebtn icon icon-cancel-1" onclick="deleteCommentAdmin('+data.course_comments[i].id+',0)"></span>';
            commentHtml+= '			<img class="box-style" src="'+((data.course_comments[i].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.course_comments[i].us_image+'" alt="'+data.course_comments[i].us_name+'">';
            commentHtml+= '			<span class="user-date">';
            commentHtml+= '				<span class="user">';
            commentHtml+= ' '+data.course_comments[i].us_name;   
            commentHtml+= '			</span><br>';
            commentHtml+= '<span class="date">';
            commentHtml+= ''+newDate;
            commentHtml+= '</span>';
            commentHtml+= '</span>';
            commentHtml+= '<div class="content-text">';
            commentHtml+= ''+data.course_comments[i].comment;
            commentHtml+= '</div>';
            commentHtml+= '</div>';
            commentHtml+= '</div>';
            //commentHtml+= '</div>'; 
            data.children_comments[data.course_comments[i].id] = data.children_comments[data.course_comments[i].id].reverse();
            if(data.children_comments[data.course_comments[i].id].length > 0 )
            {
				if(data.size_children_comments[data.course_comments[i].id].length > 2) {
                    commentHtml+= '<div class="col-sm-12 no-bg-discussion">'; 
                    commentHtml+= '<div>';
					if(data.size_children_comments[data.course_comments[i].id].length !=data.children_comments[data.course_comments[i].id].length) {
                    	commentHtml+= '<a class="prev-comm link-style txt-underline" id="view_previous_'+data.course_comments[i].id+'" onclick="loadComments('+data.course_comments[i].id+')">View previous comments</a>';
					}
                    commentHtml+= '<span class="num-of-total pull-right cert_sett" id="span_'+data.course_comments[i].id+'"><span id="showing_'+data.course_comments[i].id+'">'+data.children_comments[data.course_comments[i].id].length+'</span> of <span id="total_'+data.course_comments[i].id+'">'+data.size_children_comments[data.course_comments[i].id].length+'</span></span>';
                    commentHtml+= '</div>';
                    commentHtml+= '</div>';
				  }
                    commentHtml+= '<div id="append_'+data.course_comments[i].id+'">';
                for (var j=0; j<data.children_comments[data.course_comments[i].id].length; j++)
                {
                    var childmonthNames = [
                      "January", "February", "March",
                      "April", "May", "June", "July",
                      "August", "September", "October",
                      "November", "December"
                    ];
                    var childDate        = new Date(data.children_comments[data.course_comments[i].id][j].created_date);
                    var childday         = childDate.getDate();
                    var childmonthIndex  = childDate.getMonth();
                    var childyear        = childDate.getFullYear();
                    var childhours       = childDate.getHours();
                    var childminutes     = childDate.getMinutes();
                    var childampm        = childhours >= 12 ? 'pm' : 'am';
                    childhours           = childhours % 12;
                    childhours           = childhours ? childhours : 12; // the hour '0' should be '12'
                    childminutes         = childminutes < 10 ? '0'+childminutes : childminutes;
                    var childstrTime     = childhours + ':' + childminutes + ' ' + childampm;
                    var childDateOnly    = childmonthNames[childmonthIndex]+' '+' '+childday+','+' '+childyear;
                    var childnewDate     = childmonthNames[childmonthIndex]+' '+' '+childday+','+' '+childyear+' '+childstrTime;

                    var todayDate        = new Date();
                    var yesDate          = new Date(new Date().setDate(new Date().getDate()-1));

                    var yesday           = yesDate.getDate();
                    var yesmonthIndex    = yesDate.getMonth();
                    var yesyear          = yesDate.getFullYear();

                    var todayday         = todayDate.getDate();
                    var todaymonthIndex  = todayDate.getMonth();
                    var todayyear        = todayDate.getFullYear();
                    var todayFullDate    = childmonthNames[todaymonthIndex]+' '+' '+todayday+','+' '+todayyear;
                    var yestFullDate     = childmonthNames[yesmonthIndex]+' '+' '+yesday+','+' '+yesyear;
                    //console.log(yesDate);
                    if(childDateOnly==todayFullDate){
                        childnewDate = 'Today'+ ' '+childstrTime;
                    }
                    if(yestFullDate==childDateOnly){
                        childnewDate = 'Yesterday'+ ' '+childstrTime;
                    }
                    commentHtml+= '<hr id="hr_'+data.children_comments[data.course_comments[i].id][j].id+'">';
                    commentHtml+= '<div class="col-sm-12" id="'+data.children_comments[data.course_comments[i].id][j].id+'">';
                    commentHtml+= '	<div class="right-group-wrap old-chat clearfix">';
                    commentHtml+= '	 <span class="closebtn-cmt icon icon-cancel-1" onclick="deleteCommentAdmin('+data.course_comments[i].id+','+data.children_comments[data.course_comments[i].id][j].id+')"></span>';
                    commentHtml+= '	 <img class="box-style" src="'+((data.children_comments[data.course_comments[i].id][j].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.children_comments[data.course_comments[i].id][j].us_image+'" alt="'+data.children_comments[data.course_comments[i].id][j].us_name+'">';
					commentHtml+= '		<span class="user-date">';
                    commentHtml+= '			<div class="content-text comment-box">';
                    commentHtml+= '				<span class="user"><a href="">'+data.children_comments[data.course_comments[i].id][j].us_name+'</a></span>';
                    commentHtml+= '				'+data.children_comments[data.course_comments[i].id][j].comment;
                    commentHtml+= '			</div>';
                    commentHtml+= '			<span class="pull-left date comment-date">';
                    commentHtml+= '			'+childnewDate;
                    commentHtml+= '		</span>';
                    commentHtml+= '	 </span>';
                    commentHtml+= '</div>';
                    commentHtml+= '</div>';

                }
                    commentHtml+= '</div>';
            }
                    commentHtml+= '<div class="col-sm-12">';
                    commentHtml+= '	<div class="right-group-wrap old-chat clearfix">';
                    commentHtml+= '	<img class="box-style" src="'+((data.admin_details.us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.admin_details.us_image+'" alt="'+data.admin_details.us_name+'">';
                    commentHtml+= '			<span class="user-date">';
                    commentHtml+= '				<div class="form-group">';
                    commentHtml+= '				  <div class="col-sm-12 no-padding">';
                    commentHtml+= '					<input data-id="'+data.course_comments[i].id+'" class="form-control admin-comment" id="" maxlength="1000">';
                    commentHtml+= '				  </div>';
                    commentHtml+= '				</div>';
                    commentHtml+= '			</span>';
                    commentHtml+= '	</div>';
                    commentHtml+= '	</div>';
                    commentHtml+= '</div>';
        }
        return commentHtml;
    }
}

/* Function for posting admin comments */
$(document).on('keyup', '.admin-comment', function(e){
    var text_id = $(this).data('id'); //getter
    if(e.keyCode=='13'){
        if(this.value!=""){
            postAdminComments(text_id,this.value);
            $(this).val('');
        }
    }
});

/* Function for posting admin comments in previous comments loading */
$(document).on('keyup', '.admin-comment-previous', function(e){
    var text_id_new = $(this).data('id'); //getter
    var value_comment  = $.trim(this.value);
    if(e.keyCode=='13'){
        if(this.value!=""){
            postAdminCommentsPrevious(text_id_new,value_comment);
            $(this).val('');
        }
    }
});

/* Function for posting admin comments */
function postAdminComments(comment_id,value){
    var childDate        = new Date();
    var childday         = childDate.getDate();
    var childmonthIndex  = childDate.getMonth();
    var childyear        = childDate.getFullYear();
    var childhours       = childDate.getHours();
    var childminutes     = childDate.getMinutes();
    var childampm        = childhours >= 12 ? 'pm' : 'am';
    childhours           = childhours % 12;
    childhours           = childhours ? childhours : 12; // the hour '0' should be '12'
    childminutes         = childminutes < 10 ? '0'+childminutes : childminutes;
    var childstrTime     = childhours + ':' + childminutes + ' ' + childampm;
    $.ajax({
            url: admin_url+'course/post_admin_comment',
            type: "POST",
            data:{"is_ajax":true,'course_id' : __course_id, 'comment_id':comment_id,'comment':value},
            success: function(response) {
                var data       = $.parseJSON(response);
				var appendHtml = '';
				appendHtml+= '<hr id="hr_'+data.inserted_id+'">';
				appendHtml+= '<div class="col-sm-12" id="'+data.inserted_id+'">';
				appendHtml+= '	<div class="right-group-wrap old-chat clearfix">';
				appendHtml+= '		<span class="closebtn-cmt icon icon-cancel-1" onclick="deleteCommentAdmin('+comment_id+','+data.inserted_id+')"></span>';
				appendHtml+= '		<img class="box-style" src="'+((admin_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+admin_image+'" alt="'+admin_name+'">';
				appendHtml+= '				<span class="user-date">';
				appendHtml+= '					<div class="content-text comment-box">';
				appendHtml+= '						<span class="user"><a href="">'+admin_name+'</a></span>';
				appendHtml+= '						'+value;
				appendHtml+= '					</div>';
				appendHtml+= '					<span class="pull-left date comment-date">';
				appendHtml+= 'Today'+' 	'+childstrTime+'';
				appendHtml+= '					</span>';
				appendHtml+= '				</span>';
				appendHtml+= '	</div>';
				appendHtml+= '</div>';
				$('#append_'+comment_id).append(appendHtml);
				$('#showing_'+comment_id).html(+$('#showing_'+comment_id).html() + +1);
				$('#total_'+comment_id).html(+$('#total_'+comment_id).html() + +1);
            }
    });
}

/* Function for posting admin comments in previous section*/
function postAdminCommentsPrevious(comment_id,value){

    var childDate        = new Date();
    var childday         = childDate.getDate();
    var childmonthIndex  = childDate.getMonth();
    var childyear        = childDate.getFullYear();
    var childhours       = childDate.getHours();
    var childminutes     = childDate.getMinutes();
    var childampm        = childhours >= 12 ? 'pm' : 'am';
    childhours           = childhours % 12;
    childhours           = childhours ? childhours : 12; // the hour '0' should be '12'
    childminutes         = childminutes < 10 ? '0'+childminutes : childminutes;
    var childstrTime     = childhours + ':' + childminutes + ' ' + childampm;
    $.ajax({
            url: admin_url+'course/post_admin_comment',
            type: "POST",
            data:{"is_ajax":true,'course_id' : __course_id, 'comment_id':comment_id,'comment':value},
            success: function(response) {
                var data       = $.parseJSON(response);
				var appendHtml = '';
				appendHtml+= '<hr id="hr_'+data.inserted_id+'">';
				appendHtml+= '<div class="col-sm-12" id="'+data.inserted_id+'">';
				appendHtml+= '	<div class="right-group-wrap old-chat clearfix">';
				appendHtml+= '		<span class="closebtn-cmt icon icon-cancel-1" onclick="deleteCommentAdmin('+comment_id+','+data.inserted_id+')"></span>';
				appendHtml+= '		<img class="box-style" src="'+((admin_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+admin_image+'" alt="'+admin_name+'">';
				appendHtml+= '			<span class="user-date">';
				appendHtml+= '				<div class="content-text comment-box">';
				appendHtml+= '					<span class="user"><a href="">'+admin_name+'</a></span>';
				appendHtml+= '					'+value;
				appendHtml+= '				</div>';
				appendHtml+= '				<span class="pull-left date comment-date">';
				appendHtml+= 'Today'+' 		'+childstrTime+'';
				appendHtml+= '				</span>';
				appendHtml+= '			</span>';
				appendHtml+= '	</div>';
				appendHtml+= '</div>';
			
				$('#append_'+comment_id).append(appendHtml);
				$('#showing_'+comment_id).html(+$('#showing_'+comment_id).html() + +1);
				$('#total_'+comment_id).html(+$('#total_'+comment_id).html() + +1);
            }
    });
}

/* Function for loading previous comments */
var click_view_previous = 0;
var previous_id         = 0;
function loadComments(id){ 
    if(id!=previous_id){
        limit               = 5;
        click_view_previous = 0;
    }
if(click_view_previous=='0'){
    click_view_previous = 1; 
    limit               = limit;
}
else{
    limit               = limit*2;
}
    previous_id         = id;
    $.ajax({
            url: admin_url+'course/load_previous_comments',
            type: "POST",
            data:{"is_ajax":true,'course_id' : __course_id,'discussion_id' : id,'limit_value' : limit},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data.course_comments.length > 0){
                    $('#'+id).html(renderPreviousComments(response));
                }
            }
    });
}

/* Function for rendering previous comments */
function renderPreviousComments(response){
    var data               = $.parseJSON(response);
    var prevcommentHtml    = '';
    if(data.course_comments.length > 0 )
    {
        for (var i=0; i<data.course_comments.length; i++)
        {
            var monthNames = [
              "January", "February", "March",
              "April", "May", "June", "July",
              "August", "September", "October",
              "November", "December"
            ];
            var parentDate  = data.course_comments[i].created_date;
            var parentNew   = parentDate.replace(" ","T");
            var date        = new Date(parentNew);

            var day         = date.getDate();
            var monthIndex  = date.getMonth();
            var year        = date.getFullYear();
            var hours       = date.getHours();
            var minutes     = date.getMinutes();
            var ampm        = hours >= 12 ? 'pm' : 'am';
            hours           = hours % 12;
            hours           = hours ? hours : 12; // the hour '0' should be '12'
            minutes         = minutes < 10 ? '0'+minutes : minutes;
            var strTime     = hours + ':' + minutes + ' ' + ampm;
            var dateOnly    = monthNames[monthIndex]+' '+' '+day+','+' '+year;
            var newDate     = monthNames[monthIndex]+' '+' '+day+','+' '+year+' '+strTime;

            
            var todayDate        = new Date();
            var yesDate          = new Date(new Date().setDate(new Date().getDate()-1));

            var yesday           = yesDate.getDate();
            var yesmonthIndex    = yesDate.getMonth();
            var yesyear          = yesDate.getFullYear();

            var todayday         = todayDate.getDate();
            var todaymonthIndex  = todayDate.getMonth();
            var todayyear        = todayDate.getFullYear();
            var todayFullDate    = monthNames[todaymonthIndex]+' '+' '+todayday+','+' '+todayyear;
            var yestFullDate     = monthNames[yesmonthIndex]+' '+' '+yesday+','+' '+yesyear;
            //console.log(yesDate);
            if(dateOnly==todayFullDate){
                newDate = 'Today'+ ' '+strTime;
            }
            if(yestFullDate==dateOnly){
                newDate = 'Yesterday'+ ' '+strTime;
            }
            prevcommentHtml+= '<div class="col-sm-12 bg-white">';
            prevcommentHtml+= '<div class="right-group-wrap full-thread clearfix">';
            prevcommentHtml+= '<span class="closebtn icon icon-cancel-1" onclick="deleteCommentAdmin('+data.course_comments[i].id+',0)"></span>';
            prevcommentHtml+= '<img class="box-style" src="'+((data.course_comments[i].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.course_comments[i].us_image+'" alt="'+data.course_comments[i].us_name+'">';
            prevcommentHtml+= '<span class="user-date">';
            prevcommentHtml+= '<span class="user">';
            prevcommentHtml+= ' '+data.course_comments[i].us_name;   
            prevcommentHtml+= '</span><br>';
            prevcommentHtml+= '<span class="date">';
            prevcommentHtml+= ''+newDate;
            prevcommentHtml+= '</span>';
            prevcommentHtml+= '</span>';
            prevcommentHtml+= '<div class="content-text">';
            prevcommentHtml+= ''+data.course_comments[i].comment;
            prevcommentHtml+= '</div>';
            prevcommentHtml+= '</div>';
            prevcommentHtml+= '</div>';
            //prevcommentHtml+= '</div>'; 
            data.children_comments[data.course_comments[i].id] = data.children_comments[data.course_comments[i].id].reverse();
            if(data.children_comments[data.course_comments[i].id].length > 0 )
            {
				if(data.size_children_comments[data.course_comments[i].id].length > 2) {
                    prevcommentHtml+= '<div class="col-sm-12 no-bg-discussion">'; 
                    prevcommentHtml+= '<div>';
                    if(data.size_children_comments[data.course_comments[i].id].length !=data.children_comments[data.course_comments[i].id].length) {
                        prevcommentHtml+= '<a class="prev-comm link-style txt-underline" id="view_previous_'+data.course_comments[i].id+'" onclick="loadComments('+data.course_comments[i].id+')">View previous comments</a>';
                    }
					if(data.size_children_comments[data.course_comments[i].id].length !=data.children_comments[data.course_comments[i].id].length) {
                    	prevcommentHtml+= '<span class="num-of-total pull-right cert_sett" id="span_'+data.course_comments[i].id+'"><span id="showing_'+data.course_comments[i].id+'">'+data.children_comments[data.course_comments[i].id].length+'</span> of <span id="total_'+data.course_comments[i].id+'">'+data.size_children_comments[data.course_comments[i].id].length+'</span></span>';
					}
				    prevcommentHtml+= '</div>';
                    prevcommentHtml+= '</div>';
				}
                    prevcommentHtml+= '<div id="append_'+data.course_comments[i].id+'">';
                for (var j=0; j<data.children_comments[data.course_comments[i].id].length; j++)
                {
                    var childmonthNames = [
                      "January", "February", "March",
                      "April", "May", "June", "July",
                      "August", "September", "October",
                      "November", "December"
                    ];
                    var childDate        = data.children_comments[data.course_comments[i].id][j].created_date;
                    var childNew         = childDate.replace(" ","T");
                    var childDate        = new Date(childNew);

                    var childday         = childDate.getDate();
                    var childmonthIndex  = childDate.getMonth();
                    var childyear        = childDate.getFullYear();
                    var childhours       = childDate.getHours();
                    var childminutes     = childDate.getMinutes();
                    var childampm        = childhours >= 12 ? 'pm' : 'am';
                    childhours           = childhours % 12;
                    childhours           = childhours ? childhours : 12; // the hour '0' should be '12'
                    childminutes         = childminutes < 10 ? '0'+childminutes : childminutes;
                    var childstrTime     = childhours + ':' + childminutes + ' ' + childampm;
                    var childDateOnly    = childmonthNames[childmonthIndex]+' '+' '+childday+','+' '+childyear;
                    var childnewDate     = childmonthNames[childmonthIndex]+' '+' '+childday+','+' '+childyear+' '+childstrTime;

                    var todayDate        = new Date();
                    var yesDate          = new Date(new Date().setDate(new Date().getDate()-1));

                    var yesday           = yesDate.getDate();
                    var yesmonthIndex    = yesDate.getMonth();
                    var yesyear          = yesDate.getFullYear();

                    var todayday         = todayDate.getDate();
                    var todaymonthIndex  = todayDate.getMonth();
                    var todayyear        = todayDate.getFullYear();
                    var todayFullDate    = childmonthNames[todaymonthIndex]+' '+' '+todayday+','+' '+todayyear;
                    var yestFullDate     = childmonthNames[yesmonthIndex]+' '+' '+yesday+','+' '+yesyear;
                    //console.log(yesDate);
                    if(childDateOnly==todayFullDate){
                        childnewDate = 'Today'+ ' '+childstrTime;
                    }
                    if(yestFullDate==childDateOnly){
                        childnewDate = 'Yesterday'+ ' '+childstrTime;
                    }
				
					prevcommentHtml+= '<hr id="hr_'+data.children_comments[data.course_comments[i].id][j].id+'">';
                    prevcommentHtml+= '<div class="col-sm-12" id="'+data.children_comments[data.course_comments[i].id][j].id+'">';
                    prevcommentHtml+= '	<div class="right-group-wrap old-chat clearfix">';
                    prevcommentHtml+= '		<span class="closebtn-cmt icon icon-cancel-1" onclick="deleteCommentAdmin('+data.course_comments[i].id+','+data.children_comments[data.course_comments[i].id][j].id+')"></span>';
                    prevcommentHtml+= '		<img class="box-style" src="'+((data.children_comments[data.course_comments[i].id][j].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.children_comments[data.course_comments[i].id][j].us_image+'" alt="'+data.children_comments[data.course_comments[i].id][j].us_name+'">';
					prevcommentHtml+= '		<span class="user-date">';
                    prevcommentHtml+= '			<div class="content-text comment-box">';
                    prevcommentHtml+= '				<span class="user"><a href="">'+data.children_comments[data.course_comments[i].id][j].us_name+'</a></span>';
                    prevcommentHtml+= '				'+data.children_comments[data.course_comments[i].id][j].comment;
                    prevcommentHtml+= '			</div>';
                    prevcommentHtml+= '			<span class="pull-left date comment-date">';
                    prevcommentHtml+= '				'+childnewDate;
                    prevcommentHtml+= '			</span>';
                    prevcommentHtml+= '		</span>';
                    prevcommentHtml+= '</div>';
                    prevcommentHtml+= '</div>';

                }
                    prevcommentHtml+= '</div>';
            }
                    prevcommentHtml+= '<div class="col-sm-12">';
                    prevcommentHtml+= '<div class="right-group-wrap old-chat clearfix">';
                    prevcommentHtml+= '	<img class="box-style" src="'+((data.admin_details.us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+data.admin_details.us_image+'" alt="'+data.admin_details.us_name+'">';
                    prevcommentHtml+= '	<span class="user-date">';
                    prevcommentHtml+= '		<div class="form-group">';
                    prevcommentHtml+= '			<div class="col-sm-12 no-padding">';
                    prevcommentHtml+= '				<input data-id="'+data.course_comments[i].id+'" class="form-control admin-comment-previous" id="" maxlength="1000">';
                    prevcommentHtml+= '			</div>';
                    prevcommentHtml+= '		</div>';
                    prevcommentHtml+= '	</span>';
                    prevcommentHtml+= '</div>';
                    prevcommentHtml+= '</div>';
        }
        return prevcommentHtml;
    }
}

function deleteCommentAdmin(parent_id,child_id){
        $('#delete_confirmation_header').html('DELETE COMMENT');
        $('#delete_confirmation_content').html('Are you sure you want to delete this comment ?');
        $('#delete_confirmation').modal('show');
        $('#modal_parent_id').val(parent_id);
        $('#modal_child_id').val(child_id);
}

$(document).ready(function() {
    $(document).on('click','#continue_btn',function(){
        var parent_id = $('#modal_parent_id').val();
        var child_id  = $('#modal_child_id').val();
        if(child_id>0){
            $('#'+child_id).remove();
			$('#hr_'+child_id).remove();
            $('#showing_'+parent_id).html($('#showing_'+parent_id).html() - 1);
            $('#total_'+parent_id).html($('#total_'+parent_id).html() - 1);
            if($('#total_'+parent_id).html()=='0'){
                $('#span_'+parent_id).css('display','none');
            }
        }
        if(child_id==0){
            $('#'+parent_id).remove();
        }
        $('#delete_confirmation').modal('hide');
        $.ajax({
            url: admin_url+'course/delete_comments_admin',
            type: "POST",
            data:{"is_ajax":true,'course_id' : __course_id, 'parent_id':parent_id,'child_id':child_id},
            success: function(response) {
					if($('#showing_'+parent_id).html()=='0' && $('#total_'+parent_id).html()!='0'){
					$('#view_previous_'+parent_id).trigger('click');
            	}
            }
        });
    });
	
	$(document).on('click','#post_discussion',function(){
		//var discussion_topic = $('#search_user').val();

        var discussion_topic  = $('#search_user').val();
        discussion_topic      = discussion_topic.trim();
        discussion_topic      = $.trim( $('#search_user').val() );
            //discussion_topic = discussion_topic.replace(/[^\w\s]/gi, '');
		var childDate        = new Date();
		var childday         = childDate.getDate();
		var childmonthIndex  = childDate.getMonth();
		var childyear        = childDate.getFullYear();
		var childhours       = childDate.getHours();
		var childminutes     = childDate.getMinutes();
		var childampm        = childhours >= 12 ? 'pm' : 'am';
		childhours           = childhours % 12;
		childhours           = childhours ? childhours : 12; // the hour '0' should be '12'
		childminutes         = childminutes < 10 ? '0'+childminutes : childminutes;
		var childstrTime     = childhours + ':' + childminutes + ' ' + childampm;
		 if(discussion_topic!=''){
			 $.ajax({
				url: admin_url+'course/post_new_discussion_admin',
				type: "POST",
				data:{"is_ajax":true,'course_id' : __course_id, 'discussion_comment':discussion_topic},
				success: function(response) {
					var data           = $.parseJSON(response);  
					$('#search_user').val('');
					var  appendNewDisc =  '';
						 appendNewDisc+=  '<div class="row discussion-container listing-discuss course-cont-wrap pad0" id="'+data.inserted_id+'">';
						 appendNewDisc+=  '<div class="col-sm-12 bg-white">';   
						 appendNewDisc+=  '<div class="right-group-wrap full-thread clearfix">';   
						 appendNewDisc+=  '<span class="closebtn icon icon-cancel-1" onclick="deleteCommentAdmin('+data.inserted_id+',0)"></span>';   
						 appendNewDisc+=  '<img class="box-style" src="'+((admin_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+admin_image+'" alt="'+admin_name+'"">';   
						 appendNewDisc+=  '<span class="user-date">';   
						 appendNewDisc+=  '<span class="user">'+admin_name+'</span><br>';   
						 appendNewDisc+=  '<span class="date">Today'+' '+childstrTime+'</span>';   
						 appendNewDisc+=  '</span>';   
						 appendNewDisc+=  '<div class="content-text">';   
						 appendNewDisc+=  ' '+discussion_topic;   
						 appendNewDisc+=  '</div>';   
						 appendNewDisc+=  '</div>';   
						 appendNewDisc+=  '</div>';   
						 appendNewDisc+=  '<div id="append_'+data.inserted_id+'">';   
						 appendNewDisc+=  '</div>';   
						 appendNewDisc+=  '<div class="col-sm-12">';   
						 appendNewDisc+=  '<div class="right-group-wrap old-chat clearfix">';   
						 appendNewDisc+=  '<img class="box-style" src="'+((admin_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'))+admin_image+'" alt="'+admin_name+'">';   
						 appendNewDisc+=  '<span class="user-date">';   
						 appendNewDisc+=  '<div class="form-group">';   
						 appendNewDisc+=  '<div class="col-sm-12 no-padding">';   
						 appendNewDisc+=  '<input type="text" data-id="'+data.inserted_id+'" class="form-control admin-comment" id="" maxlength="1000">';   
						 appendNewDisc+=  '</div>';   
						 appendNewDisc+=  '</div>';   
						 appendNewDisc+=  '</span>';   
						 appendNewDisc+=  '</div>';   
						 appendNewDisc+=  '</div>';   
						 appendNewDisc+=  '</div>'; 
						 if($("#popUpMessage").length == 0) {
							  $('#dis_commented_users').prepend(appendNewDisc);
						 	  scrollToTopOfPage();
						 }
						 else{
							 $('#dis_commented_users').html('');
							 $('#dis_commented_users').prepend(appendNewDisc);
						 	 scrollToTopOfPage();
						 }
	
				}
    		});
		 }
         else
         {
            lauch_common_message('ALERT','Please enter any topic for discussion');
         }
	});
	
});

    function findParentWrap(elem, parentElem) {
            return $(elem).closest(parentElem);
    }

/* Setting the previous target to check CLICK FUNCTION */
    var previousTarget = null;

    $(".grp-click-fn").on("click",function() {
        var parentElem = findParentWrap(this,".rTableRow");

        /* Checking if clicked element is previously clicked one */
        if (this === previousTarget && !($(".wrap-left-grp.open-grp").length)) {
            $(".wrap-left-grp").addClass("open-grp");
            //$(".rTableCell").removeClass("active-table");
            return false;
        }


        if ($(".wrap-left-grp.open-grp").length) {
            $(".wrap-left-grp").removeClass("open-grp");

            $(".rTableCell").removeClass("active-table");
            $(parentElem).find(".rTableCell:last").addClass("active-table");
        }else{

             $(".rTableCell").removeClass("active-table");
            $(parentElem).find(".rTableCell:last").addClass("active-table");
        }

        /* Assigning the previous Target */
        previousTarget = this;
    })