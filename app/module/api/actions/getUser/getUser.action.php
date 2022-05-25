<?php declare(strict_types=1);

namespace OsumiFramework\App\Module\Action;

use OsumiFramework\OFW\Routing\OModuleAction;
use OsumiFramework\OFW\Routing\OAction;
use OsumiFramework\OFW\Web\ORequest;

#[OModuleAction(
	url: '/getUser/:id',
	services: 'user'
)]
class getUserAction extends OAction {
	/**
	 * Function used to get a user's data
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	public function run(ORequest $req):void {
		$status = 'ok';
		$user = $this->user_service->getUser($req->getParamInt('id'));

		if (is_null($user)) {
			$status = 'error';
		}

		$this->getTemplate()->add('status', $status);
		$this->getTemplate()->addModelComponent('user', $user, ['pass'], ['score']);
	}
}