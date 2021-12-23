<?php

/*************helper functions***************/

function clean($string) {

	return htmlentities($string);
}

function redirect($location) {

	return header("Location: {$location}");
}

function set_message($message) {

	if(!empty($message)) {

		$_SESSION['message'] = $message;

		}else {

			$message = "";
		}
}



function display_message() {

	if(isset($_SESSION['message'])) {

		echo $_SESSION['message'];
		unset($_SESSION['message']);
	}
}

function token_generator() {

	$token = $_SESSION['token'] = md5(uniqid(mt_rand(), true));

	return $token; 
}

function otp() {

	$otp = $_SESSION['otp'] = mt_rand(0, 99999);

	return $otp; 
}

function validation_errors($error_message) {

$error_message = <<<DELIMITER

<div class="col-md-12 alert alert-danger alert-mg-b alert-success-style6 alert-st-bg3 alert-st-bg14">
    <button type="button" class="col-md-12 close sucess-op" data-dismiss="alert" aria-label="Close">
		<span class="icon-sc-cl" aria-hidden="true">&times;</span>
									</button>
                 <p><strong>$error_message </strong></p>
                            </div>
DELIMITER;

   return $error_message;     

}

function validator($error_message) {

$error_message = <<<DELIMITER
<div style="background: #FFE9E6; color: #ff0000;" class="col-md-12 alert alert-danger alert-mg-b alert-success-style6 alert-st-bg3 alert-st-bg14">
    <button type="button" style="color: white;" class="col-md-12 close sucess-op" data-dismiss="modal" aria-label="Close">
		<span class="icon-sc-cl" aria-hidden="true">&times;</span>
									</button>
                 <p><strong>$error_message </strong></p>
                            </div>
DELIMITER;

   return $error_message;     

}



									/****** Helper Functions********/

function email_exist($email) {

	$sql = "SELECT * FROM user WHERE `email` = '$email'";
	$result = query($sql);

	if(row_count($result) == 1) {

		return true;

	}else {

		return false;
	} 
}

function username_exist($usname) {

	$sql = "SELECT * FROM user WHERE `user` = '$usname'";
	$result = query($sql);

	if(row_count($result) == 1) {

		return true;

	}else {

		return false;
	} 
}





/** VALIDATE USER REGISTRATION **/

if(isset($_POST['fname']) && isset($_POST['email']) && isset($_POST['pword']) && isset($_POST['cpword'])) {

$fname 			= clean(escape($_POST['fname']));
$email	 		= clean(escape($_POST['email']));
$uname	 		= clean(escape(ucwords($_POST['user'])));
$pword    		= clean(escape($_POST['pword']));
$cpword 		= clean(escape($_POST['cpword']));

if(email_exist($email)) {

			echo "Sorry! The email inputted already has an account";
		} else {

if(username_exist($uname)) {

			echo "That username is unavailable!";
		} else {

			register($fname, $email, $uname, $pword);
		}
	}
	} // post request


	

/** REGISTER USER **/
function register($fname, $email, $uname, $pword) {

	$fnam = escape($fname);
	$emai = escape($email);
	$unam = escape(ucwords($uname));
	$pwor = md5($pword);

	$datereg = date("Y-m-d");

	$_SESSION['usermail'] = $emai;
		

	$activator = md5(otp());
	
	$sql = "INSERT INTO user(`id`, `fname`, `user`, `email`, `pword`, `dreg`, `active`, `activator`)";
	$sql.= " VALUES('1', '$fnam', '$unam', '$emai', '$pwor', '$datereg', '0', '$activator')";
	$result = query($sql);

	//redirect to verify function
	$subj = "VERIFY YOUR EMAIL";
	$msg  = "Hi there! <br /><br />Kindly use the button below to activate your account;";
	$ninn = "https://cose.teagonig.com/verify?vef=".$activator;

	mail_mailer($email, $ninn, $subj, $msg);

	//open otp page
	echo 'Loading... Please Wait!';
	echo '<script>verify();</script>';
	 }



