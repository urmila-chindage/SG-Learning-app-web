function select_test(challenge_id, obj=''){
    console.log($(obj).text());
    if($(obj).text() != ''){
        $("#test_name").text($(obj).text());
    }
    $.ajax({
        method: "POST",
        url: admin_url+'challenge_zone/select_test',
        data: {
            challenge_id: challenge_id,
        },
        success: function(response){
            $("#tblcontent").html(response);
        }
    });
}

function select_course(cattegory_id, obj){

    $("#course_name").text($(obj).text());
    $.ajax({
        method: "POST",
        url: admin_url+'challenge_zone/select_category',
        data: {
            cattegory_id: cattegory_id
        },
        success: function(response){
            data = $.parseJSON(response);
            $("#test_list").html(data.str);
            $("#test_name").text(data.current_name);
            if(data.current_id == ''){
                $("#tblcontent").html('');
            }
            else{
                select_test(data.current_id);
            }
        }
    });
}

function saveexit(){
var czr_mark = 0;
var czr_id   = 0;
    $(".descriptiveval").each(function (index){
        czr_mark = $(this).val();
        czr_id   = $(this).attr('id');
        if(czr_id != ''){
            $.ajax({
                method: "POST",
                url: admin_url+'challenge_zone/save_explanatory',
                async: false,
                data: {
                    czr_id: czr_id,
                    czr_mark: czr_mark
                },
                success: function(response){
                    window.location = admin_url+'challenge_zone/report/'+challenge_id;
                }
            });
        }
    });
    window.location = admin_url+'challenge_zone/report/'+challenge_id;
}

function savecontinue(){
var czr_mark = 0;
var czr_id   = 0;
    $(".descriptiveval").each(function (index){
        czr_mark = $(this).val();
        czr_id   = $(this).attr('id');

        if(czr_id != ''){
            $.ajax({
                method: "POST",
                url: admin_url+'challenge_zone/save_explanatory',
                data: {
                    czr_id: czr_id,
                    czr_mark: czr_mark
                },
                async: false,
                success: function(response){
                    if(next_id != ''){
                        window.location = admin_url+'challenge_zone/evaluate_challenge/'+challenge_id+'/'+next_id;
                    }
                }
            });
        }
        
    });

    if(next_id != ''){
        window.location = admin_url+'challenge_zone/evaluate_challenge/'+challenge_id+'/'+next_id;
    }
}

function printreport(){
   window.location = admin_url+'challenge_zone/print_challenge/'+challenge_id+'/'+user_id;
}