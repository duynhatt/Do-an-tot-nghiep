<?php
require_once 'controller/trangchu.php';
require_once '../commons/function.php';

$act=$_GET['act']??'/';
match ($act) {
    '/' => (new trang_chu())->trang_chu(),
};