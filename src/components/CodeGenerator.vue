<!--
  - @copyright 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
  -
  - @author 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
	<div>
		<form @submit.prevent="submit">
			<label for="two-factor-admin-uid">
				{{ t('twofactor_admin', 'User ID') }}
			</label>
			<br>
			<input id="two-factor-admin-uid"
				   type="text"
				   v-model="uid"
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
	import {translate as t} from 'nextcloud-server/dist/l10n'

	import {generateCode} from '../service/AdminCodeService'

	export default {
		name: 'CodeGenerator',
		data () {
			return {
				uid: '',
				loading: false,
				error: '',
				code: undefined,
				validFor: undefined,
			}
		},
		methods: {
			submit () {
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
							this.error = t('twofactor_admin', 'user {uid} does not exist', {uid: this.uid})
						} else {
							this.error = t('twofactor_admin', 'unknown error', {uid: this.uid})
						}
					})
					.catch(console.error.bind(this))
					.then(() => this.loading = false)
			}
		}
	}
</script>

<style scoped>

</style>