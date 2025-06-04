<?php

namespace Models\Lists;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;

/**
 * Class ElementPropS21Table
 *
 * Fields:
 * <ul>
 * <li> IBLOCK_ELEMENT_ID int mandatory
 * <li> PROPERTY_75 text optional
 * <li> PROPERTY_76 text optional
 * <li> PROPERTY_77 text optional
 * </ul>
 *
 * @package Bitrix\Iblock
 **/

class CatflixUsersPropertyValuesTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_iblock_element_prop_s21';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            'IBLOCK_ELEMENT_ID' => (new IntegerField('IBLOCK_ELEMENT_ID'))
                ->configurePrimary(true)
                ->configureTitle('Element ID'),

            // Alias Email
            'EMAIL' => (new TextField('EMAIL'))
                ->configureColumnName('PROPERTY_75')
                ->configureTitle('Email'),

            // Alias Username
            'USERNAME' => (new TextField('USERNAME'))
                ->configureColumnName('PROPERTY_76')
                ->configureTitle('Username'),

            // Alias Registration Date
            'REGISTRATION_DATE' => (new TextField('REGISTRATION_DATE'))
                ->configureColumnName('PROPERTY_77')
                ->configureTitle('Registration Date'),
        ];
    }
}