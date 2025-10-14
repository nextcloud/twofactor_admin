<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;
use OCP\IUser;

class CodeMapper extends QBMapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'twofactor_admincodes');
	}

	public function entryExists(IUser $user): bool {
		$qb = $this->db->getQueryBuilder();

		$select = $qb->select($qb->func()->count('*'))
			->from($this->getTableName())
			->where($qb->expr()->eq(
				'user_id',
				$qb->createNamedParameter($user->getUID())
			));
		$res = $select->executeQuery();
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
		$delete->executeStatement();
	}

}
