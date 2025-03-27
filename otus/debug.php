<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
$date = date('d.m.Y H:i:s');
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logs/hw02_task01.log', $date . PHP_EOL, FILE_APPEND);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';