package org.aerialframework.rpc{
	import org.aerialframework.rpc.operation.Operation;

	public interface IService{
		
		function find(arg:*=null):Operation;
		
		function insert(vo:Object, returnCompleteObject:Boolean = false):Operation;
		function update(vo:Object, returnCompleteObject:Boolean = false):Operation;
		
        function count():Operation;

		/*function findFirst():void;

		function findLast():void;

		function findById(id:int):void;

		function findByField(field:Object):void;

		function findByParent(parent:Object):void;

		function findByExample(example:Object):void;

		function drop(topic:TopicVO):void;

		function count(example:Object=null):void;*/
	}
}