/* MAIL VERIFICATIONS */
function mail_mailer($email, $ninn, $subj, $msg) {

	$to = $email;
	$from = "noreply@cose.teagonig.com";
	
	$headers = "From: " . $from . "\r\n";
	$headers .= "Reply-To: ". $from . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
	$headers .= "X-Priority: 1 (Highest)\n";
	$headers .= "X-MSMail-Priority: High\n";
	$headers .= "Importance: High\n";
	
	$subject = $subj;
	
	$logo = 'https://cose.teagonig.com/home/assets/images/logo.png';
	$url = 'https://cose.teagonig.com/';
	
	$body = "
	<!DOCTYPE html>
	<html lang='en'>
	
	<head>
	<meta charset='UTF-8'>
	<title>FUOYE COSE</title>
	</head>
	<link rel='stylesheet' href='https://cose.teagonig.com/home/assets/css/vendor/bootstrap.min.css'>
	<body style='text-align: center;'>";
	$body .= "<section style='margin: 30px; margin-top: 50px ; background: #be1e2d; color: #fff;'>";
	$body .= "<img style='margin-top: 35px; width: 460px; height: 105px;' src='{$logo}' alt='TeaGo Cose'>";
	$body .= "<h1 style='margin-top: 45px; color: #fff'>{$subj}</h1>
	<br />";
	$body .= "<h3 style='margin-left: 45px; margin-top: 34px; text-align: left; font-size: 17px;'>{$msg}</h3>
	<br />";
	$body .= "<h1 style='margin-left: 45px; text-align: center;'><a href='{$ninn}'><button style='padding: 20px 30px; border: none; background: white; color: #be1e2d'><b>ACTIVATE ACCOUNT</b></button></a></h1>
	<br />";
	$body .= "<p style='margin-left: 45px; padding-bottom: 80px; text-align: left;'>Do not bother replying this
	email. This is a virtual email</p>";
	$body .= "<p text-align: center;'><a href='https://teagonig.com/contact'><img style='width:150px;heght:150px'
			src='https://cose.teagonig.com/home/assets/images/footer.png'></a>";
	$body .= "
	<h4 style='text-align: center;'>Email.: <span style='color: #fff'>hello@cose.teagonig.com</span></h4>";
	$body .= "<h4 style='text-align: center;'>Call/Chat.: <span style='color: #fff'>+234(0) 802 276 2696</span>
	</h4>";
	$body .= "<h4 style='text-align: center; padding-bottom: 80px; padding-top: 40px;'>Team TEAGO COSE</h4>";    
	$body .= "<script src='https://cose.teagonig.com/home/assets/js/vendor/bootstrap.min.js'></script>";
	$body .= "</section>";
	$body .= "</body></html>";
	$send = mail($to, $subject, $body, $headers);
}


/** SIGN IN USER **/
 	if(isset($_POST['username']) && isset($_POST['password'])) {

			$username        = clean(escape($_POST['username']));
			$password   	 = md5($_POST['password']);

			$sql = "SELECT * FROM `user` WHERE `user` = '$username' AND `pword` = '$password'";
			$result = query($sql);
			if(row_count($result) == 1) {

				$row 	    = mysqli_fetch_array($result);
				
				$user 		= $row['user'];
				$active 	= $row['active'];
				$email 		= $row['email'];
				$activate 	= $row['activator'];

				if ($active == 0) {

					$ninn = md5(otp());

					$_SESSION['usermail'] = $email;

					//update activation link
					$ups = "UPDATE users SET `activator` = '$ninn' WHERE `user` = '$username'";
					$ues = query($ups);

					$subj = "VERIFY YOUR EMAIL";
					$msg  = "Hi there! <br /><br />Kindly use the button below to activate your account;";

					mail_mailer($email, $ninn, $subj, $msg);

					//open verification page
					echo 'Loading... Please Wait!';
					echo '<script>verify();</script>';
	
					
				}  else {

					if($username == $user) {
						
						$_SESSION['user'] = $username;

						echo 'Loading... Please Wait';	

						echo '<script>window.location.href ="./home"</script>';	
					} else {

						echo "This username doesn't have an account.";
					}

			} 

		}  else {
			
			echo '<script>window.location.href ="./forgot"</script>';
		}
	}


