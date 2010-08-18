<?php
	class Authentication
	{
		private static $_instance;

		private $config;

		public function __construct()
		{
			if(isset(self::$_instance))
				trigger_error("You must not call the constructor directly! This class is a Singleton");
		}
		
		private static function init()
		{
			$file = dirname(__FILE__)."/authentication.xml";
			$f = fopen($file, "r+");
			$contents = fread($f, filesize($file));
			fclose($f);
			
			$contents = new SimpleXMLElement($contents);
			self::getInstance()->config = $contents;
			
			$parsed = self::getInstance()->parse();
			if(self::getInstance()->validate($parsed))
			{
				session_start();
				$_SESSION["valid-credentials"] = $parsed;
			}
		}
		
		protected function parse()
		{
			$contents = $this->config;
			
			$acl = array();
			
			$groups = $contents->xpath("//group");
			foreach($groups as $group)
			{
				$group = new SimpleXMLElement($group->asXML());
				
				$groupArr =& $acl["groups"][(string) $group["name"]];
				$groupArr = $this->attrToAssoc($group);
				$groupArr["raw"] = $group->asXML();
				
				$users = $group->xpath("(//user)");
				foreach($users as $user)
				{
					$userArr =& $groupArr["users"];
					$userArr[] = $this->attrToAssoc($user);
				}
			}
			
			$roles = $contents->xpath("//role");
			foreach($roles as $role)
			{
				$role = new SimpleXMLElement($role->asXML());
				$roleArr =& $acl["roles"][(string) $role["name"]];
				$roleArr = $this->attrToAssoc($role);
				$roleArr["raw"] = $role->asXML();
				
				$components = $role->xpath("(//service) | (//function) | (//member)");
				foreach($components as $compontent)
				{
					$name = $compontent->getName()."s";
					$roleArr[$name][] = $this->attrToAssoc($compontent);
				}
			}
			
			$rules = $contents->xpath("//rule");
			foreach($rules as $rule)
			{
				$rule = new SimpleXMLElement($rule->asXML());
				$ruleArr =& $acl["rules"][(string) $rule["name"]];
				$ruleArr = $this->attrToAssoc($rule);
				$ruleArr["raw"] = $rule->asXML();
			}
			
			return $acl;
		}
		
		protected function validate($parsed)
		{
			// rule #1 - group's role must be valid
			foreach($parsed["groups"] as $group)
			{
				if(empty($parsed["roles"][$group["role"]]))
				{
					$message = "Invalid role reference \"".$group["role"]."\"";
					$this->errorHandler($message, "groups", $group["raw"]);
				}
			}			
			
			// rule #2 - user's overridden role must be valid
			foreach($parsed["groups"] as $group)
				foreach($group["users"] as $user)
				{
					if(!array_key_exists("override", $user))
						continue;
					
					if(empty($parsed["roles"][$user["override"]]))
					{
						$message = "Invalid role override reference \"".$user["override"]."\"";
						$this->errorHandler($message, "user", $group["raw"]);
					}
				}
			
			// need to validate rules
			return true;
		}
		
		private function errorHandler($message, $name, $raw)
		{
			$message = "An error occured in the $name node in your authentication.xml file:\n".
							"Message: $message\n".
							$raw;

			throw new Exception($message);
		}
		
		private function attrToAssoc(SimpleXMLElement $node)
		{
			$assoc = array();
			foreach($node->attributes() as $attr => $val)
				$assoc[$attr] = (string) $val;
				
			return $assoc;
		}
		
		public function isValid()
		{
			session_start();
			$credentials = $_SESSION["credentials"];
			$validated = $_SESSION["valid-credentials"];
			
			//$users = self::getInstance()->config->xpath("//user[@name='{$credentials->username}'".
			//												"and @password='{$credentials->password}']");
			
			$validatedUser = null;
			foreach($validated["groups"] as $group)
				foreach($group["users"] as $user)
				{
					if($user["username"] == $credentials->username &&
						$user["password"] == $credentials->password)
					{
						$validatedUser = new stdClass();
						$validatedUser->username = $credentials->username;
						$validatedUser->password = $credentials->password;
						$validatedUser->group = new stdClass();
						$validatedUser->group->name = $group["name"];
						$validatedUser->group->role = $validated["roles"][$group["role"]];
					}
				}
				
			return $validatedUser ? $validatedUser : false;
		}

		public static function getInstance()
		{
			if(!isset(self::$_instance))
			{
				self::$_instance = new self();
				self::init();
			}

			return self::$_instance;
		}
	}
?>