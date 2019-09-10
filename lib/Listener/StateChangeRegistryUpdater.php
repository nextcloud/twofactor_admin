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

namespace OCA\TwoFactorAdmin\Listener;

use OCA\TwoFactorAdmin\Event\StateChanged;
use OCA\TwoFactorAdmin\Provider\AdminProvider;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;

class StateChangeRegistryUpdater implements IEventListener {

	/** @var IRegistry */
	private $registry;

	/** @var AdminProvider */
	private $provider;

	public function __construct(IRegistry $registry, AdminProvider $provider) {
		$this->registry = $registry;
		$this->provider = $provider;
	}

	public function handle(Event $event): void {
		if ($event instanceof StateChanged) {
			if ($event->isEnabled()) {
				$this->registry->enableProviderFor($this->provider, $event->getUser());
			} else {
				$this->registry->disableProviderFor($this->provider, $event->getUser());
			}
		}
	}

}
