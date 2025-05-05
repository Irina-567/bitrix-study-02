<?php

namespace Models\Lists;

use Models\AbstractIblockPropertyValuesTable;
use Bitrix\Main\Entity\ReferenceField;

class DoctorsPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    const IBLOCK_ID = 19;

//    public static function getMap(): array
//    {
//        $map = [
//            'SPECIALISATION' => new ReferenceField(
//                'SPECIALISATION',
//                SpecialisationsPropertyValuesTable::class,
//                ['=this.SPECIALISATION_ID' => 'ref.IBLOCK_ELEMENT_ID']
//            )
//        ];
//
//        return parent::getMap() + $map;
//
//    }
}
