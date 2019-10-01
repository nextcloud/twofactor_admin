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

	const CODE_TTL = 3600 * 48;

	/** @var CodeMapper */
	private $codeMapper;

	/** @var ISecureRandom */
	private $random;

	/** @var IEventDispatcher */
	private $eventDispatcher;

	/** @var ITimeFactory */
	private $timeFactory;

	public function __construct(CodeMapper $codeMapper,
								ISecureRandom $random,
								IEventDispatcher $eventDispatcher,
								ITimeFactory $timeFactory) {
		$this->codeMapper = $codeMapper;
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
		} catch (DoesNotExistException $ex) {
			// TODO: log?
			return false;
		} catch (MultipleObjectsReturnedException $ex) {
			// Actually impossible with the primary key constraints, but still
			return false;
		}

		if ($dbCode->getCode() === $code) {
			// TODO: log?
			$this->codeMapper->delete($dbCode);

			$this->eventDispatcher->dispatch(StateChanged::class, new StateChanged($user, false));

			return $dbCode->getExpires() >= $this->timeFactory->getTime();
		} else {
			return false;
		}
	}

}
