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