<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Service;

use OCA\TwoFactorAdmin\Db\Code;
use OCA\TwoFactorAdmin\Db\CodeMapper;
use OCA\TwoFactorAdmin\Event\StateChanged;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IUser;
use OCP\Security\ISecureRandom;

class CodeStorage {

	public const CODE_TTL = 3600 * 48;

	/** @var ISecureRandom */
	private $random;

	/** @var IEventDispatcher */
	private $eventDispatcher;

	/** @var ITimeFactory */
	private $timeFactory;

	public function __construct(private CodeMapper $codeMapper,
		ISecureRandom $random,
		IEventDispatcher $eventDispatcher,
		ITimeFactory $timeFactory) {
		$this->random = $random;
		$this->eventDispatcher = $eventDispatcher;
		$this->timeFactory = $timeFactory;
	}

	public function generateCode(IUser $user): string {
		$code = $this->random->generate(6, ISecureRandom::CHAR_DIGITS);

		$dbCode = new Code();
		$dbCode->setUserId($user->getUID());
		$dbCode->setCode($code);
		$dbCode->setExpires($this->timeFactory->getTime() + self::CODE_TTL);

		$this->codeMapper->deleteAll($user);
		$this->codeMapper->insert($dbCode);

		$this->eventDispatcher->dispatch(StateChanged::class, new StateChanged($user, true));

		return $code;
	}

	public function hasCode(IUser $user): bool {
		return $this->codeMapper->entryExists($user);
	}

	public function validateCode(IUser $user, string $code): bool {
		try {
			$dbCode = $this->codeMapper->find($user);
		} catch (DoesNotExistException) {
			// TODO: log?
			return false;
		} catch (MultipleObjectsReturnedException) {
			// Actually impossible with the primary key constraints, but still
			return false;
		}

		if ($dbCode->getCode() === $code) {
			// TODO: log?
			$this->codeMapper->delete($dbCode);

			$this->eventDispatcher->dispatch(StateChanged::class, new StateChanged($user, false));

			return $dbCode->getExpires() >= $this->timeFactory->getTime();
		}

		return false;
	}

	public function removeCodesForUser(IUser $user): void {
		$this->codeMapper->deleteAll($user);
		$this->eventDispatcher->dispatch(StateChanged::class, new StateChanged($user, false));
	}

}
