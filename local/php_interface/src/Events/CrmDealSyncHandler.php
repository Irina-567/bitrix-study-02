<?php

namespace Events;

use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementRequestsTable;

class CrmDealSyncHandler
{
    protected const IBLOCK_ID = 26;
    protected const LOG_PATH = '/local/debug1.log';

    /**
     * Writes a log message with timestamp
     */
    protected static function log($message, $context = []): void
    {
        $entry = '[' . date('Y-m-d H:i:s') . '] ' . $message;
        if (!empty($context)) {
            $entry .= "\n" . print_r($context, true);
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . self::LOG_PATH, $entry . "\n", FILE_APPEND);
    }

    protected static function formatMoneyProperty($value, $currency): array
    {
        return [
            'VALUE' => $value,
            'CURRENCY' => $currency,
        ];
    }

    /**
     * Handle Deal creation: create a corresponding Request InfoBlock element
     */
    public static function onCrmDealAdd(&$arFields): void
    {
        if (!Loader::includeModule('iblock') || !Loader::includeModule('crm')) {
            self::log('Failed to load modules in onCrmDealAdd');
            return;
        }

        if (empty($arFields['ID'])) {
            self::log('Deal ID not set during onCrmDealAdd', $arFields);
            return;
        }

        $dealId = $arFields['ID'];
        $deal = \CCrmDeal::GetByID($dealId);
        if (!$deal) {
            self::log('Deal not found after creation', ['DEAL_ID' => $dealId]);
            return;
        }

        self::log('Creating Request for Deal', $deal);

        $value = $deal['OPPORTUNITY'] . '|' . $deal['CURRENCY_ID'];

        $element = new \CIBlockElement;
        $result = $element->Add([
            'IBLOCK_ID' => self::IBLOCK_ID,
            'NAME' => 'Request for Deal #' . $deal['TITLE'],
            'ACTIVE' => 'Y',
            'PROPERTY_VALUES' => [
                'DEAL_ID' => $dealId,
                'AMOUNT' => $value,
                'RESPONSIBLE_ID' => $deal['ASSIGNED_BY_ID'],
            ]
        ]);

        if ($result) {
            self::log("Request element created successfully", ['ELEMENT_ID' => $result]);
        } else {
            self::log("Failed to create Request element", ['ERRORS' => $element->LAST_ERROR]);
        }
    }

    /**
     * Handle Deal update: find and update the matching Request element
     */
    public static function onCrmDealUpdate(&$arFields): void
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock') || !\Bitrix\Main\Loader::includeModule('crm')) {
            self::log('Failed to load modules in onCrmDealUpdate');
            return;
        }

        if (empty($arFields['ID'])) {
            self::log('Deal ID is missing in onCrmDealUpdate', $arFields);
            return;
        }

        $dealId = $arFields['ID'];

        self::log('Updating Request for Deal', [
            'DEAL_ID' => $dealId,
            'FIELDS' => $arFields,
        ]);

        $elementRes = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => self::IBLOCK_ID,
                'PROPERTY_DEAL_ID' => $dealId
            ],
            false,
            false,
            ['ID']
        );

        if ($element = $elementRes->Fetch()) {
            $deal = \CCrmDeal::GetByID($dealId);
            if (!$deal) {
                self::log('Deal not found during update', ['DEAL_ID' => $dealId]);
                return;
            }

            $elementId = $element['ID'];
            $iblockElement = new \CIBlockElement;

            // Format the money value as string: "amount|currency"
            $amountValue = $deal['OPPORTUNITY'] . '|' . $deal['CURRENCY_ID'];

            $propertyValues = [
                'AMOUNT' => $amountValue,
                'RESPONSIBLE_ID' => $deal['ASSIGNED_BY_ID'],
            ];

            $result = $iblockElement->SetPropertyValuesEx($elementId, self::IBLOCK_ID, $propertyValues);

            if ($result !== false) {
                self::log("Request updated successfully", ['ELEMENT_ID' => $elementId]);
            } else {
                self::log("Failed to update Request element", [
                    'ELEMENT_ID' => $elementId,
                    'LAST_ERROR' => $iblockElement->LAST_ERROR,
                ]);
            }
        } else {
            self::log("No matching Request element found for DEAL_ID", [
                'DEAL_ID' => $dealId,
                'LOOKED_FOR' => $dealId,
                'IBLOCK_ID' => self::IBLOCK_ID,
            ]);
        }
    }

    /**
     * Handle Deal deletion: remove all linked Request elements
     */
    public static function onCrmDealDelete($dealId): void
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock')) {
            self::log('Failed to load iblock module on deal delete');
            return;
        }

        self::log('onCrmDealDelete triggered', ['DEAL_ID' => $dealId]);

        // Load Request elements with matching DEAL_ID property
        $elements = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => self::IBLOCK_ID,
                'PROPERTY_DEAL_ID' => $dealId,
            ],
            false,
            false,
            ['ID', 'NAME']
        );

        $found = false;

        while ($element = $elements->Fetch()) {
            $found = true;
            $deleteResult = \CIBlockElement::Delete($element['ID']);

            if ($deleteResult) {
                self::log("Deleted Request element", ['ELEMENT_ID' => $element['ID'], 'NAME' => $element['NAME']]);
            } else {
                self::log("Failed to delete Request element", ['ELEMENT_ID' => $element['ID'], 'NAME' => $element['NAME']]);
            }
        }

        if (!$found) {
            self::log("No matching Request elements found for DEAL_ID", [
                'DEAL_ID' => $dealId,
                'IBLOCK_ID' => self::IBLOCK_ID,
            ]);
        }
    }
}
