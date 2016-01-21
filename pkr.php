#!/usr/bin/php
<?php
/*
 * Packer para los archivos JS y CSS
 *
 * Requiere Composer
 *
 * Alias Configuración:
 * [alias]
		pkr = !/home/fitorec/codes/system/packer/pkr.php
 */

$path = realpath(dirname(__FILE__));

echo "\033[01;31m Packer ".date('Y-m-d H:i:s')."\033[0m\n\n\n";

include($path . '/libs/linkorb/jsmin-php/src/jsmin-1.1.1.php');
include($path . '/libs/natxet/CssMin/src/CssMin.php');

class Pkr{
	public function __construct () {
		$cmd = "git status ";
		$lines = explode(PHP_EOL, shell_exec($cmd));
		$pattern = '/^.*:\s+(.*\.(js|css))$/';
		foreach($lines as $line) {
			$matches = array();
			preg_match($pattern, $line, $matches);
			if(count($matches) == 3) {
				if(!$this->isMin($matches[1])) {
					$this->packer($matches[1], $matches[2]);
				}
			}
		}
	}

	public function isMin($file){
		$pattern = '/^.*\.min\.(js|css)$/';
		return preg_match($pattern, $file);
	}

	public function packer($file,  $type) {
		if(!file_exists($file)) {
			return;
		}
		$fileDst = preg_replace('/\.(js|css)$/', '.min.\1', $file);
		if($type == "js") {
			$minContent = JSMin::minify(file_get_contents($file));
		} else {
			$minContent = CssMin::minify(file_get_contents($file));
		}
		echo "\033[01;41m " . $file . " \033[0m -> \033[32;40m".$fileDst."\033[0m\n";
		file_put_contents($fileDst, $minContent);
		echo shell_exec("git add " . $fileDst);
	}
}
new Pkr();
