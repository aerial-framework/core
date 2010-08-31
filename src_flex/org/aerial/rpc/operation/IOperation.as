package org.aerial.rpc.operation
{
	import mx.rpc.AsyncToken;
	
	import org.aerial.rpc.AbstractService;
	
	public interface IOperation
	{
		
		/*function get service():AbstractService;
		function set service(value:AbstractService):void;*/
		
		/*function get method():String;
		function set method(value:String):void;*/
		
		function callback(value:Function):Operation;
		function execute(offset:uint=0, limit:uint=0):AsyncToken;
	}
}