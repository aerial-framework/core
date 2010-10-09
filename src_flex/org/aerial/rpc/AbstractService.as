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
			var op:Operation = new Operation(this, "insert", IAbstractVO(vo).getObject() );
			
			return op;
		}
		
		public function update(vo:Object):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "update", IAbstractVO(vo).getObject() );
			
			return op;
		}
		
		public function save(vo:Object):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "save", IAbstractVO(vo).getObject() );
			
			return op;
		}
		
		public function drop(vo:Object):Operation
		{
			validateVO(vo);
			var op:Operation = new Operation(this, "drop", IAbstractVO(vo).getObject() );
			
			return op;
		}
		
		//Find Methods
		
		public function findAll(arg:* = null):Operation
		{
			var op:Operation = new Operation(this, "findAll", arg); 
			return op;
		}
		
		public function findFirst(arg:* = null):Operation
		{
			var op:Operation = new Operation(this, "findFirst", arg); 
			return op;
		}
		
		public function findLast(arg:* = null):Operation
		{
			var op:Operation = new Operation(this, "findLast", arg); 
			return op;
		}
		
		public function findById(id:int):Operation
		{
			var op:Operation = new Operation(this, "findLast", id); 
			return op;
		}
		
		
		//Helpers
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