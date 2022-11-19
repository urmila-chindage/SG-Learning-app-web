<?php include_once 'header.php'; ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/chrome-css.css">
<section>
    <div class="fundamentals">
        <div class="container">
            <div class="container-reduce-width">
                <h2 class="funda-head"><?php echo $assessment['lecture']['cl_lecture_name'] ?></h2>             	         
            </div><!--container-reduce-width-->
        </div><!--container-->    
    </div><!--fundamentals-->
</section> 
<?php 
$compare_head       = array();
$compare_head_html  = '';
$user_image         = '';

$rank_html      = '';
$mark_html      = '';
$duration_html  = '';
$my_account     = $this->auth->get_current_user_session('user');
$loop = 0;
if(!empty($users))
{
    $users_count = sizeof($users);
        $compare_head_html .= '<th class="table-head-compare"></th>';
    foreach ($users as $user)
    {
        $loop++;
        $compare_head[] = '<span class="compare-name compare-name-'.$user['attempt_id'].'">'.$user['name'].'</span>'.(($users_count>$loop)?'<span class="verses compare-name-'.$user['attempt_id'].'">vs</span>':'');
        $user_image     = ($user['image'] == 'default.jpg')?default_user_path():user_path(); 
        $compare_head_html .= '<th class="table-head-compare compare_block_'.$user['attempt_id'].'">';
        $compare_head_html .= '    <span class="table-dark-head">';
        $compare_head_html .= '        <span class="table-dark-head-inside">';
        if($my_account['id']!=$user['id'] && $users_count> 2 )
        {
            $compare_head_html .= '            <a href="javascript:void(0);" onclick="removeCompare('.$user['attempt_id'].')" class="compare-close">&#10006;</a>';
        }
        $compare_head_html .= '            <img src="'.$user_image.$user['image'].'" class="compare-profile-pic">';
        $compare_head_html .= '            <span class="compare-person-name">'.$user['name'].'</span>';
        $compare_head_html .= '        </span>';
        $compare_head_html .= '    </span>';
        $compare_head_html .= '</th>';
        
        $rank_html      .= '<td class="table-childs-compare compare_block_'.$user['attempt_id'].'"><span class="table-child-span table-child-orange">'.$user['rank'].'</span></td>';
        $mark_html      .= '<td class="table-childs-compare compare_block_'.$user['attempt_id'].'"><span class="table-child-span">'.$user['mark'].'</span></td>';
        $duration_html  .= '<td class="table-childs-compare compare_block_'.$user['attempt_id'].'"><span class="table-child-span">'.secondsToTime($user['duration']).'</span></td>';
    }
}


function secondsToTime($seconds)
{

  // extract hours
  $hours = floor($seconds / (60 * 60));

  // extract minutes
  $divisor_for_minutes = $seconds % (60 * 60);
  $minutes = floor($divisor_for_minutes / 60);

  // extract the remaining seconds
  $divisor_for_seconds = $divisor_for_minutes % 60;
  $seconds = ceil($divisor_for_seconds);

  $return       = '';
  if($hours > 0)
  {
      //$return .= $hours.':'; 
      $minutes = $minutes+($hours*60);
  }
  if($minutes > 0)
  {
      if($minutes > 9)
      {
          $return .= $minutes.':';           
      }
      else
      {
          $return .= '0'.$minutes.':'; 
      }
  }
  else
  {
     $return .= '00:'; 
  }
  if($seconds > 0)
  {
      if($seconds > 9)
      {
          $return .= $seconds;       
      }
      else
      {
          $return .= '0'.$seconds;                 
      }
  }
  else
  {
     $return .= '00'; 
  }
  return $return;
}

?>
<section>
    <div class="compare-names-btns-wraper">
        <div class="container">
            <div class="container-reduce-width clearfix">

                <span class="compare-comparing">Comparing</span>
                <?php echo implode('', $compare_head) ?>
                <span class="compare-btn-container" style="margin-top: 0">
                    <a href="<?php echo site_url('report/assessment/'.$assessment['assesment_id']) ?>" id="" class="btn grey-flat-btn grey-flat-btn-to-orange">Back to Results</a>
                </span>
            </div>
        </div><!--container-->
    </div><!--compare-names-btns-wraper-->
</section>

<section>
    <div class="compare-table-wrap">
        <div class="container table-container">
            <div class="container-reduce-width">
                <div class="table-responsive table-overflow">          
                    <table class="table">
                        <thead>
                            <tr>
                                <?php echo $compare_head_html ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="table-childs-compare table-child-first-compare"><span class="table-child-span table-child-start">Rank</span></td>
                                <?php echo $rank_html ?>
                            </tr>
                            <tr>
                                <td class="table-childs-compare table-child-first-compare"><span class="table-child-span table-child-start">Marks Scored</span></td>
                                <?php echo $mark_html ?>
                            </tr>
                            <tr>
                                <td class="table-childs-compare table-child-first-compare"><span class="table-child-span table-child-start">Time taken</span></td>
                                <?php echo $duration_html ?>
                            </tr>                      
                            <?php if(!empty($categories)): ?>
                            <?php foreach($categories as $category): ?>
                            <tr>
                                <td class="table-childs-compare table-child-first-compare"><span class="table-child-span table-child-start"><?php echo (($category['name'])?$category['name']:'Sections') ?></span></td>
                                <?php if(!empty($category['percentage'])): ?>
                                <?php foreach($category['percentage'] as $attempt_id => $percentage): ?>
                                    <td class="table-childs-compare compare_block_<?php echo $attempt_id ?>"><span class="table-child-span"><?php echo round(($percentage*100), 2) ?>%</span></td>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tr>                      
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div><!--table-responsive-->            
            </div><!--container-reduce-width-->
        </div>  <!--container-->  
    </div><!--compare-table-wrap-->
</section>
<script>
function removeCompare(blockId)
{
    $('.compare_block_'+blockId).remove();
    $('.compare-name-'+blockId).remove();
    if($('.table-dark-head-inside').size()<=2)
    {
        $('.compare-close').remove();
    }
}
</script>
<?php include_once 'footer.php'; ?>