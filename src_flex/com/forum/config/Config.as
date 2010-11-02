package com.forum.config
{
	import org.aerial.system.IConfig;

	public class Config implements IConfig
	{
		
		public function get AMF_GATEWAY():String
		{
			return "http://aerial-test/server.php";
		}
		
		
	}
}