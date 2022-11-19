<?php include('header.php'); ?>

<?php $list = ''; if(!empty($challenge_zone_categories)): ?>
<?php foreach($challenge_zone_categories as $c_category){
$list .= ($c_category['id'] == $category_id)?'':'<li><a href="'.site_url().'challenge_zone/category/'.$c_category['id'].'" class="term-category" data-category-id="'.$c_category['id'].'">'.$c_category['ct_name'].'</a></li>';

 ($c_category['id'] == $category_id)?$current_cat_name = $c_category['ct_name']:'';

} 
if(!isset($current_cat_name))$current_cat_name='Categories';
?>
<?php endif;?>
<section>
    <div class="category-terms">
    	<div class="container container-altr">
        	<div class="container-reduce-width">
            
				<div class="col-sm-9 col-md-9 col-lg-9 category-right col-lg-push-3 col-md-push-3 col-sm-push-3">
					<div class="row">
                    	<div class="col-xs-6 col-sm-4 col-md-3">
                        	<h3 class="">Challenge Zone</h3>
                        </div>
                   		<div class="col-xs-6 col-sm-9 hide414">
                            <div class="btn-group cat-menu">
                                <button type="button" class="form-control btn dropdown-toggle big-input" data-toggle="dropdown">
                                    <h3 class="menu-h3" id="category_selected_text"><?php echo $current_cat_name; ?></h3> 
                                    <span class="category-caret">
                                        <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                        <g>
                                        <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                        <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                        </g>
                                        </svg>                                   
                                    </span>
                                </button>
                                <ul class="dropdown-menu generate-dropdown">
                                    <?php if(!empty($challenge_zone_categories)): ?>
                                    <?php echo $list; ?>
                                    <?php endif; ?>
                                </ul>
                            </div> 
                        </div>
                  	</div>
                   	 

                
				    <div class="category-news-content">
				 		<div class="row" id="content">

						</div>                                                                                
					</div>
                               
            	</div>            
            	<?php include('sidebar_beta.php'); ?>
			</div>	<!--container-reduce-width-->
    	</div><!--container-->       
	</div><!--category-terms-->
</section>
<script type="text/javascript">
	var __limit   = '<?php echo $limit; ?>';
	var __offset  = '1';
	var __user_id = '<?php echo isset($user_id)?$user_id:''; ?>';
	var __site_url= '<?php echo site_url(); ?>';
	var __challenges = atob('<?php echo base64_encode(json_encode($challenges)); ?>');
    var __user_id = 0;
    var __user_id = '<?php echo $session['id']; ?>';
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/challenge_zone_category.js"></script>
<?php include_once('challenge_invite_modal.php'); ?>
<?php include('footer.php'); ?>

<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/challenge_invite_modal.js'; ?>" ></script>