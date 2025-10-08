<?php
// Generate a default avatar image
header('Content-Type: image/png');

// Create a 100x100 image
$image = imagecreate(100, 100);

// Define colors
$background = imagecolorallocate($image, 243, 244, 246); // Light gray background
$avatar_color = imagecolorallocate($image, 156, 163, 175); // Gray avatar color

// Fill background
imagefill($image, 0, 0, $background);

// Draw circle for head
imagefilledellipse($image, 50, 35, 30, 30, $avatar_color);

// Draw body (simple rectangle)
imagefilledrectangle($image, 20, 54, 80, 100, $avatar_color);

// Output the image
imagepng($image);
imagedestroy($image);
?>
