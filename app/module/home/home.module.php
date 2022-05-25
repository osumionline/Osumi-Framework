<?php declare(strict_types=1);

namespace OsumiFramework\App\Module;

use OsumiFramework\OFW\Routing\OModule;

/**
 * Sections of the web site
 */
#[OModule(
	type: 'html',
	actions: 'start, user, filter'
)]
class homeModule {}