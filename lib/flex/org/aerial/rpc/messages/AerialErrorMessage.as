package org.aerialframework.rpc.messages
{
    import mx.messaging.messages.ErrorMessage;

    [RemoteClass(alias="flex.messaging.messages.AerialErrorMessage")]
    public class AerialErrorMessage extends ErrorMessage
    {
        public var debug:Object;

        public function AerialErrorMessage()
        {
            super();
        }
    }
}