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
			->method("get")
			->with("user1")
			->willReturn(null);

		$ec = $this->command->execute([
			"uid" => "user1",
		]);

		$output = $this->command->getDisplay();
		$this->assertStringContainsString("Invalid UID", $output);
		$this->assertEquals(1, $ec);
	}

	public function testGenerateNewCode() {
		$user = $this->createMock(IUser::class);
		$this->userManager->expects($this->once())
			->method("get")
			->with("user1")
			->willReturn($user);
		$this->codeStorage->expects($this->once())
			->method("hasCode")
			->with($user)
			->willReturn(false);
		$this->codeStorage->expects($this->once())
			->method("generateCode")
			->with($user)
			->willReturn("123456");

		$ec = $this->command->execute([
			"uid" => "user1",
		]);

		$output = $this->command->getDisplay();
		$this->assertStringContainsString("Generated new one-time code for user1: 123456", $output);
		$this->assertStringContainsString("This code is valid for", $output);
		$this->assertEquals(0, $ec);
	}

	public function testReGenerateCode() {
		$user = $this->createMock(IUser::class);
		$this->userManager->expects($this->once())
			->method("get")
			->with("user1")
			->willReturn($user);
		$this->codeStorage->expects($this->once())
			->method("hasCode")
			->with($user)
			->willReturn(true);
		$this->codeStorage->expects($this->once())
			->method("generateCode")
			->with($user)
			->willReturn("123456");

		$ec = $this->command->execute([
			"uid" => "user1",
		]);

		$output = $this->command->getDisplay();
		$this->assertStringContainsString("There is an existing code that will be overwritten.", $output);
		$this->assertStringContainsString("Generated new one-time code for user1: 123456", $output);
		$this->assertStringContainsString("This code is valid for", $output);
		$this->assertEquals(0, $ec);
	}

}
