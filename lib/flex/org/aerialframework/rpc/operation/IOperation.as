package org.aerialframework.rpc.operation
{
	import mx.rpc.AsyncToken;
	
	import org.aerialframework.rpc.AbstractService;
	
	public interface IOperation
	{
		
		/*function get service():AbstractService;
		function set service(value:AbstractService):void;*/
		
		/*function get method():String;
		function set method(value:String):void;*/
		
		function callback(_resultHandler:Function, _faultHandler:Function = null, _tokenData:Object = null):Operation;
		function execute(offset:uint=0, limit:uint=0):AsyncToken;
	}
}