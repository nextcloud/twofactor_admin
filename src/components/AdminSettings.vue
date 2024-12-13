<!--
  - SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<div id="two-factor-admin-admin-settings" class="section">
		<h2>{{ t('twofactor_admin', 'Two-Factor Admin') }}</h2>
		<p>
			{{ t('twofactor_admin', 'The two-factor admin provider allows you to generate one-time codes for your users. This is helpful for the first login after two-factor authentication has been enabled or when the users lose access to their other factors.') }}
		</p>

		<h3>{{ t('twofactor_admin', 'Generate a code') }}</h3>
		<p>
			<CodeGenerator />
		</p>
	</div>
</template>

<script>
import CodeGenerator from './CodeGenerator.vue'

export default {
	name: 'AdminSettings',
	components: {
		CodeGenerator,
	},
}
</script>
