<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public_bitrix24/crm/configs/document_numerators/index.php");
$APPLICATION->SetTitle(GetMessage("TITLE"));
$APPLICATION->IncludeComponent(
	'bitrix:crm.document_numerators.list',
	'',
	false
);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
