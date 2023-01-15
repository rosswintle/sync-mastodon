<?php

/**
 * Registers the `mastodon_tag` taxonomy,
 * for use with 'mastodon-post'.
 */
function mastodon_tag_init() {
	register_taxonomy(
		'mastodon-tag', [ 'mastodon-post' ], [
			'hierarchical'      => false,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => true,
			'capabilities'      => [
				'manage_terms'  => 'edit_posts',
				'edit_terms'    => 'edit_posts',
				'delete_terms'  => 'edit_posts',
				'assign_terms'  => 'edit_posts',
			],
			'labels'            => [
				'name'                       => __( 'Mastodon Tags', 'sync-mastodon' ),
				'singular_name'              => _x( 'Mastodon Tag', 'taxonomy general name', 'sync-mastodon' ),
				'search_items'               => __( 'Search Mastodon Tags', 'sync-mastodon' ),
				'popular_items'              => __( 'Popular Mastodon Tags', 'sync-mastodon' ),
				'all_items'                  => __( 'All Mastodon Tags', 'sync-mastodon' ),
				'parent_item'                => __( 'Parent Mastodon Tag', 'sync-mastodon' ),
				'parent_item_colon'          => __( 'Parent Mastodon Tag:', 'sync-mastodon' ),
				'edit_item'                  => __( 'Edit Mastodon Tag', 'sync-mastodon' ),
				'update_item'                => __( 'Update Mastodon Tag', 'sync-mastodon' ),
				'view_item'                  => __( 'View Mastodon Tag', 'sync-mastodon' ),
				'add_new_item'               => __( 'Add New Mastodon Tag', 'sync-mastodon' ),
				'new_item_name'              => __( 'New Mastodon Tag', 'sync-mastodon' ),
				'separate_items_with_commas' => __( 'Separate Mastodon Tags with commas', 'sync-mastodon' ),
				'add_or_remove_items'        => __( 'Add or remove Mastodon Tags', 'sync-mastodon' ),
				'choose_from_most_used'      => __( 'Choose from the most used Mastodon Tags', 'sync-mastodon' ),
				'not_found'                  => __( 'No Mastodon Tags found.', 'sync-mastodon' ),
				'no_terms'                   => __( 'No Mastodon Tags', 'sync-mastodon' ),
				'menu_name'                  => __( 'Mastodon Tags', 'sync-mastodon' ),
				'items_list_navigation'      => __( 'Mastodon Tags list navigation', 'sync-mastodon' ),
				'items_list'                 => __( 'Mastodon Tags list', 'sync-mastodon' ),
				'most_used'                  => _x( 'Most Used', 'mastodon-tag', 'sync-mastodon' ),
				'back_to_items'              => __( '&larr; Back to Mastodon Tags', 'sync-mastodon' ),
			],
			'show_in_rest'      => true,
			'rest_base'         => 'mastodon-tag',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		]
	);

}
add_action( 'init', 'mastodon_tag_init' );

/**
 * Sets the post updated messages for the `mastodon_tag` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `mastodon_tag` taxonomy.
 */
function mastodon_tag_updated_messages( $messages ) {
	$messages['mastodon-tag'] = [
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Mastodon Tag added.', 'sync-mastodon' ),
		2 => __( 'Mastodon Tag deleted.', 'sync-mastodon' ),
		3 => __( 'Mastodon Tag updated.', 'sync-mastodon' ),
		4 => __( 'Mastodon Tag not added.', 'sync-mastodon' ),
		5 => __( 'Mastodon Tag not updated.', 'sync-mastodon' ),
		6 => __( 'Mastodon Tags deleted.', 'sync-mastodon' ),
	];

	return $messages;
}
add_filter( 'term_updated_messages', 'mastodon_tag_updated_messages' );
