<div class="modal fade" id="InviteModal" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="header-popup">
        <button type="button" class="close close-img" data-dismiss="modal">&times;</button>
        <h4 class="invite-popup-head">Invite <span class="sub-people-popup">People</span></h4>
      </div><!--header-popup-->
      <div class="modal-body">
          <p class="invite-text-popup">Invite your friends, families and people you know by </br> adding their email address</p>
          <input type="text" class="multilpe-mail-holdr alert-class" placeholder="Enter E-mail."  id="tokenfield" value="" />
          <div class="btn-center-div btn-center-alter">
            <a class="btn  orange-flat-btn  orange-course-btn send-invi-alter  inline-blk" id="invite_btn">Send Invitations</a>
          </div>
      </div>        
    </div>
  </div>
</div>

<div class="modal fade in" id="Invite_Success_modal" role="dialog">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-close-wrap">
            <button type="button" class="close close-modified" data-dismiss="modal">
              ×
            </button>
          </div><!-- modal-close-wrap -->
          <div class="modal-content-wraper">
            <img src="<?php echo assets_url(); ?>themes/<?php echo $this->config->item('theme'); ?>/img/Successful_icon.svg" class="img-responsive ticksvg-alter" alt="image">
            <span class="invitaio-header"><span class="invitation-numbers" id="invtn_not_count">2</span>&nbsp;<span class="invitation-bold">Invitaions</span> were sent</span>
            <span class="error-sending">The following recipient(s) were not
              <br>
              successfully sent</span>
              <div id="invtn_nt_snd"></div>

            <div class="text-center margin-top-alterd">
              <a href="javascript:void(0)" class="btn btn-grey-error" data-dismiss="modal">Close</a>
            </div><!-- text-center -->
          </div><!-- modal-content-wraper -->
        </div>

      </div>
    </div>



<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/bootstrap-tokenfield.js"></script>

<script type="text/javascript">
var __site_url_i  = '<?php echo site_url(); ?>';
var __assets_url_i= '<?php echo assets_url(); ?>';

$('#tokenfield').tokenfield({
	  showAutocompleteOnFocus: true
	});

  $('#invite_btn').click(function(){
  toastr.remove()
  $('#tokenfield-tokenfield').focus();
  $('.tokenfield').css('border','1px solid #ccc');
  var input_email = $('#tokenfield').val().split(",");
  var validated   = Array();
  validated       = {};
  var i = 0;
  //console.log(input_email);
    $.each( input_email, function( index, value ){
      input_email[index] = value = value.replace(/\s+/g, '');
    });
    var unique = input_email.filter(function(itm, i, input_email) {
        return i == input_email.indexOf(itm);
    })
    $.each( unique, function( index, value ){
      if(isValidEmailAddress(value)){
        validated[i] = value;
        i++;
      }
    });

    if(input_email.length == 1&& input_email[0] == ''){
      //console.log(input_email);
      $('#tokenfield-tokenfield').focus();
      $('.tokenfield').css('border','1px solid red');
      toastr["error"]('This field cannot be empty.');
    }else{

      $.ajax({
          url: __site_url_i+'login/invite_friends',
          type: "POST",
          data:{"is_ajax":true,'email_id':JSON.stringify(validated)},
          success: function(response) {
            $('#InviteModal').modal('hide');
              var data = $.parseJSON(response);
              if(data['not_send'].length>0){
                $('#invtn_nt_snd').show();
                var not_html = generate_not_invited(data['not_send']);
                $('#invtn_nt_snd').html(not_html);
                $('#invtn_not_count').html(data['send_count']);
                $('#Invite_Success_modal').modal('show');
              }else{
                $('#invtn_nt_snd').hide();
                $('#Invite_Success_modal').modal('show');
              }
          }
      });
      $("#rate_mentor").on("hidden.bs.modal", function () {
          $('#tokenfield').tokenfield('setTokens', []);
          $('#tokenfield').val('');
      });

    }

  });
  function generate_not_invited(not_send){
    var renderhtml = '';
    $.each(not_send, function(ratingkey, n_send )
    {
      renderhtml += '<span class="not-send-wrap"> <span class="close-error-wrap"><img src="'+__assets_url_i+'themes/ofabee/img/close_in round_icon.svg" class="img-responsive"></span> <span class="error-mailer">'+n_send.us_email+'</span></span>';
    });
    return renderhtml;
  }
  function isValidEmailAddress(emailAddress) {
      var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
      return pattern.test(emailAddress);
  };
</script>