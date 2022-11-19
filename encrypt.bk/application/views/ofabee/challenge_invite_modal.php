<script type="text/javascript">
  var __site_url_i          = '<?php echo site_url(); ?>';
</script>
<div class="modal fade" id="CInviteModal" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="header-popup">
        <button type="button" class="close close-img" data-dismiss="modal">&times;</button>
        <h4 class="invite-popup-head">Invite <span class="sub-people-popup">People</span></h4>
      </div><!--header-popup-->
      <div class="modal-body">
          <p class="invite-text-popup">Invite your friends, families and people you know by </br> adding their email address</p>
          <input type="text" class="multilpe-mail-holdr alert-class" placeholder="Enter E-mail."  id="ctokenfield" value="" />
          <div class="btn-center-div btn-center-alter">
            <a class="btn  orange-flat-btn  orange-course-btn send-invi-alter  inline-blk" id="invitecz_btn">Send Invitations</a>
          </div>
      </div>        
    </div>
  </div>
</div>

<div class="modal fade" id="challenge_success_invite" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content ofabee-modal-content">
      <div class="modal-header ofabee-modal-header">
        <button type="button" class="close" data-dismiss="modal">
          &times;
        </button>
      </div>
      <div class="modal-body ofabee-modal-body textarea-top">
        <img src="<?php echo assets_url(); ?>themes/ofabee/img/Successful_icon.svg" class="blocked-image">
        <span class="your_review">Your invitation has been
            <br />
            send</span>
      </div>
      <div class="modal-footer ofabee-modal-footer modal-footer-text-center">
        <button type="button" class="btn ofabee-dark" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>