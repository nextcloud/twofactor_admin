/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import Vue from 'vue'

import AdminSettings from './components/AdminSettings.vue'
import Nextcloud from './mixins/Nextcloud.js'

Vue.mixin(Nextcloud)

const View = Vue.extend(AdminSettings)
new View().$mount('#two-factor-admin-admin-settings')
