<?php

use SendGrid\Mail\Mail as SGMail;
Use eftec\bladeone\BladeOne;


// function contains(string $needle, string $haystack): bool
// {
// 	return (strpos($haystack, $needle) !== false);
// }

function path($base, ...$paths)
{
	$base = ends_with($base, '\\') ? $base : $base.'\\';
	return $base.join($paths,'\\');
}

function starts_with (string $string, string $startString): bool
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
} 

function ends_with(string $string, string $endString): bool
{ 
    $len = strlen($endString); 
    if ($len == 0) { 
        return true; 
    } 
    return (substr($string, -$len) === $endString); 
}

function render($view_name, $data=[])
{
	$view = path(BASE_PATH,'views');
	$cache = path(BASE_PATH,'views','cache');
	$blade = new BladeOne($view, $cache,BladeOne::MODE_AUTO);
	$blade->setFileExtension('.html');
	$blade->setCompiledExtension('.view-cache');
	return $blade->run($view_name, $data);
}

function env($variable)
{
	try {
		return constant($variable) ?? getenv($variable);
	} catch (Exception $e) {
		throw new Exception("Unknown environment variable '{$variable}'");
	}
}

function send_mail($to,$subject,$message,$from_email='devclareo@gmail.com',$from_name='DevClareo',$type='sendgrid')
{
	switch ($type) {
		case 'sendgrid':
			$email = new SGMail();
			$email->setFrom($from_email, $from_name);
			$email->setSubject($subject);
			$email->addTo($to, null);
			$email->addContent("text/html", $message);
			$api_key = env('SENDGRID_API_KEY');
			$sendgrid = new SendGrid($api_key);
			try {
			    $response = $sendgrid->send($email);
			    return ($response->statusCode() == 202);
			} catch (Exception $e) {
				return false;
			}
			break;
		default:
			try {
				$headers = [];
				$headers[] = 'MIME-Version: 1.0';
				$headers[] = 'Content-type: text/html; charset=iso-8859-1';
				$headers[] = "From: $from_name <$from_email>";
				return mail($to, $subject, $message, implode("\r\n", $headers));
			} catch (Exception $e) {
				return false;
			}
			break;
	}
}