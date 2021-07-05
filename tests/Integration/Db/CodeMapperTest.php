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

namespace OCA\TwoFactorAdmin\Test\Integration\Db;

use ChristophWurst\Nextcloud\Testing\DatabaseTransaction;
use ChristophWurst\Nextcloud\Testing\TestCase;
use ChristophWurst\Nextcloud\Testing\TestUser;
use OC;
use OCA\TwoFactorAdmin\Db\Code;
use OCA\TwoFactorAdmin\Db\CodeMapper;

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
		$this->expectException(\OCP\AppFramework\Db\DoesNotExistException::class);
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
