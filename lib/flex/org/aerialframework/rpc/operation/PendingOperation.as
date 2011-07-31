/**
 * User: Danny Kopping
 * Date: 2011/08/01
 */
package org.aerialframework.rpc.operation
{
    import mx.rpc.AbstractOperation;

    /**
     * A convenience class for transporting pending operations used in the Encryption process
     */
    public class PendingOperation
    {
        public var operation:AbstractOperation;
        public var args:Array;
        public var serviceName:String;
        public var tokenData:Object;
        public var resultHandler:Function;
        public var faultHandler:Function;

        public function PendingOperation(operation:AbstractOperation, args:Array, serviceName:String, 
                                         tokenData:Object, resultHandler:Function, faultHandler:Function)
        {
            this.operation = operation;
            this.args = args;
            this.serviceName = serviceName;
            this.tokenData = tokenData;
            this.resultHandler = resultHandler;
            this.faultHandler = faultHandler;
        }
    }
}
