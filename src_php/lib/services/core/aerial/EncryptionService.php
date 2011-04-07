<?php
	require_once(conf("paths/encryption")."Encrypted.php");

	class EncryptionService
	{
		private $keyResource;

		public function startSession($encrypted)
		{
			try
			{
				$e = new Encrypted();
				$e->data = $encrypted["data"];

				$sessionKey = $this->decrypt($e->data->data);
				openssl_free_key($this->keyResource);

				if(!session_start())
					session_start();

				$_SESSION["KEY"] = $sessionKey;
				return true;
			}
			catch(Exception $e)
			{
				return false;
			}
		}

		private function decrypt($bytes)
		{
			if(!$this->keyResource)
			{
				$fp = fopen(conf("paths/encryption")."exchange.key", "r");
				$priv_key = fread($fp, 8192);
				fclose($fp);
				// $passphrase is required if your key is encoded (suggested)
				$this->keyResource = openssl_get_privatekey($priv_key);
				$details = openssl_pkey_get_details($this->keyResource);
			}

			if($this->keyResource == null)
				trigger_error("Could not read private key");

			$keyBits = $details["bits"];
			$blockSize = $keyBits / 8;

			$raw = $bytes;

			$raw = substr($raw, 0, strlen($raw) - 1);
			$pieces = explode("|", $raw);

			$decryptedKey = "";

			foreach($pieces as $piece)
			{
				$piece = $this->hex2bin($piece);

				openssl_private_decrypt($piece, $decrypted, $this->keyResource);
				$decryptedKey .= $decrypted;
			}

			return $decryptedKey;
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