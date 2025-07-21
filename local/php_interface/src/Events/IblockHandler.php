<?php

namespace Events;

use Bitrix\Main\Loader;

class IblockHandler
{
    protected const IBLOCK_ID = 26;
    protected const LOG_PATH = '/local/debug2.log';

    protected static function log($message, $context = []): void
    {
        $entry = '[' . date('Y-m-d H:i:s') . '] ' . $message;
        if (!empty($context)) {
            $entry .= "\n" . print_r($context, true);
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . self::LOG_PATH, $entry . "\n", FILE_APPEND);
    }

    public static function onElementAfterUpdate(&$arFields): void
    {
        if (!Loader::includeModule('iblock') || !Loader::includeModule('crm')) {
            self::log('Failed to load modules');
            return;
        }

        if ((int)$arFields['IBLOCK_ID'] !== self::IBLOCK_ID) {
            return;
        }

        $elementId = (int)$arFields['ID'];
        self::log('onAfterIBlockElementUpdate triggered', ['ELEMENT_ID' => $elementId]);

        $props = [];
        foreach (['DEAL_ID', 'AMOUNT', 'RESPONSIBLE_ID'] as $code) {
            $res = \CIBlockElement::GetProperty(self::IBLOCK_ID, $elementId, [], ['CODE' => $code]);
            if ($prop = $res->Fetch()) {
                $props[$code] = $prop['VALUE'];
            }
        }

        $dealId = (int)($props['DEAL_ID'] ?? 0);
        $amountRaw = (string)($props['AMOUNT'] ?? '');
        $responsibleId = (int)($props['RESPONSIBLE_ID'] ?? 0);

        self::log('Extracted props', [
            'DEAL_ID' => $dealId,
            'AMOUNT_RAW' => $amountRaw,
            'RESPONSIBLE_ID' => $responsibleId
        ]);

        if ($dealId <= 0) {
            self::log('Missing or invalid DEAL_ID');
            return;
        }

        // Parse money format (e.g., 1000.00|EUR)
        [$amount, $currency] = explode('|', $amountRaw . '|');
        $amount = (float)trim($amount);
        $currency = trim($currency) ?: 'RUB';

        $updateFields = [
            'OPPORTUNITY' => $amount,
            'CURRENCY_ID' => $currency,
        ];

        if ($responsibleId > 0) {
            $updateFields['ASSIGNED_BY_ID'] = $responsibleId;
        }

        $deal = new \CCrmDeal();
        $result = $deal->Update($dealId, $updateFields);

        if ($result) {
            self::log('Deal updated successfully', [
                'DEAL_ID' => $dealId,
                'UPDATED_FIELDS' => $updateFields
            ]);
        } else {
            global $APPLICATION;
            $error = $APPLICATION->GetException();
            self::log('Failed to update Deal', [
                'DEAL_ID' => $dealId,
                'ERROR' => $error ? $error->GetString() : 'Unknown'
            ]);
        }
    }

}
