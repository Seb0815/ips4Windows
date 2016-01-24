<?
	class ips4WinNotifications extends IPSModule 
	{

   		private $header1 = 'Content-Type: text/xml';
		private $header2 = 'X-WNS-Type: ';
   		private $header3 = 'Authorization: Bearer ';
   		private $header4 = 'X-WNS-Tag: ';
    	private $Debug = false;
    	private $WNSMsgToken = "abc";
		private $UTF8 = false;

    	function __construct($InstanceID)
    	{
		    // Diese Zeile nicht löschen
            parent::__construct($InstanceID);
 			
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
		} 
    	
    	public function EnableDebug($Debug)
    	{
    	   $this->Debug = $Debug;
		}

		public function UTF8Convert($UTF8)
    	{
    	   $this->UTF8 = $UTF8;
		}
		
		public function SetWNSMsgToken($Token)
    	{
    	
    	   $this->WNSMsgToken = "abc".$Token;
		}


		public function sendBagdeNotification($device, $Value)
	 	{
			if ($this->UTF8)
			{
				$Value = utf8_encode($Value);
			}
	 	   
		   $body = '<?xml version="1.0" encoding="utf-8"?><badge version="1" value="'.$Value.'"/>';
	 	  
	 	   $response = $this->sendNotification($device,"Bagde",$body);
	 	   return $response;
	 	}

		public function sendToastNotification($device,$text1,$text2)
	 	{
			if ($this->UTF8)
			{
				$text1 = utf8_encode($text1);
				$text2 = utf8_encode($text2);
				
			}
			
	 	    $body = "<?xml version=\"1.0\" encoding=\"utf-8\"?><toast><visual><binding template=\"ToastGeneric\"><text>".$text1."</text><text>".$text2."</text></binding></visual></toast>";
			    	
	 	    $response = $this->sendNotification($device,"Toast",$body);
	 	    return $response;
	 	}

		
		public function sendTileNotificationXML($device, $xml)
	 	{
	
			$response = $this->sendNotification($device, "Tile",$xml);
	 		return $response;
		}

		public function sendTileNotification($device, $text1,$text2,$text3,$text4)
	 	{			
	 		if ($this->UTF8)
			{
				$text1 = utf8_encode($text1);
				$text2 = utf8_encode($text2);
				$text3 = utf8_encode($text3);
			}
			$bodystart = "<tile><visual>";
			$bodysmall =  
					'<binding template="TileSmall">'.
                                '<text hint-style="base">'.$text1.'</text>'.
                               '<text hint-style="caption">'.$text2.'</text>'.
                     '</binding>';

			$bodymedium =
                     '<binding template="TileMedium">'.
                                '<text hint-style="base">'.$text1.'</text>'.
                               '<text hint-style="caption">'.$text2.'</text>'.
                               '<text hint-style="caption">'.$text3.'</text>'.
                               '<text hint-style="caption">'.$text4.'</text>'.
                     '</binding>';
            $bodywide =
                     '<binding template="TileWide">'.
                                '<text hint-style="base">'.$text1.'</text>'.
                               '<text hint-style="caption">'.$text2.'</text>'.
                               '<text hint-style="caption">'.$text3.'</text>'.
                               '<text hint-style="caption">'.$text4.'</text>'.
                     '</binding>';
					            
			$bodyend = "</visual></tile>";
			
			$body = $bodystart.$bodysmall.$bodymedium.$bodywide.$bodyend;
			
						
	 	   $response = $this->sendNotification($device, "Tile",$body);
	 	   return $response;
	 	}

		private function getDeviceAddress($deviceName)
		{
				
			$ret = IPS_GetInstanceListByModuleID ("{44D8BE09-743E-484F-A64D-154C4235BE94}");
			$DeviceURL = "";
			$AccessToken = "";
			$retArray = "";
			$SecChannelExp = 0;

			if ($ret == null)
				echo "Error: ips4WinDeviceRegistration is not implemented! Please create a new instance of ips4WinDeviceRegistration!\n";
			else if (count($ret) > 1)
			{
				echo "Error: multiple ips4WinDeviceRegistration found, unsupported installation! Please make sure you have only one instance of ips4WinDeviceRegistration!\n";
			}
			else
			{
				$id = $ret[0];
				$objList = IPS_GetChildrenIDs($id);
				//print_r($objList);
				$i = count($objList);
				for ($j=0;$j<$i;$j++)
				{
					$vid = @IPS_GetObjectIDByName ( "deviceName", $objList[$j]);
					if($vid !== false)
					{
						if ($deviceName == null)
						{
							$device = GetValueString($vid);
		         			$vid = @IPS_GetObjectIDByName ( "SecChannel", $objList[$j]);
							if ($vid !== false)
								$DeviceURL = GetValueString($vid);
							
							$vid = @IPS_GetObjectIDByName ( "AccessToken", $objList[$j]);
							if ($vid !== false)
								$AccessToken = GetValueString($vid);
							
							$vid = @IPS_GetObjectIDByName ( "SecChannelExp", $objList[$j]);
							if ($vid !== false)
								$SecChannelExp = GetValueInteger($vid);

							if ($retArray == "")
							{
						  		$retArray[0] = explode("###",$device."###".$DeviceURL."###".$AccessToken."###".$SecChannelExp);
							}
							else
							{
								$retArray[count($retArray)] = explode("###",$device."###".$DeviceURL."###".$AccessToken."###".$SecChannelExp);
		         			}
							$DeviceURL = "";
							$AccessToken = "";
							$SecChannelExp = 0;
						}
						else
						{
							if (strtolower($deviceName) == strtolower(GetValueString($vid)))
							{
								$vid = @IPS_GetObjectIDByName ( "SecChannel", $objList[$j]);
								if ($vid !== false)
									$DeviceURL = GetValueString($vid);
								
								$vid = @IPS_GetObjectIDByName ( "AccessToken", $objList[$j]);
								if ($vid !== false)
									$AccessToken = GetValueString($vid);
								
								$vid = @IPS_GetObjectIDByName ( "SecChannelExp", $objList[$j]);
								if ($vid !== false)
									$SecChannelExp = GetValueInteger($vid);

								$retArray = array(explode("###",$deviceName."###".$DeviceURL."###".$AccessToken."###".$SecChannelExp));
								$j = $i;
							}
						}
					}
				}		   
			}
			return $retArray;
		}
	 	
	 
	 	private function sendNotification($device, $MsgType ,$body)
	 	{
			$deviceArray = $this->getDeviceAddress($device);
			$errorMsg = "";
			$i = count($deviceArray);
			for ($j=0;$j<$i;$j++)
			{
				if ($this->Debug)
	 			{
					echo "process ".$MsgType." Message for ".$deviceArray[$j][0]." (Device URL: )".$deviceArray[$j][1]."\n";
				}
				
				if ($deviceArray[$j][1] == "" || $deviceArray[$j][2] == "" || $deviceArray[$j][3] < time())
				{
					if ($this->Debug)
	 				{
						echo "Device URL: ".$deviceArray[$j][1]."\n";
						echo "AccessToken: ".$deviceArray[$j][2]."\n";
						echo "SecureChannel Exp: ".$deviceArray[$j][3]." vs. current time: ".time()."\n";
					}

					$errorMsg = $errorMsg."Error in ips4WinNotifications->sendNotifications: skip device ".$deviceArray[$j][0]." device URL/AccessToken is empty or SecureChannel is Expired!\n"."###";
				}
				else
				{
					$authToken = $deviceArray[$j][2];
					$headers = "";
					if ($MsgType == "Bagde")
						$headers = array($this->header1,$this->header2.'wns/badge',$this->header3.$authToken);
					else if ($MsgType == "Toast")
						$headers = array($this->header1,$this->header2.'wns/toast',$this->header3.$authToken);				
					else if ($MsgType == "Tile")
						$headers = array($this->header1,$this->header2.'wns/tile',$this->header4.$this->WNSMsgToken,$this->header3.$authToken );

					if ($this->Debug)
	 				{
						echo "headers:\n";
	 					print_r($headers);
	 					echo "body:\n".$body."\n";
					}

					// use Client URL Library
					$ch = curl_init();
					// set an options for a cURL transfer
					// look options here:  http://www.php.net/manual/en/function.curl-setopt.php
					// and what are needed here: http://msdn.microsoft.com/en-us/library/windowsphone/develop/hh202970(v=vs.92).aspx
					curl_setopt($ch, CURLOPT_URL, $deviceArray[$j][1]);
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
					  $errorMsg = $errorMsg."Error in ips4WinNotifications->sendNotifications: ".curl_error($ch)."###";
					}
					// close a cURL session
					curl_close($ch);
					if($response === FALSE)
						$errorMsg = $errorMsg."Error in ips4WinNotifications->sendNotifications: ".curl_error($ch)."###";
					else

					if($this->Debug)
					{
						echo "result:\n";
						echo $response;
					}				   
				}
			}
			return explode("###",$errorMsg);					
		}
	}
?>
