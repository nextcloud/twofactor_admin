<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorAdmin\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $id)
 * @method string getCode()
 * @method void setCode(string $id)
 * @method int getExpires()
 * @method void setExpires(int $expires)
 */
class Code extends Entity {

	protected $userId;

	protected $code;

	protected $expires;

}
