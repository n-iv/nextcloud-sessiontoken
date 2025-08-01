<?php
/**
 * nextCloud - sessiontoken
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Benjamin Sonntag <benjamin@octopuce.fr>
 * @copyright Benjamin Sonntag 2021
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
