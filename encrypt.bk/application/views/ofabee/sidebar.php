<?php $sidebar_notification = get_sidebar_notification(); 
$sidebar_terms = get_sidebar_term();
?>

<div class="col-sm-3">
    <?php if(!empty($sidebar_terms)) : ?>
        <div class="s_term">
            <h2><?php echo lang('term_of_day') ?></h2>
            <?php $is_window_term = $sidebar_terms->t_new_window ==""?'':'_blank' ?>
            <a target="<?php echo $is_window_term ?>" href="<?php echo $sidebar_terms->t_slug; ?>"><h1><?php echo $sidebar_terms->t_title ?></h1>
            <h3><?php echo substr($sidebar_terms->t_short_description, 0,70).".." ?></h3></a>
            <a href="<?php echo site_url('/term') ?>">more</a>
        </div>
    <?php endif; ?>
    <div class="generate">

    <?php if(!empty($question_category)){ ?>
        <a href="<?php echo site_url('/course/generate_test_view/'.$category_id) ?>" class="btn btn-secondary orange" data-toggle="modal" data-target=""><img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/test.png" alt="img"><?php echo lang('generate_test');?></a>
    <?php } ?>
        <!--<button type="button" class="btn btn-secondary"><img src="<?php //echo assets_url() ?>themes/<?php //echo $this->config->item('theme') ?>/images/ans.png" alt="img"> Answer writing</button>-->
    </div>
    <?php if(!empty($challenge_zone)){ ?>
    <div class="s_zone">
        <h2>Challenge zone</h2>
        <?php foreach($challenge_zone as $challange){ ?>
        <div class="s_test">
            <?php $dt = new DateTime($challenge['cz_end_date']);
                $now_date = date('Y-m-d H:i:s');
                $now_date = new DateTime($now_date);
                $today = new DateTime($now_date->format('Y-m-d H:i:s')); 
               
                //$today->setTime( 0, 0, 0 );
                $match_date = new DateTime($dt->format('Y-m-d H:i:s'));
                //$match_date->setTime( 0, 0, 0 );
                 //print_r($today);print_r($match_date);die;

                $diff = $today->diff( $match_date );
                $elapsed = $diff->format('%y years %m months %a days %h hours %i minutes %S seconds');
                
                
                
                $diffDays = (integer)$diff->format( "%R%a%h%i%S" ); ?>
            <h3><?php echo $challange['cz_title']; ?> <?php echo ($diffDays > 0)?'ends':'completed'?> 
            <?php echo ($diffDays > 0)?'by ':'on '?> <?php 
            switch( $diffDays ) {
                case 0:
                    echo "Today ".$dt->format('g:i a');
                    break;
                case +1:
                    echo "Tomorrow ".$dt->format('g:i a');;
                    break;
                default:
                    echo $dt->format('M d Y g:i a');
            }
            ?>.</h3>
            <a href="<?php echo ($diffDays > 0)?site_url('/material/challenge/'.$challange['id']):site_url('/report/challenge_zone/'.$challange['id']) ?>"><?php echo ($diffDays > 0)?'Start':'View report' ?> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
    <?php if(!empty($sidebar_notification)) :?>
     <div class="s_notification">
        <h2>Notification</h2>
        <div class="sp_text">
            <?php foreach ($sidebar_notification as $notification) : ?>
            <p><img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/sp_icon.png" alt=""> 
                <?php $is_window = ($notification['n_new_window']=="" || $notification['n_new_window']=="0")?'':'_blank' ?>
                <a target="<?php echo $is_window ?>" href="<?php echo site_url('/') ?><?php echo $notification['n_slug'] ?>"><?php echo $notification['n_title']; ?></a></p>
            <?php endforeach; ?>
            <a class="notificaiton_sidebar_view" href="<?php echo site_url('/notification') ?>">More <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
        </div>
     </div>
    <?php endif; ?>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo lang('generate_test');?></h4>
      </div>
      <div class="modal-body">
        <span id="message"></span>
        <div>
            <?php foreach($question_category as $category){?>
                <?php if($category['qc_category_count'] > 0){ ?> 
                <div class="user_gen_question" >
                <span><?php echo $category['qc_category_name']; ?> (<?php echo 'Max - '.$category['qc_category_count']; ?>)</span>
                <input class="user_gen_q_count" type="number" id="" name="" onkeypress="return preventAlphabets(event)" max="<?php echo $category['qc_category_count']; ?>" min="1" data-category="<?php echo $category['id']; ?>" />
                <span></span>
                </div>
                <?php } ?>
            <?php }?>
                <div class="user_gen_question" >
                <span>Select Duration</span>
                <select id="duration" class="user_gen_q">
                        <option value="">Select</option>
                    <?php for($i=15;$i<=180;$i = $i + 15){ ?>
                        <option value="<?php echo $i; ?>">
                        <?php 
                        $hours = floor($i / 60);
                        $minutes = ($i % 60);
                        echo sprintf('%02d:%02d', $hours, $minutes);
                        ?>
                        </option>
                    <?php } ?>
                </select>
                </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('close');?></button>
        <button type="button" class="btn btn-secondary orange" onclick="create_test()"><?php echo lang('create_test');?></button>
      </div>
    </div>
  </div>
</div>

