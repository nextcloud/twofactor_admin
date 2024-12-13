<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Test\Unit\Listener;

use ChristophWurst\Nextcloud\Testing\TestCase;
use OCA\TwoFactorAdmin\Event\StateChanged;
use OCA\TwoFactorAdmin\Listener\StateChangeRegistryUpdater;
use OCA\TwoFactorAdmin\Provider\AdminProvider;
use OCA\TwoFactorGateway\Service\Gateway\SMS\Provider\IProvider;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\EventDispatcher\Event;
use OCP\IUser;

class StateChangeRegistryUpdaterTest extends TestCase {

	/** @var IProvider */
	private $registry;

	/** @var AdminProvider */
	private $provider;

	/** @var StateChangeRegistryUpdater */
	private $listener;

	protected function setUp(): void {
		$this->registry = $this->createMock(IRegistry::class);
		$this->provider = $this->createMock(AdminProvider::class);

		$this->listener = new StateChangeRegistryUpdater($this->registry, $this->provider);
	}

	public function testHandleUnrelatedEvent() {
		$event = new Event();
		$this->registry->expects($this->never())
			->method('enableProviderFor');
		$this->registry->expects($this->never())
			->method('disableProviderFor');

		$this->listener->handle($event);
	}

	public function testEnableEvent() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, true);
		$this->registry->expects($this->once())
			->method('enableProviderFor')
			->with($this->provider, $user);

		$this->listener->handle($event);
	}

	public function testDisableEvent() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, false);
		$this->registry->expects($this->once())
			->method('disableProviderFor')
			->with($this->provider, $user);

		$this->listener->handle($event);
	}

}
