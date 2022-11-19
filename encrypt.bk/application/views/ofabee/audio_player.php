<!-- optimize mobile versions -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="<?php echo assets_url() ?>flowplayer/skin/functional.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>flowplayer/skin/flowplayer.audio.css">

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
   
<script src="<?php echo assets_url() ?>flowplayer/flowplayer.min.js"></script>
<script src="<?php echo assets_url() ?>flowplayer/flowplayer.audio.min.js"></script>

<div class="player-wrapper">
     <div id="audio_player" class="fixed-controls"></div>
</div> <!-- end content -->


<script>
    

</script>