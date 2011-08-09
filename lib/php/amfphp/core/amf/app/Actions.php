<?php
/**
 * Actions modify the AMF message PER BODY
 * This allows batching of calls
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 amfphp.org
 * @package flashservices
 * @subpackage filters
 * @version $Id: Filters.php,v 1.6 2005/04/02   18:37:51 pmineault Exp $
 */

/**
 * Catches any special request types and classifies as required
 */
function adapterAction (&$amfbody) {
	$baseClassPath = $GLOBALS['amfphp']['classPath'];

	$uriclasspath = "";
	$classname = "";
	$classpath = "";
	$methodname = "";
	$isWebServiceURI = false;

	$servicesPath = ConfigXml::getInstance()->servicesPath;
	
	$target = $amfbody->targetURI;

	if (strpos($target, "http://") === false && strpos($target, "https://") === false) { // check for a http link which means web service
		$lpos = strrpos($target, ".");
		if ($lpos === false) {
			//Check to see if this is in fact a RemotingMessage
			$body = $amfbody->getValue();
			$handled = false;

			$messageType = $body[0]->_explicitType;
			if($messageType == 'flex.messaging.messages.RemotingMessage')
			{
				$handled = true;

				//Fix for AMF0 mixed array bug in Flex 2
				if(isset($body[0]->body['length']))
				{
					unset($body[0]->body['length']);
				}

				$amfbody->setValue($body[0]->body);
				$amfbody->setSpecialHandling("RemotingMessage");
				$amfbody->setMetadata("clientId", $body[0]->clientId);
				$amfbody->setMetadata("messageId", $body[0]->messageId);

				$GLOBALS['amfphp']['lastMessageId'] = $body[0]->messageId;

				$methodname = $body[0]->operation;
				$classAndPackage = $body[0]->source;

				$classname = getClassname($classAndPackage);
				$classpath = getServiceClass($classAndPackage, $servicesPath);

				// ignore initial session start request
				$canUseEncryption = Encryption::canUseEncryption();
				if($canUseEncryption && $classAndPackage != "core.aerialframework.EncryptionService" && $methodname != "startSession")
				{
					// no classpath could be obtained from the request yet, which means it might be encrypted
					if(!$classpath)
					{
						// decrypt method and class names
						$methodname = Encryption::decrypt(new ByteArray($methodname));
						$classAndPackage = Encryption::decrypt(new ByteArray($classAndPackage));

						// ...then re-evaluate to see if the classpath is now valid
						$classname = getClassname($classAndPackage);
						$classpath = getServiceClass($classAndPackage, $servicesPath);
					}
				}
				else
				{
					$classname = $classAndPackage;
				}

				$uriclasspath = str_replace('.', '/', $classAndPackage) . '.php';

				$lpos = strrpos($classname, ".");
				if($lpos > 0)
					$lpos += 1;

				$classname = substr($classname, $lpos);

				if(!$classpath)
				{
					// try find the correct path to the PHP service for the incoming request
					if(LIB_PATH . DIRECTORY_SEPARATOR . "php/".$uriclasspath)
						$classpath = LIB_PATH . DIRECTORY_SEPARATOR . "php/" . $uriclasspath;

					if(!$classpath || !realpath($classpath))
						$classpath = realpath(LIB_PATH . DIRECTORY_SEPARATOR . "amfphp" . DIRECTORY_SEPARATOR . "services/$classname.php");

					if(!$classpath || !realpath($classpath))
						$classpath = realpath(LIB_PATH . DIRECTORY_SEPARATOR . "aerialframework" . "core/$classname.php");

					//$servicesPath = realpath($servicesPath);
					if(realpath($servicesPath . DIRECTORY_SEPARATOR . $uriclasspath))
						$classpath = realpath($servicesPath. DIRECTORY_SEPARATOR . $uriclasspath);
				}

				//$classpath = $baseClassPath . $uriclasspath;
				//die($classpath);
			}
			elseif($messageType == "flex.messaging.messages.CommandMessage")
			{
				if($body[0]->operation == 5)
				{
					$handled = true;
					$amfbody->setSpecialHandling("Ping");
					$amfbody->setMetadata("clientId", $body[0]->clientId);
					$amfbody->setMetadata("messageId", $body[0]->messageId);
					$amfbody->noExec = true;

					$GLOBALS['amfphp']["ping"] = true;
				}
				else
				{
					$GLOBALS['amfphp']["ping"] = false;
				}

				if(!empty($body[0]->body))
				{
					$credentials = explode(":", base64_decode($body[0]->body));
					Bootstrapper::getInstance()->setCredentials($credentials[0], $credentials[1]);
				}
			}

			if(!$handled)
			{
				//NetDebug::trace($messageType);

				//print_r($amfbody);
				//die();
				$handled = true;
				$amfbody->setSpecialHandling("Ping");
				$amfbody->setMetadata("clientId", $body[0]->clientId);
				$amfbody->setMetadata("messageId", $body[0]->messageId);
				$amfbody->noExec = true;
			}
		} else {
			$methodname = substr($target, $lpos + 1);
			$trunced = substr($target, 0, $lpos);
			$lpos = strrpos($trunced, ".");
			if ($lpos === false) {
				$classname = $trunced;
				if ($classname == "PageAbleResult" && $methodname == 'getRecords') {
					$val = $amfbody->getValue();
					$id = $val[0];
					$keys = explode("=", $id);
					$currset = intval($keys[1]);

					$set = $_SESSION['amfphp_recordsets'][$currset];

					$uriclasspath = $set['class'];
					$classpath = $baseClassPath . $set['class'];
					$methodname = $set['method'];

					$classname = substr(strrchr('/' . $set['class'], '/'), 1, -4);

					//Now set args for body
					$amfbody->setValue(array_merge($set['args'], array($val[1], $val[2])));

					//Tell amfbody that this is a dynamic paged resultset
					$amfbody->setSpecialHandling('pageFetch');
				}
				else if($classname == "PageAbleResult" && $methodname == 'release')
				{
					$amfbody->setSpecialHandling('pageRelease');
					$amfbody->noExec = true;
				}
				else {
					$uriclasspath = $trunced . ".php";
					//$classpath = $baseClassPath . $trunced . ".php";

					if(realpath(LIB_PATH . DIRECTORY_SEPARATOR . $uriclasspath))
						$classpath = realpath(LIB_PATH . DIRECTORY_SEPARATOR . $uriclasspath);

					if(realpath($servicesPath. DIRECTORY_SEPARATOR .$uriclasspath))
						$classpath = realpath($servicesPath . DIRECTORY_SEPARATOR . $uriclasspath);
				}
			} else {
				$classname = substr($trunced, $lpos + 1);
				$classpath = $baseClassPath . str_replace(".", "/", $trunced) . ".php"; // removed to strip the basecp out of the equation here
				$uriclasspath = str_replace(".", "/", $trunced) . ".php"; // removed to strip the basecp out of the equation here
			}
		}
	} else { // This is a web service and is unsupported
		trigger_error("Web services are not supported in this release", E_USER_ERROR);
	}

	$amfbody->classPath = $classpath;
	$amfbody->uriClassPath = $uriclasspath;
	$amfbody->className = $classname;
	$amfbody->methodName = $methodname;

	return true;
}

