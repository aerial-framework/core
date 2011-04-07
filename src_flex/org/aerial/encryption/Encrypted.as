package org.aerial.encryption
{
	import flash.utils.ByteArray;

	[RemoteClass(alias="org.aerial.encryption.Encrypted")]
	public class Encrypted
	{
		public var data:ByteArray;
		public var resetKey:Boolean;

		public function Encrypted()
		{
		}
	}
}