/** FORGOT PASSWORD **/
if(isset($_POST['fgeml'])) {
	
	$email  = clean(escape($_POST['fgeml']));

	$_SESSION['usermail'] = $email;

	if(!email_exist($email)) {

		echo "Sorry! This email doesn't have an account";
		
	} else {

	$activator = otp();

	$ssl = "UPDATE users SET `activator` = '$activator' WHERE `email` = '$email'";
	$rsl = query($ssl);

	//redirect to verify function
	$subj = "RESET YOUR PASSWORD";
	$msg  = "Hi there! <br /><br />Kindly use the otp below to restore your password;";

	mail_mailer($email, $activator, $subj, $msg);

	//open otp page
	echo 'Loading... Please Wait!';
	$_SESSION['vnext'] = "updatePword();";
	echo '<script>otpVerify(); signupClose();</script>';

	}
}



/** RESET PASSWORD **/
if(isset($_POST['fgpword']) && isset($_POST['fgcpword'])) {

	    $fgpword = md5($_POST['fgpword']);
        $eml = $_SESSION['usermail'];

	$sql = "UPDATE users SET `pword` = '$fgpword', `activator` = '' WHERE `email` = '$eml'";
	$rsl = query($sql);
	
	//get username and redirect to dashboard
	$ssl = "SELECT * FROM users WHERE `email` =  '$eml'";
	$rsl = query($ssl);
	if(row_count($rsl) == '') {
		
		echo 'Loading... Please Wait';
		echo '<script>window.location.href ="./signin"</script>';
		
	} else {

		$row  = mysqli_fetch_array($rsl);
		$user = $row['usname'];

		$_SESSION['login'] = $user;
		
		
		echo 'Loading... Please Wait';

		echo '<script>window.location.href ="./"</script>';
		
	}
}




// DASHBOARD FUNCTIONS FOR USER
function user_details() {
	
	$data = $_SESSION['login'];


	//users details
	$sql = "SELECT * FROM users WHERE `usname` = '$data'";
	$rsl = query($sql);

	//check if user details is valid
	if(row_count($rsl) == '') {

		redirect(".././logout");
		
	} else {

    $GLOBALS['t_users'] = mysqli_fetch_array($rsl);

	}

	//referal details
	$rss = "SELECT sum(`active`) AS `earn` FROM `users` WHERE `ref` = '$data'";
	$res = query($rss);
    $GLOBALS['t_ref'] = mysqli_fetch_array($res);

	$GLOBALS['t_ref_earn'] = $GLOBALS['t_ref']['earn'] * 100;

}



