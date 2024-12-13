<?php

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

style('twofactor_admin', 'challenge');
?>

<img class="two-factor-icon" src="<?php print_unescaped(image_path('core', 'actions/more-white.svg')); ?>" alt="">

<form method="POST"
	  id="admin-2fa-form">
	<p>
		<?php p($l->t('Enter the one-time code provided by your admin.')) ?>
		<?php p($l->t('Once logged in, please check your two-factor settings in order to be able to log in again.')) ?>
	</p>
	<input type="tel"
		   minlength="6"
		   maxlength="6"
		   name="challenge"
		   required="required"
		   autofocus
		   autocomplete="off"
		   autocapitalize="off"
		   placeholder="<?php p($l->t('Authentication code')) ?>">
	<button class="primary two-factor-submit"
			type="submit">
		<?php p($l->t('Submit')); ?>
	</button>
</form>
