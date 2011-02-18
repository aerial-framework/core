package org.aerial.rpc
{
	import flash.utils.getQualifiedClassName;

    import mx.rpc.AbstractOperation;
    import mx.rpc.remoting.RemoteObject;
	
	import org.aerial.rpc.IService;
	import org.aerial.rpc.operation.Operation;

	public class AbstractService extends RemoteObject implements IService
	{
		import org.aerial.rpc.messages.AerialErrorMessage; AerialErrorMessage;

		private var _voClass:Class;
		
		public function AbstractService(source:String, endpoint:String, voClass:Class)
		{
			super("Aerial");
			this.source = source;
			this.endpoint = endpoint;
			_voClass = voClass;

            this.convertParametersHandler = preprocessArguments;
		}
		
		
		//Modify Methods
		
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
			var op:Operation = new Operation(this, "findById", id);
			return op;
		}

		public function findRelated(field:String, id:int):Operation
		{
			var op:Operation = new Operation(this, "findRelated", field, id);

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