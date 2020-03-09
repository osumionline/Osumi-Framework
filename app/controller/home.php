<?php
class home extends OController{
	private $user_service;
	private $photo_service;

	function __construct(){
		$this->user_service  = new userService($this);
		$this->photo_service = new photoService($this);
	}

	/*
	 * Página de inicio
	 */
	function start($req){
		$users = $this->user_service->getUsers();

		$this->getTemplate()->add('date', $this->user_service->getLastUpdate());
		$this->getTemplate()->addPartial('users', 'home/users', ['users' => $users]);
	}

	/*
	 * Página de un usuario
	 */
	function user($req){
		$user = $this->user_service->getUser($req['id']);
		$list = $this->photo_service->getPhotos($user->get('id'));

		$this->getTemplate()->add('name', $user->get('user'));
		$this->getTemplate()->addPartial('photo_list', 'home/photo_list', ['list'=>$list]);
	}
}