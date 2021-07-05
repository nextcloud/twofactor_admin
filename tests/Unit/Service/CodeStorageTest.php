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

namespace OCA\TwoFactorAdmin\Test\Unit\Service;

use ChristophWurst\Nextcloud\Testing\TestCase;
use OCA\TwoFactorAdmin\Db\Code;
use OCA\TwoFactorAdmin\Db\CodeMapper;
use OCA\TwoFactorAdmin\Service\CodeStorage;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IUser;
use OCP\Security\ISecureRandom;
use PHPUnit\Framework\MockObject\MockObject;

class CodeStorageTest extends TestCase {

	/** @var CodeMapper|MockObject */
	private $codeMapper;

	/** @var ISecureRandom|MockObject */
	private $random;

	/** @var IEventDispatcher|MockObject */
	private $eventDispatcher;

	/** @var CodeStorage|MockObject */
	private $codeStorage;

	/** @var ITimeFactory|MockObject */
	private $timeFactory;

	protected function setUp(): void {
		parent::setUp();

		$this->codeMapper = $this->createMock(CodeMapper::class);
		$this->random = $this->createMock(ISecureRandom::class);
		$this->eventDispatcher = $this->createMock(IEventDispatcher::class);
		$this->timeFactory = $this->createMock(ITimeFactory::class);

		$this->codeStorage = new CodeStorage(
			$this->codeMapper,
			$this->random,
			$this->eventDispatcher,
			$this->timeFactory
		);
	}

	public function testGenerateCode() {
		$user = $this->createMock(IUser::class);
		$rand = "123456";
		$this->random->expects($this->once())
			->method("generate")
			->willReturn($rand);
		$this->codeMapper->expects($this->once())
			->method("insert");

		$code = $this->codeStorage->generateCode($user);

		$this->assertSame($rand, $code);
	}

	public function testHasCode() {
		$user = $this->createMock(IUser::class);
		$this->codeMapper->expects($this->once())
			->method("entryExists")
			->willReturn(false);

		$hasCode = $this->codeStorage->hasCode($user);

		$this->assertFalse($hasCode);
	}

	public function testValidateCodeNoneFound() {
		$user = $this->createMock(IUser::class);
		$this->codeMapper->expects($this->once())
			->method("find")
			->with($user)
			->willThrowException(new DoesNotExistException(""));

		$valid = $this->codeStorage->validateCode($user, "123456");

		$this->assertFalse($valid);
	}

	public function testValidateCodeMoreThanOneFound() {
		$user = $this->createMock(IUser::class);
		$this->codeMapper->expects($this->once())
			->method("find")
			->with($user)
			->willThrowException(new MultipleObjectsReturnedException(""));

		$valid = $this->codeStorage->validateCode($user, "123456");

		$this->assertFalse($valid);
	}

	public function testValidateMismatchingCode() {
		$user = $this->createMock(IUser::class);
		$dbCode = new Code();
		$dbCode->setCode("123456");
		$this->codeMapper->expects($this->once())
			->method("find")
			->with($user)
			->willReturn($dbCode);

		$valid = $this->codeStorage->validateCode($user, "987654");

		$this->assertFalse($valid);
	}

	public function testValidateExpiredCode() {
		$user = $this->createMock(IUser::class);
		$dbCode = new Code();
		$dbCode->setCode("123456");
		$dbCode->setExpires(1000);
		$this->codeMapper->expects($this->once())
			->method("find")
			->with($user)
			->willReturn($dbCode);
		$this->timeFactory->expects($this->once())
			->method('getTime')
			->willReturn(1100);

		$valid = $this->codeStorage->validateCode($user, "123456");

		$this->assertFalse($valid);
	}

	public function testValidateValidCode() {
		$user = $this->createMock(IUser::class);
		$dbCode = new Code();
		$dbCode->setCode("123456");
		$dbCode->setExpires(1000);
		$this->codeMapper->expects($this->once())
			->method("find")
			->with($user)
			->willReturn($dbCode);
		$this->timeFactory->expects($this->once())
			->method('getTime')
			->willReturn(900);

		$valid = $this->codeStorage->validateCode($user, "123456");

		$this->assertTrue($valid);
	}

}
