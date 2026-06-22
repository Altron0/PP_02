<?php

namespace Local\Classes;

use Bitrix\Main\Application;
use Bitrix\Main\DB\TransactionException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Web\Json;
use Local\Classes\Models\CarTable;
use Local\Classes\Models\StatusTable;

class Cars
{
    public function __construct(private int $ID = 0) {}

    public function list($status) {
        $data = CarTable::getList([
            'select' => ['*', '_' => 'STATUS'],
            'filter' => ['=_UF_CODE' => $status],
            'runtime' => [
                new Reference('STATUS', StatusTable::class, Join::on('this.UF_STATUS', 'ref.ID'))
            ]
        ])->fetchAll();

        if(!$data)
            $data = CarTable::getList(['select' => ['*']])->fetchAll();
        return $data;
    }

    public static function create(HttpRequest $request) {
        // Receiving data from request
        $data = $request->getJsonList()->toArray();

        // Logic creaction record in table
        $carTable = CarTable::add($data);
        
        if(!$carTable->isSuccess()){
            return ['errors' => $carTable->getErrors()];
        }

        return ['message' => 'Машина успешно создана'];
    }

    public function createMultiple(HttpRequest $request) {
        $db = Application::getConnection();
        $data = $request->getJsonList()->toArray();

        try{
            $db->startTransaction();

            $createdCarInfo = CarTable::addMulti($data);
            if(!$createdCarInfo->isSuccess()){
                throw new TransactionException(Json::encode($createdCarInfo->getErrors()));
            }

            $db->commitTransaction();
        }
        catch(TransactionException $e){
            $db->rollbackTransaction(); 
            return ['errors' => Json::decode($e->getMessage())];
        }

        return ['message' => 'Автомобили были успешно созданы'];
    }

    public function update(HttpRequest $request) {
        $data = $request->getJsonList()->toArray();

        $updatedCarInfo = CarTable::update($this->ID, $data);

        if(!$updatedCarInfo->isSuccess()){
            return ['errors' => $updatedCarInfo->getErrors()];
        }

        return ['message' => 'Автомобиль успешно обновлен', 'ID' => $updatedCarInfo->getId()];
    }

    public function delete(HttpRequest $request) {
        $deletedCarInfo = CarTable::delete($this->ID);

        if(!$deletedCarInfo->isSuccess()){
            return ['errors' => $deletedCarInfo->getErrors()];
        }

        return ['message' => 'Машина успешно удалена'];
    }
}