<?php
session_start();

// Generate a random CAPTCHA string
$randomString = generateRandomString(8);

// Store the CAPTCHA string in the session
$_SESSION["captcha"] = $randomString;

// Create an image with the CAPTCHA string
$width = 150;
$height = 50;
$im = imagecreatetruecolor($width, $height);
$bgColor = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
$grey = imagecolorallocate($im, 128, 128, 128);

imagefilledrectangle($im, 0, 0, $width, $height, $bgColor);

// Specify the correct font path
$font = __DIR__ . '/RobotoMonoItalicVariableFont.ttf'; // Replace 'arial.ttf' with the actual font file name and path if needed.

// Calculate the size of the text
$fontSize = 15;
$textBox = imagettfbbox($fontSize, 0, $font, $randomString);
$textWidth = $textBox[4] - $textBox[0];
$textHeight = $textBox[1] - $textBox[5];

// Calculate the position to center the text
$x = ($width - $textWidth) / 2-5;
$y = ($height - $textHeight) / 2 + $fontSize;

// Draw the text within the boundaries of the image
for ($i = 0; $i < strlen($randomString); $i++) {
    $char = $randomString[$i];
    $color = ($i % 2 === 0) ? $black : $grey;
    imagettftext($im, $fontSize, 0, $x + $i * $fontSize, $y, $color, $font, $char);
}

header('Content-type: image/png');
imagepng($im);
imagedestroy($im);

function generateRandomString($length = 8) {
    $characters = 'qwertyuiopasdfghjklzxcvbnm74185296930QWERTYUIOPASDFGHJKLZXCVBNM';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>
