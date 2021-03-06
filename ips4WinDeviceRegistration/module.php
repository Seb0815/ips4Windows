<?
	
	class ips4WinDeviceRegistration extends IPSModule 
	{
   		 public function __construct($InstanceID) 
		 {
            // Diese Zeile nicht l�schen
            parent::__construct($InstanceID);
 
            // Selbsterstellter Code
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
 			 
 				$sid = $this->RegisterScript("ips4WinDeviceHook", "ips4WinDeviceHook", "<? //Do not delete or modify.\ninclude(IPS_GetKernelDirEx().\"scripts/__ipsmodule.inc.php\");\ninclude(\"../modules/ips4Windows/ips4WinDeviceRegistration/module.php\");\n(new ips4WinDeviceRegistration(".$this->InstanceID."))->ProcessHookData();"); 

  				$this->RegisterHook("/hook/ips4WinDeviceReg", $sid); 
		} 

		private function RegisterHook($Hook, $TargetID) 
 		{ 
 			$ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}"); 
 			if(sizeof($ids) > 0) { 
 				$hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true); 
 				$found = false; 
 				foreach($hooks as $index => $hook) { 
 					if($hook['Hook'] == "/hook/ips4WinDeviceReg") 
					{ 
 						if($hook['TargetID'] == $TargetID) 
 							return; 
 						$hooks[$index]['TargetID'] = $TargetID; 
 						$found = true; 
 					} 
 				} 
 				if(!$found) { 
 					$hooks[] = Array("Hook" => "/hook/ips4WinDeviceReg", "TargetID" => $TargetID); 
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
 						 
 			if(!isset($_POST['deviceName']) || !isset($_POST['deviceType']) || !isset($_POST['deviceFamily'])) { 
 				IPS_LogMessage("ips4WinDeviceRegistration", "Malformed data: ".print_r($_POST, true)); 
 				return; 
 			}
			
			$id =  str_replace("-","",utf8_decode($_POST['deviceName']).utf8_decode($_POST['deviceId']));
 			$id =  str_replace(" ","",$id);
			 
 			$deviceID = $this->CreateInstanceByIdent($this->InstanceID, $id, utf8_decode($_POST['deviceName'])."-".utf8_decode($_POST['deviceId'])); 
 			
 			SetValue($this->CreateVariableByIdent($deviceID, "deviceName", "deviceName", 3), utf8_decode($_POST['deviceName'])); 
			SetValue($this->CreateVariableByIdent($deviceID, "deviceType", "deviceType", 3), utf8_decode($_POST['deviceType'])); 
			SetValue($this->CreateVariableByIdent($deviceID, "deviceId", "deviceId", 3), utf8_decode($_POST['deviceId'])); 			
			SetValue($this->CreateVariableByIdent($deviceID, "deviceFamily", "deviceFamily", 3), utf8_decode($_POST['deviceFamily'])); 
 			if (isset($_POST['SecChannel']))
			{
				SetValue($this->CreateVariableByIdent($deviceID, "SecChannelExp", "SecChannelExp", 1, "~UnixTimestamp"), intval(strtotime($_POST['SecChannelExp'])));  	
				SetValue($this->CreateVariableByIdent($deviceID, "SecChannel", "SecChannel", 3), utf8_decode($_POST['SecChannel'])); 
				SetValue($this->CreateVariableByIdent($deviceID, "AccessToken", "AccessToken", 3), utf8_decode($_POST['AccessToken'])); 		 
			}

			if (isset($_POST['geoID']))
			{
				$geoIDName = str_replace("-","",utf8_decode($_POST['geoID']));
 				$geoIDName =  str_replace(" ","",$geoIDName);

				$idGeofences = $this->CreateCategoryByIdent($deviceID,"Geofences","Geofences");
				$idGeofence = $this->CreateInstanceByIdent($idGeofences,$geoIDName,utf8_decode($_POST['geoID']));
				//SetValue($this->CreateVariableByIdent($idGeofence, "Latitude", "Latitude", 2), floatval($_POST['Latitude'])); 
	 			//SetValue($this->CreateVariableByIdent($idGeofence, "Longitude", "Longitude", 2), floatval($_POST['Longitude'])); 
				SetValue($this->CreateVariableByIdent($idGeofence, "Timestamp", "Timestamp", 1, "~UnixTimestamp"), intval(strtotime($_POST['date']))); 
	 			SetValue($this->CreateVariableByIdent($idGeofence, "LocationState", "Status", 0, "~Presence"), intval($_POST['geoPresence']) > 0);  

			}

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
	
    	

	}