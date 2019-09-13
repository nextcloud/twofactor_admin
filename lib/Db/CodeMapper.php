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

namespace OCA\TwoFactorAdmin\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use OCP\IUser;

class CodeMapper extends QBMapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'twofactor_admin_codes');
	}

	public function entryExists(IUser $user): bool {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select($qb->func()->count('*'))
			->from($this->getTableName())
			->where($qb->expr()->eq(
				'user_id',
				$qb->createNamedParameter($user->getUID())
			));
		$res = $select->execute();
		$data = $res->fetchColumn(0);
		$res->closeCursor();

		if ($data === false) {
			// No data returned
			return false;
		}

		$cnt = (int)$data;

		return $cnt > 0;
	}

	public function find(IUser $user): Code {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq(
				'user_id',
				$qb->createNamedParameter($user->getUID())
			));

		return $this->findEntity($select);
	}

	public function deleteAll(IUser $user) {
		$qb = $this->db->getQueryBuilder();

		$delete = $qb->delete($this->getTableName())
			->where($qb->expr()->eq(
				'user_id',
				$qb->createNamedParameter($user->getUID())
			));
		$delete->execute();
	}

}
