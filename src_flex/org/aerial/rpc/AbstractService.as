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
		
		public function findAll():Operation
		{
			var op:Operation = new Operation(this, "findAll"); 
			return op;
		}
		
		public function insert(vo:Object):Operation
		{
			if(!(vo is _voClass))
				throw new ArgumentError(this.source + ".insert(vo:Object) argument must be of type " + getQualifiedClassName(_voClass) + " (You used " + getQualifiedClassName(vo) + ")");
		
			var op:Operation = new Operation(this, "insert", vo);
			return op;
		}
		
		/*public function findFirst():void
		{
		}
		
		public function findLast():void
		{
		}
		
		public function findById(id:int, page:int=0):void
		{
		}
		
		public function findByField(field:Object, page:int):void
		{
		}
		
		public function findByParent(parent:Object, page:int=0):void
		{
		}
		
		public function findByExample(example:Object, page:int=0):void
		{
		}
		
		public function insert(topic:TopicVO):void
		{
		}
		
		public function update(topic:TopicVO):void
		{
		}
		
		public function drop(topic:TopicVO):void
		{
		}
		
		public function save(topic:TopicVO):void
		{
		}
		
		public function count(example:Object=null):void
		{
		}*/
	}
}