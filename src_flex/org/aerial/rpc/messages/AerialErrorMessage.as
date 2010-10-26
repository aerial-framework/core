package org.aerial.rpc.messages
{
    import mx.messaging.messages.ErrorMessage;

    [RemoteClass(alias="flex.messaging.messages.AerialErrorMessage")]
    public class AerialErrorMessage extends ErrorMessage
    {
        public var aerialLog:String;

        public function AerialErrorMessage()
        {
            super();
        }
    }
}