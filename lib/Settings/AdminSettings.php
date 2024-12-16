<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Settings;

use OCA\TwoFactorAdmin\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISubAdminSettings;

class AdminSettings implements ISubAdminSettings {

	public function getForm() {
		return new TemplateResponse(Application::APP_ID, 'settings-admin');
	}

	public function getSection() {
		return 'security';
	}

	public function getPriority() {
		return 90;
	}

}
