package org.aerialframework.encryption
{
    import org.aerialframework.libs.as3crypto.util.Hex;

    import mx.rpc.AsyncToken;
    import mx.rpc.remoting.Operation;
    import mx.rpc.remoting.RemoteObject;

    public class EncryptionService extends RemoteObject
	{
		public function EncryptionService(config:Object)
		{
			super("Aerial");

			this.source = "aerialframework.core.EncryptionService";
			this.endpoint = config.SERVER_URL;
		}

		public function startSession(data:String):AsyncToken
		{
			var encrypted:EncryptedVO = new EncryptedVO();
			encrypted.data = Hex.toArray(Hex.fromString(data));
			encrypted.resetKey = true;

			var operation:Operation = this.getOperation("startSession") as Operation;
			return operation.send(encrypted);
		}
	}
}
