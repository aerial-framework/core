package org.aerial.encryption
{
	import flash.utils.ByteArray;

	[RemoteClass(alias="org.aerial.encryption.EncryptedVO")]
	public class EncryptedVO
	{
		public var data:ByteArray;
		public var resetKey:Boolean;

		public function EncryptedVO()
		{
		}
	}
}
