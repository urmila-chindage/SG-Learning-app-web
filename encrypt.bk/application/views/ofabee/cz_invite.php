<?php include('header.php') ?>
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" onclick="invite_to_challenge(47)">Open Small Modal</button>
<div class="modal fade" id="CInviteModal" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="header-popup">
        <button type="button" class="close close-img" data-dismiss="modal">&times;</button>
        <h4 class="invite-popup-head">Invite <span class="sub-people-popup">People</span></h4>
      </div><!--header-popup-->
      <div class="modal-body">
          <p class="invite-text-popup">Invite your friends, families and people you know by </br> adding their email address</p>
          <input type="text" class="multilpe-mail-holdr alert-class" placeholder="Type email addresses separated by comma."  id="ctokenfield" value="" />
          <div class="btn-center-div btn-center-alter">
            <a class="btn  orange-flat-btn  orange-course-btn send-invi-alter  inline-blk" id="invitecz_btn">Send Invitations</a>
          </div>
      </div>        
    </div>
  </div>
</div>
<?php include('footer.php'); ?>

<script type="text/javascript">

var __challenge_invite_id = 0;
var __site_url_i          = '<?php echo site_url(); ?>';

function invite_to_challenge(id){
  __challenge_invite_id = id;
  $('#ctokenfield').tokenfield('setTokens', []);
  $('#ctokenfield').val('');
  $('#CInviteModal').modal('show');
}

$('#ctokenfield').tokenfield({
    showAutocompleteOnFocus: true
  });

  $('#invitecz_btn').click(function(){
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
                    toastr["success"](data['message']);
                }else{
                    toastr["error"](data['message']);
                }
            }
        });

      }

  });
</script>