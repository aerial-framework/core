<?php
	class Encryption
	{
		public static function decrypt(ByteArray $data)
		{
			$key = self::strToHex($_SESSION["KEY"]);

			return rc4crypt::decrypt($key, self::hex2bin($data->data), 1);
		}
	
		public static function encrypt($amf)
		{
			$key = self::strToHex($_SESSION["KEY"]);

			return rc4crypt::encrypt($key, self::hex2bin($amf), 1);
		}

		public static function strToHex($string)
		{
			$hex='';
			for ($i=0; $i < strlen($string); $i++)
			{
				$hex .= dechex(ord($string[$i]));
			}

			return $hex;
		}
	
		public static function hex2bin($str)
		{
			$bin = "";
			$i = 0;
			do
			{
				$bin .= chr(hexdec($str{$i} . $str{($i + 1)}));
				$i += 2;
			}
			while ($i < strlen($str));
			
			return $bin;
		}

		public static function isKeySet()
		{
			if(!session_start())
				return false;

			if($_SESSION["KEY"])
				return true;
			else
				return false;
		}
	}
?>