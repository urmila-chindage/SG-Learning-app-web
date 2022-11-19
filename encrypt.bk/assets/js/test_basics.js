$(document).ready(function(){
    __languages         = $.parseJSON(__languages);
    $("#a_instruction").redactor({
        focus: true,
        minHeight: 400,
        maxHeight: 400,
        callbacks: {
            init: function()
            {
                this.code.set($('#a_instruction').val());
            }
        }
    });
});

function loadInstr(elem){
    //$("#a_instruction").redactor('code.set', atob(__instruction[$(elem).attr('name')]));
    window.location.href = webConfigs('admin_url')+'test_manager/change_language/'+$(elem).find(":selected").val();
}

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function saveNext(){
    $('#savenextform').val('1');
    $('#submittedform').val('1');
    if(validateStep('step-one')){
        var total_mark = $("#test_mark").val();
        if(total_mark!=__tot_mark){
            var messageObject = {
                'body': 'Total mark in step 3 will be updated as final total mark, as it differs from step 1.Would you like to continue?',
                'button_yes':'CONTINUE', 
                'button_no':'CANCEL'
            };
            callback_warning_modal(messageObject,submitToContinue);
        } else {
            $('#save_test_basics').submit();
        }
        
    }else{
        return false;
    }

}

function save(){
    $('#savenextform').val('0');
    $('#submittedform').val('1');
    if(validateStep('step-one')){
        var total_mark = $("#test_mark").val();
        if(total_mark!=__tot_mark){
            var messageObject = {
                'body': 'Total mark in step 3 will be updated as final total mark, as it differs from step 1.Would you like to continue?',
                'button_yes':'CONTINUE', 
                'button_no':'CANCEL'
            };
            callback_warning_modal(messageObject,submitToContinue);
        } else {
            $('#save_test_basics').submit();
        }
    }else{
        return false;
    }

}

function submitToContinue(){
    $('#save_test_basics').submit();
}


function validateStep(step){ 
    switch(step){
        case 'step-one':
            var msg = '';
            
            if($('#test_name').val() == ''){
                msg = 'Please enter a valid quiz name.';
                
            }
            console.log(($('#lecture_logo_btn')[0].files[0]));

            if( cb_has_lecture_image == 1  && ($('#lecture_image_add').attr('image_name') == 'default-lecture.jpg') && (($('#lecture_logo_btn')[0].files[0] == undefined)) )
            {
                msg += 'Please upload lecture image.<br>';
            }
            if($('#test_duration').val() == 0 || $('#test_duration').val() == ''){
                msg = 'Please enter a valid quiz duration.';
            }
            if($('#test_difficulty').find(":selected").val() == 0){
                msg = 'Please select a difficulty level.';
            }
            if($('#instruction_template').find(":selected").val() == 0){
                msg = 'Please select a quiz instruction.';
            }
            if($('#cl_limited_access').val() == ''){
                msg = 'Please enter a valid quiz attempts';
            }
            if(msg!=''){
                var messageObject = {
                    'body': msg,
                    'button_yes':'OK', 
                    'button_no':'CANCEL'
                };
                callback_warning_modal(messageObject);
                return false;
            }
            
        break;
    }

    return true;
}

