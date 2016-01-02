<?
	
	class ips4WinCortana extends IPSModule 
	{
   		 public function __construct($InstanceID) 
		 {
            // Diese Zeile nicht löschen
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
 			 
				$vid = $this->RegisterVariableString("ips4Cortana", "CortanaVoiceCommands", "", "" );
				$sid = $this->RegisterScript("ips4Cortana", "ips4CortanaSprachbefehle", "<? //Do not delete or modify.\ninclude(IPS_GetKernelDirEx().\"scripts/__ipsmodule.inc.php\");\ninclude(\"../modules/ips4Windows/ips4WinCortana/module.php\");\n\n$ipsCortana = new ips4WinCortana(".$vid.");\n//Your code goes here...\n\nipsCortana->AddVoiceCommand(\"schalte das Licht im Wohnzimmer an\");\nipsCortana->AddVoiceCommand(\"schalte das Licht im Wohnzimmer aus\");\nipsCortana->writeCommandList();"); 

 				$sid = $this->RegisterScript("ips4CortanaHook", "ips4CortanaHook", "<? //Do not delete or modify.\ninclude(IPS_GetKernelDirEx().\"scripts/__ipsmodule.inc.php\");\ninclude(\"../modules/ips4Windows/ips4WinCortana/module.php\");\n(new ips4WinCortana(".$this->InstanceID."))->ProcessHookData();"); 

  				$this->RegisterHook("/hook/ips4WinCortana", $sid); 
		} 

		private function RegisterHook($Hook, $TargetID) 
 		{ 
 			$ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}"); 
 			if(sizeof($ids) > 0) { 
 				$hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true); 
 				$found = false; 
 				foreach($hooks as $index => $hook) { 
 					if($hook['Hook'] == "/hook/ips4WinCortana") 
					{ 
 						if($hook['TargetID'] == $TargetID) 
 							return; 
 						$hooks[$index]['TargetID'] = $TargetID; 
 						$found = true; 
 					} 
 				} 
 				if(!$found) { 
 					$hooks[] = Array("Hook" => "/hook/ips4WinCortana", "TargetID" => $TargetID); 
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
 			 
 			 
 			if(!isset($_POST['device']) || !isset($_POST['name']) || !isset($_POST['accessToken']) || !isset($_POST['SecChannel'])) { 
 				IPS_LogMessage("ips4WinCortana", "Malformed data: ".print_r($_POST, true)); 
 				return; 
 			} 
 			 
 			 

 		}
		public function AddVoiceCommand($command)
		{
		}
		public function writeCommandList()
		{
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
		 
	}
?>