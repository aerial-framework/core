package org.aerialframework.rpc
{
	import org.aerialframework.rpc.operation.Operation;

	public interface IService
	{
		function find(arg:*=null):Operation;
		function insert(vo:Object, returnCompleteObject:Boolean = false):Operation;
		function update(vo:Object, returnCompleteObject:Boolean = false):Operation;
        function count():Operation;
	}
}