package com.forum.config
{
	import org.aerial.system.Configuration;

	public class Config extends Configuration
	{
		public static const GATEWAY_URL:String = "http://aerial-test/amfphp/gateway.php";
		
		public function Config()
		{
			this.AMFGatewayURL = "http://aerial-test/amfphp/gateway.php";
		}
		
		
	}
}