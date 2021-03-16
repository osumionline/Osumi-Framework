<?php declare(strict_types=1);

namespace OsumiFramework\App\Module;

use OsumiFramework\OFW\Core\OModule;
use OsumiFramework\OFW\Web\ORequest;
use OsumiFramework\OFW\Routing\ORoute;
use OsumiFramework\App\Service\userService;

/**
 * Sample API module
 */
#[ORoute(
	type: 'json',
	prefix: '/api'
)]
class api extends OModule {
	private ?userService $user_service;

	function __construct() {
		$this->user_service  = new userService();
	}

	/**
	 * Function used to obtain current date
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	#[ORoute('/getDate')]
	public function getDate(ORequest $req): void {
		$this->getTemplate()->add('date', $this->user_service->getLastUpdate());
	}

	/**
	 * Function used to get the user list
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	#[ORoute('/getUsers')]
	public function getUsers(ORequest $req): void {
		$this->getTemplate()->addModelComponentList('list', $this->user_service->getUsers(), ['pass']);
	}

	/**
	 * Function used to get a users data
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	#[ORoute('/getUser/:id')]
	public function getUser(ORequest $req): void {
		$status = 'ok';
		$user = $this->user_service->getUser($req->getParamInt('id'));

		if (is_null($user)) {
			$status = 'error';
		}

		$this->getTemplate()->add('status', $status);
		$this->getTemplate()->addModelComponent('user', $user, ['pass'], ['score']);
	}
}