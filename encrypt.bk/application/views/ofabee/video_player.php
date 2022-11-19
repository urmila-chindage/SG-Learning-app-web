<?php /* ?><meta charset="UTF-8">
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<!--<link href="<?php echo assets_url() ?>player/css/bootstrap.min.css" rel="stylesheet">-->
<link href="<?php echo assets_url() ?>player/css/video-js.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>player/css/style.css" rel="stylesheet">
<video id=ofabee-video width=400  class="video-js vjs-default-skin" controls>
    <source src="" type="application/x-mpegURL">
</video>
<script src="<?php echo assets_url() ?>player/js/video.js"></script>
<script src="<?php echo assets_url() ?>player/js/videojs-hls.js"></script>
<?php */ ?>


<!-- optimize mobile versions -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- The "minimalist" skin - choose from: "minimalist.css", "functional.css", "playful.css" -->
<link rel="stylesheet" href="<?php echo assets_url() ?>flowplayer/skin/functional.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>flowplayer/flowplayer.quality-selector.css">

<!-- Minimal styling for this standalone page, can be removed -->
   


<style>
#player {
  background-color: #eee;
}
#player {
  /*background-image: url(<?php //echo $cloudfront_url.$thumb_url ?>) !important;*/
}
.flowplayer .fp-fullscreen, .flowplayer .fp-unload, .flowplayer .fp-close{
    left: 10px;
    right: auto;
}
.flowplayer .fp-player{ border-left: 1px solid #fff;}
</style>
   
<script src="<?php echo assets_url() ?>flowplayer/flowplayer.min.6.0.2.js"></script>
<script src="<?php echo assets_url() ?>flowplayer/flowplayer.hlsjs.min.v2.js"></script>
<script src="<?php echo assets_url() ?>flowplayer/flowplayer.audio.min.js"></script>

<div id="content" class="player-wrapper">
     <div id="player" class="fixed-controls"></div>
</div> <!-- end content -->
