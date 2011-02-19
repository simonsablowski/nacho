<?php

class Localization extends Application {
	protected $language = NULL;
	protected $locale = NULL;
	protected $localized = array();
	
	public function __construct() {
		
	}
	
	public function prepare() {
		if ($language = $this->getConfiguration('language') && $locale = $this->getConfiguration('locale')) {
			$this->setLanguage($language);
			setlocale(LC_ALL, $this->setLocale($locale));
		} else {
			throw new FatalError('Localization configuration incomplete, language and locale are required', $this->getConfiguration());
		}
		
		if (!file_exists($filePath = $this->getApplication()->getPath() . 'localization/' . ($fileName = $this->getConfiguration('language') . '.po'))) {
			throw new FatalError('Localization file not found', $fileName);
		}
		
		$lines = file_get_contents($filePath);
		preg_match_all('/msgid\s+"(.+)"\s*msgstr\s+"(.*)"/', $lines, $matches);
		
		if (isset($matches[1]) && isset($matches[2]) && ($count = count($matches[1])) == count($matches[2])) {
			if ($count) $this->setLocalized(array_combine($matches[1], $matches[2]));
		} else {
			throw new FatalError('Localization file is corrupt', $fileName);
		}
	}
	
	protected static function getReplaced($localized, $replacements) {
		return vsprintf($localized, is_array($replacements) ? $replacements : array(1 => $replacements));
	}
	
	public function getLocalized($string, $replacements = array()) {
		return $this->getReplaced(isset($this->localized[$string]) ? $this->localized[$string] : $string, $replacements);
	}
}