<?php declare(strict_types=1);

namespace OsumiFramework\App\Module;

use OsumiFramework\OFW\Core\OModule;
use OsumiFramework\OFW\Web\ORequest;
use OsumiFramework\OFW\Routing\ORoute;
use OsumiFramework\App\Service\userService;
use OsumiFramework\App\Service\photoService;

class home extends OModule {
	private ?userService  $user_service;
	private ?photoService $photo_service;

	function __construct() {
		$this->user_service  = new userService();
		$this->photo_service = new photoService();
	}

	/**
	 * Start page
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	#[ORoute('/')]
	public function start(ORequest $req): void {
		$users = $this->user_service->getUsers();

		$this->getTemplate()->add('date', $this->user_service->getLastUpdate());
		$this->getTemplate()->addComponent('users', 'home/users', ['users' => $users]);
	}

	/**
	 * User's page
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	 #[ORoute('/user/:id')]
	public function user(ORequest $req): void {
		$user = $this->user_service->getUser($req->getParamInt('id'));
		$list = $this->photo_service->getPhotos($user->get('id'));

		$this->getTemplate()->add('name', $user->get('user'));
		$this->getTemplate()->addComponent('photo_list', 'home/photo_list', ['list'=>$list]);
	}

	/**
	 * Test page for filters
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	#[ORoute(
		'/filter',
		filter: 'testFilter'
	)]
	public function filter(ORequest $req): void {
		echo '<pre>';
		var_dump($req);
		echo '</pre>';
	}
}