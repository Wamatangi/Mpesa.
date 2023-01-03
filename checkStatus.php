<?php
    /*This is a cron job script. 
    *Runs every 10 seconds and fetches pending transactions from database
    *Then checks transaction status via MPesa API
    */
    
    /*
    *Requires the following package.
    *Install using composer
    *composer require safaricom/mpesa
    */
    
    
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL);
    
    // Connect to server and select database.
        $host; // Host name
    	$username; // Mysql username
    	$password; // Mysql password
    	$db_name; // Database name
    	$mysqliconn = mysqli_connect($host, $username, $password)or die("cannot connect".mysqli_connect_error($mysqliconn));
    	mysqli_select_db($mysqliconn,$db_name)or die("cannot select DB");
    	// Check connection
    	if (mysqli_connect_errno($mysqliconn)){
    		echo "Failed to connect to MySQL: " . mysqli_connect_error();
    	}	

		$stamp = time();
        //get pending transactions
		//$sql ="SELECT phone, amount, user_id, CheckoutRequestID, checkStatusAttemptNo FROM deposits2 WHERE CheckStatusAttemptNo  >= 7 AND CheckStatusAttemptNo  < 10 AND status_id != 2 ORDER BY id DESC LIMIT 0, 3";
		
		// query sample transaction
		$sql ="SELECT phone, amount, user_id, CheckoutRequestID, checkStatusAttemptNo FROM deposits2 WHERE CheckoutRequestID ='ws_CO_20122022080857091724301931'";

		$transaction = mysqli_query($mysqliconn, $sql);
		$depo_phone = '';
		$depo_amount = 0;
		$user_id = 0;
		
		while ($row = mysqli_fetch_array($transaction))
        {
            $count++;
            $depo_phone = $row[0];
            $depo_amount = $row[1];
            $user_id = $row[2];
            $CheckoutRequestID = $row[3];
            $checkStatusAttempt = $row[4];
            $nextCheckStatusAttempt = $checkStatusAttempt + 1;
            
            
                /**
                 * Use this function to make a transaction status request
                 * @param $Initiator | The name of Initiator to initiating the request.
                 * @param $SecurityCredential | 	Encrypted password for the initiator to autheticate the transaction request.
                 * @param $CommandID | Unique command for each transaction type, possible values are: TransactionStatusQuery.
                 * @param $TransactionID | Organization Receiving the funds.
                 * @param $PartyA | Organization/MSISDN sending the transaction
                 * @param $IdentifierType | Type of organization receiving the transaction
                 * @param $ResultURL | The path that stores information of transaction
                 * @param $QueueTimeOutURL | The path that stores information of time out transaction
                 * @param $Remarks | 	Comments that are sent along with the transaction
                 * @param $Occasion | 	Optional Parameter
                 * @return mixed|string
                 */
                 
                 /*
                 *Mpesa parameters
                 */
                 
                //
                $consumer_key = '';
                $consumer_secret = '';
            
                //
                //$consumer_key = '';
                //$consumer_secret ='';
            
            
                $Business_Code = '';
                $Initiator =;
                $Passkey =;
                //$Passkey = ;
                $Type_of_Transaction = 'CustomerPayBillOnline';
                $Token_URL = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
                $Token_URL = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
                $OnlinePayment = 'https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query';
                $total_amount = 5;
                $CallBackURL =;
                $Time_Stamp = date("Ymdhis");
                //$password ='';
                $password ='';
                
                $PartyA = $Business_Code;
                $AccountReference = $CheckoutRequestID;
                $TransactionDesc = 'Transaction Status Query';
                $Remarks ='Transaction Status Query';
            
                require $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";
                $mpesa= new \Safaricom\Mpesa\Mpesa();
            
            
                //$checkoutRequestID = $_GET['lastCheckoutRequestID'];
                
            
                //$STKPushRequestStatus=$mpesa->STKPushQuery($checkoutRequestID,$Business_Code,$password,$Time_Stamp);
                $CommandID = 'TransactionStatusQuery';
                $IdentifierType = 4;
                
                $STKPushRequestStatus=$mpesa->transactionStatus($Initiator, $password, $CommandID, $CheckoutRequestID, $PartyA, $IdentifierType, $CallBackURL, $CallBackURL, $Remarks, $AccountReference);
                
                //dump response
                var_dump($STKPushRequestStatus);
                
                
                $data = str_replace('{','',$STKPushRequestStatus);
                $data = str_replace('}','',$STKPushRequestStatus);
                $data = explode(",",$data);
                $checkStatusResultCode = explode(":", $data[4]);
                $checkStatusResultCode = $checkStatusResultCode[1];
                
                $checkStatusResultDesc = explode(":", $data[5]);
                $checkStatusResultDesc = $checkStatusResultDesc[1];
                $checkStatusResultDesc = str_replace('"','',$checkStatusResultDesc);

        }
   function getEncryptedPasswd($plaintext)
    {
        $pk = openssl_pkey_get_public(getPublicKey());
        openssl_public_encrypt($plaintext, $encrypted, $pk, OPENSSL_PKCS1_PADDING);
        return base64_encode($encrypted);
    }

    /**
     * The Public key certificate being used. Replace with the respective certificate required depending on environment.
     * To get it, just open the certificate in notepad++ and copy paste the contents here, then remove the
     * new line characters '\n' <strong>EXCEPT</strong> the first and last newlines. Check the one below for the required format
     *
     * This is currently the G2 sandbox certificate, will not work with Daraja sandbox
     *
     * @return string
     */
    function getPublicKey()
    {
        $certStr = "-----BEGIN CERTIFICATE-----
                    MIIGkzCCBXugAwIBAgIKXfBp5gAAAD+hNjANBgkqhkiG9w0BAQsFADBbMRMwEQYK
                    CZImiZPyLGQBGRYDbmV0MRkwFwYKCZImiZPyLGQBGRYJc2FmYXJpY29tMSkwJwYD
                    VQQDEyBTYWZhcmljb20gSW50ZXJuYWwgSXNzdWluZyBDQSAwMjAeFw0xNzA0MjUx
                    NjA3MjRaFw0xODAzMjExMzIwMTNaMIGNMQswCQYDVQQGEwJLRTEQMA4GA1UECBMH
                    TmFpcm9iaTEQMA4GA1UEBxMHTmFpcm9iaTEaMBgGA1UEChMRU2FmYXJpY29tIExp
                    bWl0ZWQxEzARBgNVBAsTClRlY2hub2xvZ3kxKTAnBgNVBAMTIGFwaWdlZS5hcGlj
                    YWxsZXIuc2FmYXJpY29tLmNvLmtlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIB
                    CgKCAQEAoknIb5Tm1hxOVdFsOejAs6veAai32Zv442BLuOGkFKUeCUM2s0K8XEsU
                    t6BP25rQGNlTCTEqfdtRrym6bt5k0fTDscf0yMCoYzaxTh1mejg8rPO6bD8MJB0c
                    FWRUeLEyWjMeEPsYVSJFv7T58IdAn7/RhkrpBl1dT7SmIZfNVkIlD35+Cxgab+u7
                    +c7dHh6mWguEEoE3NbV7Xjl60zbD/Buvmu6i9EYz+27jNVPI6pRXHvp+ajIzTSsi
                    eD8Ztz1eoC9mphErasAGpMbR1sba9bM6hjw4tyTWnJDz7RdQQmnsW1NfFdYdK0qD
                    RKUX7SG6rQkBqVhndFve4SDFRq6wvQIDAQABo4IDJDCCAyAwHQYDVR0OBBYEFG2w
                    ycrgEBPFzPUZVjh8KoJ3EpuyMB8GA1UdIwQYMBaAFOsy1E9+YJo6mCBjug1evuh5
                    TtUkMIIBOwYDVR0fBIIBMjCCAS4wggEqoIIBJqCCASKGgdZsZGFwOi8vL0NOPVNh
                    ZmFyaWNvbSUyMEludGVybmFsJTIwSXNzdWluZyUyMENBJTIwMDIsQ049U1ZEVDNJ
                    U1NDQTAxLENOPUNEUCxDTj1QdWJsaWMlMjBLZXklMjBTZXJ2aWNlcyxDTj1TZXJ2
                    aWNlcyxDTj1Db25maWd1cmF0aW9uLERDPXNhZmFyaWNvbSxEQz1uZXQ/Y2VydGlm
                    aWNhdGVSZXZvY2F0aW9uTGlzdD9iYXNlP29iamVjdENsYXNzPWNSTERpc3RyaWJ1
                    dGlvblBvaW50hkdodHRwOi8vY3JsLnNhZmFyaWNvbS5jby5rZS9TYWZhcmljb20l
                    MjBJbnRlcm5hbCUyMElzc3VpbmclMjBDQSUyMDAyLmNybDCCAQkGCCsGAQUFBwEB
                    BIH8MIH5MIHJBggrBgEFBQcwAoaBvGxkYXA6Ly8vQ049U2FmYXJpY29tJTIwSW50
                    ZXJuYWwlMjBJc3N1aW5nJTIwQ0ElMjAwMixDTj1BSUEsQ049UHVibGljJTIwS2V5
                    JTIwU2VydmljZXMsQ049U2VydmljZXMsQ049Q29uZmlndXJhdGlvbixEQz1zYWZh
                    cmljb20sREM9bmV0P2NBQ2VydGlmaWNhdGU/YmFzZT9vYmplY3RDbGFzcz1jZXJ0
                    aWZpY2F0aW9uQXV0aG9yaXR5MCsGCCsGAQUFBzABhh9odHRwOi8vY3JsLnNhZmFy
                    aWNvbS5jby5rZS9vY3NwMAsGA1UdDwQEAwIFoDA9BgkrBgEEAYI3FQcEMDAuBiYr
                    BgEEAYI3FQiHz4xWhMLEA4XphTaE3tENhqCICGeGwcdsg7m5awIBZAIBDDAdBgNV
                    HSUEFjAUBggrBgEFBQcDAgYIKwYBBQUHAwEwJwYJKwYBBAGCNxUKBBowGDAKBggr
                    BgEFBQcDAjAKBggrBgEFBQcDATANBgkqhkiG9w0BAQsFAAOCAQEAC/hWx7KTwSYr
                    x2SOyyHNLTRmCnCJmqxA/Q+IzpW1mGtw4Sb/8jdsoWrDiYLxoKGkgkvmQmB2J3zU
                    ngzJIM2EeU921vbjLqX9sLWStZbNC2Udk5HEecdpe1AN/ltIoE09ntglUNINyCmf
                    zChs2maF0Rd/y5hGnMM9bX9ub0sqrkzL3ihfmv4vkXNxYR8k246ZZ8tjQEVsKehE
                    dqAmj8WYkYdWIHQlkKFP9ba0RJv7aBKb8/KP+qZ5hJip0I5Ey6JJ3wlEWRWUYUKh
                    gYoPHrJ92ToadnFCCpOlLKWc0xVxANofy6fqreOVboPO0qTAYpoXakmgeRNLUiar
                    0ah6M/q/KA==
                    -----END CERTIFICATE-----
";

        return $certStr;
    }
    

?>