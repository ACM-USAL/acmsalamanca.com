<?php
$array_videos=["404.mp4","404-2.mp4","404-3.mp4","404-4.mp4","404-5.mp4"];
$video=$array_videos[rand(0,count($array_videos))];

header("Location: http://".$_SERVER['SERVER_NAME']."/Paginas_de_error/videos/".$video);

?>
