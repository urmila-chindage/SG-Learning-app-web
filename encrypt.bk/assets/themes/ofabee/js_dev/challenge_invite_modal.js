
var __challenge_invite_id = 0;

function invite_to_challenge(id){
  __challenge_invite_id = id;
  $('#ctokenfield').tokenfield('setTokens', []);
  $('#ctokenfield').val('');
  $('#CInviteModal').modal('show');
  $('#ctokenfield-tokenfield').focus();
  $('.tokenfield').css('border','1px solid #ccc');
}

$('#ctokenfield').tokenfield({
    showAutocompleteOnFocus: true
  });

  $('#invitecz_btn').click(function(){
    $('#ctokenfield-tokenfield').focus();
    $('.tokenfield').css('border','1px solid #ccc');
    var cinput_email = $('#ctokenfield').val().split(",");
    var cvalidated   = Array();
    cvalidated       = {};
    var ci = 0;
    //console.log(input_email);
      $.each( cinput_email, function( index, value ){
        cinput_email[index] = value = value.replace(/\s+/g, '');
      });
      var cunique = cinput_email.filter(function(itm, i, input_email) {
          return i == cinput_email.indexOf(itm);
      })
      $.each( cunique, function( index, value ){
        if(isValidEmailAddress(value)){
          cvalidated[ci] = value;
          ci++;
        }
      });

      if(cinput_email.length == 1&& cinput_email[0] == ''){
        //console.log(input_email);
        $('#ctokenfield-tokenfield').focus();
        $('.tokenfield').css('border','1px solid red');
        toastr["error"]('This field cannot be empty.');
      }else{

        $.ajax({
            url: __site_url_i+'challenge_zone/invite_friends',
            type: "POST",
            data:{"is_ajax":true,'challenge_zone_id':__challenge_invite_id,'email_id':JSON.stringify(cvalidated)},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['success'] == true)
                {
                    $('#ctokenfield').tokenfield('setTokens', []);
                    $('#ctokenfield').val('');
                    $('#challenge_success_invite').modal('show');
                    $('#CInviteModal').modal('hide');

                }else{
                    toastr["error"](data['message']);
                }
            }
        });

      }

  });