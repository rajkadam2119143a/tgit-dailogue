<?php

require_once get_stylesheet_directory() . '/custom/api/class-api-manager.php'; 

class apiAuthentication {

    public function curlrequest($username, $password) {

        try {

            $postData = http_build_query([
                'emailId'  => $username,
                'password' => $password
            ]);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL            => AUTHENTICATION_API,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => true,
                CURLOPT_POST           => true,

                // SSL FIX
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,

                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_POSTFIELDS     => $postData,

                CURLOPT_HTTPHEADER => [
                    "appnName: ZING",
                    "authKey: 1lxVJZ/89KCNhoQzfds+URrveGLhwPcGap2w5F6WeIMPVNU8i6a/tldiWHQOfYnNn8xs8Y",         
                    "Content-Type: application/x-www-form-urlencoded",
	      	    "Origin: https://zing.timesgroup.com"

                ],
            ]);

            $response = curl_exec($curl);

            // Log full curl data if something goes wrong
            if (curl_errno($curl)) {
                error_log("API CURL ERROR: " . curl_error($curl));
            }

            // Log raw response
            error_log("API RAW RESPONSE: " . $response);

            $curlInfo = curl_getinfo($curl);
            error_log("API CURL INFO: " . print_r($curlInfo, true));

            // HTTP Errors
            if ($curlInfo['http_code'] != 200) {
                error_log("API ERROR HTTP CODE: " . $curlInfo['http_code']);
            }

            // Parse header + body
            $headerSize = $curlInfo['header_size'];
            $headerRaw  = substr($response, 0, $headerSize);
            $body       = substr($response, $headerSize);

            curl_close($curl);

            // Extract cookies
            preg_match('/i_s=([^;]+)/', $headerRaw, $i_s);
            preg_match('/c_in=([^;]+)/', $headerRaw, $c_in);
            preg_match('/JSESSIONID=([^;]+)/', $headerRaw, $jsessionid);
            preg_match('/Expires=(.*GMT)/', $headerRaw, $expiry);

            // Extract Authorizationkey header
            $authorizationKey = '';
            foreach (explode("\r\n", $headerRaw) as $line) {
                if (stripos($line, "Authorizationkey:") !== false) {
                    $authorizationKey = trim(str_replace("Authorizationkey:", "", $line));
                }
            }

            // Debug cookie missing logs
            if (empty($i_s))          error_log("API: i_s cookie missing for user $username");
            if (empty($c_in))         error_log("API: c_in cookie missing for user $username");
            if (empty($expiry))       error_log("API: Expires missing for user $username");
            if (empty($jsessionid))   error_log("API: JSESSIONID missing for user $username");
            if (empty($authorizationKey)) error_log("API: Authorizationkey missing for user $username");

            // Decode JSON Response
            $json = json_decode($body, true);

            if (empty($json)) {
                error_log("UsernamePWD API Response: No JSON received for user $username");
                return false;
            }

            // Build response
            $user_info = [];

            $data = $json[0]['data'] ?? [];
            $message = $json[0]['message'] ?? '';

            $user_info['message']     = $message;
            $user_info['first_name']  = $data['firstName']     ?? '';
            $user_info['last_name']   = $data['surname']       ?? '';
            $user_info['email']       = $data['emailId']       ?? '';
            $user_info['branch']      = $data['branchName']    ?? '';
            $user_info['location']    = $data['locationName']  ?? '';
            $user_info['designation'] = $data['designation']   ?? '';
            $user_info['department']  = $data['deptGroupName'] ?? '';
            $user_info['sap']         = $data['sapNumber']     ?? '';

            // Cookie values
            $user_info['i_s']       = $i_s[1]          ?? '';
            $user_info['c_in']      = $c_in[1]         ?? '';
            $user_info['jsessionid_val'] = $jsessionid[1] ?? '';
            $user_info['authKey']   = $authorizationKey;
            $user_info['expiry']    = !empty($expiry[1]) ? strtotime($expiry[1]) : 0;

            return $user_info;

        } catch (Exception $e) {

            error_log("UsernamePWD API Response: Exception for $username ? " . $e->getMessage());
            return false;
        }
    }
}

?>
