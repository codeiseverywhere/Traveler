<?php

namespace Traveler\V2\Theme\Extras;

use function apple_news_is_exporting;
use function function_exists;


function is_amp() {
	return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
}


function is_apple_news_exporting() {

	return function_exists( 'apple_news_is_exporting' ) && apple_news_is_exporting();
}
