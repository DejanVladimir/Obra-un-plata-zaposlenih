<?php
namespace MyApp;
Class Internationalization {
	public const LOG_FILE = 'i18n.log';
	public static final function translations() {
		$languages = [];
		foreach(glob(__DIR__ . DIRECTORY_SEPARATOR . 'Languages' . DIRECTORY_SEPARATOR . '*.json') as $filename) {
			$language = json_decode(file_get_contents($filename), true);
			$languages[$language['locale']] = $language['strings'];
		}
		return $languages;
	}
	public static final function translate($string, $lang = '') {
		if($lang === '' || $lang === Configuration::DEFAULT_LANGUAGE) {
			return $string;
		}
		$translations = Internationalization::translations();
		if(isset($translations[$lang][$string]) && $translations[$lang][$string]) {
			return $translations[$lang][$string];
		} else {
			if(Configuration::I18N_LOG_UNKNOWN_STRINGS) {
				file_put_contents(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . Internationalization::LOG_FILE, $string . '|' . $lang . PHP_EOL, FILE_APPEND);
			}
			return $string;
		}
	}
}