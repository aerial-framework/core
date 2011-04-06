package org.aerial.utils
{
	import com.hurlant.crypto.rsa.RSAKey;

	import com.hurlant.util.Hex;

	import flash.utils.ByteArray;

	public class EncryptionUtil
	{
		private static const pool:String = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!@#$%^&*()";

		public static function getRandomKey(length:uint):ByteArray
		{
			var key:ByteArray = new ByteArray();

			for(var i:uint = 0; i < length; i++)
				key.writeUTFBytes(pool.charAt(Math.floor(Math.random() * pool.length)));

			return key;
		}

		public static function encryptRSA(data:ByteArray, key:RSAKey):ByteArray
		{
			data.position = 0;

			var encrypted:ByteArray = new ByteArray();
			key.encrypt(data, encrypted, data.length);

			return encrypted;
		}
	}
}