<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Listener;

use OCA\TwoFactorAdmin\Event\StateChanged;
use OCA\TwoFactorAdmin\Provider\AdminProvider;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;

/**
 * @template-implements IEventListener<StateChanged>
 */
class StateChangeRegistryUpdater implements IEventListener {
	public function __construct(
		private readonly IRegistry $registry,
		private readonly AdminProvider $provider,
	) {
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
