<meta charset="UTF-8">
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<!-- <link href="<?php echo assets_url() ?>player/css/bootstrap.min.css" rel="stylesheet"> -->
<link href="<?php echo assets_url() ?>golive/videojs/css/video-js.css" rel="stylesheet">
<video id="live_video_play" width=400  class="video-js vjs-default-skin" controls>
    
    <source src="<?php echo $file_url ?>" type="application/x-mpegURL">
</video>
<script src="<?php echo assets_url() ?>golive/videojs/js/video.js"></script>
<script src="<?php echo assets_url() ?>golive/videojs/js/videojs-hls.js"></script>
<script>
    var player = videojs('live_video_play');
        player.play();
</script>