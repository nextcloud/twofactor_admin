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

namespace OCA\TwoFactorAdmin\Test\Unit\Template;

use ChristophWurst\Nextcloud\Testing\TestCase;
use OCP\Template;

class ChallengeTemplateTest extends TestCase {

	/** @var Template */
	private $template;

	protected function setUp() {
		parent::setUp();

		$this->template = new Template("twofactor_admin", "challenge");
	}

	public function testRender() {
		$html = $this->template->fetchPage();

		$this->assertStringStartsWith("<form", trim($html));
	}

}
