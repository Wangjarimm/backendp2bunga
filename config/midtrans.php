<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-FPdU6oSa2l-tKqfNsYwYhxyJ';  // Replace with your actual server key
\Midtrans\Config::$clientKey = 'SB-Mid-client-yzbioVMVKl_lTmWe';
\Midtrans\Config::$isProduction = false;           // Set to true if in production environment
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
?>
