<?php
	require("routeros_api.class.php");
	$API 				= new routeros_api();
	$API->debug 		= false;
	$user_mikrotik  	= "api-test";
	$password_mikrotik  = "api-test";
	$ip_mikrotik    	= "192.168.10.1";

	// connecting to mikrotik using api
	if($API->connect($ip_mikrotik, $user_mikrotik, $password_mikrotik)){
		
		// set variable data
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		// filter autentikasi
		$kode_keamanan 	= $_POST['kode_keamanan'];
		$key			= "$kode_keamanan@omdrakula.net";
  
		// check security code
		$API->write('/ip/hotspot/user/print',false);
		$API->write('?name='.$username,true);
		$details_user = $API->read();
		
		foreach($details_user as $data){
			// if key is true
			if ($data['email'] == $key){
				
				// change password 
				$API->comm("/ip/hotspot/user/set", array(	 
					".id"     		=> $username,
					"password"	 	=> $password,
				));
				
				// remove active connections for users hotspot 
				$id_active = false;
				
				$API->write('/ip/hotspot/active/print',false);
				$API->write('?user='.$username,true);
				$active = $API->read();
				foreach($active as $row){
					if ($row['user'] == $username){
						$id_active = $row['.id'];
						$API->write('/ip/hotspot/active/remove', false);
						$API->write('=.id='.$id_active);
						$READ = $API->read();
					}
				}

				$API->disconnect();
				//end of remove
			 
				echo "Berhasil mengubah password";
				
			// if key is false
			} else {
				echo "Kode keamanan salah";
			}
		}
		
	// if mikrotik api not connected
	} else {
		echo "Mikrotik tidak konek<br>Periksa konfigurasi koneksi API";
	}

?>
