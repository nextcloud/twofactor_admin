<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Test\Unit\Event;

use ChristophWurst\Nextcloud\Testing\TestCase;
use OCA\TwoFactorAdmin\Event\StateChanged;
use OCP\IUser;

class StateChangedTest extends TestCase {

	public function testIsEnabled() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, true);

		$this->assertSame($user, $event->getUser());
		$this->assertTrue($event->isEnabled());
	}

	public function testIsDisabled() {
		$user = $this->createMock(IUser::class);
		$event = new StateChanged($user, false);

		$this->assertSame($user, $event->getUser());
		$this->assertFalse($event->isEnabled());
	}

}
