<?php declare(strict_types=1);
class home extends OController {
	private ?userService  $user_service;
	private ?photoService $photo_service;

	function __construct() {
		$this->user_service  = new userService();
		$this->photo_service = new photoService();
	}

	/*
	 * Página de inicio
	 */
	function start($req): void {
		$users = $this->user_service->getUsers();

		$this->getTemplate()->add('date', $this->user_service->getLastUpdate());
		$this->getTemplate()->addPartial('users', 'home/users', ['users' => $users]);
	}

	/*
	 * Página de un usuario
	 */
	function user($req): void {
		$user = $this->user_service->getUser($req['params']['id']);
		$list = $this->photo_service->getPhotos($user->get('id'));

		$this->getTemplate()->add('name', $user->get('user'));
		$this->getTemplate()->addPartial('photo_list', 'home/photo_list', ['list'=>$list]);
	}
}