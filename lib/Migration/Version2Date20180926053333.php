<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Migration;

use Closure;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\DB\ISchemaWrapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version2Date20180926053333 extends SimpleMigrationStep {

	/** @var IDBConnection */
	private $db;

	/** @var ITimeFactory */
	private $timeFactory;

	public function __construct(IDBConnection $db, ITimeFactory $timeFactory) {
		$this->timeFactory = $timeFactory;
		$this->db = $db;
	}

	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		$codesTable = $schema->getTable('twofactor_admin_codes');
		$codesTable->addColumn('expires', 'bigint', [
			'notnull' => false,
			'unsigned' => true,
		]);

		return $schema;
	}

	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
		$qb = $this->db->getQueryBuilder();
		$now = $this->timeFactory->getTime();
		// Sensible default
		$twoDaysAgo = $now - 3600 * 48;

		$this->fillExpireDates($qb, $twoDaysAgo);
	}

	/**
	 * @param IQueryBuilder $qb
	 * @param int $expires
	 */
	private function fillExpireDates(IQueryBuilder $qb, int $expires) {
		$updateQuery = $qb->update('twofactor_admin_codes')
			->set('expires', $qb->createNamedParameter($expires))
			->where($qb->expr()->isNull('expires'));
		$updateQuery->execute();
	}

}
