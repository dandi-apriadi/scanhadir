<?php
header('Content-Type: text/plain');
echo "GD Check:\n";
echo "imagecreatetruecolor: " . (function_exists('imagecreatetruecolor') ? 'YES' : 'NO') . "\n";
echo "imagepng: " . (function_exists('imagepng') ? 'YES' : 'NO') . "\n";
echo "imagecolorallocate: " . (function_exists('imagecolorallocate') ? 'YES' : 'NO') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
?>
