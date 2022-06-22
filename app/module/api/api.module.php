<?php declare(strict_types=1);

namespace OsumiFramework\App\Module;

use OsumiFramework\OFW\Routing\OModule;

/**
 * Sample API module
 */
#[OModule(
	type: 'json',
	prefix: '/api',
	actions: ['getDate', 'getUser', 'getUsers']
)]
class apiModule {}