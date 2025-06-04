<?php

namespace Models;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Relations\Reference,
    Bitrix\Main\ORM\Fields\Relations\OneToMany,
    Bitrix\Main\ORM\Fields\Relations\ManyToMany,
    Bitrix\Main\Entity\Query\Join;


use Models\Lists\CatflixUsersPropertyValuesTable as CatflixUsers;
use Models\Lists\CatflixReviewsPropertyValuesTable as CatflixReviews;
use Models\Lists\CatflixCatsPropertyValuesTable as CatflixCats;

/**
 * Class BookTable
 *
 * @package Models
 */
class CatflixStreamsTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'catflix_streams';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            'id' => (new IntegerField('id',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true),
            'title' => (new StringField('title',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_TITLE_FIELD')),
            'short_description' => (new StringField('short_description',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_SHORT_DESCRIPTION_FIELD')),
            'stream_length' => (new IntegerField('stream_length',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_STREAM_LENGTH_FIELD')),

            'user_id' => (new IntegerField('user_id',
                []
            ))->configureTitle(Loc::getMessage('_ENTITY_USER_ID_FIELD')),

            //user
            (new Reference('USER', CatflixUsers::class, Join::on('this.user_id', 'ref.IBLOCK_ELEMENT_ID')))
                ->configureJoinType('inner'),

            //cats
            (new ManyToMany('CATS', CatflixCats::class))
                ->configureTableName('streams_cats')
                ->configureLocalPrimary('id', 'stream_id')
                ->configureLocalReference('CATFLIX_STREAMS')
                ->configureRemotePrimary('IBLOCK_ELEMENT_ID', 'cat_id')
                ->configureRemoteReference('b_iblock_element_prop_s23'),

            //reviews
            (new OneToMany('REVIEWS', CatflixReviews::class, 'STREAM'))
                ->configureJoinType('left'),

        ];
    }


}