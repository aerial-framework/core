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

			trace(key.length + " : " + length);
			return key;
		}

		public static function encryptRSA(data:ByteArray, key:RSAKey):String
		{
			return concatAndEncrypt(data, key.getBlockSize(), key);
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