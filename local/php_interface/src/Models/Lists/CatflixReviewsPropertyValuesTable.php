<?php
namespace Models\Lists;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\Entity\Query\Join;

/**
 * Class ElementPropS22Table
 *
 * Fields:
 * <ul>
 * <li> IBLOCK_ELEMENT_ID int mandatory
 * <li> PROPERTY_78 int optional
 * <li> PROPERTY_80 double optional
 * <li> PROPERTY_81 text optional
 * <li> PROPERTY_86 double optional
 * </ul>
 *
 * @package Bitrix\Iblock
 **/

use Models\CatflixStreamsTable as CatflixStreams;

class CatflixReviewsPropertyValuesTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_iblock_element_prop_s22';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            // Primary key (element ID)
            (new IntegerField('IBLOCK_ELEMENT_ID'))
                ->configurePrimary(true)
                ->configureTitle('Element ID'),

            // Review author (user ID)
            (new IntegerField('USER_ID'))
                ->configureColumnName('PROPERTY_78')
                ->configureTitle('Reviewer User ID'),

            // Review rating
            (new FloatField('RATING'))
                ->configureColumnName('PROPERTY_80')
                ->configureTitle('Rating'),

            // Review comment text
            (new TextField('COMMENT'))
                ->configureColumnName('PROPERTY_81')
                ->configureTitle('Comment'),

            // Link to stream by stream ID
            (new IntegerField('STREAM_ID'))
                ->configureColumnName('PROPERTY_86')
                ->configureTitle('Stream ID'),

            // Relation back to the stream (One review → One stream)
            (new Reference(
                'STREAM',
                CatflixStreams::class,
                Join::on('this.STREAM_ID', 'ref.id')
            ))->configureJoinType('inner'),
        ];
    }
}