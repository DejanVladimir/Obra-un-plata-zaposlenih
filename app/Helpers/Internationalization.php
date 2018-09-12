<?php 
function _i18n($string) {
	return \MyApp\Internationalization::translate($string, \MyApp\Configuration::APP_LANGUAGE);
}