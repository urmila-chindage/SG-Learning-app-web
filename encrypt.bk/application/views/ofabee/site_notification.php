<?php 
//echo '<pre>'; print_r($site_notification);
$list_limit = 10;
$unseen_message_count = 0;
?>
<ul>
    <?php if(isset($site_notification['unseen']) && !empty($site_notification['unseen'])): ?>
        <?php $unseen_message_count = sizeof($site_notification['unseen']); ?>
            <?php foreach ($site_notification['unseen'] as $key => $message): ?>
                <li>
                    <a href="<?php echo ($message['link']?$message['link']:'javascript:void(0)') ?>"><?php echo $message['message'].$key ?></a>
                </li>
        <?php endforeach; ?>
    <?php endif; ?>

    
    <?php $seen_message = $list_limit- $unseen_message_count; ?>
    <?php if($seen_message > 0 ): ?>
        <?php if(isset($site_notification['seen']) && !empty($site_notification['seen'])): ?>
            <?php foreach ($site_notification['seen'] as $key => $message): ?>
                <li>
                    <a href="<?php echo ($message['link']?$message['link']:'javascript:void(0)') ?>"><?php echo $message['message'] ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</ul>
<?php 
            echo $unseen_message_count.'<br />';

?>