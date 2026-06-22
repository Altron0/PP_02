<?php

namespace Local\Classes\Models;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\EventResult;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Type\Date;
use DateTime;

class DrivesTable extends DataManager
{
	public static function getTableName()
	{
		return 'test_drives';
	}

	public static function onBeforeAdd(Event $event) {
		$fields = $event->getParameter('fields');
		$result = new EventResult();
		$testDrives = DrivesTable::getList([
			'select' => ['*'],
			'filter' => ['=UF_CAR' => $fields['UF_CAR']]
		])->fetch();

		$car = CarTable::getList([
			'select' => ['*', '_' => 'STATUS'],
			'filter' => ['=ID' => $fields['UF_CAR']],
			'runtime' => [
				new Reference('STATUS', StatusTable::class, Join::on('this.UF_STATUS', 'ref.ID'))
			]
		])->fetch();

		if($testDrives)
			$result->addError(new EntityError('Данная машина уже забронирована другим пользователем'));
		
		if($car['_UF_CODE'] === 'repair')
			$result->addError(new EntityError('Машина под этим ID в ремонте', 409));
		
		$dateStart = new DateTime($fields['UF_DATE_START']);
		$dateEnd = new DateTime($fields['UF_DATE_END']);

		$qlDays = $dateStart->diff($dateEnd)->d;

		$result->modifyFields([
			'UF_TOTAL_COST' => (int)$qlDays * (int)$car['UF_PRICE_PER_DAY'],
			'UF_DATE_START' => new Date($fields['UF_DATE_START'], 'Y-m-d'),
			'UF_DATE_END' => new Date($fields['UF_DATE_END'], 'Y-m-d')
			]);

		return $result;
	}

	public static function onAfterAdd(Event $event) {
		$primary = $event->getParameter('fields')['UF_CAR'];
		$result = new EventResult();

		$status = StatusTable::getList([
			'select' => ['*'],
			'filter' => ['=UF_CODE' => 'repair']
		])->fetch();

		CarTable::update((int)$primary, [
			'UF_STATUS' => $status['ID']
		]);

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
					'title' => Loc::getMessage('DRIVES_ENTITY_ID_FIELD'),
					'size' => 8,
				]
			),
			new IntegerField(
				'UF_CAR',
				[
					'title' => Loc::getMessage('DRIVES_ENTITY_UF_CAR_FIELD'),
				]
			),
			new DatetimeField(
				'UF_DATE_START',
				[
					'title' => Loc::getMessage('DRIVES_ENTITY_UF_DATE_START_FIELD'),
				]
			),
			new DatetimeField(
				'UF_DATE_END',
				[
					'title' => Loc::getMessage('DRIVES_ENTITY_UF_DATE_END_FIELD'),
				]
			),
			new IntegerField(
				'UF_TOTAL_COST',
				[
					'title' => Loc::getMessage('DRIVES_ENTITY_UF_TOTAL_COST_FIELD'),
				]
			),
            new Reference('CARS', CarTable::class, Join::on('this.UF_CAR', 'ref.ID'))->configureJoinType('inner')
		];
	}
}