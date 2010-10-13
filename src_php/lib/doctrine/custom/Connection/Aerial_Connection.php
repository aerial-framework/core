<?php
	class Aerial_Connection extends Doctrine_Connection_Common
	{
		public function connect()
		{
			// correct the DSN for true connection (remove "aerial-" from beginning of DB_ENGINE)
			$dsn = explode(":", $this->options["dsn"]);
			$dsn[0] = explode("-", $dsn[0]);
			$dsn[0] = $dsn[0][1];

			$this->options["dsn"] = implode(":", $dsn);

			if ($this->isConnected) {
				return false;
			}

			$event = new Doctrine_Event($this, Doctrine_Event::CONN_CONNECT);

			$this->getListener()->preConnect($event);

			$e     = explode(':', $this->options['dsn']);
			$found = false;

			if (extension_loaded('pdo')) {
				if (in_array($e[0], self::getAvailableDrivers())) {
					try {
						$this->dbh = new PDO($this->options['dsn'], $this->options['username'],
										 (!$this->options['password'] ? '':$this->options['password']), $this->options['other']);

						$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					} catch (PDOException $e) {
						throw new AerialException(AerialException::CONNECTION, $e);
					}
					$found = true;
				}
			}

			if ( ! $found) {
				$class = 'Doctrine_Adapter_' . ucwords($e[0]);

				if (class_exists($class)) {
					$this->dbh = new $class($this->options['dsn'], $this->options['username'], $this->options['password'], $this->options);
				} else {
					throw new Doctrine_Connection_Exception("Couldn't locate driver named " . $e[0]);
				}
			}

			// attach the pending attributes to adapter
			foreach($this->pendingAttributes as $attr => $value) {
				// some drivers don't support setting this so we just skip it
				if ($attr == Doctrine_Core::ATTR_DRIVER_NAME) {
					continue;
				}
				$this->dbh->setAttribute($attr, $value);
			}

			$this->isConnected = true;

			$this->getListener()->postConnect($event);
			return true;
		}
	}
?>