//get account name
if(isset($_POST['bank']) && isset($_POST['acctn']) && isset($_POST['trd'])) {

	$bank  = clean(escape($_POST['bank']));
	$acctn = clean(escape($_POST['acctn']));


	//get bank code first
    $banks = array(
		array('id' => '1','name' => 'Access Bank','code'=>'044'),
		array('id' => '2','name' => 'Citibank','code'=>'023'),
		array('id' => '3','name' => 'Diamond Bank','code'=>'063'),
		array('id' => '4','name' => 'Dynamic Standard Bank','code'=>''),
		array('id' => '5','name' => 'Ecobank Nigeria','code'=>'050'),
		array('id' => '6','name' => 'Fidelity Bank Nigeria','code'=>'070'),
		array('id' => '7','name' => 'First Bank of Nigeria','code'=>'011'),
		array('id' => '8','name' => 'First City Monument Bank','code'=>'214'),
		array('id' => '9','name' => 'Guaranty Trust Bank','code'=>'058'),
		array('id' => '10','name' => 'Heritage Bank Plc','code'=>'030'),
		array('id' => '11','name' => 'Jaiz Bank','code'=>'301'),
		array('id' => '12','name' => 'Keystone Bank Limited','code'=>'082'),
		array('id' => '13','name' => 'Providus Bank Plc','code'=>'101'),
		array('id' => '14','name' => 'Polaris Bank','code'=>'076'),
		array('id' => '15','name' => 'Stanbic IBTC Bank Nigeria Limited','code'=>'221'),
		array('id' => '16','name' => 'Standard Chartered Bank','code'=>'068'),
		array('id' => '17','name' => 'Sterling Bank','code'=>'232'),
		array('id' => '18','name' => 'Suntrust Bank Nigeria Limited','code'=>'100'),
		array('id' => '19','name' => 'Union Bank of Nigeria','code'=>'032'),
		array('id' => '20','name' => 'United Bank for Africa','code'=>'033'),
		array('id' => '21','name' => 'Unity Bank Plc','code'=>'215'),
		array('id' => '22','name' => 'Wema Bank','code'=>'035'),
		array('id' => '23','name' => 'Zenith Bank','code'=>'057'),
		array('id' => '24','name' => 'HighStreet MFB bank','code'=>'090175'),
		array('id' => '25','name' => 'TCF MFB','code' => '90115'),
	  array(
		  'id' => 132,
		  'code' => '560',
		  'name' => 'Page MFBank'
	  ),
	  array(
		  'id' => 133,
		  'code' => '304',
		  'name' => 'Stanbic Mobile Money'
	  ),
	  array(
		  'id' => 134,
		  'code' => '308',
		  'name' => 'FortisMobile'
	  ),
	  array(
		  'id' => 135,
		  'code' => '328',
		  'name' => 'TagPay'
	  ),
	  array(
		  'id' => 136,
		  'code' => '309',
		  'name' => 'FBNMobile'
	  ),
	  array(
		  'id' => 137,
		  'code' => '011',
		  'name' => 'First Bank of Nigeria'
	  ),
	  array(
		  'id' => 138,
		  'code' => '326',
		  'name' => 'Sterling Mobile'
	  ),
	  array(
		  'id' => 139,
		  'code' => '990',
		  'name' => 'Omoluabi Mortgage Bank'
	  ),
	  array(
		  'id' => 140,
		  'code' => '311',
		  'name' => 'ReadyCash (Parkway)'
	  ),
	  array(
		  'id' => 143,
		  'code' => '306',
		  'name' => 'eTranzact'
	  ),
	  array(
		  'id' => 145,
		  'code' => '023',
		  'name' => 'CitiBank'
	  ),
	  array(
		  'id' => 147,
		  'code' => '323',
		  'name' => 'Access Money'
	  ),
	  array(
		  'id' => 148,
		  'code' => '302',
		  'name' => 'Eartholeum'
	  ),
	  array(
		  'id' => 149,
		  'code' => '324',
		  'name' => 'Hedonmark'
	  ),
	  array(
		  'id' => 150,
		  'code' => '325',
		  'name' => 'MoneyBox'
	  ),
	  array(
		  'id' => 151,
		  'code' => '301',
		  'name' => 'JAIZ Bank'
	  ),
		array(
		  'id' => 153,
		  'code' => '307',
		  'name' => 'EcoMobile'
	  ),
	  array(
		  'id' => 154,
		  'code' => '318',
		  'name' => 'Fidelity Mobile'
	  ),
	  array(
		  'id' => 155,
		  'code' => '319',
		  'name' => 'TeasyMobile'
	  ),
	  array(
		  'id' => 156,
		  'code' => '999',
		  'name' => 'NIP Virtual Bank'
	  ),
	  array(
		  'id' => 157,
		  'code' => '320',
		  'name' => 'VTNetworks'
	  ),
		array(
		  'id' => 159,
		  'code' => '501',
		  'name' => 'Fortis Microfinance Bank'
	  ),
	  array(
		  'id' => 160,
		  'code' => '329',
		  'name' => 'PayAttitude Online'
	  ),
	  array(
		  'id' => 161,
		  'code' => '322',
		  'name' => 'ZenithMobile'
	  ),
	  array(
		  'id' => 162,
		  'code' => '303',
		  'name' => 'ChamsMobile'
	  ),
	  array(
		  'id' => 163,
		  'code' => '403',
		  'name' => 'SafeTrust Mortgage Bank'
	  ),
	  array(
		  'id' => 164,
		  'code' => '551',
		  'name' => 'Covenant Microfinance Bank'
	  ),
	  array(
		  'id' => 165,
		  'code' => '415',
		  'name' => 'Imperial Homes Mortgage Bank'
	  ),
	  array(
		  'id' => 166,
		  'code' => '552',
		  'name' => 'NPF MicroFinance Bank'
	  ),
	  array(
		  'id' => 167,
		  'code' => '526',
		  'name' => 'Parralex'
	  ),
	  array(
		  'id' => 169,
		  'code' => '084',
		  'name' => 'Enterprise Bank'
	  ),
		array(
		  'id' => 187,
		  'code' => '314',
		  'name' => 'FET'
	  ),
	  array(
		  'id' => 188,
		  'code' => '523',
		  'name' => 'Trustbond'
	  ),
	  array(
		  'id' => 189,
		  'code' => '315',
		  'name' => 'GTMobile'
	  ),
		array(
		  'id' => 182,
		  'code' => '327',
		  'name' => 'Pagatech'
	  ),
	  array(
		  'id' => 183,
		  'code' => '559',
		  'name' => 'Coronation Merchant Bank'
	  ),
	  array(
		  'id' => 184,
		  'code' => '601',
		  'name' => 'FSDH'
	  ),
	  array(
		  'id' => 185,
		  'code' => '313',
		  'name' => 'Mkudi'
	  ),
	   array(
		  'id' => 171,
		  'code' => '305',
		  'name' => 'Paycom'
	  ),
	  array(
		  'id' => 172,
		  'code' => '100',
		  'name' => 'SunTrust Bank'
	  ),
	  array(
		  'id' => 173,
		  'code' => '317',
		  'name' => 'Cellulant'
	  ),
	  array(
		  'id' => 174,
		  'code' => '401',
		  'name' => 'ASO Savings and & Loans'
	  ),
	  array(
		  'id' => 176,
		  'code' => '402',
		  'name' => 'Jubilee Life Mortgage Bank'
	  ),
	);

	$row = 0; 
	
	while($row < 68) {
        
        if($banks[$row]['name'] == $bank){
    
        $bankcode = $banks[$row]['code'];
        }
		
		$row++;
    }
	
	//echo $bank;

	$request = [

		'account_number' => $acctn,
		'account_bank' => $bankcode
	];
	
	$curl = curl_init();
	
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.flutterwave.com/v3/accounts/resolve',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => json_encode($request),
		CURLOPT_HTTPHEADER => array(
			'Authorization: Bearer FLWSECK-1109e7cb4c9e1871e91a90f1d91c8479-X',
			'Content-Type: application/json'
		),
		));
	
	    $response = curl_exec($curl);
		$err = curl_error($curl);

		if($err){
		// there was an error contacting the rave API
		die('Error Retrieving Your Account Name');
		}
		
		curl_close($curl);

		
		$res = json_decode($response);

        if($res->status == "success") {
		echo $res->data->account_name;
        } else {

            echo "Error Retrieving Your Account Name";
        }
	
}


