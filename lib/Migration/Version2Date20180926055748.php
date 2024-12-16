<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version2Date20180926055748 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 *
	 * @return ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		$this->makeExpiresNotNull($schema);

		return $schema;
	}

	/**
	 * @param Closure $schemaClosure
	 *
	 * @throws SchemaException
	 */
	private function makeExpiresNotNull(ISchemaWrapper $schema) {
		$codesTable = $schema->getTable('twofactor_admin_codes');
		$expiresCol = $codesTable->getColumn('expires');
		$expiresCol->setNotnull(true);
		$expiresCol->setDefault(0);
		$idCol = $codesTable->getColumn('id');
		$idCol->setAutoincrement(true);
	}

}
