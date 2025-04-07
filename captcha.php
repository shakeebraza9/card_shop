<?php
session_start();

// Set headers for PNG output.
header('Content-type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Expires: Sat, 1 Jan 2000 00:00:00 GMT');

// Define image dimensions (30% smaller than original dimensions)
$width = (int)(110 * 0.7);  // ~77px
$height = (int)(76 * 0.7);  // ~53px

// Create base image and allocate colors.
$image = imagecreatetruecolor($width, $height);
$bg_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
$noise_color = imagecolorallocate($image, 100, 100, 100);

// Fill background.
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Generate CAPTCHA code (5 characters: digits, lowercase, and uppercase letters).
$charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$captcha_length = 5;
$captcha_code = '';
$charset_length = strlen($charset);
for ($i = 0; $i < $captcha_length; $i++) {
    $captcha_code .= $charset[random_int(0, $charset_length - 1)];
}
$_SESSION['captcha_code'] = $captcha_code;  // Stored as generated (case sensitive)

// Add noise: dots only (less dots for better readability)
for ($i = 0; $i < 50; $i++) {
    imagesetpixel($image, random_int(0, $width - 1), random_int(0, $height - 1), $noise_color);
}

// Set font parameters.
$font_path = __DIR__ . '/fonts/arial.ttf'; // Ensure the font file exists.
$base_font_size = 14;

$code_length = strlen($captcha_code);
$x = 5; // Starting x position.
for ($i = 0; $i < $code_length; $i++) {
    $char = $captcha_code[$i];
    $angle = random_int(-10, 10);  // Reduced angle range for readability.
    $font_size = $base_font_size + random_int(-1, 1);  // Slight font size variation.
    $y = random_int($height - 15, $height - 5);  // Vertical offset.
    $bbox = imagettftext($image, $font_size, $angle, $x, $y, $text_color, $font_path, $char);
    // Update x position based on character width plus minimal spacing.
    $x += ($bbox[2] - $bbox[0]) + random_int(2, 3);
}

// --- Apply Wave Distortion ---
// Use a reduced amplitude for better legibility.
$distorted = imagecreatetruecolor($width, $height);
imagefill($distorted, 0, 0, $bg_color);
$waveAmplitude = 1; // Lower amplitude.
$wavePeriod = 30;

for ($y = 0; $y < $height; $y++) {
    $offset = (int)($waveAmplitude * sin(2 * M_PI * $y / $wavePeriod));
    for ($x = 0; $x < $width; $x++) {
        $srcX = $x + $offset;
        if ($srcX >= 0 && $srcX < $width) {
            $color = imagecolorat($image, $srcX, $y);
            imagesetpixel($distorted, $x, $y, $color);
        }
    }
}

// Optionally, you can apply a Gaussian blur for smoothing.
// imagefilter($distorted, IMG_FILTER_GAUSSIAN_BLUR);

// Output final CAPTCHA image and free memory.
imagepng($distorted);
imagedestroy($image);
imagedestroy($distorted);
?>
