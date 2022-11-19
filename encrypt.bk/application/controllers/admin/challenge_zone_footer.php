    <!-- Manin Iner container end -->
</body>
<!-- body end-->

</html>

<!-- bootstrap library -->

<?php if($this->router->fetch_class() == 'challenge_zone'){
    include_once 'challenge_modals.php';
} ?>

<script>
    var __controller         = '<?php echo $this->router->fetch_class() ?>';
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/language.js"></script>

<script src="<?php echo assets_url() ?>js/app.js" type="text/javascript"></script>

<script type="text/javascript">

    $(document).ready(function(){
        App.initEqualizrHeight(".builder-left-inner", ".builder-right-inner");
    });
    
</script>