if(isset($_POST['gend']) && isset($_POST['inst']) && isset($_POST['dept']) && isset($_POST['level']) && isset($_POST['matric']) && isset($_POST['bank']) && isset($_POST['acctn']) && isset($_POST['actn']) && isset($_POST['pword'])) {

	$gend 	 = clean(escape($_POST['gend']));
	$inst 	 = clean(escape($_POST['inst']));
	$dept 	 = clean(escape($_POST['dept']));
	$level	 = clean(escape($_POST['level']));
	$matric  = clean(escape($_POST['matric']));
	$bank    = clean(escape($_POST['bank']));
	$acctn   = clean(escape($_POST['acctn']));
	$actn    = clean(escape($_POST['actn']));
	$pword   = md5($_POST['pword']);

	$user = $_SESSION['login'];

	$sql = "UPDATE users SET `gend` = '$gend', `inst` = '$inst', `tpin` = '$pword', `dept` = '$dept', `level` = '$level', `matric` = '$matric', `bname` = '$bank', `bact` = '$acctn', `actname` = '$actn' WHERE `usname` = '$user'";
	$res = query($sql);

	echo "Loading... Please wait";
	echo '<script>window.location.href ="./"</script>';
}



//transfer function
function transfer($usus) {

	$sql = "SELECT * FROM users WHERE `usname` = '$usus'";
	$res = query($sql);

	if(row_count($res) == null) {
		
		echo "Username is invalid";
		die();
	} else {

		$GLOBALS['t_trans'] = mysqli_fetch_array($res);
		
	}
}



