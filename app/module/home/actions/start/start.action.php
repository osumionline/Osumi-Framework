<?php declare(strict_types=1);

namespace OsumiFramework\App\Module\Action;

use OsumiFramework\OFW\Routing\OModuleAction;
use OsumiFramework\OFW\Routing\OAction;
use OsumiFramework\OFW\Web\ORequest;
use OsumiFramework\App\Component\Home\UsersComponent;

#[OModuleAction(
	url: '/',
	services: ['user'],
	inlineCSS: ['start'],
	inlineJS: ['start', 'test']
)]
class startAction extends OAction {
	/**
	 * Start page
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	public function run(ORequest $req):void {
		$users = $this->user_service->getUsers();
		$users_component = new UsersComponent(['users' => $users]);

		$this->getTemplate()->add('date', $this->user_service->getLastUpdate());
		$this->getTemplate()->add('users', $users_component);
	}
}
