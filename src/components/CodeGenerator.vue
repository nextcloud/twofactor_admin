<!--
  - SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<div>
		<form @submit.prevent="submit">
			<label for="two-factor-admin-uid">
				{{ t('twofactor_admin', 'User ID') }}
			</label>
			<br>
			<input id="two-factor-admin-uid"
				v-model="uid"
				type="text"
				:disabled="loading"
				required>
			<input type="submit"
				:disabled="loading"
				:value="t('twofactor_admin', 'Generate')">
		</form>
		<div v-if="error">
			{{ t('twofactor_admin', 'Could not generate a code: {error}', {error}) }}
		</div>
		<div v-if="code">
			{{ t('twofactor_admin', 'The generated code is {code}. It is valid for {hours} hours', {code, hours: (validFor / 3600)}) }}
		</div>
	</div>
</template>

<script>
import { translate as t } from '@nextcloud/l10n'
import { generateCode } from '../service/AdminCodeService.js'

export default {
	name: 'CodeGenerator',
	data() {
		return {
			uid: '',
			loading: false,
			error: '',
			code: undefined,
			validFor: undefined,
		}
	},
	methods: {
		submit() {
			this.code = undefined
			this.validUntil = undefined
			this.loading = true
			this.error = undefined

			return generateCode(this.uid)
				.then(data => {
					this.code = data.code
					this.validFor = data.validFor
				})
				.catch(e => {
					if (e.response && e.response.status === 404) {
						this.error = t('twofactor_admin', 'user {uid} does not exist', { uid: this.uid })
					} else if (e.response && e.response.status === 403) {
						this.error = t('twofactor_admin', 'you are not allowed to generate codes for this user')
					} else {
						this.error = t('twofactor_admin', 'unknown error', { uid: this.uid })
					}
				})
				.catch(console.error.bind(this))
				.then(() => { this.loading = false })
		},
	},
}
</script>
