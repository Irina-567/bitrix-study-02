<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    "NAME" => Loc::getMessage("OTUS_COMPONENT_NAME"),
    "DESCRIPTION" => Loc::getMessage("OTUS_COMPONENT_DESC"),
    "ICON" => "/images/euro-icon.gif",
    "PATH" => [
        "ID" => "otus",
        "NAME" => Loc::getMessage("OTUS_GROUP_NAME"),
        "CHILD" => [
            "ID" => "otus_misc",
            "NAME" => Loc::getMessage("OTUS_CHILD_GROUP_NAME"),
        ]
    ],
];
