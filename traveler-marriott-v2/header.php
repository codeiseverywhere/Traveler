<?php

use function Traveler\V2\Theme\template;

$logoUrl = get_template_directory_uri() . '/public/images/logo.svg';

if ( (int) 1 !== get_current_blog_id() ) {

	$logoUrl = get_template_directory_uri() . '/public/images/logo-es.svg';
}

template( 'layout/head' );

?>
<body>
