<?php

// Load các dependencies cần thiết từ Composer và khởi tạo ứng dụng Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Khởi tạo kernel của console
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Chạy lệnh queue:work để xử lý job trong queue và dừng sau khi xử lý xong
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput([
        'command' => 'queue:work',
        '--stop-when-empty' => true, // Dừng lại sau khi không còn job nào trong queue
    ]),
    new Symfony\Component\Console\Output\BufferedOutput
);

// Kết thúc ứng dụng và trả về status
$kernel->terminate($input, $status);
echo "Queue work processed with status: " . $status;
