var __checked = 'checked="checked"';
$(document).on('click', '#basic-addon2', function(){
    __offset = 1;
    getQuestions();
});

//Get category list for Auto suggest
var current = '';
var timeOut = '';
$(document).on('keyup', '#q_category', function(){
    clearTimeout(timeOut);
    timeOut = setTimeout(function(){ 
        var keyword = $('#q_category').val();
        var url 	= admin_url+'generate_test/get_question_category_list';
        var tagHTML	= '';
        current 	= this;
        if( keyword ){
            $.ajax({
                    url: url,
                    type: "POST",
                    data:{ 'q_category':keyword, 'is_ajax':true},
                    success: function (response){
                        var data    = $.parseJSON(response);
                        if( data['tags'].length > 0 ){
                            for( var i = 0; i < data['tags'].length; i++){
                                tagHTML += '<li id="'+data['tags'][i]['id']+'">'+data['tags'][i]['qc_category_name']+'</li>';
                            }
                        }
                        $("#listing_question_category").html(tagHTML).show();       
                    },
            });
        }
    }, 600);
});

var current_li = '';
$(document).on('click', '.auto-search-lister li', function(){
    $('#q_category').val($(this).text());
    $(this).parent().html('').hide();
});

var __filter_dropdown = '';
var __category_id     = '';
var __topic_id        = '';

var __offset        = 2;
var __requestInProgress = false;
function getQuestions()
{
    if(__requestInProgress == true)
    {
        return false;
    }
    $('#load_more_question').html('Loading Question...<ripples></ripples>');
    __requestInProgress = true;
    var keyword  = $('#generate_questions_keyword').val();
    $.ajax({
        url: admin_url+'generate_test/generate_questions_json',
        type: "POST",
        data:{"is_ajax":true, "filter":__filter_dropdown, 'offset':__offset, "category_id":__category_id, "topic_id":__topic_id , "keyword":keyword},
        success: function(response) {
            var data = $.parseJSON(response);
            var remainingQuestion = 0;
            $('#load_more_question').hide();
            if(data['questions'].length > 0){
                 __offset++;
                if(__offset == 2)
                {
                    remainingQuestion = (data['total_questions'] - data['questions'].length);
                    var totalQuestionsHtml = data['questions'].length+' / '+data['total_questions']+' '+((data['total_questions'] == 1)?"Question":"Questions");
                    scrollToTopOfPage();
                    $('.question-count').html(totalQuestionsHtml);
                    $('#generate_test_wrapper').html(renderQuestionsHtml(response));
                }
                else
                {
                    remainingQuestion = (data['total_questions'] - (((__offset-2)*data['limit'])+data['questions'].length));
                    var totalQuestionsHtml = (((__offset-2)*data['limit'])+data['questions'].length)+' / '+data['total_questions']+' Questions';
                    $('.question-count').html(totalQuestionsHtml);
                    $('#generate_test_wrapper').append(renderQuestionsHtml(response));                     
                }
            }else{
                $('.question-count').html("No Questions");
                $('#generate_test_wrapper').html(renderPopUpMessage('error', 'No Questions found.'));
            }
            if(data['show_load_button'] == true)
            {
                $('#load_more_question').show();
            }
            remainingQuestion = (remainingQuestion>0)?'('+remainingQuestion+')':'';
            $('#load_more_question').html('Load More Question '+remainingQuestion+'<ripples></ripples>');
            __requestInProgress = false;
        }
    });
}

function renderQuestionsHtml(response)
{
    var data        = $.parseJSON(response);
    var questionsHtml  = '';
    var j = '';
    
    if(data['questions'].length > 0 )
    {
        for (var i=0; i<data['questions'].length; i++)
        {
            j = i + 1;
            var question = data['questions'][i]['q_question'];
            var question_html_stripped = question.replace(/(<([^>]+)>)/ig,"");
            var question_short = (question_html_stripped.length > 80)?question_html_stripped.substr(0,77)+'...':question_html_stripped;
            var q_type = {1:"Single Choice", 2:"Multiple Choice", 3:"Subjective"};
            
            questionsHtml += '<div class="default-view-txt m0 test-folder" style="float: none; padding: 7px;" id="question_wrapper_'+data['questions'][i]['id']+'">';
            questionsHtml += '  <input '+((typeof __importQuestionIds[data['questions'][i]['id']] != 'undefined')?__checked:'')+' type="checkbox" class="import-questions" value="'+data['questions'][i]['id']+'">';
            questionsHtml += '  <span class="question-sl-no">'+((data['limit']*(__offset-2))+j)+' .'+'</span>';
            questionsHtml += '  '+question_short+'';
            questionsHtml += '  <span class="question-type">'+q_type[data['questions'][i]['q_type']]+'</span>';
            questionsHtml += '</div>';
        }
    }
    return questionsHtml;
}

function filter_category(category_id)
{
   __category_id        = category_id;
   $('#filter_dropdown_text_category').html($('#dropdown_list_'+category_id).text()+'<span class="caret"></span>');
   __offset = 1;
   getQuestions();
}

function filter_topics(topic_id)
{
   __topic_id        = topic_id;
   $('#filter_dropdown_text_topics').html($('#dropdown_topic_list_'+topic_id).text()+'<span class="caret"></span>');
   __offset = 1;
   getQuestions();
}

function filter_generate_test_by(filter)
{
   __filter_dropdown        = filter;
   $('#filter_dropdown_text_difficulty').html($('#filer_dropdown_list_'+filter).text()+'<span class="caret"></span>');
   __offset = 1;
   getQuestions();
}


var __importQuestionIds = {};
var __questionIdChecked = 0;
$(document).on('change', '.import-questions', function(){
    __questionIdChecked = $(this).val();
    if($(this).prop('checked') == true)
    {
        __importQuestionIds[__questionIdChecked] = __questionIdChecked;
    }
    else
    {
        delete __importQuestionIds[__questionIdChecked];
    }
    if(Object.keys(__importQuestionIds).length > 0 )
    {
        $('#import_question_confirmed').removeClass('disabled');
    }
    else
    {
        $('#import_question_confirmed').addClass('disabled');        
    }
});

var __importQuestionInProgress = false;
function importQuestionToAssessment()
{
    alert(__lectureId);
    $.ajax({
        url: admin_url+'generate_test/import_question',
        type: "POST",
        data:{"is_ajax":true, "question_ids":JSON.stringify(__importQuestionIds), 'assessment_id':__assessmentId, 'lectureId':__lectureId},
        success: function(response) {
            var data = $.parseJSON(response);
            if(data['error'] == false)
            {
                location.href = admin_url+'coursebuilder/lecture/'+__lectureId;
            }
            else
            {
                lauch_common_message('Something went Wrong' , 'Please try to import question again!!');
            }
            __importQuestionInProgress = false;
        }
    });
}