function getClassname($classAndPackage)
{
	$lpos = strrpos($classAndPackage, ".");
	if($lpos !== FALSE)
	{
		$classname = substr($classAndPackage, $lpos + 1);
	}
	else
	{
		$classname = $classAndPackage;
	}

	return $classname;
}

function getServiceClass($classAndPackage, $services_path)
{
	$uriclasspath = str_replace('.','/',$classAndPackage) . '.php';

	if(realpath(LIB_PATH . "/". $uriclasspath))
		$classpath = realpath(LIB_PATH . "/". $uriclasspath);

	if(realpath($services_path."/".$uriclasspath))
		$classpath = realpath($services_path."/".$uriclasspath);

	return $classpath;
}

/**
 * ExecutionAction executes the required methods
 */
function executionAction (&$amfbody)
{
	$specialHandling = $amfbody->getSpecialHandling();

	if (!$amfbody->isSpecialHandling() || $amfbody->isSpecialHandling(array('describeService', 'pageFetch', 'RemotingMessage')))
	{
		$construct = &$amfbody->getClassConstruct();
		$method = $amfbody->methodName;
		$args = $amfbody->getValue();

		if($specialHandling == 'describeService')
		{
			include_once(AMFPHP_BASE . "util/DescribeService.php");
			$ds = new DescribeService();
			$results = $ds->describe($construct, $amfbody->className);
		}
		else if($specialHandling == 'pageFetch')
		{
			$args[count($args) - 2] = $args[count($args) - 2] - 1;

			$dataset = Executive::doMethodCall($amfbody, $construct, $method, $args);
			$results = array("cursor" => $args[count($args) - 2] + 1,
							 "data" => $dataset);
			$amfbody->setMetadata('type', '__DYNAMIC_PAGE__');
		}
		else
		{
			try
			{
				if($args[0]["_explicitType"] == "org.aerialframework.encryption.EncryptedVO" && $args[0]["resetKey"] == true)
					unset($_SESSION["KEY"]);

				// check to see if the incoming request is encrypted
				if(@$args[0]["_explicitType"] == "org.aerialframework.encryption.EncryptedVO" && Encryption::canUseEncryption())
				{
					$bytes = $args[0]["data"];

					$decrypted = Encryption::decrypt($bytes);
					$deserializer = new AMFDeserializer("");

					try
					{
						$args = $deserializer->deserializeSpecial($decrypted);
					}
					catch(Exception $e)
					{
						throw new Aerial_Encryption_Exception(Aerial_Encryption_Exception::AMF_ENCODING_ERROR);
					}
				}
				else
				{
					if(ConfigXml::getInstance()->useEncryption &&
								get_class($construct) != "EncryptionService" && $method != "startSession")
					{
						throw new Aerial_Encryption_Exception(Aerial_Encryption_Exception::ENCRYPTION_NOT_USED_ERROR);
					}
				}

				$time = microtime_float();
				$results = Executive::doMethodCall($amfbody, $construct, $method, $args); // do the magic
			}
			catch(Exception $fault)
			{
				if(get_class($fault) == "VerboseException")
				{
					$ex = new MessageException($fault->getCode(), $fault->getMessage(), $fault->getFile(),
						$fault->getLine(), 'AMFPHP_RUNTIME_ERROR');
				}
				else
				{
					$code = "AMFPHP_RUNTIME_ERROR";
					if($fault->getCode() != 0)
					{
						$code = $fault->getCode();
					}
					$ex = new MessageException(E_USER_ERROR, $fault->getMessage(), $fault->getFile(),
						$fault->getLine(), $code);
				}
				MessageException::throwException($amfbody, $ex);
				$results = '__amfphp_error';
			}

			global $amfphp;
			$amfphp['callTime'] += microtime_float() - $time;
		}

		if($results !== '__amfphp_error')
		{
			if($specialHandling == 'RemotingMessage')
			{

				$wrapper = new AcknowledgeMessage($amfbody->getMetadata("messageId"),
												  $amfbody->getMetadata("clientId"));
				$wrapper->body = $results;
				$amfbody->setResults($wrapper);
			}
			else
			{
				$amfbody->setResults($results);
			}

			$amfbody->responseURI = $amfbody->responseIndex . "/onResult";
		}
		return false;
	}
	elseif($specialHandling == 'Ping')
	{
		$wrapper = new AcknowledgeMessage($amfbody->getMetadata("messageId"),
										  $amfbody->getMetadata("clientId"));
		$amfbody->setResults($wrapper);
		$amfbody->responseURI = $amfbody->responseIndex . "/onResult";
	}
	else if($specialHandling == 'pageRelease')
	{
		//Ignore PageAbleResult.release
		$amfbody->setResults(true);
		$amfbody->setMetaData('type', 'boolean');
		$amfbody->responseURI = $amfbody->responseIndex . "/onResult";
		return false;
	}
	return true;
}
?>