<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arCurrencies = \Bitrix\Currency\CurrencyManager::getCurrencyList();

$arComponentParameters = [
    "PARAMETERS" => [
        "CURRENCY" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage("OTUS_CURRENCY_PARAM"),
            "TYPE" => "LIST",
            "VALUES" => $arCurrencies,
            "DEFAULT" => "USD",
            "REFRESH" => "Y",
        ],
    ],
];
