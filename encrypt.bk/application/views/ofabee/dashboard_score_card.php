<style>
.score-card-tabs{ display: none;}
</style>
<section class="score-card-tabs" id="tab_test_report" style="display: block;">
    <?php include_once 'report_test_report.php'; ?>
</section>
<section class="score-card-tabs" id="tab_error_revision">
    <?php include_once 'report_revision.php'; ?>
</section>
<script>
$(document).on('click', '#sub-nav .my-submenu-ul li', function(){
    var tabId = $(this).attr('data-tab');
    $('.score-card-tabs').hide();
    $('#'+tabId).show();
});
</script>