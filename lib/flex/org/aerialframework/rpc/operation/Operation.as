package org.aerialframework.rpc.operation
{
    import mx.rpc.AbstractOperation;
    import mx.rpc.AsyncResponder;
    import mx.rpc.AsyncToken;
    import mx.rpc.events.FaultEvent;
    import mx.rpc.events.ResultEvent;
    import mx.utils.OrderedObject;
    
    import org.aerialframework.encryption.Encryption;
    import org.aerialframework.errors.AerialError;
    import org.aerialframework.libs.as3crypto.util.Hex;
    import org.aerialframework.rpc.AbstractService;

    public class Operation implements IOperation
    {

        private var _service:AbstractService;
        private var _method:String;
        private var _resultHandler:Function;
        private var _faultHandler:Function;
        private var _tokenData:Object;
        private var _op:AbstractOperation;
        private var _args:Array;
        private var _offset:uint;
        private var _limit:uint;
        private var _page:uint;
        private var _sort:OrderedObject;
        private var _relations:Array;
		private var _returnCompleteObject:Boolean;

        public function Operation(service:AbstractService, remoteMethod:String, returnCompleteObject:Boolean, ...args)
        {
            _service = service;
            _method = remoteMethod;
            _op = service.getOperation(_method);
            _args = args;
            _limit = 0;
            _offset = 0;
            _sort = new OrderedObject();
            _relations = new Array();
			_returnCompleteObject = returnCompleteObject; 
        }

        public function callback(resultHandler:Function, faultHandler:Function = null, tokenData:Object = null):Operation
        {
            _faultHandler = faultHandler;
            _resultHandler = resultHandler;
            _tokenData = tokenData;
            return this;
        }

        public function addRelation(relation:String):Operation
        {
            _relations.push(relation);
            return this;
        }

        public function sortBy(field:String, order:String = "ASC"):Operation
        {
            //TODO: Validate that the fields exist in the VO.  Do we create a static graph object to conserve proc?
            _sort[field] = order;
            return this;
        }

        public function sortAsc(field:String, ... fields):Operation
        {
            _sort[field] = "ASC";

            for each(var f:String in fields)
                _sort[f] = "ASC";

            return this;
        }

        public function sortDesc(field:String, ... fields):Operation
        {
            _sort[field] = "DESC";

            for each(var f:String in fields)
                _sort[f] = "DESC";

            return this;
        }

        public function sortClear(field:String = null):void
        {
            if(field)
            {
                if(_sort.hasOwnProperty(field)) delete _sort[field];
            }
            else
            {
                _sort = null;
            }
        }

        private function notifyResultHandler(event:ResultEvent, token:Object = null):void
        {
            event.preventDefault();
            if(token)
            {
                _resultHandler(event, token);
            }
            else
            {
                _resultHandler(event);
            }
        }

        private function notifyFaultHandler(event:FaultEvent, token:Object = null):void
        {
            if(_faultHandler != null)
            {
                event.preventDefault();
                if(token)
                {
                    _faultHandler(event, token);
                }
                else
                {
                    _faultHandler(event);
                }
            }
        }

        public function nextPage():AsyncToken
        {
            if(_limit > 0)
            {
                _offset += _limit;
            }
            return  _execute();
        }

        public function previousPage():AsyncToken
        {
            if(_limit > 0)
            {
                _offset -= _limit;
            }
            return  _execute();
        }
		
		public function count():AsyncToken
		{
			_limit = 0;
			_offset = 0;
			
			return  _execute(true);
		}

        public function execute(limit:uint = 0, offset:uint = 0):AsyncToken
        {
            _limit = limit;
            _offset = offset;

            return  _execute();
        }

        private function _execute(count:Boolean = false):AsyncToken
        {
            _args.push(_returnCompleteObject, _limit, _offset, _sort, _relations, count);
			var args:Array = _args;
            var encryption:Encryption = Encryption.instance;

            var initialized:Boolean = Encryption.instance.encryptedSessionInitialized;
            var started:Boolean = Encryption.instance.encryptedSessionStarted;

			var asyncToken:AsyncToken;
			
            if(_service.aerialConfig.USE_ENCRYPTION)
            {
                if(_method == "startSession")
                    started = true;             // fake encryption started if the startSession operation is being executed

                if(!initialized)            // if the user has not initialized the session, nothing can be done yet
                {
                    throw new AerialError(AerialError.ENCRYPTED_SESSION_NOT_STARTED_ERROR);
                    return null;
                }

                var plainServiceName:String = _service.source;

                var encryptedMethodName:String = Encryption.encryptRC4(Hex.toArray(Hex.fromString(_method)), encryption.encryptionKey);
                var encryptedServiceName:String = Encryption.encryptRC4(Hex.toArray(Hex.fromString(_service.source)), encryption.encryptionKey);

                if(encryption.encryptSourceAndOperation)
                {
                    _op = _service.getOperation(encryptedMethodName);

                    if(started)         // if not started, the service source will be encrypted when called
                        _service.source = encryptedServiceName;
                }

                if(!started)
                {
                    Encryption.instance.addPendingOperation(new PendingOperation(_op, args, plainServiceName,
                            _tokenData, notifyResultHandler, notifyFaultHandler));
                }
                else
                    asyncToken = _op.send(args);

                // restore service source
                if(encryption.encryptSourceAndOperation && started)
                    _service.source = plainServiceName;
            }
            else
                asyncToken = _op.send(args);

            if(_resultHandler !== null && ((_service.aerialConfig.USE_ENCRYPTION && started) || !_service.aerialConfig.USE_ENCRYPTION))
                asyncToken.addResponder(new AsyncResponder(notifyResultHandler, notifyFaultHandler, _tokenData));

            return asyncToken;
        }
    }
}