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

namespace OCA\TwoFactorAdmin\Test\Unit\Provider;

use OCA\TwoFactorAdmin\Provider\AdminProvider;
use OCA\TwoFactorAdmin\Service\CodeStorage;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Template;
use PHPUnit\Framework\MockObject\MockObject;

class AdminProviderTest extends \ChristophWurst\Nextcloud\Testing\TestCase {

	/** @var IL10N|MockObject */
	private $l10n;

	/** @var IURLGenerator|MockObject */
	private $urlGenerator;

	/** @var CodeStorage|MockObject */
	private $codeStorage;

	/** @var AdminProvider */
	private $provider;

	protected function setUp(): void {
		parent::setUp();

		$this->l10n = $this->createMock(IL10N::class);
		$this->urlGenerator = $this->createMock(IURLGenerator::class);
		$this->codeStorage = $this->createMock(CodeStorage::class);

		$this->provider = new AdminProvider(
			'twofactor_admin',
			$this->l10n,
			$this->urlGenerator,
			$this->codeStorage
		);
	}

	public function testId() {
		$id = $this->provider->getId();

		$this->assertEquals('admin', $id);
	}

	public function testDisplayName() {
		$this->l10n->method('t')->willReturnArgument(0);

		$displayName = $this->provider->getDisplayName();

		$this->assertEquals('Admin code', $displayName);
	}

	public function testDescription() {
		$this->l10n->method('t')->willReturnArgument(0);

		$displayName = $this->provider->getDescription();

		$this->assertEquals('Use a one-time code provided by your admin', $displayName);
	}

	public function testGetTemplate() {
		$user = $this->createMock(IUser::class);
		$expected = new Template('twofactor_admin', 'challenge');

		$tmpl = $this->provider->getTemplate($user);

		$this->assertEquals($expected, $tmpl);
	}

	public function testVerifyCorrectChallenge() {
		$user = $this->createMock(IUser::class);
		$code = '123456';
		$this->codeStorage->expects($this->once())
			->method('validateCode')
			->with($user, $code)
			->willReturn(true);

		$result = $this->provider->verifyChallenge($user, $code);

		$this->assertTrue($result);
	}

	public function testVerifyIncorrectChallenge() {
		$user = $this->createMock(IUser::class);
		$code = '123456';
		$this->codeStorage->expects($this->once())
			->method('validateCode')
			->with($user, $code)
			->willReturn(false);

		$result = $this->provider->verifyChallenge($user, $code);

		$this->assertFalse($result);
	}

	public function testIsTwoFactorAuthEnabled() {
		$user = $this->createMock(IUser::class);
		$this->codeStorage->expects($this->once())
			->method('hasCode')
			->with($user)
			->willReturn(true);

		$enabled = $this->provider->isTwoFactorAuthEnabledForUser($user);

		$this->assertTrue($enabled);
	}

	public function testGetDarkIcon() {
		$this->urlGenerator->expects($this->once())
			->method('imagePath')
			->with('core', 'actions/more.svg')
			->willReturn('/path/to/image.svg');

		$path = $this->provider->getDarkIcon();

		$this->assertSame('/path/to/image.svg', $path);
	}

	public function testGetLightIcon() {
		$this->urlGenerator->expects($this->once())
			->method('imagePath')
			->with('core', 'actions/more-white.svg')
			->willReturn('/path/to/image.svg');

		$path = $this->provider->getLightIcon();

		$this->assertSame('/path/to/image.svg', $path);
	}

}
