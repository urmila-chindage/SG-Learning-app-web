<form action="<?php echo $action_url; ?>" method="post" name="feepal_form" style="display:none;">
   <?php /* ?> <input type="text" name="FID" value="<?php echo $FID ?>">
    <input type="text" name="USER_ID" value="<?php echo $session['id'] ?>">
    <input type="text" name="EMAIL" value="<?php echo $session['us_email'] ?>">
    <input type="text" name="FULLNAME" value="<?php echo $session['us_name'] ?>">
    <input type="text" name="AMOUNT" value="<?php echo $amount; ?>">
    <input type="text" name="PHONENO" value="<?php echo $session['us_phone']; ?>">
    <input type="text" name="CHECKSUM" value="<?php echo $CHECKSUM ?>">
    <input type="text" name="ERP" value="<?php echo $ERP ?>"><?php */ ?>
    <input type="text" name="FID" value="<?php echo $FID ?>">
    <input type="text" name="USER_ID" value="<?php echo $USER_ID ?>">
    <input type="text" name="EMAIL" value="<?php echo $EMAIL ?>">
    <input type="text" name="FULLNAME" value="<?php echo $FULLNAME ?>">
    <input type="text" name="AMOUNT" value="<?php echo $amount ?>">
    <input type="text" name="PHONENO" value="<?php echo $PHONENO; ?>">
    <input type="text" name="CHECKSUM" value="<?php echo $CHECKSUM ?>">
    <input type="text" name="ERP" value="<?php echo $ERP ?>">
    <input type="text" name="TRANSACTION_NO" value="<?php echo $USER_ID."_".$item_details['id']."_".rand(0,6) ?>">
</form>

<script type="text/javascript">
 var feepal_form = document.forms.feepal_form;
     feepal_form.submit();
</script>