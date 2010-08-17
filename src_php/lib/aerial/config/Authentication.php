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
			
			self::getInstance()->parse();
		}
		
		public function parse()
		{
			$contents = $this->config;
			
			$acl = array();
			
			$groups = $contents->xpath("//group");
			foreach($groups as $group)
			{
				$group = new SimpleXMLElement($group->asXML());
				
				$groupArr =& $acl["groups"][(string) $group["name"]];
				$groupArr = $this->attrToAssoc($group);
				
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
			}
			
			print_r($acl);	
			die();
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
			
			$users = self::getInstance()->config->xpath("//user[@name='{$credentials->username}'".
															"and @password='{$credentials->password}']");
			
			die(empty($users));
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