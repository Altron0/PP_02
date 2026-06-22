<?php

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Routing\RoutingConfigurator;
use Bitrix\Main\Web\Json;
use Local\Classes\Cars;
use Local\Classes\TestDrive;

return static function (RoutingConfigurator $router) {
    $router
        ->prefix('api/v1')
        ->group(function(RoutingConfigurator $router) {
            //CRUP operations for cars
            $router->get('cars/{status}', function ($status) {
                $response = Context::getCurrent()->getResponse();
                $car = new Cars();

                $data = $car->list($status);

                $response->appendContent(Json::encode($data));

                return $response;
            });
            $router->post('cars/create', function() {
                $response = Context::getCurrent()->getResponse();
                $request = Context::getCurrent()->getRequest();

                $newCarInfo = Cars::create($request);

                if($newCarInfo['errors'])
                    $response->setStatus(409);
                else
                    $response->setStatus(201);

                $response->appendContent(Json::encode($newCarInfo));
                return $response;
            });
            $router->post('cars/multiple/create', function() {
                $response = Context::getCurrent()->getResponse();
                $request = Context::getCurrent()->getRequest();
                $car = new Cars();
                $newCarsInfo = $car->createMultiple($request);

                if($newCarsInfo['errors']){
                    $response->setStatus(409);
                    $response->appendContent(Json::encode($newCarsInfo));
                }

                $response->appendContent(Json::encode($newCarsInfo));
                $response->setStatus(201);
                return $response;
            });
            $router->put('cars/{carId}/update', function($carId) {
                $response = Context::getCurrent()->getResponse();
                $request = Context::getCurrent()->getRequest();
                
                $car = new Cars($carId);

                $isUpdateCar = $car->update($request);

                if($isUpdateCar['errors']){
                    $response->setStatus(409);
                }
                else
                    $response->setStatus(201);

                $response->appendContent(Json::encode($isUpdateCar));

                return $response;
            });
            $router->delete('cars/{carId}/delete', function($carId) {
                $response = Context::getCurrent()->getResponse();
                $request = Context::getCurrent()->getRequest();

                $car = new Cars($carId);

                $deleteCarInfo = $car->delete($request);

                if($deleteCarInfo['errors']){
                    $response->setStatus(409);
                }
                else
                    $response->setStatus(201);

                $response->appendContent(Json::encode($deleteCarInfo));

                return $response;
            });

            //Route for creation test drive
            $router->post('cars/test-drive', function() {
                $response = Context::getCurrent()->getResponse();
                $request = Context::getCurrent()->getRequest();

                $createdTestDriveInfo = TestDrive::create($request);

                if($createdTestDriveInfo['errors']){
                    $response->setStatus(409);
                }
                else
                    $response->setStatus(201);

                $response->appendContent(Json::encode($createdTestDriveInfo));

                return $response;
            });
        });
};