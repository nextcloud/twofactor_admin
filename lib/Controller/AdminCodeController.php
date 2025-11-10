<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
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

	/** @var IGroupManager */
	private $groupManager;

	/** @var ISubAdmin */
	private $subAdmin;

	/** @var IUserSession */
	private $userSession;

	public function __construct(
		IRequest $request,
		IUserManager $userManager,
		IGroupManager $groupManager,
		ISubAdmin $subAdmin,
		private CodeStorage $codeStorage,
		IUserSession $userSession,
	) {
		parent::__construct(Application::APP_ID, $request);
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
		$this->subAdmin = $subAdmin;
		$this->userSession = $userSession;
	}

	/**
	 *
	 * @SubAdminRequired
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
