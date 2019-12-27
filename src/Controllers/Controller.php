<?php
namespace Alpha\Controllers;

use Zend\Diactoros\Response\JsonResponse;
use Siler\GraphQL;
use Alpha\Domains\GraphQLDomain;
use Alpha\Domains\DefaultDomain;

class Controller
{
	function index()
	{
		redirect(url('chat'));
	}

	function chat()
	{	
		$check = check_params(['phone'],'get');
		if (!$check['status']){
			redirect(url('setup'));
		}
		extract($check['params']);
		$phone = '+'.trim($phone);
		$profile = DefaultDomain::getDetails($phone);
		if (!$profile['status'])
		{
			redirect(url('setup'));
		}
		$profile = $profile['data'];
		$data = compact('profile');
		return render('index', $data);
	}

	function assets()
	{
		$dir = path(BASE_PATH,'assets');
	    return new JsonResponse(DefaultDomain::getDirContents($dir));
	}

	function _404()
	{
		return render('404');
	}

	function update_profile()
	{
		$check = check_params(['phone']);
		if (!$check['status']){
			return new JsonResponse($check);
		}
		extract($check['params']);
		$update = DefaultDomain::updateDetails($phone,post('username'),post('email'),post('location'),post('status'),post('fullname'));
		return new JsonResponse($update);
	}

	function get_details()
	{
		$check = check_params([]);
		if (!$check['status']){
			return new JsonResponse($check);
		}
		extract($check['params']);
		$details = DefaultDomain::getDetails();
		return new JsonResponse($details);
	}

	function verify()
	{
		if (!get('phone')) { redirect(url('setup')); }
		$phone = str_replace(' ', '', get('phone'));
		DefaultDomain::account($phone);
		$code = 'C-'.DefaultDomain::generateCode();
		// print_r($code);

		// send $code through sms to $phone


		$data = compact('code');
		return render('verify-phone',$data);
	}

	function setup()
	{
		switch (method()) {
			case 'post':
				$check = check_params(['user','password']);
				if (!$check['status']){
					return render('signin',$check);
				}
				extract($check['params']);
				$login = DefaultDomain::signin($user,$password);
				if (!$login['status']) {
					return render('signin',$login);
				}
				$_SESSION['alpha_e_portal']['user'] = $login['user'];
				$_SESSION['alpha_e_portal']['id'] = $login['data']->id;
				header('location:/'.$login['user']);
				break;
			default:
				return render('setup');
				break;
		}
	}

	function signout()
	{
		session_destroy();
		return render('signout');
	}

	function graphql()
	{
		$schema = GraphQL\schema(GraphQLDomain::Types(), GraphQLDomain::Resolvers());
		GraphQL\init($schema);
	}
}