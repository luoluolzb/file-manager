<?php

/**
 * 简单文件管理器
 */
class SimpleFileManager
{
	private $dirName, $rootDir;
	
	public function __construct($dirName, $rootDir = null)
	{
		$this->dirName = $dirName;
		if(!isset($rootDir)){
			$this->rootDir = $dirName;
		}
		else{
			$this->rootDir = $rootDir;
		}
	}
	
	/**
	 * 获取当前目录下的文件列表
	 * @return array                文件列表
	 */
	public function getFileList()
	{
		$res_dir = array();
		$res_file = array();
		$handle = opendir($this->dirName);
		
		if(!$handle){
			return $res_dir;
		}
		while($name = readdir($handle)){
			if($name == '.' || $name == '..'){
				continue;
			}

			$fullName = $this->getFullName($name);
			$info = array(
				'name' => mb_convert_encoding($name, 'UTF-8', 'GBK')
			);
			
			if(is_dir($fullName)){
				$info['type'] = "dir";
			}
			else{
				$info['type'] = "file";
				$info['size'] = filesize($fullName);
				$info['size_string'] = self::local_fileSize_string($info['size']);
			}
			$info['perms'] = fileperms($fullName);
			$info['perms_string'] = self::local_fileperms_string($info['perms']);
			
			if(is_dir($fullName)){
				$res_dir[] = $info;
			}else if(is_file($fullName)){
				$res_file[] = $info;
			}
		}
		closedir($handle);
		return array_merge($res_dir, $res_file);
	}

	public function mkdir($dirName)
	{
		return mkdir(self::getFullName($dirName));
	}

	public function rename($name, $newName)
	{
		return rename(self::getFullName($name), self::getFullName($newName));
	}

	public function unzip($fileName, $toName = "")
	{
		$exp = explode('.', $fileName);
		$ext = end($exp);
		if(strtolower($ext) != 'zip'){
			return FALSE;
		}

		$fullName = self::getFullName($fileName);
		if(!file_exists($fullName)){
			return FALSE;
		}

		$zipArc = new ZipArchive();
		if(!$zipArc->open($fullName)){
			return FALSE;
		}
		if(!$zipArc->extractTo($toName == "" ? $this->dirName : self::getFullName($toName))){
			$zipArc->close();
			return FALSE;
		}
		return $zipArc->close();
	}

	public function zip($fromName, $toName = "")
	{
		$toName = self::getFullName($toName);
		$zipArc = new ZipArchive();
		if(!$zipArc->open($toName, ZipArchive::CREATE)){
			return FALSE;
		}
		foreach ($fromName as $i => $name) {
			self::_addFileToZip($zipArc, "", self::getFullName($name));
		}
		return $zipArc->close();
	}

	private function _addFileToZip(& $zipArc, $dir, $name)
	{
		if(is_file($name)){
			$localName = ($dir == "" ? basename($name) : $dir.'/'.basename($name));
			$zipArc->addFile($name, $localName);
		}
		else{
			$dir = ($dir == "" ? basename($name) : $dir.'/'.basename($name));
			$handle = opendir($name);
			while($fileName = readdir($handle)){
				if($fileName == '.' || $fileName == '..'){
					continue;
				}
				self::_addFileToZip($zipArc, $dir, $name.'/'.$fileName);
			}
			closedir($handle);
		}
	}
	
	/**
	 * 删除当前目录下的一个文件或目录
	 * @param  string|array $name 文件名或文件列表
	 * @return bool         true成功, false失败
	 */
	public function delete($name)
	{
		if(is_string($name)){
			return self::_delete($this->getFullName($name));
		}
		else if(is_array($name)){
			$ret = true;
			foreach ($name as $i => $fileName){
				$ret = $ret && self::_delete($this->getFullName($fileName));
			}
			return $ret;;
		}
		return false;
	}

	/**
	 * 删除当前目录下的一个文件或目录
	 * @param  string $name 文件或目录名
	 * @return bool         true成功, false失败
	 */
	private function _delete($fullName)
	{
		if(is_file($fullName)){
			return unlink($fullName);
		}
		else if(is_dir($fullName)){
			$handle = opendir($fullName);
			while($name = readdir($handle)){
				if($name == '.' || $name == '..'){
					continue;
				}
				self::_delete($fullName.'/'.$name);
			}
			closedir($handle);
			return rmdir($fullName);
		}
		return false;
	}
	
	/**
	 * 获取当前目录下的一个文件内容
	 * @param  string $fileName 文件名
	 * @return bool             true成功, false失败
	 */
	public function getFileContents($fileName)
	{
		$fullName = $this->getFullName($fileName);
		if(is_file($fullName)){
			return file_get_contents($fullName);
		}
		return false;
	}
	
	/**
	 * 获取当前目录下的一个文件完整路径
	 * @param  string $name 文件名
	 * @return string       完整文件名
	 */
	public function getFullName($name)
	{
		return $this->dirName.'/'.$name;
	}
	
	/**
	 * 切换当前目录
	 * @param  string $dirName 目录名
	 * @return string          当前目录
	 */
	public function changeDir($dirName)
	{
		$this->dirName .= '/'.$dirName;
		return $this->dirName;
	}
	
	/**
	 * 切换当前目录
	 * @param  string $dirName 目录名
	 * @return string          当前目录
	 */
	public function backLastDir()
	{
		$pos = strrpos($this->dirName, $this->dirChar);
		$this->dirName = substr($this->dirName, 0, $pos);
		if(strlen($this->dirName) < strlen($this->rootDir)){
			$this->dirName = $this->rootDir;
		}
		return $this->dirName;
	}
	
	/**
	 * 切换当前目录
	 * @param  string $dirName 目录名
	 * @return string          当前目录
	 */
	public function backRootDir()
	{
		$this->dirName = $this->rootDir;
		return $this->dirName;
	}

	public static function local_fileSize_string($size)
	{
		if($size < 1024){
			return $size.' Bytes';
		}
		else if($size < 1048576){
			return (int)($size/1024).' KB';
		}
		else{
			return (int)($size/1048576).' MB';
		}
	}

	public static function local_fileperms_string($perms)
	{
		if (($perms & 0xC000) == 0xC000) {
		    // Socket
		    $info = 's';
		} elseif (($perms & 0xA000) == 0xA000) {
		    // Symbolic Link
		    $info = 'l';
		} elseif (($perms & 0x8000) == 0x8000) {
		    // Regular
		    $info = '-';
		} elseif (($perms & 0x6000) == 0x6000) {
		    // Block special
		    $info = 'b';
		} elseif (($perms & 0x4000) == 0x4000) {
		    // Directory
		    $info = 'd';
		} elseif (($perms & 0x2000) == 0x2000) {
		    // Character special
		    $info = 'c';
		} elseif (($perms & 0x1000) == 0x1000) {
		    // FIFO pipe
		    $info = 'p';
		} else {
		    // Unknown
		    $info = 'u';
		}

		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
		            (($perms & 0x0800) ? 's' : 'x' ) :
		            (($perms & 0x0800) ? 'S' : '-'));

		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
		            (($perms & 0x0400) ? 's' : 'x' ) :
		            (($perms & 0x0400) ? 'S' : '-'));

		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
		            (($perms & 0x0200) ? 't' : 'x' ) :
		            (($perms & 0x0200) ? 'T' : '-'));

		return $info;
	}
}

?>