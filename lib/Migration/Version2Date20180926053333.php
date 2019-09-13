<?php

declare(strict_types=1);

/**
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
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