//sending money to a savearn user
if(isset($_POST['amt']) && isset($_POST['usus'])) {

$amt = clean(escape($_POST['amt']));
$usus = clean(escape($_POST['usus']));

//get current user details
user_details();

$mainuser = ucwords($t_users['usname']);

//check if user is crediting self
if($mainuser == ucwords($usus)) {
	
	echo "You can't send money to yourself";
	
} else {

	//check if user exist
	transfer($usus);

	//chcek if user has enough funds
	$bal = ($t_users['wallet'] + $t_ref_earn) - 100;
	if($bal < $amt) {

		echo "A minimum of NGN100 should be left on your account";
		
	} else {

		//deduct current user wallet
		$newbal = $bal - $amt;
		
		//get beneficiary user wallet and add amt
		$tbal = $t_trans['wallet'] + $amt;
		
		//update user wallet
		$sql = "UPDATE users SET `wallet` = '$newbal' WHERE `usname` = '$mainuser'";
		$res = query($sql);

		//credit beneficiary
		$bsql = "UPDATE users SET `wallet` = '$tbal' WHERE `usname` = '$usus'";
		$bres = query($bsql);

		//notify user transaction history
		$date = date("Y-m-d h:i:sa");
		$ref = "tpay".rand(0, 999);
		$msg  = "Your transfer of NGN".number_format($newbal)." to ". $usus ." was successful";
		$sbj  = "Debit Alert";

		$msql = "INSERT INTO msgs(`usname`, `status`, `sn`, `msg`, `date`, `ticket`, `sbj`)";
		$msql .="VALUES('$mainuser', 'unread', '1', '$msg', '$date', '$ref', '$sbj')";
		$mes = query($msql);

		//notify beneficiary
		$bref  = "tpay".rand(0, 999);
		$bmsg  = "You have been credited NGN".number_format($newbal)." from ". $usus;
		$bsbj  = "Credit Alert";

		$bmsql = "INSERT INTO msgs(`usname`, `status`, `sn`, `msg`, `date`, `ticket`, `sbj`)";
		$bmsql .="VALUES('$mainuser', 'unread', '1', '$msg', '$date', '$ref', '$sbj')";
		$bmes = query($bmsql);


		//create an alert message
		$_SESSION['transfered'] = "Success";
		echo "Loading... Please wait";
		echo '<script>window.location.href ="./"</script>';
		
	}
	
}
}


/*** SAVING PLANS FUNCTIOn */

//campus plan
if(isset($_POST['campan']) && isset($_POST['rrcampan'])) {

	$ammt  =  clean($_POST['campan']);
	$det   =  clean($_POST['rrcampan']);

	//get user wallet balance
	user_details();

	$user = $t_users['usname'];

	//chcek if user has enough funds
	$bal = ($t_users['wallet'] + $t_ref_earn) - 100;

	if($bal < $ammt) {

		echo "<script>
        iziToast.error({
          title: 'Error!',
          message: 'You do not have enough funds in your wallet. Kindly fund your wallet and try again',
          position: 'topCenter'
        });</script>";
		
	} else {

		//deduct current user wallet
		$newbal = $bal - $ammt + 100;

		//notify user transaction history
		$date = date("Y-m-d h:i:sa");
		$ref = "tpay".rand(0, 999);
		$msg  = "Your ". $det ." of NGN".number_format($ammt)." was successful";
		$sbj  = "Savings Alert";

		$nsql = "INSERT INTO msgs(`usname`, `status`, `sn`, `msg`, `date`, `ticket`, `sbj`)";
		$nsql .="VALUES('$user', 'unread', '1', '$msg', '$date', '$ref', '$sbj')";
		$nes = query($nsql);

		//update user wallet
		$sql = "UPDATE users SET `wallet` = '$newbal' WHERE `usname` = '$user'";
		$res = query($sql);

		//credit savings wallet
		$vsql = "INSERT INTO savings(`usname`, `datepaid`, `plan`, `duration`, `amt`, `status`, `mode`, `descrip`)";
		$vsql .="VALUES('$user', '$date', '$det', 'A week before exam', '$ammt', 'Active', 'Wallet', 'Campus Savings')";
		$ves = query($vsql);

		//create an alert message
		$_SESSION['campusplan'] = "Success";
		echo "Loading... Please wait";
		echo '<script>window.location.href ="./withdrawal"</script>';
	}

	
}


