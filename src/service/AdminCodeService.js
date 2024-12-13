/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

/**
 * @param {string} uid UserID
 * @return {Promise}
 */
export const generateCode = uid => {
	const url = generateUrl('/apps/twofactor_admin/api/admin/code')

	return axios.post(url, { uid })
		.then(resp => resp.data)
}
