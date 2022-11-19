<script type="text/javascript">
    $(document).ready(function() {
      $("#subscribe").click(function() {
        var email = $("#subscribe_email").val();
        var subscribes = [];
        subscribes[0]  = 0;
        $.post("<?php echo site_url('register/newsletter_subscribe'); ?>", {
        email1: email,
        subscribes : subscribes
        }, function(data) {
            var obj = jQuery.parseJSON(data);
            $.each(obj, function(key,value) {
                if (value.response == 'success'){
                  toastr["success"](value.category+" "+"Successfully subscribed..");
                }else{
                  toastr["error"](value.response);
                }
            }); 
        });
      });
    });
    $(function() {
        $('#subscribes').multiselect({
            maxHeight: 400,
            dropUp: true
        });
    });
</script>

<div class="footer-widget subscribe-widget">
    <h3>Subscribe to Our Newsletter</h3>
    <form>
    <div class="input-group">
    <input id="subscribe_email" type="text" class="form-control" placeholder="Email">
    <span class="input-group-btn">
        <button id="subscribe" class="btn btn-subscribe" type="button">
          Subscribe
        </button>
    </span>
    </div>
    </form>
</div>    