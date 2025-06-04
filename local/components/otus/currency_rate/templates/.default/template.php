<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

    use Bitrix\Main\Localization\Loc;

    Loc::loadMessages(__FILE__);
?>

<div id="currency-rate-component">
    <form id="currency-form">
        <label for="currency"><?= Loc::getMessage('OTUS_SELECT_CURRENCY') ?></label>
        <select name="currency" id="currency" onchange="submitCurrencyForm()">
            <?php foreach ($arResult['CURRENCIES'] as $code => $name): ?>
                <option value="<?= htmlspecialcharsbx($code) ?>" <?= $arResult['CURRENCY'] === $code ? 'selected' : '' ?>>
                    <?= htmlspecialcharsbx($name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <div id="currency-result">
        <?php if (!empty($arResult['RATE'])): ?>
            <hr>
            <div>
                <strong><?= Loc::getMessage('OTUS_EXCHANGE_RATE') ?></strong><br>
                <?= htmlspecialcharsbx($arResult['CURRENCY']) ?> /
                <?= htmlspecialcharsbx($arResult['BASE_CURRENCY']) ?> =
                <?= number_format($arResult['RATE'], 4) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function submitCurrencyForm() {
        const form = document.getElementById('currency-form');
        const formData = new FormData(form);
        const resultBlock = document.getElementById('currency-result');

        formData.append('ajax', 'Y');

        resultBlock.innerHTML = '<hr><div><em><?= Loc::getMessage("OTUS_LOADING") ?></em></div>';

        fetch(location.href, {
            method: 'POST',
            body: formData,
        })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const updatedResult = doc.getElementById('currency-result');
                resultBlock.innerHTML = updatedResult ? updatedResult.innerHTML : '<div><?= Loc::getMessage("OTUS_ERROR_LOADING") ?></div>';
            });
    }
</script>
