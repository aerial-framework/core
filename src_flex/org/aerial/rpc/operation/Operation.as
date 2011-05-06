package org.aerial.rpc.operation
{
	import com.hurlant.util.Hex;

	import flash.events.Event;
	
	import mx.rpc.AbstractOperation;
	import mx.rpc.AsyncToken;
	import mx.rpc.Responder;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.http.Operation;

	import org.aerial.bootstrap.Aerial;
	import org.aerial.rpc.AbstractService;
	import org.aerial.utils.EncryptionUtil;

	public class Operation implements IOperation
	{
		
		private var _service:AbstractService;
		private var _method:String;
		private var _resultHandler:Function;
		private var _faultHandler:Function;
		private var token:AsyncToken;
		private var _op:AbstractOperation;
		private var _args:Array;
		private var _offset:uint;
		private var _limit:uint;
		private var _page:uint;
		private var _sort:Object;
		private var _relations:Array;
		 
		public function Operation(service:AbstractService, remoteMethod:String, ...args)
		{
			_service = service;
			_method = remoteMethod;
			_op = service.getOperation(_method);
			_args = args;
			_limit = 0;
			_offset = 0;
			_sort = new Object();
			_relations = new Array();
		}
		
		
		public function callback(resultHandler:Function, faultHandler:Function = null):Operation
		{
			_faultHandler = faultHandler;
			_resultHandler = resultHandler;
			return this;
		}
		
		public function addRelation(relation:String):Operation
		{
			_relations.push(relation);
			return this;
		}
		
		public function sortBy(field:String, order:String = "ASC"):Operation
		{
			//TODO: Validate that the fields exist in the VO.  Do we create a static graph object to conserve proc?
			_sort[field] = order;
			return this;
		}
		
		public function sortAsc(field:String, ... fields):Operation
		{
			_sort[field] = "ASC";
			
			for each(var f:String in fields)
				_sort[f] = "ASC";

			return this;
		}
		
		public function sortDesc(field:String, ... fields):Operation
		{
			_sort[field] = "DESC";
			
			for each(var f:String in fields)
				_sort[f] = "DESC";
			
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
            _args.push(_limit, _offset, _sort, _relations);

			if(Aerial.ENCRYPT_SERVICE_AND_FUNCTION)
			{
				_op = _service.getOperation(EncryptionUtil.encryptRC4(Hex.toArray(Hex.fromString(_method)),
						Aerial.instance.encryptionKey));
				_service.source = EncryptionUtil.encryptRC4(Hex.toArray(Hex.fromString(_service.source)),
						Aerial.instance.encryptionKey);
			}

			token = _op.send(_args);
			
			if(_resultHandler !== null) token.addResponder(new Responder(notifyResultHandler, notifyFaultHandler));
		
			return token;
		}
	}
}