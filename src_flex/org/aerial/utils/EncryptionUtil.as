package org.aerial.utils
{
	import com.hurlant.crypto.prng.ARC4;
	import com.hurlant.crypto.rsa.RSAKey;

	import com.hurlant.util.Hex;

	import flash.utils.ByteArray;

	import org.aerial.errors.AerialError;

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

		public static function encryptRSA(data:ByteArray, key:RSAKey):String
		{
			return concatAndEncrypt(data, key.getBlockSize(), key);
		}

		public static function encryptRC4(data:ByteArray, key:ByteArray):String
		{
			if(!key || key.length == 0)
			{
				throw new AerialError(AerialError.INVALID_ENCRYPTION_KEY_ERROR);
				return null;
			}

			try
			{
				var rc4:ARC4 = new ARC4(key);
				rc4.encrypt(data);
			}
			catch(e:Error)
			{
				throw new AerialError(AerialError.ENCRYPTION_ERROR);
				return null;
			}

			return Hex.fromArray(data);
		}

		public static function decryptRC4(data:ByteArray, key:ByteArray):ByteArray
		{
			if(!key || key.length == 0)
			{
				throw new AerialError(AerialError.INVALID_ENCRYPTION_KEY_ERROR);
				return null;
			}

			try
			{
				var rc4:ARC4 = new ARC4(key);
				rc4.init(key);
				rc4.decrypt(data);
			}
			catch(e:Error)
			{
				throw new AerialError(AerialError.DECRYPTION_ERROR);
				return null;
			}

			return data;
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