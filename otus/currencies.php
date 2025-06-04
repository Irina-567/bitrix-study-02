<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Курс валюты");
?>

<?php
$APPLICATION->IncludeComponent(
    "otus:currency_rate",
    ".default",
    [
        "CURRENCY" => "USD", // можно оставить по умолчанию, пользователь выберет в параметрах
    ]
);

?>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
