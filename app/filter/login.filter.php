<?php declare(strict_types=1);

namespace OsumiFramework\App\Filter;

//use OsumiFramework\OFW\Plugins\OToken;

/**
 * Security filter for clients
 *
 * @param array $params Parameter array received on the call
 *
 * @param array $headers HTTP header array received on the call
 *
 * @return array Return filter status (ok / error) and information
 */
function loginFilter(array $params, array $headers): array {
	global $core;
	/*
	// Sample header authentication code
	$ret = ['status'=>'error', 'id'=>null];

	$tk = new OToken($core->config->getExtra('secret'));
	if ($tk->checkToken($headers['Authorization'])) {
		$ret['status'] = 'ok';
		$ret['id'] = intval($tk->getParam('id'));
	}
	*/
	$ret = ['status'=>'ok', 'id'=>1];

	return $ret;
}
