<?php
/**
 * View that renders theme import page.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.8
 */

$list = isset( $gateways ) && $gateways ? $gateways : array();

if ( ! isset( $form_data ) ) {
	$form_data = array();
}
$checked_types = isset( $form_data['types'] ) ? $form_data['types'] : array();
$type_options = isset( $form_data['type_options'] ) ? $form_data['type_options'] : array();

if ( ! isset( $results ) ) {
	$results = array();
}
?>

<style type="text/css">
.import-row{
	margin-top:30px;
	padding-left:25px;
}
.import-row__title{
	font-size:1.2em;
	margin-left:-25px;
}
	.import-row__title--checked{
		font-weight:bold;
	}
.import-row__description{
	padding-top:5px;
}
.import-row__options{
	padding-top:10px;
}
	.import-row__options__user__option{
		padding-left:25px;
	}
	.import-row__options__user__option--disabled{
		opacity:0.25;
	}
.import-row__results{
	color:#137913;
}
	.import-row__results--errors{
		color:#EE0000;
	}
.import-row__errors{
	color:#EE0000;
}
.import-notice{
	display: none;
}
.widgets-report__title{
	font-weight: bold;
}
.widgets-report__list{
	color:#444;
	padding-left:20px;
}
</style>

<div>
	<h2>Adventure Tours Demo Data Importer</h2>

	<div class="import-notice"><b>NOTE:</b> Please install "WordPress Importer" plugin (<a target="_blank" href="https://wordpress.org/plugins/wordpress-importer/">wordpress-importer</a>) to be able to import posts, pages, products and tours.</div>
	<form id="theme_import_form" action="<?php echo esc_url( $form_action ); ?>" method="POST">
		<?php if ( ! empty( $form_hidden_fields ) ) {
			foreach ($form_hidden_fields as $field_name => $field_value) {
				printf( '<input type="hidden" name="%s" value="%s"/>',
					esc_attr( $field_name ),
					esc_attr( $field_value )
				);
			}
		} ?>
		<?php foreach ($list as $option_key => $option) { ?>
			<?php
				if ( empty( $option['enabled'] ) ) {
					continue;
				}

				$option_addional_fields = '';
				if (in_array($option_key, array('post', 'page', 'product', 'faq'))) {
					$option_addional_fields = '';

					$current_type_options = !empty( $type_options[ $option_key ] ) ? $type_options[ $option_key ] : array(
						'another_user' => 'AdventureTours',
						'include_menus' => true
					);

					if ( 'page' == $option_key ) {
						$option_addional_fields .= strtr('<div class="import-row__options">
								<input type="checkbox" name="import_data[type_options][{option_key}][include_menus]" value="on"{checbox_checked}/><span>Including Menus</span>
							</div>', array(
								'{option_key}' => $option_key,
								'{checbox_checked}' => empty( $current_type_options['include_menus'] ) ? '' : ' checked="cheched"',
							)
						);
					}

					$option_addional_fields .= strtr('<div class="import-row__options">
							<div class="import-row__options__user"> 
								<div>Select an author for imported posts/pages</div>
								<div class="import-row__options__user__option import-row__options__user__option--another-user">
									<input type="text" name="import_data[type_options][{option_key}][another_user]" value="{another_user}"/>
									<span><b>Note:</b> user will be created if it does not exist</span>
								</div>
								<div class="import-row__options__user__option">
									<input type="checkbox" name="import_data[type_options][{option_key}][current_user]" value="on"{checbox_checked}/>
									<span>current user ({current_username})</span>
								</div>
							</div>
						</div>', array(
							'{option_key}' => $option_key,
							'{checbox_checked}' => empty( $current_type_options['current_user'] ) ? '' : ' checked="cheched"',
							'{another_user}' => empty( $current_type_options['another_user'] ) ? '' : esc_attr( $current_type_options['another_user'] ),
							'{current_username}' => get_user_meta( get_current_user_id(), 'nickname', true ),
						));
				}

				$is_available = !empty( $option['available'] );

				echo strtr('<div class="import-row">
					<div class="import-row__title">
						<input type="checkbox" name="import_data[types][{option_key}]" value="{option_key}"{checbox_state}{checbox_checked}/>
						{label}
					</div>
					<div class="import-row__description">{description}</div>
					{additional_fields}
					<div class="import-row__errors">{errors}</div>
					<div class="import-row__results{results_is_error_class}">{results}</div>
				</div>',array(
					'{option_key}' => $option_key,
					'{label}' => isset( $option['title'] ) ? $option['title'] : ucwords( str_replace('_', ' ', $option_key ) ),
					'{description}' => isset( $option['description'] ) ? $option['description'] : '',
					'{checbox_state}' => $is_available ? '' : ' disabled="disabled"',
					'{checbox_checked}' => empty( $checked_types[ $option_key ] ) ? '' : ' checked="checked"',
					'{errors}' => !empty($option['errors']) ? join( '<br>', $option['errors'] ) : '',
					'{results}' => !empty($results[$option_key]) ? $results[$option_key] : '',
					'{results_is_error_class}' => !empty($results['errors'][$option_key]) ? ' import-row__results--errors' : '',
					'{additional_fields}' => $option_addional_fields,
				) );
			?>
		<?php } ?>
		<div class="import-row">
			<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Start', 'adventure-tours' ); ?>" />
		</div>
	</form>
</div>

<script type="text/javascript">
jQuery(function($){
	var import_form = $('#theme_import_form'),
		checkboxes = import_form.find('input[type=checkbox]'),
		type_checboxes = checkboxes.filter('[name*="[types]"]'),
		main_btn = import_form.find('input[type=submit]');

	type_checboxes.on('change',function(){
			var cur_cb = checkboxes.filter(this),
				cur_is_checked = cur_cb.is(':checked'),
				title_el = cur_cb.parents('.import-row__title'),
				checked_class = 'import-row__title--checked';
			if ( cur_is_checked ) {
				title_el.addClass( checked_class );
			} else {
				title_el.removeClass( checked_class );
			}

			if ( cur_is_checked || type_checboxes.filter(':checked').length ) {
				main_btn.prop('disabled', false);
			} else {
				main_btn.prop('disabled', true);
			}
		})
		.filter(':first,:checked').trigger('change');

	checkboxes.filter('[name$="[current_user]"]').on('change',function(){
		var el = jQuery(this),
			row = el.parents('.import-row__options__user'),
			rel_element = row.find('.import-row__options__user__option--another-user'),
			diabled_class = 'import-row__options__user__option--disabled';
		if (el.is(':checked')) {
			rel_element.addClass(diabled_class);
		} else {
			rel_element.removeClass(diabled_class);
		}
	}).trigger('change');

	return;
	import_form.submit(function(){
		var form = $(this),
			data = form.serialize();
		$.ajax({
			method:'POST',
			url: form.attr('action'),
			data: data,
			success:function(r){
				alert(r);
			}
		})
		return false;
	})
});
</script>