<?php

if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

if (file_exists(__DIR__ . '/src/autoloader.php')) {
    require_once __DIR__ . '/src/autoloader.php';
}

if (class_exists('UserTypes\MedicalBooking')) {
    file_put_contents(__DIR__.'/debug.log', "Custom type loaded\n", FILE_APPEND);
}

include_once __DIR__ . '/classes/Dadata.php';

use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();

//infoblock custom field type
$eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    ['UserTypes\MedicalBooking', 'GetUserTypeDescription']
);

