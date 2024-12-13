<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Provider;

use OCA\TwoFactorAdmin\Service\CodeStorage;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesIcons;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Template;

class AdminProvider implements IProvider, IProvidesIcons {

	/** @var string */
	private $appName;

	/** @var IL10N */
	private $l10n;

	/** @var IURLGenerator */
	private $urlGenerator;

	/** @var CodeStorage */
	private $codeStorage;

	public function __construct(string $appName,
		IL10N $l10n,
		IURLGenerator $urlGenerator,
		CodeStorage $codeStorage) {
		$this->appName = $appName;
		$this->l10n = $l10n;
		$this->urlGenerator = $urlGenerator;
		$this->codeStorage = $codeStorage;
	}

	/**
	 * Get unique identifier of this 2FA provider
	 *
	 * @return string
	 */
	public function getId(): string {
		return 'admin';
	}

	/**
	 * Get the display name for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDisplayName(): string {
		return $this->l10n->t('Admin code');
	}

	/**
	 * Get the description for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDescription(): string {
		return $this->l10n->t('Use a one-time code provided by your admin');
	}

	/**
	 * Get the template for rending the 2FA provider view
	 *
	 * @param IUser $user
	 *
	 * @return Template
	 */
	public function getTemplate(IUser $user): Template {
		return new Template($this->appName, 'challenge');
	}

	/**
	 * Verify the given challenge
	 *
	 * @param IUser $user
	 * @param string $challenge
	 *
	 * @return bool
	 */
	public function verifyChallenge(IUser $user, string $challenge): bool {
		return $this->codeStorage->validateCode($user, $challenge);
	}

	/**
	 * Decides whether 2FA is enabled for the given user
	 *
	 * @param IUser $user
	 *
	 * @return boolean
	 */
	public function isTwoFactorAuthEnabledForUser(IUser $user): bool {
		return $this->codeStorage->hasCode($user);
	}

	public function getLightIcon(): String {
		return $this->urlGenerator->imagePath('core', 'actions/more-white.svg');
	}

	public function getDarkIcon(): String {
		return $this->urlGenerator->imagePath('core', 'actions/more.svg');
	}
}
