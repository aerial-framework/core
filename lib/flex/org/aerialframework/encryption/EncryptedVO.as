package org.aerialframework.encryption
{
	import flash.utils.ByteArray;

	[RemoteClass(alias="org.aerialframework.encryption.EncryptedVO")]
	public class EncryptedVO
	{
		public var data:ByteArray;
		public var resetKey:Boolean;

		public function EncryptedVO()
		{
		}
	}
}