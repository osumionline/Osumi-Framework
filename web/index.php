<?php declare(strict_types=1);

namespace OsumiFramework;

require '../ofw/vendor/core/ocore.class.php';

use OsumiFramework\OFW\Core\OCore;

$core = new OCore();
$core->load();
$core->run();