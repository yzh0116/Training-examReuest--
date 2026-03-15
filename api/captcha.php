<?php
session_start();

// 检查 GD 库是否安装
if (!extension_loaded('gd')) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'GD library not installed']);
    exit;
}

function generateCaptcha($width = 120, $height = 40) {
    try {
        $image = imagecreatetruecolor($width, $height);
        if (!$image) {
            throw new Exception('Failed to create image');
        }
        
        // 背景色 - 沙滩粉色系
        $bgColor = imagecolorallocate($image, 253, 248, 245);
        imagefill($image, 0, 0, $bgColor);
        
        // 生成验证码 - 使用 random_int 更好的随机性
        $code = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        for ($i = 0; $i < 4; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        $_SESSION['captcha'] = $code;
        
        // 文字颜色
        $textColor = imagecolorallocate($image, 26, 26, 26);
        
        // 使用内置字体绘制文字
        $fontSize = 5;
        $x = 15;
        for ($i = 0; $i < 4; $i++) {
            $y = random_int(22, 28);
            imagestring($image, $fontSize, $x, $y, $code[$i], $textColor);
            $x += 25;
        }
        
        // 添加干扰线
        for ($i = 0; $i < 3; $i++) {
            $lineColor = imagecolorallocate($image, 200, 180, 160);
            imageline($image, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $lineColor);
        }
        
        // 添加噪点
        for ($i = 0; $i < 50; $i++) {
            $dotColor = imagecolorallocate($image, random_int(180, 220), random_int(160, 200), random_int(140, 180));
            imagesetpixel($image, random_int(0, $width), random_int(0, $height), $dotColor);
        }
        
        // 输出图片
        header('Content-Type: image/png');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        imagepng($image);
        imagedestroy($image);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
}

generateCaptcha();
