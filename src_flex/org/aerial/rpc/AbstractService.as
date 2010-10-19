package org.aerial.rpc
{
	import flash.utils.getQualifiedClassName;
	
	import mx.rpc.remoting.RemoteObject;
	
	import org.aerial.rpc.IService;
	import org.aerial.rpc.operation.Operation;

	public class AbstractService extends RemoteObject implements IService
	{
		
		private var _voClass:Class;
		
		public function AbstractService(source:String, endpoint:String, voClass:Class)
		{
			super("Aerial");
			this.source = source;
			this.endpoint = endpoint;
			_voClass = voClass;
		}
		
		
		//Modifiy Methods
		
		public function insert(vo:Object):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "insert", vo);
			
			return op;
		}
		
		public function update(vo:Object):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "update", vo);
			
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
		
		
		// Find Methods
		
		public function findAll(criteria:* = null):Operation
		{
			var op:Operation = new Operation(this, "findAll", criteria); 
			return op;
		}
		
		public function findFirst(criteria:* = null):Operation
		{
			var op:Operation = new Operation(this, "findFirst", criteria); 
			return op;
		}
		
		public function findLast(criteria:* = null):Operation
		{
			var op:Operation = new Operation(this, "findLast", criteria); 
			return op;
		}
		
		public function findById(id:int):Operation
		{
			var op:Operation = new Operation(this, "findLast", id); 
			return op;
		}
		
		
		// Helpers
		
		private function validateVO(vo:Object):void{
			if(!(vo is _voClass))
				throw new ArgumentError(this.source + ".insert(vo:Object) argument must be of type " + getQualifiedClassName(_voClass) + " (You used " + getQualifiedClassName(vo) + ")");
		}
		
		/*
		public function findByField(field:Object, page:int):void
		{
		}
		
		public function findByParent(parent:Object, page:int=0):void
		{
		}
		
		public function findByExample(example:Object, page:int=0):void
		{
		}
		
		public function count(example:Object=null):void
		{
		}*/
	}
}