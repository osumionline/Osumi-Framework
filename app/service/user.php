<?php declare(strict_types=1);
class userService extends OService {
	function __construct() {
		$this->loadService();
	}

	public function getLastUpdate(): string {
		return date('d-m-Y H:i:s');
	}

	public function getUsers(): array {
		$db = new ODB();
		$sql = "SELECT * FROM `user`";
		$db->query($sql);
		$list = [];

		while ($res=$db->next()) {
			$user = new User();
			$user->update($res);

			array_push($list, $user);
		}

		return $list;
	}

	public function getUser(int $id): User {
		$user = new User();
		$user->find(['id'=>$id]);

		return $user;
	}
}