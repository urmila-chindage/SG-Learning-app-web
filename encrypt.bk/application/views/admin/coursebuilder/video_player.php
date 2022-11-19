<?php if(isset($content_security_status) && $content_security_status): ?>
  <link rel="stylesheet" href="<?php echo assets_url() ?>plyr/1.8.2/skin.css">
  <!-- <video preload="none" id="player" autoplay controls crossorigin></video> -->
  <div id="player"></div>
  <script src="<?php echo assets_url() ?>plyr/1.8.2/flowplayertokens.js"></script>
  <script src="<?php echo assets_url() ?>plyr/1.8.2/flowplayer.min.js"></script>
  <script src="<?php echo assets_url() ?>plyr/1.8.2/hls.min.js"></script>
  <script src="<?php echo assets_url() ?>plyr/1.8.2/flowplayer.vod-quality-selector.min.js"></script>

  <script>
  var player = flowplayer("#player", {
      embed: false,
      hlsQualities: true,
      seekable: true,
      clip: {
      sources: [
      { type: "application/x-mpegurl", src: '<?php echo $file_url ?>' }
      ],
      qualities: ['240p', '360p', '480p', '720p', '1080p'],
      defaultQuality: '480p'
      }
      })
  </script>
<?php else: ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo assets_url() ?>plyr/plyr.css">
  <script src="<?php echo assets_url() ?>plyr/plyr.min.js"></script>
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
  <div id="player" data-plyr-provider="vimeo" data-plyr-embed-id="<?php echo $file_url; ?>"></div>

  <script>
  window.onload = function () {
          const player = new Plyr('#player', {
              /* options */
          });
  }
  </script>
<?php endif; ?>
