<style>
	.update-nag { display: none; }
	#instructions {max-width: 750px;}
	.theme-screen-picture {float: right; margin: 0 0 20px 20px; border: 1px solid #ddd; width: 300px; height:auto;}
	#changeLogList {list-style-type: disc;list-style-position: inside;}
</style>

<?php
$screenshotSrc = get_template_directory_uri(). '/screenshot.png';
?>

<div class="wrap">
	<div id="icon-tools" class="icon32"></div>
	<h2><?php echo esc_html( $themeName ); ?> <?php esc_html_e( 'Theme Updates','adventure-tours' ); ?></h2>
	<div id="message" class="updated below-h2">
		<p><strong><?php printf( esc_html__( 'There is a new version of the %s Theme available.','adventure-tours' ), $themeName ); ?></strong> <?php printf( esc_html__( 'You currently have version %s installed. Please update to version %s.','adventure-tours' ), $currentVersion, $newVersion ); ?></p>
	</div>
	<div id="instructions">
		<img class="theme-screen-picture" src="<?php echo esc_url( $screenshotSrc ); ?>" />
		<h3><?php esc_html_e( 'Update Instructions','adventure-tours' ); ?></h3>
		<p><?php printf( esc_html__( 'To update %s, simply download the latest files from %s and install the upgraded version.','adventure-tours' ), $themeName, '<a href="http://themeforest.net/">Themeforest</a>' ); ?></p>
		<h4><?php esc_html_e( 'Quick guide','adventure-tours' ); ?></h4>
		<ol style="padding-left:20px; overflow:hidden;">
			<li><?php esc_html_e( 'Download the most recent Theme Files','adventure-tours' ); ?></li>
			<li><?php esc_html_e( 'Install the Updated Theme','adventure-tours' ); ?></li>
		</ol>
		<div style="clear:both"></div>
	</div>
	<?php if ( ! empty( $updatesFlatLog ) ) {?>
	<div>
		<h3><?php esc_html_e( 'Changelog','adventure-tours' ); ?></h3>
		<ul id="changeLogList">
		<?php foreach ( $updatesFlatLog as $message ) { ?>
			<li><?php echo esc_html( $message ); ?></li>
		<?php } ?>
		</ul>
	</div>
	<?php } ?>
</div>
