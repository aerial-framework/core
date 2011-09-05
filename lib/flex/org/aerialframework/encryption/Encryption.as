package org.aerialframework.encryption
{
    import flash.events.EventDispatcher;
    import flash.utils.ByteArray;

    import mx.rpc.AsyncResponder;

    import mx.rpc.AsyncToken;
    import mx.rpc.Fault;
    import mx.rpc.Responder;
    import mx.rpc.events.FaultEvent;
    import mx.rpc.events.InvokeEvent;
    import mx.rpc.events.ResultEvent;

    import org.aerialframework.errors.AerialError;
    import org.aerialframework.events.EncryptionEvent;
    import org.aerialframework.libs.as3crypto.crypto.prng.ARC4;
    import org.aerialframework.libs.as3crypto.crypto.rsa.RSAKey;
    import org.aerialframework.libs.as3crypto.util.Hex;
    import org.aerialframework.libs.as3crypto.util.der.PEM;
    import org.aerialframework.rpc.AbstractService;
    import org.aerialframework.rpc.operation.PendingOperation;

    [Event(name="encryptedSessionStarted", type="org.aerialframework.events.EncryptionEvent")]
    [Event(name="encryptedSessionFailed", type="org.aerialframework.events.EncryptionEvent")]
    public class Encryption extends EventDispatcher
    {
        private static const pool:String = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!@#$%^&*()";

        [Embed(source="/org/aerialframework/encryption/exchange.pub",mimeType="application/octet-stream")]
        private static var publicKey:Class;

        private static var _instance:Encryption;

        private var _encryptionKey:ByteArray;
        private var _usingEncryption:Boolean;
        private var _encryptSourceAndOperation:Boolean;
        private var _keySize:uint;

        private var _encryptedSessionInitialized:Boolean;
        private var _encryptedSessionStarted:Boolean;

        private var _encryptedSessionFailed:Boolean;

        private var _pendingOperations:Array;

        public var config:Object;

        {
            _instance = new Encryption();
        }

        public static function get instance():Encryption
        {
            return _instance;
        }

        /**
         * Starts an asynchronous encrypted session
         *
         * @param encryptSourceAndOperation Encrypt source and operation strings
         */
        public function startEncryptedSession(config:Object, encryptSourceAndOperation:Boolean=false, keySize:uint=1024):void
        {
            this.config = config;

            if(!config.USE_ENCRYPTION)
            {
                throw new AerialError(AerialError.ENCRYPTION_NOT_ENABLED_ERROR);
                return;
            }

            this.encryptSourceAndOperation = encryptSourceAndOperation;
            this.keySize = keySize;

            // if the encrypted session has already been started, skip to the success event
            if(config.USE_ENCRYPTION && this.encryptedSessionStarted)
            {
                this.dispatchEvent(new EncryptionEvent(EncryptionEvent.ENCRYPTED_SESSION_STARTED));
                return;
            }

            _usingEncryption = true;

            var encryptionService:EncryptionService = new EncryptionService(this.config);

            _encryptionKey = Encryption.getRandomKey(keySize);

            var pubKey:RSAKey = PEM.readRSAPublicKey(new publicKey);

            var encrypted:String = Encryption.encryptRSA(_encryptionKey, pubKey);

            var requestToken:AsyncToken = encryptionService.startSession(encrypted);
            requestToken.addResponder(new Responder(sessionStartStatusHandler, sessionStartStatusFaultHandler));

            _encryptedSessionInitialized = true;
        }

        public function get encryptedSessionInitialized():Boolean
        {
            return _encryptedSessionInitialized;
        }

        public function get encryptionKey():ByteArray
        {
            return _encryptionKey;
        }

        public function get pendingOperations():Array
        {
            return _pendingOperations;
        }

        public function addPendingOperation(pending:PendingOperation):void
        {
            if(!_pendingOperations)
                _pendingOperations = [];

            _pendingOperations.push(pending);

            if(this.encryptedSessionFailed)
            {
                fakeOperationFailure();
                _pendingOperations = [];
            }
        }

        private function sessionStartStatusHandler(event:ResultEvent):void
        {
            var decryptedResult:ByteArray;
            var result:Boolean;

            if(event.result is EncryptedVO && event.result != null)
            {
                decryptedResult = Encryption.decryptRC4((event.result as EncryptedVO).data, this.encryptionKey);

                result = decryptedResult.readBoolean();
            }
            else
                result = event.result;

            _encryptedSessionStarted = result;

            var type:String = result ? EncryptionEvent.ENCRYPTED_SESSION_STARTED : EncryptionEvent.ENCRYPTED_SESSION_FAILED;
            this.dispatchEvent(new EncryptionEvent(type));

            if(result)          // if the encrypted session has started successfully
            {
                for each(var pending:PendingOperation in this.pendingOperations)
                {
                    var encryptedServiceName:String = Encryption.encryptRC4(Hex.toArray(Hex.fromString(pending.operation.service.source)), this.encryptionKey);
                    (pending.operation.service as AbstractService).source = encryptedServiceName;

                    var token:AsyncToken = pending.operation.send(pending.args);
                    token.addResponder(new AsyncResponder(pending.resultHandler, pending.faultHandler, pending.tokenData));
                    
                    (pending.operation.service as AbstractService).source = pending.serviceName;
                }
            }
            else
            {
                fakeOperationFailure();
            }

            _encryptedSessionFailed = false;
            _pendingOperations = [];
        }

        private function fakeOperationFailure():void
        {
            for each(var pending:PendingOperation in this.pendingOperations)
            {
                var fakeFault:Fault = new Fault("EncryptionError", EncryptionEvent.ENCRYPTED_SESSION_FAILED);
                pending.faultHandler.apply(this, [new FaultEvent(FaultEvent.FAULT, false, false, fakeFault)]);
            }
        }

        private function sessionStartStatusFaultHandler(event:FaultEvent):void
        {
            this.dispatchEvent(new EncryptionEvent(EncryptionEvent.ENCRYPTED_SESSION_FAILED));

            fakeOperationFailure();

            _pendingOperations = [];

            _encryptedSessionFailed = true;
            _encryptedSessionStarted = false;
        }

        public function get encryptedSessionFailed():Boolean
        {
            return _encryptedSessionFailed;
        }

        public function get encryptedSessionStarted():Boolean
        {
            return _encryptedSessionStarted;
        }

        public function get usingEncryption():Boolean
        {
            return _usingEncryption;
        }

        /**
         * Whether or not to encrypt source and operation strings
         */
        public function get encryptSourceAndOperation():Boolean
        {
            return _encryptSourceAndOperation;
        }

        public function set encryptSourceAndOperation(value:Boolean):void
        {
            _encryptSourceAndOperation = value;
        }

        /**
         * The size of the randomly generated key
         */
        public function get keySize():uint
        {
            return _keySize;
        }

        public function set keySize(value:uint):void
        {
            _keySize = value;
        }

        /**
         * Utilities
         */

        public static function getRandomKey(length:uint):ByteArray
        {
            var key:ByteArray = new ByteArray();

            for(var i:uint = 0; i < length; i++)
                key.writeUTFBytes(pool.charAt(Math.floor(Math.random() * pool.length)));

            return key;
        }

        public static function encryptRSA(data:ByteArray, key:RSAKey):String
        {
            return concatAndEncrypt(data, key.getBlockSize(), key);
        }

        public static function encryptRC4(data:ByteArray, key:ByteArray):String
        {
            if(!key || key.length == 0)
            {
                throw new AerialError(AerialError.INVALID_ENCRYPTION_KEY_ERROR);
                return null;
            }

            try
            {
                var rc4:ARC4 = new ARC4(key);
                rc4.encrypt(data);
            }
            catch(e:Error)
            {
                throw new AerialError(AerialError.ENCRYPTION_ERROR);
                return null;
            }

            return Hex.fromArray(data);
        }

        public static function encryptRC4String(str:String, key:ByteArray):String
        {
            var bytes:ByteArray = new ByteArray();
            bytes.writeUTFBytes(str);

            return encryptRC4(bytes, key);
        }

        public static function decryptRC4(data:ByteArray, key:ByteArray):ByteArray
        {
            if(!key || key.length == 0)
            {
                throw new AerialError(AerialError.INVALID_ENCRYPTION_KEY_ERROR);
                return null;
            }

            try
            {
                var rc4:ARC4 = new ARC4(key);
                rc4.init(key);
                rc4.decrypt(data);
            }
            catch(e:Error)
            {
                throw new AerialError(AerialError.DECRYPTION_ERROR);
                return null;
            }

            return data;
        }

        private static function concatAndEncrypt(data:ByteArray, blockSize:int, key:RSAKey):String
        {
            blockSize = blockSize / 2;          // halve the blocksize to avoid errors in PHP OpenSSL
            data.position = 0;

            var encrypted:String = "";
            if(data.length >= blockSize)
            {
                for(var i:uint = 0; i < Math.ceil(data.length / blockSize); i++)
                {
                    var subset:ByteArray = new ByteArray();
                    var len:uint = blockSize;

                    var offset:uint = i * blockSize;

                    if(offset + len > data.length)
                        len = data.length % blockSize;

                    for(var j:uint = offset; j < offset + len; j++)
                    {
                        data.position = j;
                        subset.writeByte(data.readByte());
                    }

                    var encBytes:ByteArray = new ByteArray();
                    subset.position = 0;

                    key.encrypt(subset, encBytes, subset.length);
                    encrypted += Hex.fromArray(encBytes) + "|";
                }
            }

            return encrypted;
        }
    }
}