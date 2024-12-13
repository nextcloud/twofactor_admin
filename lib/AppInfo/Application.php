<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\AppInfo;

use OCA\TwoFactorAdmin\Event\StateChanged;
use OCA\TwoFactorAdmin\Listener\StateChangeRegistryUpdater;
use OCP\AppFramework\App;
use OCP\AppFramework\IAppContainer;
use OCP\EventDispatcher\IEventDispatcher;

class Application extends App {

	public const APP_ID = 'twofactor_admin';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);

		$container = $this->getContainer();
		$this->registerListeners($container);
	}

	private function registerListeners(IAppContainer $container): void {
		/** @var IEventDispatcher $dispatcher */
		$dispatcher = $container->query(IEventDispatcher::class);

		$dispatcher->addServiceListener(StateChanged::class, StateChangeRegistryUpdater::class);
	}

}
