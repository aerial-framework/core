package org.aerialframework.rpc
{
    
    import flash.utils.ByteArray;
    import flash.utils.getQualifiedClassName;
    
    import mx.rpc.AbstractOperation;
    import mx.rpc.remoting.RemoteObject;
    
    import org.aerialframework.encryption.EncryptedVO;
    import org.aerialframework.encryption.Encryption;
    import org.aerialframework.errors.AerialError;
    import org.aerialframework.rpc.messages.AerialErrorMessage;
    import org.aerialframework.rpc.operation.Operation;
    import org.aerialframework.system.DoctrineQuery;

    public class AbstractService extends RemoteObject implements IService
    {
        AerialErrorMessage;

        private var _voClass:Class;
		public var aerialConfig:Object;

        public function AbstractService(source:String, config:Object, voClass:Class)
        {
            super("Aerial");
			aerialConfig = config;
            this.source = source;
            this.endpoint = config.SERVER_URL;
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
            var op:Operation = new Operation(this, "insert", returnCompleteObject, vo);

            return op;
        }

        public function update(vo:Object, returnCompleteObject:Boolean = false):Operation
        {
            validateVO(vo);
            var op:Operation = new Operation(this, "update", returnCompleteObject, vo);

            return op;
        }

        public function save(vo:Object, returnCompleteObject:Boolean = false):Operation
        {
            validateVO(vo);
            var op:Operation = new Operation(this, "save", returnCompleteObject, vo);

            return op;
        }

        public function drop(vo:Object):Operation
        {
            validateVO(vo);
            var op:Operation = new Operation(this, "drop", false, vo);

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

            if(aerialConfig.USE_ENCRYPTION)
            {
                if(Encryption.instance.usingEncryption && !Encryption.instance.encryptedSessionStarted)
                {
                    throw new AerialError(AerialError.ENCRYPTED_SESSION_NOT_STARTED_ERROR);
                    return null;
                }
                else if(Encryption.instance.usingEncryption && Encryption.instance.encryptedSessionStarted &&
                        !Encryption.instance.encryptionKey)
                {
                    throw new AerialError(AerialError.NO_ENCRYPTION_KEY_ERROR);
                    return null;
                }
            }

            if(aerialConfig.USE_ENCRYPTION && Encryption.instance.encryptedSessionStarted)
            {
                var encrypted:EncryptedVO = new EncryptedVO();
                encrypted.data = new ByteArray();
                encrypted.data.writeObject(args[0]);

                encrypted.data.position = 0;

                var encData:String = Encryption.encryptRC4(encrypted.data, Encryption.instance.encryptionKey);
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
            if(!(result is EncryptedVO) || !aerialConfig.USE_ENCRYPTION)
                return result;

            if(!Encryption.instance.encryptedSessionStarted)
            {
                throw new AerialError(AerialError.ENCRYPTED_SESSION_NOT_STARTED_ERROR);
                return null;
            }

            if(!Encryption.instance.encryptionKey)
            {
                throw new AerialError(AerialError.NO_ENCRYPTION_KEY_ERROR);
                return null;
            }

            var decryptedResult:ByteArray = Encryption.decryptRC4((result as EncryptedVO).data, Encryption.instance.encryptionKey);

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
            var op:Operation = new Operation(this, "find", true, criteria);
            return op;
        }

        public function count():Operation
        {
            var op:Operation = new Operation(this, "count", false);

            return op;
        }

        public function query(query:DoctrineQuery):Operation
        {
            var op:Operation = new Operation(this, "query", true, query.properties);
            return op;
        }

        // Helpers

        private function validateVO(vo:Object):void
        {
            if(!(vo is _voClass))
                throw new ArgumentError(this.source + ".insert(vo:Object) argument must be of type " + getQualifiedClassName(_voClass) + " (You used " + getQualifiedClassName(vo) + ")");
        }
    }
}