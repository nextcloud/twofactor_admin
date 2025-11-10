<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Event;

use OCP\EventDispatcher\Event;
use OCP\IUser;

class StateChanged extends Event {

	/** @var IUser */
	private $user;

	public function __construct(
		IUser $user,
		private bool $enabled,
	) {
		parent::__construct();

		$this->user = $user;
	}

	public function getUser(): IUser {
		return $this->user;
	}

	public function isEnabled(): bool {
		return $this->enabled;
	}

}
