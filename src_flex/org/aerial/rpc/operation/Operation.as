package org.aerial.rpc.operation
{
	import flash.events.Event;
	
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
		private var _sort:Object;
		private var _relations:Object;
		 
		public function Operation(service:AbstractService, method:String, args:*=null)
		{
			_service = service;
			_method = method;
			_op = service.getOperation(_method);
			_args = args;
			_sort = new Array();
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
			_sort[field] = order;
			return this;
		}
		
		public function sortClear(field:String = null):void
		{
			if(field){
				if(_sort.hasOwnProperty(field)) delete _sort[field];	
			}else{
				_sort = null;
			}
		}
		
		public function notifyCaller(event:ResultEvent):void
		{
			event.preventDefault(); //Prevent the service result handler from firing.
			_callback(event);
		}

		
		public function execute(offset:uint=0, limit:uint=0):AsyncToken
		{
			
			token = _op.send(_args, offset, limit);
			if(_callback !== null) token.addResponder(new Responder(notifyCaller,null));
			
			return token;
		}
	}
}