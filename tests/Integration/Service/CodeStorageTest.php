<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Test\Integration;

use ChristophWurst\Nextcloud\Testing\DatabaseTransaction;
use ChristophWurst\Nextcloud\Testing\TestCase;
use ChristophWurst\Nextcloud\Testing\TestUser;
use OCA\TwoFactorAdmin\Service\CodeStorage;
use OCP\Server;

class CodeStorageTest extends TestCase {

	use DatabaseTransaction;
	use TestUser;

	/** @var CodeStorage */
	private $codeStorage;

	protected function setUp(): void {
		parent::setUp();

		$this->codeStorage = Server::get(CodeStorage::class);
	}

	public function testValidateInexistentCode() {
		$user = $this->createTestUser();
		$code = '123456';

		$valid = $this->codeStorage->validateCode($user, $code);

		$this->assertFalse($valid);
	}

	public function testValidateWrongCode() {
		$user = $this->createTestUser();
		$this->codeStorage->generateCode($user);

		$valid = $this->codeStorage->validateCode($user, '123456');

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
