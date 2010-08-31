package org.aerial.system
{
	public class Configuration
	{
		
		private var _AMFGatewayURL:String;
		
		public function Configuration()
		{
			
		}


		public function get AMFGatewayURL():String
		{
			return _AMFGatewayURL;
		}

		public function set AMFGatewayURL(value:String):void
		{
			_AMFGatewayURL = value;
		}

	}
}