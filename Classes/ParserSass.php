<?php

class tx_DyncssPhpsass_ParserSass extends tx_Dyncss_Parser_AbstractParser{
	/**
	 * @var string $filetype type of the file, either sass or scss
	 */
	protected $fileType = 'sass';

	/**
	 * prepare the parser
	 */
	function __construct() {
		$this->initEmConfiguration();
		// ensure no one else has loaded lessc already ;)
		if(!class_exists('SassParser')) {
			include_once(t3lib_extMgm::extPath('dyncss_phpsass') . 'Resources/Private/Php/PHPSass/SassParser.php');
		}
		$this->parser = new SassParser(
			array(
				'style'  => 'nested',
				'cache'  => FALSE,
				'syntax' => $this->fileType,
				'debug'  => FALSE,
				'callbacks' => array(
					'warn'  => 'tx_DyncssPhpsass_ParserSass_callback',
					'debug' => 'tx_DyncssPhpsass_ParserSass_callback',
				)
			)
		);

		if($this->config['enableDebugMode']) {
			$this->parser->debug      = TRUE;
			$this->parser->debug_info = TRUE;
		}
	}
	/**
	 * @param $string
	 * @param null $name
	 * @return mixed
	 */
	protected function _compile($string, $name = null) {
		// TODO: Implement _compile() method.
	}

	/**
	 * @param $string
	 * @return mixed
	 */
	protected function _prepareCompile($string) {
		/**
		 * Change the initial value of a less constant before compiling the file
		 */
		if(is_array($this->overrides)) {
			foreach($this->overrides as $key => $value) {
				$string = preg_replace(
					'/\$' . $key . ':(.*);/U',
					 '\$' . $key . ': ' . $value,
					 $string,
					 1
				);
			}
		}
		return $string;
	}

	/**
	 * @param $inputFilename
	 * @param $outputFilename
	 * @param $cacheFilename
	 */
	protected function _compileFile($inputFilename, $preparedFilename, $outputFilename, $cacheFilename) {
		try {
			$this->parser->load_paths[] = dirname($inputFilename);
			$this->parser->load_paths[] = PATH_site;
			return $this->parser->toCss($preparedFilename);
		} catch(Exception $e) {
			return $e;
		}

	}
}

function tx_DyncssPhpsass_ParserSass_callback($filename, $parser) {

}