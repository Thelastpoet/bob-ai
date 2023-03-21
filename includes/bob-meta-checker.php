<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @package Bob_SEO_Optimizer
 */
class Bob_Meta_Checker {

	/**
	 * Returns the active SEO plugin's metaa key for a post.
	 *
	 * @param int $post_id The ID of the post to get the meta key for.
	 * @return string The meta key.
	 */
	public static function get_meta_key( $post_id ) {
		$seo_plugin_option = get_option( 'bob_seo_optimizer_seo_plugin', 'yoast_seo' );

		// Map the selected SEO plugin option to the corresponding meta key.
		switch ( $seo_plugin_option ) {
			case 'yoast_seo':
				$meta_key = '_yoast_wpseo_metadesc';
				break;
			case 'rank_math':
				$meta_key = 'rank_math_description';
				break;
			case 'seopress':
				$meta_key = '_seopress_titles_desc';
				break;
			case 'the_seo_framework':
				$meta_key = '_genesis_description';
				break;
			case 'all_in_one_seo':
				$meta_key = '_aioseop_description';
				break;
			default:
				$meta_key = '';
				break;
		}

		return $meta_key;
	}
}