<?php
session_start();

// 如果 GD 库不可用，使用文本验证码
$code = '';
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
for ($i = 0; $i < 4; $i++) {
    $code .= $chars[random_int(0, strlen($chars) - 1)];
}

$_SESSION['captcha'] = $code;

// 返回 SVG 格式的验证码（不需要 GD 库）
header('Content-Type: image/svg+xml');
header('Cache-Control: no-cache, no-store, must-revalidate');

$width = 120;
$height = 40;

// 生成随机颜色
$bgColor = '#FDF8F5';
$textColor = '#1A1A1A';

// 生成 SVG
$svg = <<<SVG
<svg width="{$width}" height="{$height}" xmlns="http://www.w3.org/2000/svg">
  <rect width="100%" height="100%" fill="{$bgColor}"/>
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" 
        font-family="monospace" font-size="20" fill="{$textColor}" 
        font-weight="bold" letter-spacing="8">{$code}</text>
SVG;

// 添加干扰线
for ($i = 0; $i < 3; $i++) {
    $x1 = random_int(0, $width);
    $y1 = random_int(0, $height);
    $x2 = random_int(0, $width);
    $y2 = random_int(0, $height);
    $color = sprintf('#%02x%02x%02x', random_int(180, 220), random_int(160, 200), random_int(140, 180));
    $svg .= "<line x1=\"{$x1}\" y1=\"{$y1}\" x2=\"{$x2}\" y2=\"{$y2}\" stroke=\"{$color}\" stroke-width=\"1\"/>";
}

$svg .= '</svg>';

echo $svg;
