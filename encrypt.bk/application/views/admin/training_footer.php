<?php $course_id = isset($course["id"]) ? $course["id"] : '';?>

<script>
    var __course_id = '<?php echo $course_id; ?>';
</script>
<?php if(!isset($no_content_js)):?>
<script type="text/javascript" src="<?php echo assets_url() ?>js/training_content.js"></script>
<?php endif; ?>
<?php include_once 'footer.php';?>