<?php include 'header.php'; ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
<script type="text/javascript">
	var rattings = $.parseJSON('<?php echo $rattings; ?>');
</script>
<div class="wrapper">
	<div class="sction_1">
		<div class="row">
			<?php include 'sidebar.php'; ?>
			<div class="col-sm-9 padding_L">
				<div class="sction_2">
					<div class="tab-content responsive">
						<div class="tab-pane active" id="courses">
							<div class="row">
	                            <div class="col-xs-12">
	                                <div class="input-group col-md-12 martop">
						                <input type="text" class="form-control input-lg customsearch" placeholder="<?php echo lang('search') ?>" id="searchid" value="<?php echo $query; ?>"/>
						                <span class="input-group-btn">
						                    <button id="searchbtn" class="btn btn-info btn-lg btn-search" type="button">
						                        <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/search.png" alt="img">
						                    </button>
						                </span>
						            </div>
	                            </div>
                                <?php if(!empty($courses)) { ?>
	                            <?php foreach($courses as $key => $course){ ?>

    <div class="col-xs-6 col-sm-4 col-md-4 <?php echo ($key>2)?'margin':'' ?> sdfw martop hoverbox">
    
<div class="tab_box2">
	<div id="whishdiv_<?php echo $course['id']; ?>"><?php echo $course['wish_stat']; ?></div>
    <div class="img_hover">
    	<img class="img-responsive img searchimage" src="<?php echo (($course['cb_image'] == 'default.jpg')?default_course_path():course_path(array('course_id' => $course['id']))).$course['cb_image']?>" alt="">
    	<span><a href="<?php echo site_url().'/'.$course['cb_slug']; ?>" >Go to course</a></span>
    </div>
    <div class="lowercontent">
                
                <h3 class="searchtitle"><?php echo $course['cb_title']; ?></h3>
                <h4 class="searchauthor">By <?php
                     $tutor_names = array();
                    foreach ($course['course_tutors'] as $val) {
                         $tutor_names[] = $val['us_name'];
                    }
                    echo (empty($tutor_names))?$admin:implode(', ',$tutor_names);
                ?>
                </h4>            
                <div class="star">
                <div id="rate_div_<?php echo $key; ?>"></div>
                <span></span>
                <div class="price">
                    <?php if($course['cb_is_free'] == 1){ ?>
                        <h4 class="left">Free</h4>
                    <?php }else if($course['cb_is_free'] != '' && $course['cb_is_free'] != 0){ ?>
                         <h4 class="left">Rs.<?php echo $course['cb_discount']; ?> <span>Rs.<?php echo $course['cb_price']; ?></span></h4>
                    <?php }else{ ?>
                        <h4 class="left">Rs.<?php echo $course['cb_price']; ?></h4>
                    <?php } ?>
                </div>
             </div>
          </div>
	</div>
</div>


								<?php } ?>
                                <?php } else{ ?>
                                    <?php echo "<h3 style='text-align:center'>No results found</h3>";?>
                                <?php } ?>
                            </div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/jquery.rateyo.min.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/search.js'; ?>" ></script>
<?php include 'footer.php'; ?>