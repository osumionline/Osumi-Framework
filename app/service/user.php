<?php
class userService extends OService{
	function __construct(){
		$this->loadService();
	}

	public function getLastUpdate(){
		return date('d-m-Y H:i:s');
	}

	public function getUsers(){
		$db = new ODB();
		$sql = "SELECT * FROM `user`";
		$db->query($sql);
		$list = [];

		while ($res=$db->next()){
			$user = new User();
			$user->update($res);

			array_push($list, $user);
		}

		return $list;
	}

	public function getUser($id){
		$user = new User();
		$user->find(['id'=>$id]);

		return $user;
	}
}