<?php
namespace Alpha\Domains;
use ALphaORM\ALphaORM as DB;

class DefaultDomain
{
	static function signin(string $email, string $password): array
	{
		#	Generic failure object to be returned
		$failed = [
			'status' => false,
			'message' => 'Login Failed!'
		];

		$users = DB::find('user','email = :email', [ 'email' => $email ]);
		if (count($users) != 1) { return $failed; }
		$user = array_values($users)[0];

		if (!password_verify($password, $user->password)) { return $failed; }

		return [
			'status' => true,
			'message' => 'Login Successful!',
			'data' => [
				'id' => $user->id
			]
		];
	}

	static function generateCode($chars=5)
	{
		$code = '';
		for ($i = 0; $i<$chars; $i++) 
		{
		    $code .= mt_rand(0,9);
		}
		return $code;
	}

	static function getDirContents(string $dir, &$results = array())
	{
	    $files = scandir($dir);

	    foreach($files as $key => $value){
	        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
	        if(!is_dir($path)) {
	            $results[] = preg_replace('#.*/assets#', '/assets', str_replace('\\', '/', $path));
	        } else if($value != "." and $value != ".." and $value != 'fonts' and $value != 'flags') {
	            self::getDirContents($path, $results);
	            $results[] = preg_replace('#.*/assets#', '/assets', str_replace('\\', '/', $path));
	        }
	    }

   	 return $results;
   	}

   	static function account(string $phone)
   	{
   		$users = DB::findAll('user', 'phone = :phone',[ 'phone' => $phone ]);
   		if (count($users) != 1) {
   			$user = DB::create('user');
   			$user->phone = $phone;
   			DB::store($user);
   		}
   	}

   	static function updateDetails(string $phone, $username, $email, $location, $status, $fullname)
   	{
   		if (isset($_FILES['file'])) {
	   		$phone = $phone;

	   		# $ext = end(explode('.', $_FILES['file']['name']));
	   		$filename = path(BASE_PATH,'uploads','images',"PROFILE_IMG_$phone.png");
	   		
	   		$uploaded = move_uploaded_file($_FILES['file']['tmp_name'], $filename);
	   		return [
	   			'status' => $uploaded,
	   			'message' => '' 
	   		];		
   		}elseif ($username) {
   			$users = DB::findAll('user', 'phone = :phone',[ 'phone' => $phone ]);
	   		if (count($users) != 1) {
	   			return [ 'status' => false, 'message' => 'User does not exist'];
	   		}
	   		$user = $users[0];
	   		$user->username = $username;
	   		DB::store($user);
	   		return [
	   			'status' => $user->username == $username,
	   			'message' => ''
	   		];
   		}elseif ($email) {
   			$users = DB::findAll('user', 'phone = :phone',[ 'phone' => $phone ]);
	   		if (count($users) != 1) {
	   			return [ 'status' => false, 'message' => 'User does not exist'];
	   		}
	   		$user = $users[0];
	   		$user->email = $email;
	   		DB::store($user);
	   		return [
	   			'status' => $user->email == $email,
	   			'message' => ''
	   		];
   		}elseif ($location) {
   			$users = DB::findAll('user', 'phone = :phone',[ 'phone' => $phone ]);
	   		if (count($users) != 1) {
	   			return [ 'status' => false, 'message' => 'User does not exist'];
	   		}
	   		$user = $users[0];
	   		$user->location = $location;
	   		DB::store($user);
	   		return [
	   			'status' => $user->location == $location,
	   			'message' => ''
	   		];
   		}elseif ($status) {
   			$users = DB::findAll('user', 'phone = :phone',[ 'phone' => $phone ]);
	   		if (count($users) != 1) {
	   			return [ 'status' => false, 'message' => 'User does not exist'];
	   		}
	   		$user = $users[0];
	   		$user->status = $status;
	   		DB::store($user);
	   		return [ 
	   			'status' => $user->status == $status,
	   			'message' => ''
	   		];
   		}elseif ($fullname) {
   			$users = DB::findAll('user', 'phone = :phone',[ 'phone' => $phone ]);
	   		if (count($users) != 1) {
	   			return [ 'status' => false, 'message' => 'User does not exist'];
	   		}
	   		$user = $users[0];
	   		$user->fullname = $fullname;
	   		DB::store($user);
	   		return [ 
	   			'status' => $user->fullname == $fullname,
	   			'message' => ''
	   		];
   		}
   	}

   	static function getDetails(string $phone)
   	{
   		$users = DB::findAll('user', 'phone = :phone',[ 'phone' => $phone ]);
   		if (count($users) != 1) {
   			return [ 'status' => false, 'message' => 'User does not exist'];
   		}
   		$user = $users[0];
   		$username = $user->username ?? '';
   		$fullname = $user->fullname ?? '';
   		$email = $user->email ?? '';
   		$location = $user->location ?? '';
   		$status = $user->status ?? '';
   		$phone = str_replace('+', '', $phone);
   		$picture =  file_exists(path(BASE_PATH,'uploads','images',"PROFILE_IMG_$phone.png")) ? "/uploads/images/PROFILE_IMG_$phone.png" : '/assets/images/men.svg';
   		$details = compact('username','fullname','email','location','status','picture');
   		return [ 
   			'status' => true,
   			'message' => '',
   			'data' => $details
   		];
   	}
}