<?php
namespace Models\Lists;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;

/**
 * Class ElementPropS23Table
 *
 * Fields:
 * <ul>
 * <li> IBLOCK_ELEMENT_ID int mandatory
 * <li> PROPERTY_83 text optional
 * <li> PROPERTY_84 int optional
 * <li> PROPERTY_85 text optional
 * </ul>
 *
 * @package Bitrix\Iblock
 **/

class CatflixCatsPropertyValuesTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_iblock_element_prop_s23';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            'IBLOCK_ELEMENT_ID' => (new IntegerField('IBLOCK_ELEMENT_ID',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_S23_ENTITY_IBLOCK_ELEMENT_ID_FIELD'))
                ->configurePrimary(true)
                ->configureTitle('Element ID')
            ,
            //CAT_NAME
            'CAT_NAME' => (new TextField('CAT_NAME'))
                ->configureColumnName('PROPERTY_83')
                ->configureTitle('Cats name')
            ,
            //CAT_OWNER
            'CAT_OWNER' => (new TextField('CAT_OWNER'))
                ->configureColumnName('PROPERTY_84')
                ->configureTitle('Cats owner')
            ,
            //DESCRIPTION
            'DESCRIPTION' => (new TextField('DESCRIPTION'))
            ->configureColumnName('PROPERTY_85')
            ->configureTitle('Description')
            ,
        ];
    }
}