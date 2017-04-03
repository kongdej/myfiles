<head>
  <link href="video-js.css" rel="stylesheet">

  <!-- If you'd like to support IE8 -->
  <script src="ie8/videojs-ie8.min.js"></script>
</head>

<body>
<?php
	echo $_GET['id'];
?>
  <video id="my-video" class="video-js vjs-default-skin vjs-big-play-centered vjs-16-9" controls preload="auto" width="640" height="264"
  poster="contents/MY_VIDEO_POSTER.jpg" data-setup="{}">
    <source src="contents/MY_VIDEO.mp4" type='video/mp4'>
    <source src="MY_VIDEO.webm" type='video/webm'>
    <p class="vjs-no-js">
      To view this video please enable JavaScript, and consider upgrading to a web browser that
      <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
    </p>
  </video>

  <script src="video.js"></script>
</body>

