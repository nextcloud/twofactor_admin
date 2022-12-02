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
