<?php
	/*
	 * Filtro de seguridad para clientes
	 */
	function loginFilter($req){
		global $c;
		$req['filter'] = ['status'=>'error', 'data'=>null];

		$tk = new OToken($c->getExtra('secret'));
		if ($tk->checkToken($req['headers']['Authorization'])){
			$req['filter']['status'] = 'ok';
			$req['filter']['id'] = (int)$tk->getParam('id');
		}

		return $req;
	}