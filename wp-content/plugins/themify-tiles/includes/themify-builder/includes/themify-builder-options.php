<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function themify_builder_tabs( $field, $module_name, $styling = false ) {
	$id = $field['id']; ?>
	<div class="themify_builder_tabs" id="themify_builder_tabs_<?php echo $id; ?>">
		<ul class="clearfix">
		<?php foreach( $field['tabs'] as $key => $tab ) : ?>
			<li><a href="#tf_<?php echo $id . '_' . $key; ?>"> <?php echo $tab['label']; ?> </a></li>
		<?php endforeach; ?>
		</ul>

		<?php foreach( $field['tabs'] as $key => $tab ) : ?>
			<div id="tf_<?php echo $id . '_' . $key; ?>" class="themify_builder_tab">
				<?php
				if( $styling ) {
					themify_render_styling_settings( $tab['fields'] );
				} else {
					themify_builder_module_settings_field( $tab['fields'], $module_name );
				}
				?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

function themify_render_styling_settings( $fields ) {
	foreach( $fields as $styling ):

		if( $styling['type'] == 'tabs' ) {
			themify_builder_tabs( $styling, '', true );
			continue;
		}

		echo $styling['type'] != 'separator' ? '<div class="themify_builder_field">' : '';
		if ( isset( $styling['label'] ) ) {
			echo '<div class="themify_builder_label">'.$styling['label'].'</div>';
		}
		echo $styling['type'] != 'separator' ? '<div class="themify_builder_input">' : '';
		if ( $styling['type'] == 'multi' ) {
			foreach( $styling['fields'] as $field ) {
				themify_builder_styling_field( $field );
			}
		} else {
			themify_builder_styling_field( $styling );
		}
		echo $styling['type'] != 'separator' ? '</div>' : ''; // themify_builder_input
		echo $styling['type'] != 'separator' ? '</div>' : ''; // themify_builder_field

	endforeach;
}

function themify_builder_get_binding_data( $field ) {
	if( isset( $field['binding'] ) ) {
		echo " data-binding='". json_encode( $field['binding'] ) ."'";
	}
}

if ( ! function_exists( 'themify_builder_module_settings_field' ) ) {
	/**
	 * Module Settings Fields
	 * @param array $module_options 
	 * @return string
	 */
	function themify_builder_module_settings_field( $module_options, $module_name ) {
		foreach ( $module_options as $field ):

			$id = isset( $field['id'] ) ? $field['id'] : '';

			// custom field types used by 3rd party module authors
			if( function_exists( "themify_builder_field_{$field['type']}" ) ) {
				call_user_func( "themify_builder_field_{$field['type']}", $field, $module_name );
				continue;
			} elseif( $field['type'] == 'group' ) { // simple wrapper for multiple related options
				$classes = isset( $field['wrap_with_class'] ) ? $field['wrap_with_class'] : '';
				echo '<div class="themify_builder_field '. $id .' '. $classes .'">';
				themify_builder_module_settings_field( $field['fields'], $module_name );
				echo '</div>';
				continue;
			} else if( $field['type'] == 'tabs' ) {
				themify_builder_tabs( $field, $module_name );
				continue;
			}

			if( isset( $field['separated'] ) && $field['separated'] == 'top' ): ?>
				<hr />
			<?php endif; ?>

			<?php if( $field['type'] != 'builder' && ( !isset($field['hide']) || $field['hide'] == false) ): ?>
			<div class="themify_builder_field <?php echo $id; ?> <?php echo (isset($field['wrap_with_class'])) ? $field['wrap_with_class'] : ''; ?>">
			<?php endif; ?>

				<?php if(isset($field['id']) && isset($field['label']) && $field['label'] != false): ?>
					<div class="themify_builder_label"><?php echo $field['label']; ?></div>
				<?php endif; ?>

			<?php
				if( $field['type'] == 'multi' ) {
					echo '<div class="'. $id .' tf_multi_fields tf_fields_count_'. count( $field['fields'] ) .'">';
					foreach( $field['fields'] as $_field ) {
						themify_builder_module_settings_field( array( $_field ), $module_name );
					}
					echo '</div>';

				} else if('wp_editor' == $field['type']){
					wp_editor( '', $field['id'], array('editor_class' => $field['class'] . ' tfb_lb_wp_editor tfb_lb_option', 'textarea_rows' => 20));

				} elseif( 'builder' == $field['type'] ) { ?>

				<div class="<?php echo (isset($field['wrap_with_class'])) ? $field['wrap_with_class'] : ''; ?>">
				<hr />

				<div id="<?php echo $field['id']; ?>" class="themify_builder_module_opt_builder_wrap themify_builder_row_js_wrapper tfb_lb_option">
					
					<div class="themify_builder_row clearfix">
					
						<div class="themify_builder_row_top">
							<div class="row_menu">
								<div class="menu_icon">
								</div>
								<ul style="display: none;" class="themify_builder_dropdown">
									<li><a href="#" class="themify_builder_duplicate_row"><?php _e('Duplicate', 'themify') ?></a></li>
									<li><a href="#" class="themify_builder_delete_row"><?php _e('Delete', 'themify') ?></a></li>
								</ul>
							</div>
							<!-- /row_menu -->
							<div class="toggle_row"></div><!-- /toggle_row -->
						</div>
						<!-- /row_top -->
						
						<div class="themify_builder_row_content">

							<?php foreach( $field['options'] as $option ): ?>
								<div class="themify_builder_field <?php echo (isset($option['wrap_with_class'])) ? $option['wrap_with_class'] : ''; ?>">

									<?php if( isset($option['label']) && $option['label'] != false ): ?>
										<div class="themify_builder_label"><?php echo $option['label']; ?></div><!-- /themify_builder_input_title -->
									<?php endif; ?>

									<div class="themify_builder_input"<?php echo $option['type'] == 'wp_editor' ? ' style="width:100%;"' : ''; ?>>
										<?php if( $option['type'] == 'text' ): ?>

											<?php if( isset($option['colorpicker']) && $option['colorpicker'] == true ) : ?>
												<span class="builderColorSelect"><span></span></span> 
												<input type="text" class="<?php echo $option['class']; ?> colordisplay" <?php echo themify_builder_get_binding_data( $option ); ?> />
												<input id="<?php echo $option['id'] ?>" name="<?php echo $option['id'] ?>" value="<?php if(isset($option['value'])) echo $option['value']; ?>" class="builderColorSelectInput tfb_lb_option_child" type="hidden"  data-input-id="<?php echo $option['id']; ?>" />
											<?php else : ?>
												<input name="<?php echo $option['id']; ?>" class="<?php echo $option['class']; ?> tfb_lb_option_child <?php echo isset( $add_class ) ? $add_class : ''; ?>" type="text" data-input-id="<?php echo $option['id']; ?>" />
												<?php if( isset($option['iconpicker']) && $option['iconpicker'] == true ) : ?>
													<a class="button button-secondary hide-if-no-js themify_fa_toggle" href="#"><?php _e( 'Insert Icon', 'themify' ); ?></a>
												<?php endif; ?>
												<?php if( isset( $option['after'] ) ) echo $option['after']; ?>
											<?php endif; ?>

										<?php elseif( 'image' == $option['type'] ): ?>
											<input data-input-id="<?php echo $option['id']; ?>" name="<?php echo $option['id'] ?>" placeholder="<?php if(isset($option['value'])) echo $option['value']; ?>" class="<?php echo $option['class']; ?> themify-builder-uploader-input tfb_lb_option_child" type="text" /><br />

											<div class="small">

												<?php if ( is_multisite() && !is_upload_space_available() ): ?>
													<?php echo sprintf( __( 'Sorry, you have filled your %s MB storage quota so uploading has been disabled.', 'themify' ), get_space_allowed() ); ?>
												<?php else: ?>
													<div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="<?php echo $option['id']; ?>themify-builder-plupload-upload-ui">
														<input id="<?php echo $option['id']; ?>themify-builder-plupload-browse-button" type="button" value="<?php esc_attr_e(__('Upload', 'themify') ); ?>" class="builder_button" />
														<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($option['id'] . 'themify-builder-plupload'); ?>"></span>
													</div> <?php _e( 'or', 'themify' ); ?> <a href="#" class="themify-builder-media-uploader tf-upload-btn" data-uploader-title="<?php _e('Upload an Image', 'themify') ?>" data-uploader-button-text="<?php _e('Insert file URL', 'themify') ?>"><?php _e('Browse Library', 'themify') ?></a>

												<?php endif; ?>

											</div>

											<p class="thumb_preview">
												<span class="img-placeholder"></span>
												<a href="#" class="themify_builder_icon small delete themify-builder-delete-thumb"></a>
											</p>

										<?php elseif( 'audio' == $option['type'] ): ?>
											<input data-input-id="<?php echo $option['id']; ?>" name="<?php echo $option['id'] ?>" placeholder="<?php if(isset($option['value'])) echo $option['value']; ?>" class="<?php echo $option['class']; ?> themify-builder-uploader-input tfb_lb_option_child" type="text" /><br />

											<div class="small">

												<?php if ( is_multisite() && !is_upload_space_available() ): ?>
													<?php echo sprintf( __( 'Sorry, you have filled your %s MB storage quota so uploading has been disabled.', 'themify' ), get_space_allowed() ); ?>
												<?php else: ?>
													<div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="<?php echo $option['id']; ?>themify-builder-plupload-upload-ui" data-extensions="<?php echo esc_attr( implode( ',', wp_get_audio_extensions() ) ); ?>">
														<input id="<?php echo $option['id']; ?>themify-builder-plupload-browse-button" type="button" value="<?php esc_attr_e(__('Upload', 'themify') ); ?>" class="builder_button" />
														<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($option['id'] . 'themify-builder-plupload'); ?>"></span>
													</div> <?php _e( 'or', 'themify' ); ?> <a href="#" class="themify-builder-media-uploader tf-upload-btn" data-uploader-title="<?php _e('Upload an Image', 'themify') ?>" data-uploader-button-text="<?php _e('Insert file URL', 'themify') ?>" data-library-type="audio"><?php _e('Browse Library', 'themify') ?></a>

												<?php endif; ?>

											</div>

										<?php elseif( $option['type'] == 'textarea' ): ?>
											<textarea name="<?php echo $option['id']; ?>" class="<?php echo $option['class']; ?> tfb_lb_option_child" <?php echo (isset($option['rows'])) ? 'rows="'.$option['rows'].'"' : ''; ?> data-input-id="<?php echo $option['id']; ?>"></textarea><br />

											<?php if( isset($option['radio']) ): ?>
												<div data-input-id="<?php echo $option['radio']['id']; ?>" class="tfb_lb_option_child tf-radio-choice">
													<?php echo $option['radio']['label']; ?>
													<?php foreach( $option['radio']['options'] as $k => $v ): ?>
														<input id="<?php echo $option['radio']['id'] .'_'. $k; ?>" type="radio" name="<?php echo $option['radio']['id']; ?>" class="themify-builder-radio-dnd" value="<?php echo $k; ?>" />
														<label for="<?php echo $option['radio']['id'] .'_'. $k; ?>" class="pad-right themify-builder-radio-dnd-label"><?php echo $k; ?></label>
													<?php endforeach; ?>
												</div>
											<?php endif; // endif radio input ?>

									<?php elseif( $option['type'] == 'select' ) : ?>
										<select data-input-id="<?php echo $option['id']; ?>" name="<?php echo $option['id'] ?>" class="tfb_lb_option_child">
											<?php if( isset($option['empty']) ): ?>
												<option value="<?php echo $option['empty']['val']; ?>"><?php echo $option['empty']['label']; ?></option>
											<?php endif; ?>
											
											<?php
											foreach ($option['options'] as $key => $value) {
												$selected = ( isset($option['default']) && $option['default'] == $value ) ? ' selected="selected"' : '';
												echo '<option value="' . $key . '" '.$selected.'>' . $value . '</option>';
											}
											?>
										</select>

										<?php
										elseif('wp_editor' == $option['type']):
											wp_editor( '', $option['id'], array('editor_class' => $option['class'] . ' tfb_lb_wp_editor tfb_lb_option_child', 'textarea_rows' => 20));
											?>

										<?php elseif( 'radio' == $option['type'] ): ?>
											<div data-input-id="<?php echo $option['id']; ?>" class="tfb_lb_option_child tf-radio-choice">
												<?php foreach( $option['options'] as $k => $v ): ?>
													<input id="<?php echo $option['id'] .'_'. $k; ?>" type="radio" name="<?php echo $option['id']; ?>" class="themify-builder-radio-dnd" value="<?php echo $k; ?>" />
													<label for="<?php echo $option['id'] .'_'. $k; ?>" class="pad-right themify-builder-radio-dnd-label"><?php echo $k; ?></label>
												<?php endforeach; ?>
											</div>
										<?php endif; // endif radio input ?>

										<?php if( isset($option['help']) ): ?>
											<?php if( isset($option['help']['new_line'])): ?>
												<br />
											<?php endif; ?>
											<small><?php echo $option['help']['text']; ?></small>
										<?php endif; ?>

									</div><!-- /themify_builder_input -->

								</div>
								<!-- /themify_builder_field -->

							<?php endforeach; ?>

						</div>
						<!-- /themify_builder_row_content -->

					</div>
					<!-- /builder_row -->

				</div>
				<!-- /themify_builder_module_opt_builder_wrap -->
					
				<p class="add_new"><a href="#"><span class="themify_builder_icon add"></span><?php _e('Add new row', 'themify') ?></a></p>
				</div>
				<!-- /builder wrapper -->
				<?php
				}

				else{
			?>
				<div class="themify_builder_input<?php echo isset($field['pushed']) && $field['pushed'] != '' ? ' '.$field['pushed'] : ''; ?>">
					<?php if( 'text' == $field['type'] ): ?>

						<?php if( isset($field['colorpicker']) && $field['colorpicker'] == true ) : ?>
							<span class="builderColorSelect"><span></span></span> 
							<input type="text" class="<?php echo $field['class']; ?> colordisplay" <?php echo themify_builder_get_binding_data( $field ); ?> />
							<input id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php if(isset($field['value'])) echo $field['value']; ?>" class="builderColorSelectInput tfb_lb_option" type="hidden" />
						<?php else : ?>
							<input id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php if(isset($field['value'])) echo $field['value']; ?>" class="<?php echo $field['class'] . ' '; echo isset( $add_class ) ? $add_class : ''; ?> tfb_lb_option" type="text" <?php echo themify_builder_get_binding_data( $field ); ?> />
							<?php if( isset( $field['after'] ) ) echo $field['after']; ?>

							<?php if( isset($field['unit']) ): ?>
								<select id="<?php echo $field['unit']['id']; ?>" class="tfb_lb_option" <?php echo themify_builder_get_binding_data( $field ); ?>>
									<?php foreach($field['unit']['options'] as $u): ?>
									<option value="<?php echo $u['value']; ?>" <?php echo ( isset( $field['unit']['selected'] ) && $field['unit']['selected'] == $u['value'] ) ? 'selected="selected"':''; ?>><?php echo $u['value']; ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; // unit ?>
						<?php endif; ?>

					<?php elseif( 'icon' == $field['type'] ): ?>
					<input id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" value="<?php if(isset($field['value'])) echo $field['value']; ?>" class="themify_field_icon <?php if( isset( $field['class'] ) ) echo $field['class']; ?> tfb_lb_option" type="text" <?php echo themify_builder_get_binding_data( $field ); ?> />
					<a class="button button-secondary hide-if-no-js themify_fa_toggle" href="#" data-target="#<?php echo $field['id']; ?>"><?php _e( 'Insert Icon', 'themify' ); ?></a>

					<?php elseif( 'radio' == $field['type'] ): ?>
					<?php
					$option_js = (isset($field['option_js']) && $field['option_js'] == true) ? 'tf-option-checkbox-js' : '';
					$option_js_wrap = (isset($field['option_js']) && $field['option_js'] == true) ? 'tf-option-checkbox-enable' : '';
					?>
						<div id="<?php echo $field['id']; ?>" class="tfb_lb_option tf-radio-input-container <?php echo $option_js_wrap; ?>">
							<?php foreach($field['options'] as $k => $v): ?>
							<?php
								$default_checked = (isset($field['default']) && $field['default'] == $k) ? 'checked="checked"' : '';
								$data_el = (isset($field['option_js']) && $field['option_js'] == true) ? 'data-selected="tf-group-element-'.$k.'"' : '';
							?>
							<input id="<?php echo $field['id'].'_'.$k; ?>" name="<?php echo $field['id']; ?>" type="radio" class="<?php echo $option_js; ?>" value="<?php echo $k; ?>" <?php echo $default_checked .' '.$data_el; ?>/>
							<label for="<?php echo $field['id'].'_'.$k; ?>" class="pad-right"><?php echo $v; ?></label> 
							
							<?php if( isset($field['break']) && $field['break'] == true ): ?>
							<br />
							<?php endif; ?>

							<?php endforeach; ?>
						</div>

					<?php elseif( 'layout' == $field['type'] ): ?>
					<p id="<?php echo $field['id']; ?>" class="layout_icon tfb_lb_option themify-layout-icon">
						<?php foreach($field['options'] as $option): ?>
						<a href="#" id="<?php echo $option['value']; ?>" title="<?php echo $option['label']; ?>" class="tfl-icon">
							<?php $image_url = ( filter_var( $option['img'], FILTER_VALIDATE_URL ) ) ? $option['img'] : THEMIFY_BUILDER_URI . '/img/builder/' . $option['img']; ?>
							<img src="<?php echo $image_url; ?>" alt="<?php echo $option['label']; ?>" />
						</a>
						<?php endforeach; ?>
					</p>

					<?php elseif( 'image' == $field['type'] ): ?>
					<input id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" placeholder="<?php if(isset($field['value'])) echo $field['value']; ?>" class="<?php echo $field['class']; ?> themify-builder-uploader-input tfb_lb_option" type="text" <?php echo themify_builder_get_binding_data( $field ); ?> /><br />
					
					<div class="small">

						<?php if ( is_multisite() && !is_upload_space_available() ): ?>
							<?php echo sprintf( __( 'Sorry, you have filled your %s MB storage quota so uploading has been disabled.', 'themify' ), get_space_allowed() ); ?>
						<?php else: ?>
						<div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="<?php echo $field['id']; ?>themify-builder-plupload-upload-ui">
								<input id="<?php echo $field['id']; ?>themify-builder-plupload-browse-button" type="button" value="<?php esc_attr_e(__('Upload', 'themify') ); ?>" class="builder_button" />
								<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($field['id'] . 'themify-builder-plupload'); ?>"></span>
						</div> <?php _e('or', 'themify') ?> <a href="#" class="themify-builder-media-uploader tf-upload-btn" data-uploader-title="<?php _e('Upload an Image', 'themify') ?>" data-uploader-button-text="<?php _e('Insert file URL', 'themify') ?>"><?php _e('Browse Library', 'themify') ?></a>

						<?php endif; ?>

					</div>
					
					<p class="thumb_preview">
						<span class="img-placeholder"></span>
						<a href="#" class="themify_builder_icon small delete themify-builder-delete-thumb"></a>
					</p>
					
					<?php elseif( 'checkbox' == $field['type'] ): ?>

						<div id="<?php echo $field['id']; ?>" class="tfb_lb_option themify-checkbox">
						<?php foreach( $field['options'] as $opt): ?>
							<?php
								$checkbox_checked = '';
								if( isset($field['default']) && is_array($field['default']) ) {
									$checkbox_checked = in_array($opt['name'], $field['default']) ? 'checked="checked"' : '';
								}
								elseif( isset($field['default']) ) {
									$checkbox_checked = checked( $field['default'], $opt['name'], false );
								}
							?>
							<input id="<?php echo $field['id'] . '_' . $opt['name']; ?>" name="<?php echo $field['id']; ?>[]" type="checkbox" class="tf-checkbox" value="<?php echo $opt['name']?>" <?php echo $checkbox_checked; ?> /> 
							<label for="<?php echo $field['id'] . '_' . $opt['name']; ?>" class="pad-right"><?php echo $opt['value']; ?></label>
							
							<?php if( isset($opt['help']) ): ?>
							<small><?php echo $opt['help']; ?></small>
							<?php endif; ?>
							
							<?php if( !isset($field['new_line']) || $field['new_line'] == true ): ?>
							<br />
							<?php endif; ?>

						<?php endforeach; ?>
						</div>

					<?php elseif( 'textarea' == $field['type'] ): ?>
					<textarea id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" class="<?php echo $field['class']; ?> tfb_lb_option" row="3" type="text" <?php echo themify_builder_get_binding_data( $field ); ?>></textarea>

					<?php elseif( 'select' == $field['type'] ): ?>
					
					<?php if( !isset($field['hide']) || $field['hide'] == false ): ?>
						<select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" class="tfb_lb_option" <?php echo themify_builder_get_binding_data( $field ); ?>>
							<?php if( isset($field['empty']) ): ?>
								<option value="<?php echo $field['empty']['val']; ?>"><?php echo $field['empty']['label']; ?></option>
							<?php endif; ?>
							
							<?php
							foreach ($field['options'] as $key => $value) {
								$selected = ( isset($field['default']) && $field['default'] == $value ) ? ' selected="selected"' : '';
								echo '<option value="' . $key . '" '.$selected.'>' . $value . '</option>';
							}
							?>
						</select>
					<?php endif; // isset hide ?>
					
					<?php if( isset($field['help']) ): ?>
					<br />
					<?php endif; // isset help ?>

					<?php elseif( 'selectbasic' == $field['type'] ): ?>
					<select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" class="tfb_lb_option" <?php echo themify_builder_get_binding_data( $field ); ?>>
						<?php
						foreach ($field['options'] as $value) {
							$selected = ( isset($field['default']) && $field['default'] == $value ) ? ' selected="selected"' : '';
							echo '<option value="' . $value . '" '.$selected.'>' . $value . '</option>';
						}
						?>
					</select>

					<?php elseif( 'select_menu' == $field['type'] ): ?>
					<select id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" class="tfb_lb_option select_menu_field" <?php echo themify_builder_get_binding_data( $field ); ?>>
						<option value=""><?php _e('Select a Menu...', 'themify') ?></option>
						<?php
						foreach ($field['options'] as $key => $value) {
							$selected = ( isset($field['default']) && $field['default'] == $value ) ? ' selected="selected"' : '';
							echo '<option value="' . $value->slug . '" '.$selected.' data-termid="'. $value->term_id .'">' . $value->name . '</option>';
						}
						?>
					</select>

					<?php elseif( 'query_category' == $field['type'] ): ?>
					<?php
						$terms_tax = isset($field['options']['taxonomy'])? $field['options']['taxonomy']: 'category';			
						$terms_options = '';
						$terms_by_tax = get_terms($terms_tax);
						$terms_list = array();
						$terms_list['0'] = array(
							'title' => __('All Categories', 'themify'),										
							'slug'	=> '0'
						);
						foreach ($terms_by_tax as $term) {
							$terms_list[$term->term_id] = array(
								'title' => $term->name,
								'slug'	=> $term->slug
							);
						}
						foreach ($terms_list as $term_id => $term) {
							$term_selected = '';
							$terms_options .= sprintf(
								'<option value="%s" data-termid="%s" %s>%s</option>',
								$term['slug'],
								$term_id,
								$term_selected,
								$term['title']
							);
						}
						?>
						<select id="<?php echo $field['id'].'_dropdown'; ?>" class="query_category_single" <?php echo themify_builder_get_binding_data( $field ); ?>>
							<option></option>
							<?php echo $terms_options; ?>
						</select>
					 <?php _e('or', 'themify') ?>
					<input class="small query_category_multiple" type="text" /><br /><small><?php _e('multiple category IDs (eg. 2,5,8) or slug (eg. news,blog,featured)', 'themify'); ?></small><br />
					<input type="hidden" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" value="" class="tfb_lb_option themify-option-query-cat" />

					<?php
					///////////////////////////////////////////
					// Query category single field
					///////////////////////////////////////////
					elseif( 'query_category_single' == $field['type'] ): ?>
					<?php
						echo preg_replace('/>/', '><option></option>',
						wp_dropdown_categories(
						array(
							'taxonomy' => isset($field['options']['taxonomy'])?$field['options']['taxonomy']: 'category', 
							'class' => 'tfb_lb_option',
							'show_option_all' => __('All Categories', 'themify'),
							'hide_empty' => 0,
							'echo' => 0,
							'name' => $field['id'],
							'selected' => ''
						)), 1);
						echo '<br />';
					?>

					<?php 
						///////////////////////////////////////////
						// Multifield
						///////////////////////////////////////////
						elseif( 'multifield' == $field['type'] ): ?>

						<?php if( isset($field['options']['select']) ): ?>
						<select id="<?php echo $field['options']['select']['id']; ?>" class="tfb_lb_option" <?php echo themify_builder_get_binding_data( $field ); ?>>
							<?php foreach( $field['options']['select']['options'] as $opt => $label ): ?>
							<option value="<?php echo $opt; ?>"><?php echo $label; ?></option>
							<?php endforeach; ?>
						</select>
						<?php endif; ?>
						
						<?php if( isset($field['options']['text']) ): ?>
						<input id="<?php echo $field['options']['text']['id']; ?>" class="xsmall tfb_lb_option" type="text" <?php echo themify_builder_get_binding_data( $field ); ?> /> 
							<?php if( isset($field['options']['text']['help']) ): ?>
							<small><?php echo $field['options']['text']['help']; ?></small>
							<?php endif; ?>
						<?php endif; ?>

						<?php if( isset($field['options']['colorpicker']) ): ?>
						<?php $color_class = isset($field['options']['colorpicker']['class']) ? $field['options']['colorpicker']['class'] : 'xsmall'; ?>
							<span class="builderColorSelect"><span></span></span> 
							<input id="<?php echo $field['options']['colorpicker']['id']; ?>" class="<?php echo $color_class; ?> tfb_lb_option builderColorSelectInput" type="text" />
						<?php endif; ?>

						<?php 
						///////////////////////////////////////////
						// Type Slider option
						///////////////////////////////////////////
						elseif( 'slider' == $field['type'] ):
						?>

						<?php foreach( $field['options'] as $fieldsec): ?>

						<?php if( $fieldsec['type'] == 'select' ): ?>
							<select id="<?php echo $fieldsec['id'] ?>" name="<?php echo $fieldsec['id'] ?>" class="tfb_lb_option" <?php echo themify_builder_get_binding_data( $field ); ?>>
								<?php if( isset($fieldsec['empty']) ): ?>
									<option value="<?php echo $fieldsec['empty']['val']; ?>"><?php echo $fieldsec['empty']['label']; ?></option>
								<?php endif; ?>
								
								<?php
								foreach ($fieldsec['options'] as $key => $value) {
									$selected = ( isset($fieldsec['default']) && $fieldsec['default'] == $value ) ? ' selected="selected"' : '';
									echo '<option value="' . $key . '" '.$selected.'>' . $value . '</option>';
								}
								?>
							</select>

						<?php elseif( $fieldsec['type'] == 'text' ): ?>
							<input id="<?php echo $fieldsec['id'] ?>" name="<?php echo $fieldsec['id'] ?>" placeholder="<?php if(isset($fieldsec['value'])) echo $fieldsec['value']; ?>" class="<?php echo $fieldsec['class']; ?> tfb_lb_option" type="text" />
							<?php echo (isset($fieldsec['unit'])) ? '<small>'.$fieldsec['unit'].'</small>' : ''; ?>
						<?php endif; ?>
						<?php echo (isset($fieldsec['help'])) ? $fieldsec['help'] : ''; ?><br />
						<?php endforeach; ?>
					<?php endif; ?>

					<?php
					// hook actions
					do_action( 'themify_builder_lightbox_fields', $field, $module_name );
					?>
					
					<?php if( isset($field['break']) && $field['break'] == true ): ?>
						<br />
					<?php endif; ?>
					
					<?php if(isset($field['help'])): ?>
					<small><?php echo $field['help']; ?></small>
					<?php endif; ?>
				</div>
				<!-- /themify_builder_input -->
				<?php } ?>
			
			<?php if( $field['type'] != 'builder' && (!isset($field['hide']) || $field['hide'] == false) ): ?>
			</div>
			<!-- /themify_builder_field -->
			<?php endif; ?>
		
		<?php if( isset( $field['separated'] ) && $field['separated'] == 'bottom' ): ?>
			<hr />
		<?php endif; endforeach;
	}
}

if ( ! function_exists( 'themify_builder_styling_field' ) ) {
	/**
	 * Module Styling Fields
	 * @param array $styling 
	 * @return string
	 */
	function themify_builder_styling_field( $styling ){
		switch ($styling['type']) {
			
			case 'text':
				$html = '<input id="'.$styling['id'].'" name="'.$styling['id'].'" type="text" value="" class="'.$styling['class'].' tfb_lb_option">';
				$html .= isset( $styling['description'] ) ? $styling['description'] : '';
				echo $html;
			break;

			case 'textarea':
				$html = '<textarea id="' . $styling['id'] . '" name="' . $styling['id'] . '" class="' . $styling['class'] . ' tfb_lb_option">' . $styling['value'] . '</textarea>';
				$html .= isset( $styling['description'] ) ? $styling['description'] : '';
				echo $html;
			break;

			case 'separator':
				echo $html = isset($styling['meta']['html']) && '' != $styling['meta']['html']? $styling['meta']['html'] : '<hr class="meta_fields_separator" />';
			break;

			case 'image': ?>
				<input id="<?php echo $styling['id']; ?>" name="<?php echo $styling['id'] ?>" placeholder="<?php if(isset($styling['value'])) echo $styling['value']; ?>" class="<?php echo $styling['class']; ?> themify-builder-uploader-input tfb_lb_option" type="text" /><br />
								
				<div class="small">

					<?php if ( is_multisite() && !is_upload_space_available() ): ?>
						<?php echo sprintf( __( 'Sorry, you have filled your %s MB storage quota so uploading has been disabled.', 'themify' ), get_space_allowed() ); ?>
					<?php else: ?>
					<div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="<?php echo $styling['id']; ?>themify-builder-plupload-upload-ui">
							<input id="<?php echo $styling['id']; ?>themify-builder-plupload-browse-button" type="button" value="<?php esc_attr_e(__('Upload', 'themify') ); ?>" class="builder_button" />
							<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($styling['id'] . 'themify-builder-plupload'); ?>"></span>
					</div> <?php _e('or', 'themify') ?> <a href="#" class="themify-builder-media-uploader tf-upload-btn" data-uploader-title="<?php _e('Upload an Image', 'themify') ?>" data-uploader-button-text="<?php _e('Insert file URL', 'themify') ?>"><?php _e('Browse Library', 'themify') ?></a>

					<?php endif; ?>

				</div>
				
				<p class="thumb_preview">
					<span class="img-placeholder"></span>
					<a href="#" class="themify_builder_icon small delete themify-builder-delete-thumb"></a>
				</p>


				<?php
			break;

			case 'video': ?>
				<input id="<?php echo $styling['id']; ?>" name="<?php echo $styling['id'] ?>" placeholder="<?php if(isset($styling['value'])) echo $styling['value']; ?>" class="<?php echo $styling['class']; ?> themify-builder-uploader-input tfb_lb_option" type="text" /><br />

				<div class="small">

					<?php if ( is_multisite() && !is_upload_space_available() ): ?>
						<?php echo sprintf( __( 'Sorry, you have filled your %s MB storage quota so uploading has been disabled.', 'themify' ), get_space_allowed() ); ?>
					<?php else: ?>
					<div class="themify-builder-plupload-upload-uic hide-if-no-js tf-upload-btn" id="<?php echo $styling['id']; ?>themify-builder-plupload-upload-ui" data-extensions="<?php echo esc_attr( implode( ',', wp_get_video_extensions() ) ); ?>">
						<input id="<?php echo $styling['id']; ?>themify-builder-plupload-browse-button" type="button" value="<?php esc_attr_e(__('Upload', 'themify') ); ?>" class="builder_button" />
						<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($styling['id'] . 'themify-builder-plupload'); ?>"></span>
					</div> <?php _e('or', 'themify') ?> <a href="#" class="themify-builder-media-uploader tf-upload-btn" data-uploader-title="<?php _e('Upload a Video', 'themify') ?>" data-uploader-button-text="<?php _e('Insert file URL', 'themify') ?>" data-library-type="video"><?php _e('Browse Library', 'themify') ?></a>

					<?php endif; ?>

				</div>

				<?php if ( isset( $styling['description'] ) ) {
					echo '<small>' . $styling['description'] . '</small>';
				} ?>

				<?php
			break;

			case 'select': ?>
				
				<select id="<?php echo $styling['id'] ?>" name="<?php echo $styling['id']; ?>" class="tfb_lb_option <?php echo isset( $styling['class'] ) ? $styling['class'] : ''; ?>">
					<?php if( isset( $styling['default'] ) ): ?>
					<option value="<?php echo $styling['default']; ?>"><?php echo $styling['default']; ?></option>
					<?php endif;

					foreach( $styling['meta'] as $option ): ?>
					<option value="<?php echo $option['value']; ?>"><?php echo $option['name']; ?></option>
					<?php endforeach; ?>

				</select>

				<?php if ( isset( $styling['description'] ) ) {
					echo $styling['description'];
				} ?>

			<?php
			break;

			case 'animation_select': ?>
				<?php $class = isset( $styling['class'] ) ? $styling['class'] : ''; ?>
				<select id="<?php echo $styling['id'] ?>" name="<?php echo $styling['id']; ?>" class="tfb_lb_option <?php echo $class; ?>">
					<option value=""></option>
					
					<?php
					$animation = Themify_Builder_model::get_preset_animation();
					foreach( $animation as $group ): ?>

						<optgroup label="<?php echo $group['group_label']; ?>">
							<?php foreach( $group['options'] as $opt ): ?>
								<option value="<?php echo $opt['value']; ?>"><?php echo $opt['name']; ?></option>
							<?php endforeach; ?>
						</optgroup>

					<?php endforeach; ?>

				</select>

				<?php if ( isset( $styling['description'] ) ) {
					echo $styling['description'];
				} ?>

			<?php
			break;

			case 'font_select':
			$fonts = array_merge( themify_get_web_safe_font_list(), themify_get_google_web_fonts_list() );
			 ?>
				
				<select id="<?php echo $styling['id'] ?>" name="<?php echo $styling['id']; ?>" class="tfb_lb_option <?php echo $styling['class']; ?>">
					<?php if( isset( $styling['default'] ) ): ?>
					<option value="<?php echo $styling['default']; ?>"><?php echo $styling['default']; ?></option>
					<?php endif;

					foreach( $fonts as $option ): ?>
					<option value="<?php echo $option['value']; ?>"><?php echo $option['name']; ?></option>
					<?php endforeach; ?>

				</select>

				<?php if ( isset( $styling['description'] ) ) {
					echo $styling['description'];
				} ?>

			<?php
			break;

			case 'color': ?>
				<span class="builderColorSelect"><span></span></span>
				<input type="text" class="<?php echo $styling['class']; ?> colordisplay"/>
				<input id="<?php echo $styling['id'] ?>" name="<?php echo $styling['id'] ?>" value="" class="builderColorSelectInput tfb_lb_option" type="hidden" />
			<?php
			break;

			case 'radio':
				$option_js = (isset($styling['option_js']) && $styling['option_js'] == true) ? 'tf-option-checkbox-js' : '';
				$option_js_wrap = (isset($styling['option_js']) && $styling['option_js'] == true) ? 'tf-option-checkbox-enable' : '';
				?>
				<div id="<?php echo $styling['id']; ?>" class="tfb_lb_option tf-radio-input-container <?php echo $option_js_wrap; ?>">
				<?php
				foreach( $styling['meta'] as $option ) {
					$checked = isset( $option['selected'] ) && $option['selected'] == true ? 'checked="checked"' : '';
					$data_el = (isset($styling['option_js']) && $styling['option_js'] == true) ? 'data-selected="tf-group-element-'.$option['value'].'"' : '';
					?>
					<input type="radio" id="<?php echo $styling['id'] . '_' . $option['value']; ?>" name="<?php echo $styling['id']; ?>" value="<?php echo $option['value']; ?>" class="tfb_lb_option <?php echo $option_js; ?>" <?php echo $checked . ' ' . $data_el; ?>> <label for="<?php echo $styling['id'] . '_' . $option['value']; ?>"><?php echo $option['name']; ?></label>
				<?php
				}
				?>
					<?php if ( isset( $styling['description'] ) ) {
						echo '<br/><small>' . $styling['description'] . '</small>';
					} ?>
				</div>
				<?php

			break;
		}
	}
}