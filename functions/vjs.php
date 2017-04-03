<?php
	$file =  $_GET['f'];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>HTML5 Video Player</title>
  <script src="videojs/video.js" type="text/javascript" charset="utf-8"></script>
  <script type="text/javascript">
    VideoJS.setupAllWhenReady();
  </script>
  <link rel="stylesheet" href="videojs/video-js.css" type="text/css" media="screen" title="Video JS">
</head>
<body>

  <div>
    <video id="video_1" class="video-js" width="640" height="264" controls="controls" preload="auto" poster="">
      <source src="<?php echo $file; ?>" type='video/mp4' />
    </video>
  </div>

</body>
</html>


