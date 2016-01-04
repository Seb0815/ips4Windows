<?
	
	class ips4WinCortana extends IPSModule 
	{
		private $id;
    	private $CommandList = null;
    	private $FeedbackList = null;
    	private $ExampleList = null;
    	private $PhraseList = null;
    	private $Debug = false;

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
 			 
				$vid = $this->RegisterVariableString("ips4CortanaVoiceCommands", "CortanaVoiceCommands", "", 0);
				$this->id = $vid;
				$this->RegisterScript("ips4CortanaVoiceCommandsScript", "ips4CortanaSprachbefehle","<? //Do not delete or modify.\ninclude(IPS_GetKernelDirEx().\"scripts/__ipsmodule.inc.php\");\ninclude(\"../modules/ips4Windows/ips4WinCortana/module.php\");\n\n\$ipsCortana = new ips4WinCortana(".$vid.");\n\n//Your code goes here...\n\n\$ipsCortana->EnableDebug(true);\n\n //you can add to 100 sections (called Commands, each need a unique name like Block1/Block2 or what ever you like)\n //each section can have up to 10 speach commands and include multiple PhraseLists (like {room}) entries\n\$ipsCortana->AddVoiceCommand("Block1",\"schalte das Licht im {room} {action}\");\n\$ipsCortana->AddVoiceCommand("Block2",\"{action} das Rollo im {room}\");\n\n //each section needs a Feedback and Example entry. Feedback is used when command was processed from Cortana\n //Example will shown, when user asks Cortana \"What can I say?/Was kann ich sagen?\"\n\$ipsCortana->AddCommandExample("Block1","schalte das Licht im Wohnzimmer an");\n\$ipsCortana->AddCommandExample("Block2","schliesse das Rollo im Büro");\n\n\$ipsCortana->AddCommandFeedback("Block1","ok, ich verarbeite deinen Befehl");\n\$ipsCortana->AddCommandFeedback("Block2","ich arbeite dran");\n\n //you can add multiple PhraseLists with an total of max. 2000 entries\n\$ipsCortana->AddActionPhraseCommand("action","öffne");\n\$ipsCortana->AddActionPhraseCommand("action","schliesse");\n\$ipsCortana->AddActionPhraseCommand("action","an");\n\$ipsCortana->AddActionPhraseCommand("action","aus");\n\$ipsCortana->AddActionPhraseCommand("room","Wohnzimmer");\n\$ipsCortana->AddActionPhraseCommand("room","Büro");\n\n //ProcessData will convert all the entries to something readable for ips4Windows App\n\$ipsCortana->ProcessData();"); 


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

		public function AddVoiceCommand($Command, $ListenFor)
		{
		   //max. 100 Commands with each 10 ListenFor https://msdn.microsoft.com/en-us/library/windows/apps/dn706593.aspx
		   $arr = null;
		   $error = false;
		   if (isset($this->CommandList))
		   {
				if(isset($this->CommandList[$Command]))
				{
					$arr = $this->CommandList[$Command];
				}
				else
				{
				   $i = count($this->CommandList);
				   if (($i+1) > 100)
					{
						echo "Error! CommandList has reached max number of command elements (100)!\n";
						$error = true;
					}
				}
			}

			if (!$error)
			{
			if (isset($arr))
				{
				   $i = count($arr);
				   if ($i < 10)
				   	$arr[$i++] = $ListenFor ;
					else
					{
					   echo "Error! Command '".$Command."' has reached max number of elements (10), please use new Command name\n";
						$error = true;
					}
				}
				else
				{
				   $arr[0] = $ListenFor ;
				}
			}

			if ($error)
			{
				echo "Nothing added to CommandList max. numbers reached (Command Members: ".count($this->CommandList)."/100 Command '".$Command."' Members: ".count($this->CommandList[$Command])."/10)\n";
			}
			else
			{
				$this->CommandList[$Command] = $arr;

				if ($this->Debug)
				{
					echo "Added '".$ListenFor ."' to Command '".$Command."' (Members:".count($this->CommandList[$Command]).")\n";
				}
			}
		}
		public function AddCommandFeedback($Command, $Feedback)
		{
		   // only one per Command allowed https://msdn.microsoft.com/en-us/library/windows/apps/dn706593.aspx
		   $arr = null;
		   $error = false;
		   
		   $this->FeedbackList[$Command] = $Feedback;
		   if ($this->Debug)
			{
				echo "Added '".$Feedback."' as Feedback for Command '".$Command."'\n";
			}

		}
		public function AddCommandExample($Command, $Example)
		{
		   // only one per Command allowed https://msdn.microsoft.com/en-us/library/windows/apps/dn706593.aspx
		   $arr = null;
		   $error = false;

		   $this->ExampleList[$Command] = $Example;
		   if ($this->Debug)
			{
				echo "Added '".$Example."' as Example for Command '".$Command."'\n";
			}

		}
		public function AddActionPhraseCommand($Label, $Phrase)
		{
		   //max. 2000 Phrases in total allowed https://msdn.microsoft.com/en-us/library/windows/apps/dn706593.aspx
		   $arr = null;
		   $error = false;
		   if (isset($this->PhraseList))
		   {
				if(isset($this->PhraseList[$Label]))
				{
					$arr = $this->PhraseList[$Label];
				}
			}

			if (isset($arr))
			{
			   $i = count($this->PhraseList[$Label]);
			 	$arr[$i] = $Phrase;
			}
			else
			{
			   $arr[0] = $Phrase ;
			}


		
		   $counter = 1;
		   if(isset($this->PhraseList))
		   {
			   foreach($this->PhraseList as $key => $value)
			   {
			     	$counter = $counter + count($this->PhraseList[$key]);
				}
				if ($counter > 2000)
				{
				   $error = true;
				}
		   }
			if (!$error)
			{
				$this->PhraseList[$Label] = $arr;
				if ($this->Debug)
				{
					echo "Added '".$Phrase ."' to Label '".$Label." (Members:".$counter."/2000)\n";
				}
			}
			else
			{
			   echo "Error, max. number (2000) of PhraseList reached, nothing added!\n";
			}
		}
		
		
		public function ProcessData()
		{
		   $error = false;
			$Content = "";

			//CommandList processing
		   $CommandListString = "";
		   if ($this->Debug)
			{
			   echo "working on CommandList...";
 			}
			foreach ($this->CommandList as $key => $value)
			{
				$temp=implode("#°#",$value);
			   if (strlen($CommandListString) > 0)
					$CommandListString = $CommandListString."#+#".$key."#~#".$temp;
				else
					$CommandListString = $key."#~#".$temp;
					
				if(!isset($this->FeedbackList[$key]))
				{
				   echo "Error, missing Feedback entry for Command '".$key."'\n";
				   $error = true;
				}
				if(!isset($this->ExampleList[$key]))
				{
				   echo "Error, missing Example entry for Command '".$key."'\n";
				   $error = true;
				}
			}
			//echo "CommandListString:".$CommandListString."\n";
			
			if ($this->Debug)
			{
			   echo "processed!\n";
 			}

			//FeedbackList processiong
		   $FeedbackListString = "";
		   if ($this->Debug)
			{
			   echo "working on FeedbackList...";
 			}
			foreach ($this->FeedbackList as $key => $value)
			{
			   if (strlen($FeedbackListString) > 0)
					$FeedbackListString = $FeedbackListString."#+#".$key."#~#".$value;
				else
					$FeedbackListString = $key."#~#".$value;
			}

			if ($this->Debug)
			{
			   echo "...processed!\n";
 			}

			//ExampleList processiong
			$ExampleListString = "";
		   if ($this->Debug)
			{
			   echo "working on ExampleListString...";
 			}
			foreach ($this->ExampleList as $key => $value)
			{
			   if (strlen($ExampleListString) > 0)
					$ExampleListString = $ExampleListString."#+#".$key."#~#".$value;
				else
					$ExampleListString = $key."#~#".$value;
			}
			
			if ($this->Debug)
			{
			   echo "processed!\n";
 			}

			//PhraseList processing
		   $PhraseListString = "";
			if ($this->Debug)
			{
			   echo "working on PhraseList...";
 			}

			foreach ($this->PhraseList as $key => $value)
			{
				$temp=implode("#°#",$value);
			   if (strlen($PhraseListString) > 0)
					$PhraseListString = $PhraseListString."#+#".$key."#~#".$temp;
				else
					$PhraseListString = $key."#~#".$temp;
			
			}

			if ($this->Debug)
			{
			   echo "processed!\n";
 			}

			if($error)
			{
	  			SetValueString($this->id,"");
	  			echo "Error while processing data - nothing written!\n";
	  		}
			else
			{
				$Content = $CommandListString."#*#".$FeedbackListString."#*#".$ExampleListString."#*#".$PhraseListString;

			   $utf8 = utf8_encode($Content);
	  			$base64 = base64_encode($utf8);
	  			SetValueString($this->id,$base64);
	  			echo "Data successful written\n";

			}

		}
		 
	}
?>