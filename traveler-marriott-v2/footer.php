<?php

use WPEngine\GeoIp;

$geo_country = 'N/A';
$geo_state   = 'N/A';

if ( class_exists( 'WPEngine\GeoIp' ) ) {
	$geo         = GeoIp::instance();
	$geo_country = $geo->country() == '' ? 'N/A' : $geo->country();
	$geo_state   = $geo->region() == '' ? 'N/A' : $geo->region();
}

$brandFooter = get_template_directory_uri() . '/public/images/brand-footer.jpg';
$classFooter = 'footer-regular';

if ( wp_is_mobile() ) {

	$brandFooter = get_template_directory_uri() . '/public/images/brand-footer-tablet.jpg';
}

if ( (int) 1 !== get_current_blog_id() ) {

	$brandFooter = get_template_directory_uri() . '/public/images/brand-footer-es.jpg';

	if ( wp_is_mobile() ) {

		$brandFooter = get_template_directory_uri() . '/public/images/brand-footer-tablet-es.jpg';
	}
}

if ( is_singular( 'storybooked' ) || is_page_template( 'interactive.php' ) ) {

	$classFooter = 'footer-interactive';
}
?>

<script type="text/javascript">
    let dataLayer = {
        "site_id": "TRV",
        "browser_loc_country": "<?= $geo_country; ?>",
        "browser_loc_state": "<?= $geo_state; ?>",
    };
</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<style>

		.mustshow{display:block !important;}

	</style>
<script>
	var $ = jQuery;
	$( window ).bind( "load", function() {
	  if ($(window).width() < 767) {
	  	$( "#main-animated-menu" ).addClass('menuclose');
	  	$( "#main-animated-menu" ).fadeOut('fast');
	  	$( "header .menu-icon" ).click(function(e) {
			e.preventDefault();
			if($( "#main-animated-menu" ).hasClass('menuclose'))
			{
				$( "#main-animated-menu" ).removeClass('menuclose');
				$( "#main-animated-menu" ).fadeOut('slow');
			}
			else{

				$( "#main-animated-menu" ).addClass('menuclose');
				$( "#main-animated-menu" ).fadeIn('slow');
			}
		});

	  }
	  else {
	  	$("#main-animated-menu").addClass('mustshow');
	  }

	});
	</script>
<?php wp_footer(); ?>
</body>
</html>
