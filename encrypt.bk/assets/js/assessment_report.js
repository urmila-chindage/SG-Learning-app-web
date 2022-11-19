function select_test(lecture_id, obj){
    //console.log($(obj).text());
    if(obj!="")
    {
        $("#test_name").html(obj+' '+'<span class="caret"></span>');
    }
    if($(obj).text() != ''){
        $("#test_name").html($(obj).text()+' '+'<span class="caret"></span>');
    }
    $('#hidden_lecture_id').val(lecture_id);
    $.ajax({
        method: "POST",
        url: admin_url+'coursebuilder/select_assessment_test',
        data: {
            lecture_id: lecture_id,
        },
        success: function(response){
            if(response=='')
            {
                $("#tblcontent").html('<div id="popUpMessage" class="alert alert-danger" style="text-align:center;">No user results found for this assesment<br></div>');
            }
            else
            {
                $("#tblcontent").html(response);
            }
        }
    });
}

function select_course(course_id, obj){

    $("#course_name").html($(obj).text()+' '+'<span class="caret"></span>');
    $.ajax({
        method: "POST",
        url: admin_url+'coursebuilder/select_assessment_test_course',
        data: {
            course_id: course_id
        },
        success: function(response){
            data = $.parseJSON(response);
            $('[id="test_old"]').remove();
            $("#test_list").html(data.str);
            $("#test_name").html(data.current_name+' '+'<span class="caret"></span>');
            if(data.current_id == ''){
                $("#test_name").html('No Tests Found'+' '+'<span class="caret"></span>');
                $('#hidden_lecture_id').val('');
                $("#tblcontent").html('<div id="popUpMessage" class="alert alert-danger" style="text-align:center;">No assesments found for this course<br></div>');
                // $('#div_export').css('width','120px');
            }
            else{
                select_test(data.current_id);
                $("#test_name").html(data.current_name+' '+'<span class="caret"></span>');
            }
        }
    });
}

$(document).on('click','#export_results',function(){
    var hidden_id = $('#hidden_lecture_id').val();
    if(hidden_id)
    {
        window.location.href = admin_url+'coursebuilder/export_assessment_report/'+hidden_id;
    }
});

function saveexit(){
var ar_mark = 0;
var ar_id   = 0;
var obj     = {};
    $(".descriptiveval").each(function (index){
        ar_mark    = $(this).val();
        ar_id      = $(this).attr('id');
        obj[ar_id] = ar_mark;
     });

    if(ar_id != ''){
        $.ajax({
            method: "POST",
            url: admin_url+'coursebuilder/save_assessment_explanatory',
            data: {
                ar_data: obj,
            },
            async: false,
            success: function(response){
                window.location = admin_url+'coursebuilder/report/'+lecture_id;
            }
        });
    }
    window.location = admin_url+'coursebuilder/report/'+lecture_id;
}

function savecontinue(){
var ar_mark = 0;
var ar_id   = 0;
var obj     = {};
    $(".descriptiveval").each(function (index){
        ar_mark    = $(this).val();
        ar_id      = $(this).attr('id');
        obj[ar_id] = ar_mark;
     });
         
    if(ar_id != ''){
        $.ajax({
            method: "POST",
            url: admin_url+'coursebuilder/save_assessment_explanatory',
            data: {
                ar_data: obj,
            },
            async: false,
            success: function(response){
                var data = $.parseJSON(response);
                window.location = admin_url+'coursebuilder/evaluate_assessment/'+lecture_id+'/'+next_id+'/'+data['ar_attempt_id'];
            }
        });
    }
    else{
        if(next_id != ''){
        window.location = admin_url+'coursebuilder/evaluate_assessment/'+lecture_id+'/'+next_id+'/'+attempt_id;
        }
    }

    if(next_id != ''){
        window.location = admin_url+'coursebuilder/evaluate_assessment/'+lecture_id+'/'+next_id+'/'+attempt_id;
    }
}

function printreport(){
   window.location = admin_url+'coursebuilder/print_assessment/'+lecture_id+'/'+user_id+'/'+attempt_id;
}