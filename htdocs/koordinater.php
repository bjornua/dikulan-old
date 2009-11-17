<?php
  $seats = simplexml_load_file('pladser.xml');
  
  
  $gulvplan = imagecreatefrompng('gulvplan.png');
  $dot = imagecreatefrompng('red-dot.png');
  $dot_width  = imagesx($dot);
  $dot_height = imagesy($dot);
  
  foreach($seats as $seat){
    $x = $seat['x'] - floor($dot_width / 2);
    $y = $seat['y'] - floor($dot_width / 2);
    imagecopy($gulvplan, $dot, $x, $y, 0, 0, $dot_width, $dot_height);
  }
  
  
  $result = $gulvplan;
  
  
  
  if(!headers_sent() && is_resource($result)){
    header('Content-type: image/png');
    echo imagepng($result);
  }
?>
