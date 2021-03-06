<?
	
	class ips4WinCortana extends IPSModule 
	{
		private $id;
    	private $CommandList = null;
    	private $FeedbackList = null;
    	private $ExampleList = null;
    	private $PhraseList = null;
    	private $Debug = false;
		private $Result = null;
		private $UTF8 = false;

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
 			 
				$vid = $this->RegisterVariableString("ips4WinCortanaVoiceCommands", "CortanaVoiceCommands", "", 0);
				$this->id = $vid;
				$this->RegisterScript("ips4WinCortanaVoiceCommandsScript", "ips4WinCortanaSprachbefehle","<? //Do not delete or modify.\ninclude(IPS_GetKernelDirEx().\"scripts/__ipsmodule.inc.php\");\ninclude(\"../modules/ips4Windows/ips4WinCortana/module.php\");\n\n\$ips4WinCortana = new ips4WinCortana(".$vid.");\n\n//Your code goes here...\n\n\$ips4WinCortana->EnableDebug(true);\n\n //you can add up to 100 sections (called Commands, each need a unique name like Block1\/Block2 or what ever you like)\n //each section can have up to 10 speach commands and include multiple PhraseLists (like {room}) entries\n\$ips4WinCortana->AddVoiceCommand(\"Block1\",\"schalte das Licht im {room} {action}\");\n\$ips4WinCortana->AddVoiceCommand(\"Block2\",\"{action} das Rollo im {room}\");\n\n //each section needs a Feedback and Example entry. Feedback is used when command was processed from Cortana\n //Example will shown, when user asks Cortana \"What can I say?/Was kann ich sagen?\"\n\$ips4WinCortana->AddCommandExample(\"Block1\",\"schalte das Licht im Wohnzimmer an\");\n\$ips4WinCortana->AddCommandExample(\"Block2\",\"schliesse das Rollo im B�ro\");\n\n\$ips4WinCortana->AddCommandFeedback(\"Block1\",\"ok, ich verarbeite deinen Befehl\");\n\$ips4WinCortana->AddCommandFeedback(\"Block2\",\"ich arbeite dran\");\n\n //you can add multiple PhraseLists with an total of max. 2000 entries\n\$ips4WinCortana->AddActionPhraseCommand(\"action\",\"�ffne\");\n\$ips4WinCortana->AddActionPhraseCommand(\"action\",\"schliesse\");\n\$ips4WinCortana->AddActionPhraseCommand(\"action\",\"an\");\n\$ips4WinCortana->AddActionPhraseCommand(\"action\",\"aus\");\n\$ips4WinCortana->AddActionPhraseCommand(\"room\",\"Wohnzimmer\");\n\$ips4WinCortana->AddActionPhraseCommand(\"room\",\"B�ro\");\n\n //ProcessData will convert all the entries to something readable for ips4Windows App\n\$ips4WinCortana->ProcessData();"); 


 				$sid = $this->RegisterScript("ips4WinCortanaHook", "ips4WinCortanaHook", "<? //Do not delete or modify.\ninclude(IPS_GetKernelDirEx().\"scripts/__ipsmodule.inc.php\");\ninclude(\"../modules/ips4Windows/ips4WinCortana/module.php\");\n\$ips4WinCortana = new ips4WinCortana(".$this->InstanceID.");\n\$ips4WinCortana->ProcessHookData();\nif (\$_POST['ips4Command'] != \"GetConfig\")\n{\n\n//Your code goes here...\n\n\$resultArray = \$ips4WinCortana->GetCortanaResult();\n//\$resultArray has all the data from Cortana\n//textSpoken - full message captured by Cortana\n//CommandName - Command of textSpoken line, user defined in ips4CortanaSprachbefehle script (aka section)\n//action - used entry from PhraseList defined in ips4CortanaSprachbefehle script\n//room - used entry from PhraseList defined in ips4CortanaSprachbefehle script\n//feedback - if you used SendFeedbackVerify or SendFeedbackVerifyDetailed you will get the result from this commands, multiple iteration will be added with \"#+#\" as seperator. Use explode ( \"#+#\" , \$resultArray->feedback) to work with the result array\nIPS_LogMessage(\"WebHook Cortana\", print_r(\$resultArray, true));\n//do something useful\n//...\n//Cortana waits for feedback, otherwise the request will timeout in seconds and shows an error\n//Options: sucess - SendFeedbackSuccess(Msg), failure - SendFeedbackFailure(Msg)\n//verify with yes/no - SendFeedbackVerify(Msg), verify with more option - SendFeedbackVerifyDetailed(Msg, ListofOptions as array)\n\$result = \$ips4WinCortana->SendFeedbackSuccess(\"Erfolgreich ausgef�hrt\");\n//last line of the script must be an echo result\necho \$result;\n}"); 

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
 			 
 			 
 			if(!isset($_POST['ips4Command'])) { 
 				IPS_LogMessage("ips4WinCortana", "Malformed data: ".print_r($_POST, true)); 
 				return; 
 			}
			
			if ($_POST['ips4Command'] == "GetConfig")
			{
				$vid = @IPS_GetObjectIDByIdent("ips4WinCortanaVoiceCommands", $this->InstanceID); 
 				if($vid === false) 
 				{
					echo "ERROR: Variable ips4WinCortanaVoiceCommands not found in IPS, please check installation of instance \"ips4WinCortana\"!"; 
				}
				else
				{
					$ret = GetValueString ($vid);
					if ($ret == "")
					{
						$ret = "ERROR: ips4WinCortanaVoiceCommands has no entries, please check script \"ipsWinCortanaSprachbefehle\" and don't forget to start the script!";
					}
					echo $ret;
				}
			}
			elseif ($_POST['ips4Command'] == "Data")
			{
				$this->Result = $_POST;	
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

		public function GetCortanaResult()
		{
			return $this->Result;
		}

		public function SendFeedbackSuccess($FeedbackMessage)
		{	
			//Erfolgmeldung		
			$Content = "OK#*#".$FeedbackMessage;
			 
			$Content = $this->convertForOutput($Content);
			return $Content;
		}
		public function SendFeedbackFailure($FeedbackMessage)
		{
			//Fehlerbenachrichtigung
			$Content = "ERROR#*#".$FeedbackMessage;
			 
			$Content = $this->convertForOutput($Content);
			return $Content;
		}
		public function SendFeedbackVerify($FeedbackMessage)
		{	
			//R�ckfrage Ja/Nein		
			$Content = "VERIFY#*#".$FeedbackMessage;
			 
			$Content = $this->convertForOutput($Content);
			return $Content;
		}
		public function SendFeedbackVerifyDetailed($FeedbackMessage, $ListValues)
		{	
			//R�ckfrage mit Auswahl Liste		
			$Content = "VERIFYDETAILED#*#".$FeedbackMessage."#*#";
			$i=0;
			foreach($ListValues as $value)
			{
				if ($i==0)
					$Content = $Content.$value;
				else
					$Content = $Content."#+#".$value;
				$i++;
			}
			 
			$Content = $this->convertForOutput($Content);
			return $Content;
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
			   $arr[0] = $Phrase;
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
				$temp=implode("#=#",$value);
			   if (strlen($CommandListString) > 0)
					$CommandListString = $CommandListString."#+#".$key."#-#".$temp;
				else
					$CommandListString = $key."#-#".$temp;
					
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
					$FeedbackListString = $FeedbackListString."#+#".$key."#-#".$value;
				else
					$FeedbackListString = $key."#-#".$value;
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
					$ExampleListString = $ExampleListString."#+#".$key."#-#".$value;
				else
					$ExampleListString = $key."#-#".$value;
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
				$temp=implode("#=#",$value);
			   if (strlen($PhraseListString) > 0)
					$PhraseListString = $PhraseListString."#+#".$key."#-#".$temp;
				else
					$PhraseListString = $key."#-#".$temp;
			
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
				
				$Content = $this->convertForOutput($Content);
			
				SetValueString($this->InstanceID,$Content);
	  			echo "Data successful written\n";

				if (IPS_ModuleExists("{44D8BE09-743E-484F-A64D-154C4235BE94}"))
				{
					ips4WNS_sendRawNotification(0,null,"ips4Windows#CortanaRefresh");					
				}


			}

		}
		public function EnableDebug($Debug)
    	{
    	   $this->Debug = $Debug;
		}

		public function UTF8Convert($UTF8)
    	{
    	   $this->UTF8 = $UTF8;
		}
		
		private function convertForOutput($text) 
		{ 
			$utf8 = $text;

			if ($this->UTF8)
				$utf8 = utf8_encode($text);
			
			$searchFor = array("�","�","�","�","�","�","�");
			$replaceWith = array("#ae#","#AE#","#oe#","#OE#","#ue#","#UE#","#ss#");
			$text = str_replace ($searchFor, $replaceWith, $utf8); 
			
			$base64 = base64_encode($text);

			return $base64;	
		} 

		 
	}
?>