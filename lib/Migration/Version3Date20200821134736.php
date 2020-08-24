<?php

declare(strict_types=1);

namespace OCA\TwoFactorAdmin\Migration;

use Closure;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\DB\ISchemaWrapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version3Date20200821134736 extends SimpleMigrationStep {

	/** @var IDBConnection */
	private $db;

	public function __construct(IDBConnection $db) {
		$this->db = $db;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 *
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		$table = $schema->createTable('twofactor_admincodes');
		$table->addColumn('id', 'bigint', [
			'autoincrement' => true,
			'notnull' => true,
			'unsigned' => true,
		]);
		$table->addColumn('user_id', 'string', [
			'notnull' => true,
			'length' => 64,
		]);
		$table->addColumn('code', 'string', [
			'notnull' => true,
			'length' => 6,
		]);
		$table->addColumn('expires', 'bigint', [
			'notnull' => true,
			'default' => 0,
			'unsigned' => true,
		]);
		$table->setPrimaryKey(['id']);
		$table->addUniqueIndex(['id', 'user_id'], 'twofactor_admincodes_uniq');

		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();
		if (!$schema->hasTable('twofactor_admin_codes')) {
			return;
		}

		$select = $this->db->getQueryBuilder();
		$insert = $this->db->getQueryBuilder();

		$select->select('*')
			->from('twofactor_admin_codes');
		$insert->insert('twofactor_admincodes')
			->values([
				'user_id' => $insert->createParameter('user_id'),
				'code' => $insert->createParameter('code'),
				'expires' => $insert->createParameter('expires'),
			]);

		$result = $select->execute();
		while (($row = $result->fetch()) !== false) {
			if (!isset($row['user_id'], $row['code'])) {
				continue;
			}

			$insert->setParameter('user_id', $row['user_id'], IQueryBuilder::PARAM_STR);
			$insert->setParameter('code', $row['code'], IQueryBuilder::PARAM_STR);
			$insert->setParameter('expires', (int)($row['expires'] ?? 0), IQueryBuilder::PARAM_INT);

			$insert->execute();
		}
		$result->closeCursor();
	}
}
