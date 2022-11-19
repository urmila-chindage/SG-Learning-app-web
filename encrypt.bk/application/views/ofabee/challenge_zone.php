<?php include('header.php'); ?>
<section id="nav-group">
<div class="nav-group">
	<div class="container container-altr">
    	<div class="container-reduce-width">
        	<h2 class="funda-head pl15">Online Test</h2>
    
        </div>
    </div>
</div>
</section>

<section>
	<div class="all-challenges">
	    <div class="container container-altr">
	        <div class="container-reduce-width">
	        
	            <div class="col-sm-12 col-md-12 col-lg-12" id="main_area">
		            

	            </div>
	        </div>	<!--container-reduce-width-->
	    </div><!--container altr-->       
	</div><!--all-challenges-->
</section>
<script type="text/javascript">
	var __limit   = '<?php echo $limit; ?>';
	var __offset  = '1';
	var __user_id = '<?php echo isset($user_id)?$user_id:''; ?>';
	var __site_url= '<?php echo site_url(); ?>';
	var __challenges = atob('<?php echo base64_encode(json_encode($challenge_zones)); ?>');
	var __user_id = 0;
	var __user_id = '<?php echo $session['id']; ?>';
	var __total_challenges = '<?php echo $total_challenges; ?>';
    var __challenges_recieved = 0;
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/challenge_zone.js"></script>
<?php include_once('challenge_invite_modal.php'); ?>
<?php include('footer.php'); ?>

<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/challenge_invite_modal.js'; ?>" ></script>