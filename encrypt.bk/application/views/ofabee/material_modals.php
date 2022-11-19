<div class="modal fade" id="InviteModal_member" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="header-popup">
                <button type="button" class="close close-img" data-dismiss="modal">&times;</button>
                <h4 class="invite-popup-head">Invite <span class="sub-people-popup">People</span></h4>
            </div><!--header-popup-->
            <div class="modal-body">
                <p class="invite-text-popup">Invite your friends, families and people you know by </br> adding their email address</p>
                <input type="text" class="multilpe-mail-holdr alert-class" placeholder="Email addresses"  id="tokenfield_member" value="" />
                <div class="btn-center-div btn-center-alter">
                    <a class="btn  orange-flat-btn  orange-course-btn send-invi-alter  inline-blk" id="invite_btn_member">Send Invitations</a>
                </div>
            </div>        
        </div>
    </div>
</div>

<div class="modal fade" id="member_invite_success" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-close-wrap">
                <button type="button" class="close close-modified" data-dismiss="modal">
                        &times;
                </button>
            </div><!-- modal-close-wrap -->
            <div class="modal-content-wraper">
                <img src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/img/Successful_icon.svg' ?>" class="img-responsive ticksvg-alter" alt="image">
                <span class="invitaio-header">
                    <span id="invitation_count_members" class="invitation-numbers"></span>&nbsp;
                    <span class="invitation-bold">Invitations</span> were sent
                </span>
                <div class="text-center margin-top-alterd">
                        <a href="javascript:void(0)" class="btn btn-grey-error" data-dismiss="modal">Close</a>
                </div><!-- text-center -->
            </div><!-- modal-content-wraper -->
        </div>

    </div>
</div>

<div id="rate_course" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content ofabee-modal-content">
            <div class="modal-header ofabee-modal-header border-bottom-replaced">
                <button type="button" class="close" data-dismiss="modal">
                        &times;
                </button>
                <h4 class="modal-title ofabee-modal-title">Rate this course</h4>
            </div>
            <div class="modal-body ofabee-modal-body textarea-top">
                <div class="starrating-inside">
                    <span class="rate-this-label ratelabel-block">Your rating</span>
                    <select id="example2">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                    </select>
                </div>
                <textarea class="ofabee-textarea" id="review_course" placeholder="Tell others what you think about this course and why did you leave this rating"></textarea>
            </div>
            <div class="modal-footer ofabee-modal-footer btn-center-responsive">
                <button type="button" class="btn ofabee-dark" data-dismiss="modal">Cancel</button>
                <button id="submit_rating_course" type="button" class="btn ofabee-orange" >Submit</button>
            </div>
        </div>

    </div>
</div>


<div id="rate_course_preview" class="modal fade ofabee-modal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content ofabee-modal-content">
            <div class="modal-header ofabee-modal-header">
                <button type="button" class="close" data-dismiss="modal">
                        &times;
                </button>
            </div>
            <div class="modal-body ofabee-modal-body textarea-top">
                <img src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/img/Successful_icon.svg' ?>" class="blocked-image">
                <span class="your_review">Your review has been
                        <br />
                        submitted</span>
                <div class="starrating-inside text-center">
                    <span class="blocked_rating">Your rating</span>
                    <select id="example4">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <span class="preview_purpose" id="preview_review_course"></span>
            </div>
            <div class="modal-footer ofabee-modal-footer modal-footer-text-center">
                <button type="button" class="btn ofabee-dark" data-dismiss="modal">
                        Close
                </button>
            </div>
        </div>

    </div>
</div>


<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/bootstrap-tokenfield.js"></script>

<script type="text/javascript">
$('#tokenfield_member').tokenfield({
	  showAutocompleteOnFocus: true
	});

  $('#invite_btn_member').click(function(){
  var input_email = $('#tokenfield_member').val().split(",");
  var validated   = Array();
  validated       = {};
  var i = 0;
  var __site_url_i  = '<?php echo site_url(); ?>';
  var __course_id = '<?php echo $course_details['id']; ?>';
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
      toastr.remove();
      toastr["error"]('This field cannot be empty.');
    }else{

      $.ajax({
          url: __site_url_i+'material/invite_members',
          type: "POST",
          data:{"is_ajax":true,'email_id':JSON.stringify(validated),'course':__course_id},
          success: function(response) {
              var data = $.parseJSON(response);
              if(data['success'] == true)
              {
                  
                  $('#InviteModal_member').on('hidden.bs.modal', function (e) {
                      $("#invitation_count_members").html(data['invite_count']);
                      $("#member_invite_success").modal('show');
                  });
                  $('#InviteModal_member').modal('hide');
                  //toastr["success"](data['message']);
              }else{
                  toastr["error"](data['message']);
              }
          }
      });

    }

  });

  function isValidEmailAddress(emailAddress) {
      var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
      return pattern.test(emailAddress);
  };
</script>