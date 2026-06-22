<?php

namespace Local\Classes\Models;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\ORM\Fields\FieldError;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Validators\UniqueValidator;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Web\Json;


class CarTable extends DataManager
{

	public static function getTableName()
	{
		return 'cars';
	}

	public static function onBeforeAdd(Event $event){
		$fields = $event->getParameters()['fields'];
		$result = new EventResult();
		$isUniqueCar = CarTable::getList([
			'select' => ['UF_VIN'],
			'filter' => ['=UF_VIN' => (int)$fields['UF_VIN']]
		])->fetch();

		if($isUniqueCar){
			$result->addError(new FieldError($event->getEntity()->getField('UF_VIN'), 'Поле UF_VIN должно быть уникально для автомобиля ' . $fields['UF_MODEL'], '409'));
		}
		return $result;
	}

	public static function onBeforeUpdate(Event $event){
		$statusID = $event->getParameter('fields')['UF_STATUS'];
		$result = new EventResult();

		$status = StatusTable::getById($statusID)->fetch();

		if(!$status){
			$result->addError(new EntityError('Статуса с таким ID не существует '. $statusID, '409'));
		}

		return $result;
	}

	public static function onBeforeDelete(Event $event){
		$primary = $event->getParameter('primary')['ID'];
		$result = new EventResult();

		$drives = DrivesTable::getList([
			'select' => ['*'],
			'filter' => ['=UF_CAR' => $primary]
		])->fetch();

		if($drives){
			$result->addError(new EntityError('Автомобиль с таким ID ' . $primary . ' забронирован', '409'));
		}

		return $result;
	}

	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary' => true,
					'autocomplete' => true,
					'title' => Loc::getMessage('_ENTITY_ID_FIELD'),
					'size' => 8,
				]
			),
			new TextField(
				'UF_MODEL',
				[
					'title' => Loc::getMessage('_ENTITY_UF_MODEL_FIELD'),
					''
				]
			)->configureRequired(true),
			new IntegerField(
				'UF_YEAR',
				[
					'title' => Loc::getMessage('_ENTITY_UF_YEAR_FIELD'),
				]
			),
			new IntegerField(
				'UF_VIN',
				[
					'title' => Loc::getMessage('_ENTITY_UF_VIN_FIELD'),
				]
			)->configureUnique(false),
			new IntegerField(
				'UF_STATUS',
				[
					'title' => Loc::getMessage('_ENTITY_UF_STATUS_FIELD'),
				]
			),
			new IntegerField(
				'UF_PRICE_PER_DAY',
				[
					'title' => Loc::getMessage('_ENTITY_UF_PRICE_PER_DAY_FIELD'),
				]
			)
		];
	}
}