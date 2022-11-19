<?php include 'header.php'; ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
<div class="wrapper">
	<div class="sction_1">
    	<div class="row">
        	<div class="col-sm-12 survey-iframe" id="survey_iframe">
            	<h3>SURVEY FORM</h3>
            	 <?php echo base64_decode($survey_data['s_html']); ?>
        	</div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
