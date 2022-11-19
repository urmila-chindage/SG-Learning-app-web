<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- optimize mobile versions -->

<!-- <link rel="stylesheet" href="<?php echo assets_url() ?>flowplayer/skin/functional.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>flowplayer/skin/flowplayer.audio.css"> -->
<link rel="stylesheet" href="<?php echo assets_url() ?>plyr/plyr.css">
<style>
    /* mixed playlist player */
    /* make cover image fill container width */
    .is-audio.flowplayer .fp-player {
        background-size: cover;          /* default: contain */
        background-position: top center; /* default: center */
    }
    /* simple playlist styling */
    .flowplayer {
        background-color: #333;
        /* allow room for playlist buttons */
        margin-bottom: 100px;
    }
    .fp-player{ background-size: auto 100% !important;}
</style>

<!-- Flowplayer library -->
<!-- <script src="<?php echo assets_url() ?>flowplayer/flowplayer.min.js"></script>
<script src="<?php echo assets_url() ?>flowplayer/flowplayer.audio.min.js"></script> -->
<script src="<?php echo assets_url() ?>plyr/plyr.min.js"></script>
<!-- Specific script for this demo -->
<script>
    // window.onload = function () {

    //     flowplayer("#mixed", {
    //         ratio: 9 / 16,
    //         splash: true,
    //         embed : false,
    //         playlist: [{
    //                 audio: true,
    //                 coverImage: "<?php echo assets_url() ?>images/speaker.png",
    //                 sources: [
    //                     {type: "audio/mpeg", src: "<?php echo $file_url ?>"}
    //                 ]
    //             }]

    //     });

    //     $('.fp-brand').remove();
    //     $('.fp-player').next('a').remove();

    // };
    window.onload = function () {
        const player = new Plyr('#player', {
            /* options */
        });
        player.source = {
            type: 'audio',
            title: 'Example title',
            sources: [
                {
                    src: '<?php echo $file_url ?>',
                    type: 'audio/mp3',
                }
            ],
        };
    }
</script>
<!-- <div id="mixed"></div> -->
<audio id="player" controls>
</audio>