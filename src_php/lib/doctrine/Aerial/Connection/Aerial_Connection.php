<?php
	class Aerial_Connection extends Doctrine_Connection_Common
	{
		public function connect()
		{
			if ($this->isConnected) {
				return false;
			}

			$event = new Doctrine_Event($this, Doctrine_Event::CONN_CONNECT);

			$this->getListener()->preConnect($event);

			$e     = explode(':', $this->options['dsn']);
			$this->driverName = $e[0]; 

			$found = false;

			if (extension_loaded('pdo')) {
				if (in_array($e[0], self::getAvailableDrivers())) {
					try {
						$this->dbh = new PDO($this->options['dsn'], $this->options['username'],
										 (!$this->options['password'] ? '':$this->options['password']), $this->options['other']);

						$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					} catch (PDOException $e) {
						throw new Aerial_Exception(Aerial_Exception::CONNECTION,
								array("username" => $this->options['username'],
										"password" => preg_replace("%.%", "*", $this->options['password'])),
								$e);
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

		/**
		 * rethrowException
		 *
		 * @throws Doctrine_Connection_Exception
		 */
		public function rethrowException(Exception $e, $invoker, $query = null)
		{
			$event = new Doctrine_Event($this, Doctrine_Event::CONN_ERROR);

			$this->getListener()->preError($event);

			$name = 'Doctrine_Connection_' . $this->driverName . '_Exception';

			$message = $e->getMessage();
			if ($query) {
				$message .= sprintf('. Failing Query: "%s"', $query);
			}

			$exc  = new $name($message, (int) $e->getCode());
			if ( ! isset($e->errorInfo) || ! is_array($e->errorInfo)) {
				$e->errorInfo = array(null, null, null, null);
			}
			$exc->processErrorInfo($e->errorInfo);
	
			 if ($this->getAttribute(Doctrine_Core::ATTR_THROW_EXCEPTIONS)) {
				throw $exc;
			}

			$this->getListener()->postError($event);
		}
	}
?>