//flex plan
if(isset($_POST['flxamt']) && isset($_POST['dest']) && isset($_POST['plann'])) {

	$flxamt     =  clean($_POST['flxamt']);
	$dest       =  clean($_POST['dest']);
	$plann      =  clean($_POST['plann']);
	$date 		=  date("Y-m-d h:i:sa");

	//get user wallet balance
	user_details();

	$user = $t_users['usname'];

	//chcek if user has enough funds
	$bal = ($t_users['wallet'] + $t_ref_earn) - 100;

	if($bal < $flxamt) {

		echo "<script>
        iziToast.error({
          title: 'Error!',
          message: 'You do not have enough funds in your wallet. Kindly fund your wallet and try again',
          position: 'topCenter'
        });</script>";
		
	} else {

		//deduct current user wallet
		$newbal = $bal - $flxamt + 100;

		/*//notify user transaction history
		$ref = "tpay".rand(0, 999);
		$msg  = "Your ". $plann ." of NGN".number_format($flxamt)." was successful";
		$sbj  = "Savings Alert";

		$nsql = "INSERT INTO msgs(`usname`, `status`, `sn`, `msg`, `date`, `ticket`, `sbj`)";
		$nsql .="VALUES('$user', 'unread', '1', '$msg', '$date', '$ref', '$sbj')";
		$nes = query($nsql);

		//update user wallet
		$sql = "UPDATE users SET `wallet` = '$newbal' WHERE `usname` = '$user'";
		$res = query($sql);*/

		//credit savings wallet
		$vsql = "INSERT INTO savings(`usname`, `datepaid`, `plan`, `amt`, `status`, `mode`, `descrip`)";
		$vsql .="VALUES('$user', '$date', '$plann', '$flxamt', 'Active', 'Wallet', '$dest')";
		$ves = query($vsql);

		//create an alert message
		$_SESSION['flexplan'] = "Success";
		echo "Loading... Please wait";
		echo '<script>window.location.href ="./withdrawal"</script>';
	}
}


//classic plan
if(isset($_POST['classic']) && isset($_POST['cldd']) && isset($_POST['clplan'])) {

	$classic    =  clean($_POST['classic']);
	$cldd       =  clean($_POST['cldd']);
	$clplan     =  clean($_POST['clplan']);

	//get user wallet balance
	user_details();

	$user = $t_users['usname'];

	//chcek if user has enough funds
	$bal = ($t_users['wallet'] + $t_ref_earn) - 100;

	if($bal < $classic) {

		echo "<script>
        iziToast.error({
          title: 'Error!',
          message: 'You do not have enough funds in your wallet. Kindly fund your wallet and try again',
          position: 'topCenter'
        });</script>";
		
	} else {

		//deduct current user wallet
		$newbal = $bal - $classic + 100;

		//notify user transaction history
		$date = date("Y-m-d h:i:sa");
		$ref = "tpay".rand(0, 999);
		$msg  = "Your ". $clplan ." of NGN".number_format($classic)." was successful";
		$sbj  = "Savings Alert";

		$nsql = "INSERT INTO msgs(`usname`, `status`, `sn`, `msg`, `date`, `ticket`, `sbj`)";
		$nsql .="VALUES('$user', 'unread', '1', '$msg', '$date', '$ref', '$sbj')";
		$nes = query($nsql);

		//update user wallet
		$sql = "UPDATE users SET `wallet` = '$newbal' WHERE `usname` = '$user'";
		$res = query($sql);

		//credit savings wallet
		$vsql = "INSERT INTO savings(`usname`, `datepaid`, `plan`, `duration`, `amt`, `status`, `mode`, `descrip`)";
		$vsql .="VALUES('$user', '$date', '$clplan', '$cldd', '$classic', 'Active', 'Wallet', 'Classic Saving')";
		$ves = query($vsql);

		//create an alert message
		$_SESSION['classicplan'] = "Success";
		echo "Loading... Please wait";
		echo '<script>window.location.href ="./withdrawal"</script>';
	}
}
?>