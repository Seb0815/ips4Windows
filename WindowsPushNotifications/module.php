<?
	
	class WindowsNotification extends IPSModule 
	{
   	private $header1 = 'Content-Type: text/xml';
		private $header2 = 'X-WNS-Type: ';
   	private $header3 = 'Authorization: Bearer ';
   	private $header4 = 'X-WNS-Tag: ';
   	private $deviceURI;
    	private $authToken;
    	private $Debug = false;
    	private $WNSMsgToken = "abc";

    	function __construct($ObjID)
    	{
 		// Diese Zeile nicht löschen
            	parent::__construct($InstanceID);

			$deviceToken = GetValueString($ObjID);
			$data = explode("###",$deviceToken);
			if (count($data) > 1)
			{
				$this->deviceURI = $data[0];
				$this->authToken = $data[1];
			}
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