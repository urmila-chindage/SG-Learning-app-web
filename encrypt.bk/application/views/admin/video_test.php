<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Ajax Upload</title>

    <!-- Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
		<link rel="icon" href="<?php echo base_url('favicon.png') ?>">
  </head>
  <?php 
    include_once 'upload_class.php';
    include_once 'foto_upload_script.php';
  ?>
  <body>
	<div class="container">
		<form id="Form1" method="post" role="form" enctype="multipart/form-data">
			
			<div class="input-group">
			  <input type="file" class="form-control input-sm" name="fileToUpload" id="fileToUpload" />
			  <span class="input-group-btn">
					<button type="submit" class="btn btn-primary btn-sm" id="buttonForm">Upload</button>
			  </span>
			</div>						
		</form>
		<img id="loading" src="loading.gif" />
		<div id="result"></div>
		<div class="text-muted" id="message"></div>
	</div>   

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="http://malsup.github.com/min/jquery.form.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		$("#loading").hide();
		var options = {
			beforeSubmit:  showRequest,
			success:       showResponse,
			url:       'video/upload_catalog_image_to_localserver',
                        data:{"is_ajax":true, "id": "12", "s_id" :"450", "s_name" : 'new'},// your upload script
			dataType:  'json'
		};
		$('#Form1').submit(function() {
			$('#message').html('');
			$(this).ajaxSubmit(options);
			return false;
		});
	}); 
	function showRequest(formData, jqForm, options) { 
		var fileToUploadValue = $('#fileToUpload').fieldValue();
		if (!fileToUploadValue[0]) { 
			$('#message').html('You need to select a file!'); 
			return false; 
		}
		$("#loading").show();
		return true; 
	} 
	function showResponse(data, statusText, xhr, $form)  {
		$("#loading").hide();
		if (statusText == 'success') {
			var msg = data.error.replace(/##/g, "<br />");
			if (data.img != '') {
				$('#result').html('<br /><img src="files/photo/' + data.img + '" />');
				$('#message').html('<br />' + msg + '<a href="index.html">Click here</a> to upload another file.'); 
				$('#formcont').html('');
			} else {
				$('#message').html(msg); 
			}
		} else {
			$('#message').html('Unknown error!'); 
		}
	} 
	</script>
    
  </body>
</html>
