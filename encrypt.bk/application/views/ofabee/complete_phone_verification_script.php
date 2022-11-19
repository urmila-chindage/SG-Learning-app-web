<script type="text/javascript">
    var __modalCond = 0;
    function step_edit_mobile(){
        $('#mobile_form').val($.trim($('#myphone').text()));
        $('#myphone, #mobile_edit,#mobile_verify').hide();
        $('#mobile_form,#mobile_save,#mobile_cancel').show();
    }

    function cancel_step_edit_mobile(){
        $('#myphone,#mobile_edit,#mobile_verify').show();
        $('#mobile_cancel,#mobile_save,#mobile_form').hide();
    }
    
    function step_save_mobile_edit(){
        $('#mobile_save').text('Saving..');
        var old_number = $.trim($('#myphone').text());
        var new_number = $('#mobile_form').val();
        //console.log('Old :'+old_number);
        //console.log('New :'+new_number);
        if(old_number != new_number){
            if(IsmobileNumber(new_number)){
                update_number(new_number);
            }else{
                $('#mobile_save').text('Save');
                alert('Invalid mobile number.');
            }
        }else{
            if(!IsmobileNumber(old_number)){
                $('#mobile_save').text('Save');
                alert('Enter a valid number.');
            }else{
                $('#mobile_verify').show();
                save_success();
            }
        }
    }

    function save_success(){
        $('#mobile_save').text('Save');
        $('#myphone, #mobile_edit').show();
        $('#mobile_cancel,#mobile_save,#mobile_form').hide();
        $('#myphone').text($('#mobile_form').val());
    }

    function update_number(number){
        $.ajax({
            url: __site_url + 'dashboard/save_mobile_ajax',
            type: "POST",
            data: {"is_ajax": true, 'number': number},
            success: function (response) {
                var data = $.parseJSON(response);
                if(data['success'] == true){
                    save_success();
                    $('#mobile_verify').replaceWith('<span class="save-head"  id="mobile_verify">Verified</span>');
                    window.location = "/dashboard/step/2";
                }else{
                    alert(data['message']);
                }
            }
        });
    }

    function IsmobileNumber(number){
        var Numbers = number;
        var IndNum = /^([0|\+[0-9]{1,5})?([7-9][0-9]{9})$/;
        if(IndNum.test(Numbers)){
            return true;
        }else{
            return false;
        }
    }

</script>