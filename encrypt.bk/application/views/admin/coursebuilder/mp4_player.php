<!-- optimize mobile versions -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- The "minimalist" skin - choose from: "minimalist.css", "functional.css", "playful.css" -->
<link rel="stylesheet" href="<?php echo assets_url() ?>flowplayer/skin/functional.css">

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
<script>
$(document).ready(function(){
  var player = flowplayer("#player", {
    embed: false,
      clip: {
      sources: [
        { type: "video/mp4", src:
          "<?php echo $file_url ?>" }
      ]
    }

  }).one('ready', function(ev, api) {
      $('.fp-player').next('a').remove();
      $('.fp-brand').remove();
  });;
});
</script>

<div id="content">
     <div id="player"></div>
</div> <!-- end content -->