package org.aerial.rpc.operation
{
	import flash.events.Event;
	
	import mx.rpc.AbstractOperation;
	import mx.rpc.AsyncToken;
	import mx.rpc.Responder;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.http.Operation;
	
	import org.aerial.rpc.AbstractService;
	
	public class Operation implements IOperation
	{
		
		private var _service:AbstractService;
		private var _method:String;
		private var _resultHandler:Function;
		private var _faultHandler:Function;
		private var token:AsyncToken;
		private var _op:AbstractOperation;
		private var _args:*; 
		private var _offset:uint;
		private var _limit:uint;
		private var _page:uint;
		private var _sort:Object;
		private var _relations:Object;
		 
		public function Operation(service:AbstractService, method:String, args:*=null)
		{
			_service = service;
			_method = method;
			_op = service.getOperation(_method);
			_args = args;
			_limit = 0;
			_offset = 0;
			_sort = new Object();
			_relations = new Object();
		}
		
		
		public function callback(resultHandler:Function, faultHandler:Function = null):Operation
		{
			_faultHandler = faultHandler;
			_resultHandler = resultHandler;
			return this;
		}
		
		public function relations(relations:Object):Operation
		{
			_relations = relations;
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
		
		private function notifyResultHandler(event:ResultEvent):void
		{
			event.preventDefault(); 
			_resultHandler(event);
		}
		
		private function notifyFaultHandler(event:FaultEvent):void
		{
			if(_faultHandler != null){
				event.preventDefault();
				_faultHandler(event);
			}
		}
		
		public function nextPage():AsyncToken
		{
			if(_limit > 0){
				_offset += _limit;
			}
			return  _execute(_limit, _offset);
		}
		
		public function previousPage():AsyncToken
		{
			if(_limit > 0){
				_offset -= _limit;
			}
			return  _execute(_limit, _offset);
		}
		
		
		public function execute(limit:uint=0, offset:uint=0):AsyncToken
		{
			_limit = limit;
			_offset = offset;
			
			return  _execute(_limit, _offset);
		}
		
		private function _execute(limit:uint, offset:uint):AsyncToken
		{
			token = _op.send(_args, _limit, _offset, _sort, _relations);
			
			if(_resultHandler !== null) token.addResponder(new Responder(notifyResultHandler, notifyFaultHandler));
		
			return token;
		}
		
		
	}
}