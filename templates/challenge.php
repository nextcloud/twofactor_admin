<?php
style('twofactor_admin', 'challenge');
?>

<form method="POST"
	  id="admin-2fa-form">
	<p><?php p($l->t('Enter the one-time code provided by your admin.')) ?></p>
	<p><?php p($l->t('Once logged in, please check your two-factor settings in order to be able to log in again.')) ?></p>
	<input type="tel"
		   minlength="6"
		   maxlength="6"
		   name="challenge"
		   required="required"
		   autofocus
		   autocomplete="off"
		   autocapitalize="off"
		   placeholder="<?php p($l->t('Authentication code')) ?>">
	<button type="submit">
		<span><?php p($l->t('Submit')); ?></span>
	</button>
</form>
