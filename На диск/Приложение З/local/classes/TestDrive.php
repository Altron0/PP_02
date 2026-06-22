<?php

namespace Local\Classes;

use Bitrix\Main\HttpRequest;
use DateTime;
use Local\Classes\Models\DrivesTable;

class TestDrive
{
    public static function create(HttpRequest $request) {
        $data = $request->getJsonList()->toArray();

        $dateStart = new DateTime($data['UF_DATE_START']);
        $dateEnd = new DateTime($data['UF_DATE_END']);

        $testDrive = DrivesTable::add($data);

        if(!$testDrive->isSuccess()){
            return ['errors' => $testDrive->getErrors()];
        }

        return ['message' => 'Машина успешно забронирована с ' . $dateStart->format('Y-m-d') . ' по ' . $dateEnd->format('Y-m-d')];
    }
}