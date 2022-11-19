<!-- Bootstrap -->
<script src="<?php echo base_url('assets')?>/js/bootstrap.min.js" type="text/javascript"></script>

<!-- body end-->
<script src="<?php echo base_url('assets')?>/js/jquery.form-validator.min.js" type="text/javascript"></script>
<script>
    $.validate({
        modules : 'location, date, security, file',
        onModulesLoaded : function() {
        },
        errorMessagePosition : 'top' ,
        validateOnBlur : false
    });

  // Restrict presentation length

</script>

</html>