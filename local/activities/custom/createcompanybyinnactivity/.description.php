<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

$arActivityDescription = [
    "NAME" => Loc::getMessage("CREATECOMPANYBYINN_DESCR_NAME"),
    "DESCRIPTION" => Loc::getMessage("CREATECOMPANYBYINN_DESCR_DESCR"),
    "TYPE" => "activity",
    "CLASS" => "CreateCompanyByInnActivity",
    "JSCLASS" => "BizProcActivity",
    "CATEGORY" => [
        "ID" => "other",
    ],
    "RETURN" => [
        "Companyname" => [
            "NAME" => Loc::getMessage("CREATECOMPANYBYINN_DESCR_FIELD_TEXT"),
            "TYPE" => "string",
        ],
    ],
];
