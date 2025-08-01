<?php
/**
 * Nextcloud - sessiontoken
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Nicolas Varlot <nicolas.varlot@ac-versailles.fr>
 * @copyright Nicolas Varlot 2025
 */

return [
	'routes' => [
		[
			'name' => 'Sessiontoken#token',
			'url' => '/token',
			'verb' => 'POST',
		],
	],
];
