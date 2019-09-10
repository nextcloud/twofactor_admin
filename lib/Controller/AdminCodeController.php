<?php

declare(strict_types=1);

/**
 * @copyright 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
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
 */

namespace OCA\TwoFactorAdmin\Controller;

use OCA\TwoFactorAdmin\AppInfo\Application;
use OCA\TwoFactorAdmin\Service\CodeStorage;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\Group\ISubAdmin;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\IUserManager;
use OCP\IUserSession;

class AdminCodeController extends Controller {

	/** @var IUserManager */
	private $userManager;

	/** @var CodeStorage */
	private $codeStorage;

	/** @var IGroupManager */
	private $groupManager;

	/** @var ISubAdmin */
	private $subAdmin;

	/** @var IUserSession */
	private $userSession;

	public function __construct(IRequest $request,
								IUserManager $userManager,
								IGroupManager $groupManager,
								ISubAdmin $subAdmin,
								CodeStorage $codeStorage,
								IUserSession $userSession) {
		parent::__construct(Application::APP_ID, $request);
		$this->codeStorage = $codeStorage;
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
		$this->subAdmin = $subAdmin;
		$this->userSession = $userSession;
	}

	/**
	 * @param string $uid
	 *
	 * @SubAdminRequired
	 *
	 * @return JSONResponse
	 */
	public function create(string $uid): JSONResponse {
		$currentUser = $this->userSession->getUser();
		$user = $this->userManager->get($uid);

		if ($currentUser === null) {
			// This is pretty much impossible
			return new JSONResponse(null, Http::STATUS_BAD_REQUEST);
		}
		if ($user === null) {
			return new JSONResponse(null, Http::STATUS_NOT_FOUND);
		}

		if (!$this->groupManager->isAdmin($currentUser->getUID())
			&& !$this->subAdmin->isUserAccessible($currentUser, $user)) {
			// Nope
			return new JSONResponse(null, Http::STATUS_FORBIDDEN);
		}

		return new JSONResponse([
			'code' => $this->codeStorage->generateCode($user),
			'validFor' => CodeStorage::CODE_TTL,
		]);
	}

}

