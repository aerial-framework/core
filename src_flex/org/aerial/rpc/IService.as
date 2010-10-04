package org.aerial.rpc{
	import org.aerial.rpc.operation.Operation;

	public interface IService{
		
		function findAll(arg:*):Operation;
		
		function insert(vo:Object):Operation;
		

		/*function findFirst():void;

		function findLast():void;

		function findById(id:int):void;

		function findByField(field:Object):void;

		function findByParent(parent:Object):void;

		function findByExample(example:Object):void;

		

		function update(topic:TopicVO):void;

		function drop(topic:TopicVO):void;

		function save(topic:TopicVO):void;

		function count(example:Object=null):void;*/
	}
}