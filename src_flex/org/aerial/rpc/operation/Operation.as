package org.aerial.rpc.operation
{
	import flash.events.Event;
	
	import model.vo.IPropertyMap;
	
	import mx.rpc.AbstractOperation;
	import mx.rpc.AsyncToken;
	import mx.rpc.Responder;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.http.Operation;
	
	import org.aerial.rpc.AbstractService;
	
	public class Operation implements IOperation
	{
		
		private var _service:AbstractService;
		private var _method:String;
		private var _callback:Function;
		private var token:AsyncToken;
		private var _op:AbstractOperation;
		private var _args:*;
		 
		public function Operation(service:AbstractService, method:String, args:*=null)
		{
			_service = service;
			_method = method;
			_op = service.getOperation(_method);
			_args = args;
		}
		
		
		public function callback(value:Function):Operation
		{
			_callback = value;
			return this;
		}
		
		public function relations(value:Object):Operation
		{
			return this;
		}
		
		public function sortBy(field:String, order:String = "ASC"):Operation
		{
			
			return this;
		}
		
		public function notifyCaller(event:ResultEvent):void
		{
			event.preventDefault(); //Prevent the service result handler from firing.
			_callback(event);
		}

		
		public function execute(offset:uint=0, limit:uint=0):AsyncToken
		{
			token = _op.send(IPropertyMap(_args).getPropertyMap(), offset, limit);
			if(_callback !== null) token.addResponder(new Responder(notifyCaller,null));
			
			return token;
		}
	}
}