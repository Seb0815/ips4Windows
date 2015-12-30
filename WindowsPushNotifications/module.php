<?
	
	class WindowsNotifications extends IPSModule 
	{
   		private $header1 = 'Content-Type: text/xml';
		private $header2 = 'X-WNS-Type: ';
   		private $header3 = 'Authorization: Bearer ';
   		private $header4 = 'X-WNS-Tag: ';
   		private $deviceURI;
    	private $authToken;
    	private $Debug = false;
    	private $WNSMsgToken = "abc";

		 public function __construct($InstanceID) 
		 {
            // Diese Zeile nicht löschen
            parent::__construct($InstanceID);
 
            // Selbsterstellter Code
			$this->deviceURI = "";
			$this->authToken = "";
         }
		     	

		public function Create() 
		{ 
 			//Never delete this line! 
 			parent::Create(); 
 		} 

	
		public function ApplyChanges() 
		{ 
				//Never delete this line! 
 				parent::ApplyChanges(); 
 			 
 				$sid = $this->RegisterScript("ips4WNSHook", "ips4WNSHook", "<? //Do not delete or modify.\ninclude(IPS_GetKernelDirEx().\"scripts/__ipsmodule.inc.php\");\ninclude(\"../modules/ips4Windows/WindowsNotifications/module.php\");\n(new WindowsNotifications(".$this->InstanceID."))->ProcessHookData();"); 
  				$this->RegisterHook("/hook/ips4WinDeviceRegistration", $sid); 
		} 

		private function RegisterHook($Hook, $TargetID) 
 		{ 
 			$ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}"); 
 			if(sizeof($ids) > 0) { 
 				$hooks = json_decode(IPS_GetProperty($ids[0], "ips4WNSHook"), true); 
 				$found = false; 
 				foreach($hooks as $index => $hook) { 
 					if($hook['Hook'] == "/hook/ips4WinDeviceRegistration") 
					{ 
 						if($hook['TargetID'] == $TargetID) 
 							return; 
 						$hooks[$index]['TargetID'] = $TargetID; 
 						$found = true; 
 					} 
 				} 
 				if(!$found) { 
 					$hooks[] = Array("Hook" => "/hook/ips4WinDeviceRegistration", "TargetID" => $TargetID); 
 				} 
 				IPS_SetProperty($ids[0], "Hooks", json_encode($hooks)); 
 				IPS_ApplyChanges($ids[0]); 
 			} 
 		} 

		public function ProcessHookData() 
 		{ 
 			if($_IPS['SENDER'] == "Execute") { 
 				echo "This script cannot be used this way."; 
 				return; 
 			} 
 			 
 			 
 			if(!isset($_POST['device']) || !isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['accessToken'])) { 
 				IPS_LogMessage("WindowsNotifications", "Malformed data: ".print_r($_POST, true)); 
 				return; 
 			} 
 			 
 			$deviceID = $this->CreateInstanceByIdent($this->InstanceID, utf8_decode($_POST['id']), "Device"); 
 			SetValue($this->CreateVariableByIdent($deviceID, "SecChannel", "SecChannel", 3), utf8_decode($_POST['id'])); 
 			SetValue($this->CreateVariableByIdent($deviceID, "Name", "Name", 2), utf8_decode($_POST['name'])); 
 			SetValue($this->CreateVariableByIdent($deviceID, "Timestamp", "Timestamp", 1, "~UnixTimestamp"), intval(strtotime($_POST['date']))); 
 			 

 		} 


		private function CreateCategoryByIdent($id, $ident, $name) 
		 { 
			 $cid = @IPS_GetObjectIDByIdent($ident, $id); 
			 if($cid === false) 
			 { 
				 $cid = IPS_CreateCategory(); 
				 IPS_SetParent($cid, $id); 
				 IPS_SetName($cid, $name); 
				 IPS_SetIdent($cid, $ident); 
 			 } 
 			 return $cid; 
 		} 
 		 
 		private function CreateVariableByIdent($id, $ident, $name, $type, $profile = "") 
 		 { 
 			 $vid = @IPS_GetObjectIDByIdent($ident, $id); 
 			 if($vid === false) 
 			 { 
 				 $vid = IPS_CreateVariable($type); 
 				 IPS_SetParent($vid, $id); 
 				 IPS_SetName($vid, $name); 
 				 IPS_SetIdent($vid, $ident); 
 				 if($profile != "") 
 					IPS_SetVariableCustomProfile($vid, $profile); 
 			 } 
 			 return $vid; 
 		} 
		 
		private function CreateInstanceByIdent($id, $ident, $name, $moduleid = "{485D0419-BE97-4548-AA9C-C083EB82E61E}") 
    	 { 
			 $iid = @IPS_GetObjectIDByIdent($ident, $id); 
 			 if($iid === false) 
 			 { 
 				 $iid = IPS_CreateInstance($moduleid); 
 				 IPS_SetParent($iid, $id); 
 				 IPS_SetName($iid, $name); 
 				 IPS_SetIdent($iid, $ident); 
 			 } 
 			 return $iid; 
 		} 


    	
    	public function EnableDebug($Debug)
    	{
    	   $this->Debug = $Debug;
	}
		
		public function SetWNSMsgToken($Token)
    	{
    	
    	   $this->WNSMsgToken = "abc".$Token;
		}


		public function sendBagdeNotification($Value)
	 	{
	 	   $headers = array($this->header1,$this->header2.'wns/badge',$this->header3.$this->authToken);
	 	   $body = '<?xml version="1.0" encoding="utf-8"?><badge version="1" value="'.$Value.'"/>';
	 	   if ($this->Debug)
	 	   {
	 	      echo "headers\n";
	 	      print_r($headers);
	 	      echo "body\n".$body."\n";
			}
	 	   $response = $this->sendNotification($headers,$body);
	 	   return $response;
	 	}

		 public function sendToastNotification($text1,$text2)
	 	 {
	 	   $headers = array($this->header1,$this->header2.'wns/toast',$this->header3.$this->authToken);
	 	   $body = '<?xml version="1.0" encoding="utf-8"?><toast><visual><binding template="ToastText02">'.
	 	   '<text id="1">'.$text1.'</text><text id="2">'.$text2.'</text>'.
			'</binding></visual></toast>';
			
			if ($this->Debug)
	 	   {
	 	      echo "headers\n";
	 	      print_r($headers);
	 	      echo "body\n".$body."\n";
			}
	 	   $response = $this->sendNotification($headers,$body);
	 	   return $response;
	 	}
	 	 	public function sendTileNotification($text1,$text2,$text3, $text4)
	 	 {
			$text1 = utf8_encode($text1);
			$text2 = utf8_encode($text2);
			$text3 = utf8_encode($text3);
			$text4 = utf8_encode($text4);
			
			$body_medium = '<binding template="TileSquare150x150PeekImageAndText03" fallback="TileSquarePeekImageAndText01">'.
       			  '<image id="1" src="ms-appx:///assets/House-white.100.png" alt="ipsControlImage"/>'.
       			  '<text id="1">'.$text1.'</text><text id="2">'.$text2.'</text><text id="3">'.$text3.'</text><text id="4">'.$text4.'</text>'.
     				  '</binding>';
     		$body_large = '<binding template="TileWide310x150PeekImageAndText02" fallback="TileWidePeekImage02">'.
       			  '<image id="1" src="ms-appx:///assets/House-white.100.png" alt="ipsControlImage"/>'.
       			  '<text id="1">'.$text1.'</text><text id="2">'.$text2.'</text><text id="3">'.$text3.'</text><text id="4">'.$text4.'</text>'.
     				  '</binding>';
			
	 	   $headers = array($this->header1,$this->header2.'wns/tile',$this->header4.$this->WNSMsgToken,$this->header3.$this->authToken );
	 	   $body = '<tile><visual version="2">'.$body_medium.$body_large.'</visual></tile>';


			if ($this->Debug)
	 	   {
	 	      echo "headers\n";
	 	      print_r($headers);
	 	      echo "body\n".$body."\n";
			}
	 	   $response = $this->sendNotification($headers,$body);
	 	   return $response;
	 	}
	 	
	 	public function sendTileNotification_gross($text1,$text2,$text3)
	 	 {

	 	   $headers = array($this->header1,$this->header2.'wns/tile',$this->header4.$this->WNSMsgToken,$this->header3.$this->authToken );
	 	   $body = '<tile><visual version="3"><binding template="TileWide310x150IconWithBadgeAndText">'.
	 	      '<image id="1" src="ms-appx:///assets/House-white.100.png" alt="ipsControlImage"/>'.
	 	   	'<text id="1">'.$text1.'</text><text id="2">'.$text2.'</text><text id="3">'.$text3.'</text>'.
			'</binding></visual></tile>';

			if ($this->Debug)
	 	   {
	 	      echo "headers\n";
	 	      print_r($headers);
	 	      echo "body\n".$body."\n";
			}
	 	   $response = $this->sendNotification($headers,$body);
	 	   return $response;
	 	}
	 	public function sendTileNotification_mittel($text1,$text2,$text3)
	 	 {

	 	   $headers = array($this->header1,$this->header2.'wns/tile',$this->header4.$this->WNSMsgToken,$this->header3.$this->authToken );
	 	   $body = '<tile><visual version="3"><binding template="TileSquare150x150IconWithBadge">'.
	 	      '<image id="1" src="ms-appx:///assets/House-white.100.png" alt="ipsControlImage"/>'.
	 	   	'</binding></visual></tile>';

			if ($this->Debug)
	 	   {
	 	      echo "headers\n";
	 	      print_r($headers);
	 	      echo "body\n".$body."\n";
			}
	 	   $response = $this->sendNotification($headers,$body);
	 	   return $response;
	 	}
	 	public function sendTileNotification_klein($text1,$text2,$text3)
	 	 {
	 	   $headers = array($this->header1,$this->header2.'wns/tile',$this->header4.$this->WNSMsgToken,$this->header3.$this->authToken );
	 	   $body = '<tile><visual version="3"><binding template="TileSquare71x71IconWithBadge"><image id="1" src="ms-appx:///assets/House-white.100.png" alt="ipsControlImage"/></binding></visual></tile>';


			if ($this->Debug)
	 	   {
	 	      echo "headers\n";
	 	      print_r($headers);
	 	      echo "body\n".$body."\n";
			}
	 	   $response = $this->sendNotification($headers,$body);
	 	   return $response;
	 	}
	 	
	 	private function sendNotification($headers,$body)
	 	{
	 	 	if ($this->Debug)
	 	   {
	 	      echo "URL:\n".$this->deviceURI."\n";
			}
			
			if ($this->deviceURI != "")
			{
		 		// use Client URL Library
				$ch = curl_init();
				// set an options for a cURL transfer
				// look options here:  http://www.php.net/manual/en/function.curl-setopt.php
				// and what are needed here: http://msdn.microsoft.com/en-us/library/windowsphone/develop/hh202970(v=vs.92).aspx
				curl_setopt($ch, CURLOPT_URL, $this->deviceURI);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				// perform a cURL session
				$response = curl_exec($ch);

				// Check for errors
				if($response === FALSE)
				{
				  echo "Error in WindowsNotifications->sendNotifications: ".curl_error($ch);
				}
				// close a cURL session
				curl_close($ch);
				if($response === FALSE)
					return "Error in WindowsNotifications->sendNotifications: ".curl_error($ch);
				else
			   return true;
			}
			else
				return "Error in WindowsNotifications->sendNotifications: DeviceURI is null! Will skip message";
		

			
		}

	}