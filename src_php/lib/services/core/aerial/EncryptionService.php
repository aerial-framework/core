<?php
	require_once(conf("paths/encryption")."Encrypted.php");

	class EncryptionService
	{
		public function startSession($encrypted)
		{
//			$e = new Encrypted();
//			$e->data = $encrypted["data"];

			return $this->decrypt($encrypted);
		}

		private function decrypt($bytes)
		{
			$fp = fopen(conf("paths/encryption")."exchange.key", "r");
			$priv_key = fread($fp, 8192);
			fclose($fp);
			// $passphrase is required if your key is encoded (suggested)
			$res = openssl_get_privatekey($priv_key);
			$details = openssl_pkey_get_details($res);

			$keyBits = $details["bits"];
			$blockSize = $keyBits / 8;

			$raw = $bytes;
			$pieces = explode("|", $raw);

			$total = "";

			foreach($pieces as $piece)
			{
				$piece = $this->hex2bin($piece);

				openssl_private_decrypt($piece, $decrypted, $res);
				$total .= $decrypted;
			}

			/*$pieces = array();

			for($i = 0; $i < strlen($raw) / $blockSize; $i++)
			{
				$pieces[] = base64_encode(substr($raw, $i * $blockSize, ($i + 1) * $blockSize));
			}

			if(count($pieces) * $blockSize != strlen($raw) / $blockSize)
			{
				$pieces[] = substr($raw, strlen($raw) / $blockSize);
			}

			$decrypted = "";
			foreach($pieces as $piece)
			{
				$p = "";
				openssl_private_decrypt(base64_decode($piece), $p, $res);

				$decrypted .= $p;
			}*/

			//openssl_private_decrypt($raw, $decrypted, $res);

			/*while ($msg = openssl_error_string())
                echo $msg . "<br />\n";*/

			return $total;
		}

		private function hex_to_str($hex)
		{
			for ($i = 0; $i < strlen($hex) - 1; $i += 2)
			{
				$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
			}
			return $string;
		}

		private function hex2bin($str)
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
	}
?>