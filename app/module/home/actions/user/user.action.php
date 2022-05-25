<?php declare(strict_types=1);

namespace OsumiFramework\App\Module\Action;

use OsumiFramework\OFW\Routing\OModuleAction;
use OsumiFramework\OFW\Routing\OAction;
use OsumiFramework\OFW\Web\ORequest;
use OsumiFramework\App\DTO\UserDTO;
use OsumiFramework\App\Component\PhotoListComponent;

#[OModuleAction(
	url: '/user/:id',
	services: 'user, photo',
	components: 'home/photo_list'
)]
class userAction extends OAction {
	/**
	 * User's page
	 *
	 * @param UserDTO $req Data Transfer Object with "isValid" method and methods for this functions parameters
	 * @return void
	 */
	public function run(UserDTO $req):void {
		if (!$req->isValid()) {
			echo "ERROR!";
			exit;
		}
		$id_user = $req->getIdUser();
		$user = $this->user_service->getUser($id_user);
		$list = $this->photo_service->getPhotos($user->get('id'));

		$photo_list_component = new PhotoListComponent(['list'=>$list]);

		$this->getTemplate()->add('name', $user->get('user'));
		$this->getTemplate()->add('photo_list', $photo_list_component);
	}
}