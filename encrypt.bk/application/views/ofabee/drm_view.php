<!DOCTYPE html>
<html>
<head>

<script src="https://cdn.radiantmediatechs.com/rmp/5.5.7/js/rmp-shaka.min.js"></script>
<!-- Player container element -->
<div id="rmpPlayer"></div>
<!-- Set up player configuration options -->
<script>
var src = {
  dash: 'https://ofabee-test.s3-ap-southeast-1.amazonaws.com/content_security/h264.mpd',
  // here is our AES-128 HLS fallback 
  hls: 'https://ofabee-test.s3-ap-southeast-1.amazonaws.com/content_security/h264_master.m3u8'
};
var settings = {
  licenseKey: 'Y2tlZWlpZXNzZSEqXyU3djA3c2tkM2V1PSt2djJ2a3Vza2M9KzAyeWVpP3JvbTVkYXNpczMwZGIwQSVfKg==',
  src: src,
  width: 640,
  height: 360,
  poster: 'https://your-poster-url.jpg',
  dashFirst: true,
  hlsEngine: 'hlsjs',
  // passing DRM settings
  shakaDrm: {
    servers: {
      'com.widevine.alpha': 'https://proxy.uat.widevine.com/proxy/',
      'com.microsoft.playready': '//drm-playready-licensing.axtest.net/AcquireLicense'
    }
  },
  // shakaRequestConfiguration: {
  //   license: {
  //     headers: {
  //       'X-AxDRM-Message': 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ2ZXJzaW9uIjoxLCJjb21fa2V5X2lkIjoiYjMzNjRlYjUtNTFmNi00YWUzLThjOTgtMzNjZWQ1ZTMxYzc4IiwibWVzc2FnZSI6eyJ0eXBlIjoiZW50aXRsZW1lbnRfbWVzc2FnZSIsImtleXMiOlt7ImlkIjoiOWViNDA1MGQtZTQ0Yi00ODAyLTkzMmUtMjdkNzUwODNlMjY2IiwiZW5jcnlwdGVkX2tleSI6ImxLM09qSExZVzI0Y3Iya3RSNzRmbnc9PSJ9XX19.4lWwW46k-oWcah8oN18LPj5OLS5ZU-_AQv7fe0JhNjA' 
  //     }
  //   }
  // }
};
var elementID = 'rmpPlayer';
var rmp = new RadiantMP(elementID);
rmp.init(settings);
</script>
</html>