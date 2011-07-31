package org.aerialframework.rpc
{
	import flash.utils.getQualifiedClassName;
	
	import mx.rpc.AbstractOperation;
	import mx.rpc.AsyncToken;
	import mx.rpc.remoting.RemoteObject;
	
	import org.aerialframework.rpc.IService;
	import org.aerialframework.rpc.operation.Operation;
	import org.aerialframework.system.DoctrineQuery;
	
	public class AbstractService extends RemoteObject implements IService
	{
		import org.aerialframework.rpc.messages.AerialErrorMessage; AerialErrorMessage;
		
		private var _voClass:Class;
		
		public function AbstractService(source:String, endpoint:String, voClass:Class)
		{
			super("Aerial");
			this.source = source;
			this.endpoint = endpoint;
			_voClass = voClass;
			
			this.convertParametersHandler = preprocessArguments;
		}
		
		public function get voClass():Class
		{
			return _voClass;
		}

		/*Modify Methods*/
		public function insert(vo:Object, returnCompleteObject:Boolean = false):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "insert", vo, returnCompleteObject);
			
			return op;
		}
		
		public function update(vo:Object, returnCompleteObject:Boolean = false):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "update", vo, returnCompleteObject);
			
			return op;
		}
		
		public function save(vo:Object):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "save", vo);
			
			return op;
		}
		
		public function drop(vo:Object):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "drop", vo);
			
			return op;
		}
		
		/**
		 * Pre-processes an array of given arguments so that it will not send an array of arguments
		 * but rather a collection of arguments
		 *
		 * @param args The arguments to be sent to PHP
		 * @return
		 */
		public function preprocessArguments(args:Array):Array
		{
			return args[0];
		}
		
		// Find Methods
		
		public function find(criteria:* = null):Operation
		{
			var op:Operation = new Operation(this, "find", criteria);
			return op;
		}
		
		public function count():Operation
		{
			var op:Operation = new Operation(this, "count");
			
			return op;
		}
		
		public function executeDQL(query:DoctrineQuery):AsyncToken
		{
			var op:Operation = new Operation(this, "executeDQL", query.properties);
			return op.execute();
		}
		
		// Helpers
		
		private function validateVO(vo:Object):void{
			if(!(vo is _voClass))
				throw new ArgumentError(this.source + ".insert(vo:Object) argument must be of type " + getQualifiedClassName(_voClass) + " (You used " + getQualifiedClassName(vo) + ")");
		}
	}
}