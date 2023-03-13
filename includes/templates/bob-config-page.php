<div class="wrap">
	<h1><?php esc_html_e( 'Bob Settings', 'bob' ); ?></h1>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=bob-settings' ) ); ?>">
		<?php wp_nonce_field( 'bob-settings-group', 'bob-settings-nonce' ); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'API Key:', 'bob' ); ?></th>
					<td><input type="text" name="bob-openai-api-key" value="<?php echo esc_attr( $openai_api_key ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Model:', 'bob' ); ?></th>
					<td><input type="text" name="bob-openai-model" value="<?php echo esc_attr( $openai_model ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Select your preferred SEO plugin:', 'bob' ); ?></th>
					<td>
						<select name="bob_seo_optimizer_seo_plugin">
							<?php foreach ( $seo_plugin_options as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $selected_seo_plugin ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button( esc_html__( 'Save Settings', 'bob' ), 'primary', 'submit' ); ?>
	</form>
</div>