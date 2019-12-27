<?php

use Volnix\CSRF\CSRF;

function post($param='', $default=null)
{
	if ($param === '') {
		return $_POST;
	}
	return $_POST[$param] ?? $default;
}

function get($param='', $default=null)
{
	if ($param === '') {
		return $_GET;
	}
	return $_GET[$param] ?? $default;
}

function method($method='')
{
	if ($method === '') {
		return strtolower($_SERVER['REQUEST_METHOD']);
	}
	return (strtolower($method) === strtolower($_SERVER['REQUEST_METHOD']));
}

function get_csrf($csrf_name, $html=false)
{
	return $value ? CSRF::getHiddenInputString($csrf_name) : CSRF::getToken($csrf_name);	
}

function validate_csrf($csrf_name)
{
	return CSRF::validate(post(), $csrf_name);
}

function check_params($params, $method='post', $validations = [])
{
	$output = [];
	$method_params = (strtolower($method) === 'post') ? $_POST : $_GET;
	foreach ($params as $param) {
		if ((!array_key_exists($param, $method_params)) or (empty($method_params[$param]) and trim($method_params[$param]) == '' )) {
			return [ 
				'status' => false,
				'message' => ucwords(str_replace('_', ' ', $param))." is required!"
			];
		}
		if (array_key_exists($param,$validations)) {
			$valid = validate_param($method_params[$param],$validations[$param]);
			if (!$valid) {
				$type = in_array(substr($validations[$param], 0, 1), explode(',', 'a,e,i,o,u')) ? 'an ' : 'a ';
				$type .= $validations[$param] ;
				return [
					'status' => false,
					'message' => ucwords(str_replace('_', ' ', $param))." must be ". (str_replace('_', ' ', $type)) 
				];
			}
		}
		$output[$param] = $method_params[$param];
	}
	return [ 'status' => true, 'params' => $output];
}

function validate_param($value, $validation)
{
	switch ($validation) {
		case 'email':
			return filter_var($value,FILTER_VALIDATE_EMAIL);
			break;
		
		case 'int':
			$value = intval($value);
			if ($value == 0) {
				return true;
			}
			return filter_var($value,FILTER_VALIDATE_INT);
			break;
		case 'supported_crypto_currency':
			return in_array($value, CryptoController::CRYPTO_CURRENCIES);
			break;
	}
}