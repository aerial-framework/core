<?php
	class Authentication
	{
		private static $_instance;
        public static $credentials;

        public static $nextClass;
        public static $nextMethod;
        public static $nextArgs;

        const ALLOW = "allow";
        const DENY = "deny";

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
				self::$credentials = $parsed;
		}
		
		protected function parse()
		{
			$contents = $this->config;
			
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
        
        public function logout()
        {
            self::getInstance()->credentials = null;
        }
		
		public function isValid()
		{
            if(!USE_AUTH)
                return false;
            
			session_start();
			$credentials = $_SESSION["credentials"];
			$validated = self::$credentials;
            
            //print_r($validated);
            //die();
			
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
				
			return !empty($validatedUser) ? $this->checkPermissions($validatedUser) : false;
		}
        
        private function checkPermissions($user)
        {
            $group = $user->group;
            $role = $user->group->role;
            
            $services = $role["services"];
            $functions = $role["functions"];
            $member = $role["member"];
            
            $role = $role["rule"];
            
            $authenticated = true;
            
            $servicesArr = array();
            
            //NetDebug::trace("Checking services:\n");
            //foreach($services as $service)
            //    $servicesArr[] = $service["name"];
                
            foreach($functions as $function)
            {
                $service = substr($function["name"], 0, strrpos($function["name"], "."));
                $func = substr($function["name"], strrpos($function["name"], ".") + 1);
                
                // check if any of the functions defined are equivalent to the upcoming execution
                if($service == self::$nextClass && $func == self::$nextMethod)
                {
                    // if there's an allow override, skip everything else and grant access
                    if($function["override"] == self::ALLOW)
                        return true;
                    
                    // if there's a deny override, skip everything else and deny access
                    if($function["override"] == self::DENY)
                        return false;
                    
                    // check to see that this service is not denied
                    foreach($services as $service)
                    {
                        // if not a matching service, skip
                        if($service["name"] != self::$nextClass)
                            continue;
                        
                        // check for override
                        if($service["override"] != null)
                        {
                            if($service["override"] == self::ALLOW)
                                $authenticated = true;
                                
                            if($service["override"] == self::DENY)
                                $authenticated = false;
                                
                            continue;
                        }
                        
                        // catchall
                        if($role == self::ALLOW)
                            $authenticated = true;
                            
                        if($role == self::DENY)
                            $authenticated = false;
                    }
                    
                    //NetDebug::trace("Nu? $authenticated");
                }
            }
            
            /*foreach($services as $service)
            {
                if($service["name"] != self::$nextClass)
                {
                    NetDebug::trace("\t".$service["name"]." is on the allow list\n");
                    continue;
                }
                
                if($service["override"] != null)
                {
                    if($service["override"] == self::ALLOW)
                        $authenticated = true;
                        
                    if($service["override"] == self::DENY)
                        $authenticated = false;
                        
                    continue;
                }
                
                $inArray = in_array($service["name"], $servicesArr);
                
                if($inArray)
                {
                    if($role == self::DENY)
                    {
                        NetDebug::trace("\t".self::$nextClass." is on the deny list\n");
                        $authenticated = false;
                    }
                    elseif($role == self::ALLOW)
                    {
                        NetDebug::trace("\t".self::$nextClass." is on the allow list\n");
                        $authenticated = true;
                    }
                }
                
                NetDebug::trace("Checking service: ".$service["name"].":".($authenticated ? "Y" : "N")."\n");
            }
                    
                //if($service["name"] == self::$nextClass && $role == self::DENY)
                //{
                //    echo "\t".self::$nextClass." is on the deny list\n";
                //    $authenticated = false;
                //}
              
            NetDebug::trace("Checking functions:\n");
            foreach($functions as $function)
            {
                $name = $function["name"];
                $service = substr($name, 0, strrpos($name, "."));
                $func = substr($name, strrpos($name, ".") + 1);                
                
                //if($function["override"] != null)
                //{
                //    if($function["override"] == self::ALLOW)
                //        $authenticated = true;
                //        
                //    if($function["override"] == self::DENY)
                //        $authenticated = false;
                //        
                //    continue;
                //}
                
                $inArray = in_array($func, get_class_methods($service));
                //print_r(get_class_methods($service));
                //echo $func." >>> ".($inArray ? "Yes" : "No")."\n\n";
                
                //if($inArray)
                //{
                //    if($role == self::DENY)
                //    {
                //        NetDebug::trace("\t$service is on the deny list\n");
                //        $authenticated = false;
                //    }
                //    elseif($role == self::ALLOW)
                //    {
                //        NetDebug::trace("\t$service is on the allow list\n");
                //        $authenticated = true;
                //    }
                //}
                
                //echo "\t$class>$func\n";
            }
            
            //if(in_array("UserService", $services, false) && $role == self::DENY)
            //    $authenticated = false;
                    
            //die("Authenticated? ".($authenticated ? "YES" : "NO"));
            */
            return $authenticated;
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