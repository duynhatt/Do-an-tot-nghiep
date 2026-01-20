<?php
require_once 'controller/maincontroller.php';
require_once 'controller/aboutcontroller.php';
require_once 'controller/shopcontroller.php';
require_once 'controller/contactcontroller.php';

$act=$_GET['act']??'/';
match ($act){
        '/' => (new trang_chu())->trang_chu(),
    'about' => (new aboutController())->about(),
    'shop' => (new shopController())->shop(),
    'shop_single' => (new shopController())->shop_single(),
    'contact' => (new contactController())->contact()
};