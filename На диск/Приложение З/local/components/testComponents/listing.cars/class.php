<?php

use Local\Classes\Cars;
use Local\Classes\Models\CarTable;
use Local\Classes\Models\DrivesTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

class ListingCarsComponent extends CBitrixComponent
{
	public function onPrepareComponentParams($arParams): array
	{
		return $arParams;
	}
	
	public function executeComponent(): void
	{
		if($this->startResultCache()){
			$this->prepareResult();
			$this->includeComponentTemplate();
		}
	}
	
	private function prepareResult(): void
	{
		$driveCars = DrivesTable::getList([
			'select' => ['*', '_' => 'CARS'],
		])->fetchAll();

		$this->arResult['cars'] = $driveCars;
	}
}
