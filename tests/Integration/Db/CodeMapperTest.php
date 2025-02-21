<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Test\Integration\Db;

use ChristophWurst\Nextcloud\Testing\DatabaseTransaction;
use ChristophWurst\Nextcloud\Testing\TestCase;
use ChristophWurst\Nextcloud\Testing\TestUser;
use OC;
use OCA\TwoFactorAdmin\Db\Code;
use OCA\TwoFactorAdmin\Db\CodeMapper;
use OCP\AppFramework\Db\DoesNotExistException;

class CodeMapperTest extends TestCase {

	use DatabaseTransaction;
	use TestUser;

	/** @var CodeMapper */
	private $mapper;

	protected function setUp(): void {
		parent::setUp();

		$this->mapper = OC::$server->query(CodeMapper::class);
	}

	public function testNoEntryExists() {
		$user = $this->createTestUser();

		$exists = $this->mapper->entryExists($user);

		$this->assertFalse($exists);
	}

	public function testEntryExists() {
		$user = $this->createTestUser();
		$code = new Code();
		$code->setUserId($user->getUID());
		$code->setCode('123456');
		$code->setExpires(time());

		$this->mapper->insert($code);

		$exists = $this->mapper->entryExists($user);

		$this->assertTrue($exists);
	}

	public function testExists() {
		$user = $this->createTestUser();
		$code = new Code();
		$code->setUserId($user->getUID());
		$code->setCode('123456');
		$code->setExpires(time());

		$this->mapper->insert($code);

		$exists = $this->mapper->entryExists($user);

		$this->assertTrue($exists);
	}

	public function testFindNotFound() {
		$this->expectException(DoesNotExistException::class);
		$user = $this->createTestUser();

		$this->mapper->find($user);
	}

	public function testFindExisting() {
		$user = $this->createTestUser();
		$code = new Code();
		$code->setUserId($user->getUID());
		$code->setCode('123456');
		$code->setExpires(time());

		$this->mapper->insert($code);

		$result = $this->mapper->find($user);

		$this->assertEquals($code->getId(), $result->getId());
	}

	public function testDeleteAll() {
		$user = $this->createTestUser();
		$code = new Code();
		$code->setUserId($user->getUID());
		$code->setCode('123456');
		$code->setExpires(time());

		$this->mapper->insert($code);

		$this->mapper->deleteAll($user);

		$exists = $this->mapper->entryExists($user);
		$this->assertFalse($exists);
	}

}
