<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Test\Unit\Command;

use ChristophWurst\Nextcloud\Testing\TestCase;
use OCA\TwoFactorAdmin\Command\Generate;
use OCA\TwoFactorAdmin\Service\CodeStorage;
use OCP\IUser;
use OCP\IUserManager;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateTest extends TestCase {

	/** @var CodeStorage|MockObject */
	private $codeStorage;

	/** @var IUserManager|MockObject */
	private $userManager;

	/** @var CommandTester */
	private $command;

	protected function setUp(): void {
		parent::setUp();

		$this->codeStorage = $this->createMock(CodeStorage::class);
		$this->userManager = $this->createMock(IUserManager::class);

		$generateCommand = new Generate(
			$this->codeStorage,
			$this->userManager
		);
		$this->command = new CommandTester($generateCommand);
	}

	public function testGenerateCodeInvalidUID() {
		$this->userManager->expects($this->once())
			->method('get')
			->with('user1')
			->willReturn(null);

		$ec = $this->command->execute([
			'uid' => 'user1',
		]);

		$output = $this->command->getDisplay();
		$this->assertStringContainsString('Invalid UID', $output);
		$this->assertEquals(1, $ec);
	}

	public function testGenerateNewCode() {
		$user = $this->createMock(IUser::class);
		$this->userManager->expects($this->once())
			->method('get')
			->with('user1')
			->willReturn($user);
		$this->codeStorage->expects($this->once())
			->method('hasCode')
			->with($user)
			->willReturn(false);
		$this->codeStorage->expects($this->once())
			->method('generateCode')
			->with($user)
			->willReturn('123456');

		$ec = $this->command->execute([
			'uid' => 'user1',
		]);

		$output = $this->command->getDisplay();
		$this->assertStringContainsString('Generated new one-time code for user1: 123456', $output);
		$this->assertStringContainsString('This code is valid for', $output);
		$this->assertEquals(0, $ec);
	}

	public function testReGenerateCode() {
		$user = $this->createMock(IUser::class);
		$this->userManager->expects($this->once())
			->method('get')
			->with('user1')
			->willReturn($user);
		$this->codeStorage->expects($this->once())
			->method('hasCode')
			->with($user)
			->willReturn(true);
		$this->codeStorage->expects($this->once())
			->method('generateCode')
			->with($user)
			->willReturn('123456');

		$ec = $this->command->execute([
			'uid' => 'user1',
		]);

		$output = $this->command->getDisplay();
		$this->assertStringContainsString('There is an existing code that will be overwritten.', $output);
		$this->assertStringContainsString('Generated new one-time code for user1: 123456', $output);
		$this->assertStringContainsString('This code is valid for', $output);
		$this->assertEquals(0, $ec);
	}

}
