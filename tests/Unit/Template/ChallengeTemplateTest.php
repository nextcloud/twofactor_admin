<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Test\Unit\Template;

use ChristophWurst\Nextcloud\Testing\TestCase;
use OCP\Template;

class ChallengeTemplateTest extends TestCase {

	/** @var Template */
	private $template;

	protected function setUp(): void {
		parent::setUp();

		$this->template = new Template("twofactor_admin", "challenge");
	}

	public function testRender() {
		$html = $this->template->fetchPage();

		$this->assertStringStartsWith("<img", trim($html));
	}

}
