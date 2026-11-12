<?php

require_once get_stylesheet_directory() . '/custom/api/class-api-manager.php';

class apiAuthentication
{

	public function curlrequest($username, $password)
	{
		// echo '76';
		try {
			$curl = curl_init();
			if ( false === $curl ) {
				error_log( 'UsernamePWD API Response :--Unable to initialize cURL for username' . $username );
				return array();
			}

			curl_setopt_array($curl, array(
				CURLOPT_URL => AUTHENTICATION_API,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => 'emailId=' . urlencode($username) . '&password=' . urlencode($password),
				CURLOPT_HTTPHEADER => array(
					'appnName: ZING',
					'authKey: 1lxVJZ/89KCNhoQzfds+URrveGLhwPcGap2w5F6WeIMPVNU8i6a/tldiWHQOfYnNn8xs8Y',
					'Content-Type: application/x-www-form-urlencoded',
					'Origin: '.API_ORIGIN_NAME,
				),
				// 🔽 THIS LINE IS THE FIX 🔽
				// CURLOPT_CAINFO => __DIR__ . '/cacert.pem',
				// CURLOPT_SSL_VERIFYPEER => false
			));

			$response = curl_exec($curl);

			if ( false === $response ) {
				error_log('cURL error: ' . curl_error($curl));
				curl_close($curl);
				return array();
			}

			if (curl_errno($curl)) {
				$error_msg = curl_error($curl);
				error_log("Response1 curl logged error-----");
				error_log($error_msg);
			}

			//error_log("Response1 logged error-----");
			//error_log($response);

			$header_size1 = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header1 = substr($response, 0, $header_size1);
			$body1 = substr($response, $header_size1);
			// print_r("header size -----------------".$header_size1);
			// print_r("<br>");
			// print_r("-----------------------------------------------");
			// print_r("<br>");
			// print_r("header-----------------".$header1);
			// print_r("<br>");
			// print_r("-----------------------------------------------");
			// print_r("<br>");
			// print_r("body------------".$body1); 
			// print_r("<br>");
			// print_r("-----------------------------------------------");
			// print_r("<br>");die;
			curl_close($curl);

			// preg_match('/i_s=.*";/', $response, $matches);
		preg_match('/i_s=.*;/', $header1, $matches);
		preg_match('/c_in=.*;/', $header1, $record);
		preg_match('/Expires=.*GMT/', $header1, $expiry);
		preg_match('/JSESSIONID=.*;/', $header1, $jsessionid);

		$parts = explode("\r\n\r\n", $header1, 2);
		$headers = isset($parts[0]) ? $parts[0] : '';
		$authorizationkey = '';
		foreach (explode("\r\n", $headers) as $hdr) {
			if (strstr($hdr, "Authorizationkey:") !== false) {
				$authorizationkey = $hdr;
			}
		}

		$authkey_val = explode(":", $authorizationkey);
		$authorization2 = isset($authkey_val[1]) ? $authkey_val[1] : '';
		$i_s_val = trim($authorization2);

		$cookie_cin = isset($record[0]) ? explode(";", $record[0]) : array();
		$cookiec_in = isset($cookie_cin[0]) ? explode("=", $cookie_cin[0]) : array();
		$c_in = isset($cookiec_in[1]) ? $cookiec_in[1] : '';

		$cookie_expiry = isset($expiry[0]) ? explode("=", $expiry[0]) : array();
		$expiry = isset($cookie_expiry[1]) ? $cookie_expiry[1] : '';

		$cookie_jsessionid = isset($jsessionid[0]) ? explode(";", $jsessionid[0]) : array();
		$cookiejsessionid = isset($cookie_jsessionid[0]) ? explode("=", $cookie_jsessionid[0]) : array();
		$jsessionid_val = isset($cookiejsessionid[1]) ? $cookiejsessionid[1] : '';

			$result = json_decode($body1, true);

			if (!empty($result)) {

				foreach ($result as $val) {
					$user_info = array();

					if (isset($jsessionid_val)) {
						$user_info['jsessionid_val'] = $jsessionid_val;
					}

					//Message
					if (!empty($val['message'])) {
						error_log("UsernamePWD API Response : message attribute value : " . $val['message'] . " for user = " . $username);
						$user_info['message'] = $val['message'];
					}

					//First Name    
					if (!empty($val['data']['firstName'])) {
						$user_info['first_name'] = $val['data']['firstName'];
					} else {
						error_log("UsernamePWD API Response : data.firstname  is empty for user = " . $username);
					}

					//Last Name
					if (!empty($val['data']['surname'])) {
						$user_info['last_name'] = $val['data']['surname'];
					} else {
						error_log("UsernamePWD API Response : data.surname  is empty for user = " . $username);
					}

					//Email
					if (!empty($val['data']['emailId'])) {
						$user_info['email'] = $val['data']['emailId'];
					} else {
						error_log("UsernamePWD API Response : data.emailId  is empty for user = " . $username);
					}

					//Branch
					if (!empty($val['data']['branchName'])) {
						$user_info['branch'] = $val['data']['branchName'];
					} else {
						error_log("UsernamePWD API Response : data.branchName  is empty for user = " . $username);
					}

					//Location
					if (!empty($val['data']['locationName'])) {
						$user_info['location'] = $val['data']['locationName'];
					} else {
						error_log("UsernamePWD API Response : data.locationName  is empty for user = " . $username);
					}

					//Designation 
					if (!empty($val['data']['designation'])) {
						$user_info['designation'] = $val['data']['designation'];
					} else {
						error_log("UsernamePWD API Response : data.designation  is empty for user = " . $username);
					}

					//Department    
					if (!empty($val['data']['deptGroupName'])) {
						$user_info['department'] = $val['data']['deptGroupName'];
					} else {
						error_log("UsernamePWD API Response : data.deptGroupName  is empty for user = " . $username);
					}

					//Sap
					if (!empty($val['data']['sapNumber'])) {
						$user_info['sap'] = $val['data']['sapNumber'];
					} else {
						error_log("UsernamePWD API Response : data.sapNumber  is empty for user = " . $username);
					}

					//DeptGroup
					if (!empty($val['data']['deptGroup'])) {
						$user_info['deptGroup'] = $val['data']['deptGroup'];
					} else {
						error_log("UsernamePWD API Response : data.deptGroup is empty for user = " . $username);
					}

					//Cookie i_s
					//if (!empty($i_s)) {
					if (!empty($i_s_val)) {
						$user_info['i_s'] = $i_s_val;
						$user_info['i_s_expiry'] = strtotime($expiry);
						//$user_info['i_s_expiry'] = '1612433600';
					} else {
						error_log("UsernamePWD API Response : i_s  is empty for user = " . $username);
					}

					//Cookie c_in
					if (!empty($c_in)) {
						$user_info['c_in'] = $c_in;
						$user_info['c_in_expiry'] = strtotime($expiry);
						//$user_info['c_in_expiry'] = '1612433600';
					} else {
						error_log("UsernamePWD API Response : c_in  is empty for user = " . $username);
					}

					// try{
					//   $apiManager = new apiManager();
					//   $api_manager_response = $apiManager->curlrequest($i_s_val,$jsessionid_val);

					//   if (!empty($api_manager_response)) {
					//     $user_info['manager_email'] = $api_manager_response;
					//     error_log("Response received from Authentication API");
					//   }else {
					//     error_log("Manager API error from Authentication API");
					//   }
					// }catch(Exception $e){
					//     error_log("Manager API error");
					// }

					return $user_info;
				}
			} else {
				error_log("UsernamePWD API Response :--No data fetched for username" . $username);
			}
		} catch (Exception $e) {
			error_log("UsernamePWD API Response :--Exception thrown for username" . $username . "-Exception-" . $e->getMessage());
			throw new Exception("Some error");
		}
	}
}
