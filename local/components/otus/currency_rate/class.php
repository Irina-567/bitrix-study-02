<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Currency\CurrencyTable;
use CCurrencyRates;

Loc::loadMessages(__FILE__);

class CurrencyRateComponent extends \CBitrixComponent
{
    protected $request;

    /**
     * Preparation and validation of component parameters
     */
    public function onPrepareComponentParams($arParams)
    {
        if (empty($arParams['CURRENCY'])) {
            $arParams['CURRENCY'] = 'USD';
        }

        return $arParams;
    }

    /**
     * Get list of currencies
     */
    private function getCurrencies(): array
    {
        return CurrencyManager::getCurrencyList();
    }

    /**
     * Get selected currency
     */
    private function getSelectedCurrency(): string
    {
        return $this->request->getPost('currency') ?: $this->arParams['CURRENCY'];
    }

    /**
     * Get currency rate relative to base currency
     */
    private function getCurrencyRate(string $currency, string $baseCurrency): float
    {
        return CCurrencyRates::GetConvertFactor($currency, $baseCurrency);
    }

    /**
     * Component's main logic
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('currency')) {
                ShowError(Loc::getMessage('OTUS_CURRENCY_MODULE_ERROR'));
                return;
            }

            $this->request = Application::getInstance()->getContext()->getRequest();

            $currency = $this->getSelectedCurrency();
            $baseCurrency = CurrencyManager::getBaseCurrency();
            $rate = $this->getCurrencyRate($currency, $baseCurrency);

            $this->arResult = [
                'CURRENCY' => $currency,
                'BASE_CURRENCY' => $baseCurrency,
                'RATE' => $rate,
                'CURRENCIES' => $this->getCurrencies(),
            ];

            $this->includeComponentTemplate();
        } catch (\Bitrix\Main\SystemException $e) {
            ShowError($e->getMessage());
        }
    }
}
