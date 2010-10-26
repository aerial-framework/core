<?php
class GenerationController
{
	public static function getNumFiles($path)
	{
		$num = 0;

		try
		{
			$r = new RecursiveDirectoryIterator($path);
		}
		catch(Exception $e)
		{
			return 0;	
		}


		foreach (new RecursiveIteratorIterator($r) as $file)
		{
			if(strpos($file->getPath(), ".svn") !== false)
			continue;

			$num++;
		}

		return $num;
	}

	public static function createFolder($path)
	{
		if(!is_dir($path))
		{
			if(defined('AERIAL_DIR_CHMOD') && trim(AERIAL_DIR_CHMOD)  <> ""){
				mkdir($path, AERIAL_DIR_CHMOD, true);
				chmod($path, AERIAL_DIR_CHMOD); //There are some caveats to mkdir setting permissions so we'll reinforce the permissions here.
			}else{
				mkdir($path, 0777, true);
			}
		}
	}

	/**
	 * Delete Files
	 *
	 * Deletes all files contained in the supplied directory path.
	 * Files must be writable or owned by the system in order to be deleted.
	 * If the second parameter is set to TRUE, any directories contained
	 * within the supplied base directory will be nuked as well.
	 *
	 * @package	CodeIgniter
	 * @author		ExpressionEngine Dev Team
	 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
	 * @license	http://codeigniter.com/user_guide/license.html
	 * @link		http://codeigniter.com
	 *
	 * @access		public
	 * @param		string	path to file
	 * @param		bool	whether to delete any directories found in the path
	 * @return		bool
	 */
	public static function removeFolder($path, $del_dir = FALSE, $level = 0)
	{
		// Trim the trailing slash
		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if ( ! $current_dir = @opendir($path))
		return;

		while(FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename != "." and $filename != "..")
			{
				if (is_dir($path.DIRECTORY_SEPARATOR.$filename))
				{
					// Ignore empty folders
					if (substr($filename, 0, 1) != '.')
					{
						self::removeFolder($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $level + 1);
					}
				}
				else
				{
					unlink($path.DIRECTORY_SEPARATOR.$filename);
				}
			}
		}
		@closedir($current_dir);

		if ($del_dir == TRUE AND $level > 0)
		{
			@rmdir($path);
		}
	}

	public static function emptyFolder($path)
	{
		if(is_dir($path))
		{
			self::removeFolder($path, true);
			self::createFolder($path);
		}
		else
			self::createFolder($path);
	}


	public static function readTemplate($template)
	{
		$f = fopen(AERIAL_INTERNAL."/templates/$template.tmpl", "r");
		$contents = fread($f, filesize(AERIAL_INTERNAL."/templates/$template.tmpl"));
		return str_replace("\r\n", "\n", $contents);
	}

	public static function writeFile($path, $contents)
	{
		file_put_contents($path, $contents);
		if(defined('AERIAL_FILE_CHMOD') && trim(AERIAL_FILE_CHMOD)  <> "") chmod($path, AERIAL_FILE_CHMOD);
	}

	public static function getTemplatePart($stub)
	{
		$parts = simplexml_load_file(AERIAL_INTERNAL."/templates/template-parts.xml");
		foreach($parts->xpath("//part") as $part)
		if($part->attributes()->name == $stub)
		return (string) $part->children()->content;

		return "";
	}
}
?>