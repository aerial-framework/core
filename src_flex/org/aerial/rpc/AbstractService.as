package org.aerial.rpc
{
	import com.hurlant.crypto.prng.ARC4;

	import com.hurlant.util.Hex;

	import flash.utils.ByteArray;
	import flash.utils.getQualifiedClassName;
	
	import mx.rpc.AbstractOperation;
	import mx.rpc.AsyncToken;
	import mx.rpc.remoting.RemoteObject;

	import mx.utils.ObjectUtil;

	import org.aerial.bootstrap.Aerial;
	import org.aerial.encryption.Encrypted;
	import org.aerial.errors.AerialError;
	import org.aerial.rpc.IService;
	import org.aerial.rpc.operation.Operation;
	import org.aerial.system.DoctrineQuery;
	import org.aerial.utils.EncryptionUtil;

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
			this.convertResultHandler = processResults;
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
			var argument:Array = [];

			if(Aerial.USE_ENCRYPTION)
			{
				if(Aerial.instance.usingEncryption && !Aerial.instance.encryptedSessionStarted)
				{
					throw new AerialError(AerialError.ENCRYPTED_SESSION_NOT_STARTED_ERROR);
					return null;
				}
				else if(Aerial.instance.usingEncryption && Aerial.instance.encryptedSessionStarted &&
																			!Aerial.instance.encryptionKey)
				{
					throw new AerialError(AerialError.NO_ENCRYPTION_KEY_ERROR);
					return null;
				}
			}

			if(Aerial.USE_ENCRYPTION && Aerial.instance.encryptedSessionStarted)
			{
				var encrypted:Encrypted = new Encrypted();
				encrypted.data = new ByteArray();
				encrypted.data.writeObject(args[0]);

				encrypted.data.position = 0;

				var encData:String = EncryptionUtil.encryptRC4(encrypted.data, Aerial.instance.encryptionKey);
				encrypted.data = new ByteArray();
				encrypted.data.writeUTFBytes(encData);

				encrypted.data.position = 0;

				encrypted.data.position = 0;
				argument = [encrypted];
			}
			else
				argument = args[0];

			return argument;
		}

        private function processResults(result:*, operation:AbstractOperation):*
		{
			if(!(result is Encrypted) || !Aerial.USE_ENCRYPTION)
				return result;

			if(!Aerial.instance.encryptedSessionStarted)
			{
				throw new AerialError(AerialError.ENCRYPTED_SESSION_NOT_STARTED_ERROR);
				return null;
			}

			if(!Aerial.instance.encryptionKey)
			{
				throw new AerialError(AerialError.NO_ENCRYPTION_KEY_ERROR);
				return null;
			}

			var decryptedResult:ByteArray = EncryptionUtil.decryptRC4((result as Encrypted).data, Aerial.instance.encryptionKey);

			var result:*;
			try
			{
				result = decryptedResult.readObject();
			}
			catch(e:Error)
			{
				throw new AerialError(AerialError.DECRYPTION_ERROR);
				return null;
			}

			return result;
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