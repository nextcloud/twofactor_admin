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

namespace OCA\TwoFactorAdmin\Test\Integration;

use ChristophWurst\Nextcloud\Testing\DatabaseTransaction;
use ChristophWurst\Nextcloud\Testing\TestCase;
use ChristophWurst\Nextcloud\Testing\TestUser;
use OC;
use OCA\TwoFactorAdmin\Service\CodeStorage;

class CodeStorageTest extends TestCase {

	use DatabaseTransaction;
	use TestUser;

	/** @var CodeStorage */
	private $codeStorage;

	protected function setUp() {
		parent::setUp();

		$this->codeStorage = OC::$server->query(CodeStorage::class);
	}

	public function testValidateInexistentCode() {
		$user = $this->createTestUser();
		$code = "123456";

		$valid = $this->codeStorage->validateCode($user, $code);

		$this->assertFalse($valid);
	}

	public function testValidateWrongCode() {
		$user = $this->createTestUser();
		$this->codeStorage->generateCode($user);

		$valid = $this->codeStorage->validateCode($user, "123456");

		$this->assertFalse($valid);
	}

	public function testValidateCorrectCode() {
		$user = $this->createTestUser();
		$code = $this->codeStorage->generateCode($user);

		$valid = $this->codeStorage->validateCode($user, $code);

		$this->assertTrue($valid);
	}

	public function testValidateCorrectCodeTwice() {
		$user = $this->createTestUser();
		$code = $this->codeStorage->generateCode($user);

		$valid = $this->codeStorage->validateCode($user, $code);
		$validAgain = $this->codeStorage->validateCode($user, $code);

		$this->assertTrue($valid);
		$this->assertFalse($validAgain);
	}

}
