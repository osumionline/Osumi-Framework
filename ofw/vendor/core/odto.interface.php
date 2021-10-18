<?php declare(strict_types=1);

namespace OsumiFramework\OFW\Core;

use OsumiFramework\OFW\Web\ORequest;

interface ODTO {
	public function isValid(): bool;
	public function load(ORequest $req): void;
}