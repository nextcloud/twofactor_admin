<?php

declare(strict_types=1);

namespace OCA\TwoFactorAdmin\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version2Date20200817150242 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		$table = $schema->getTable('twofactor_admin_codes');
		$table->getColumn('id')->setAutoincrement(true);
		$table->dropPrimaryKey();
		$table->setPrimaryKey(['id']);
		$table->addUniqueIndex(['id', 'user_id'], 'twofactor_admin_codes_uniq');

		return $schema;
	}

}
