<?php
use SyncMastodon\Sync_Mastodon_Options;

if (Sync_Mastodon_Options::get_post_type() !== 'mastodon-post') {
    return;
}

/**
 * Registers the `mastodon_post` post type.
 */
function mastodon_post_init()
{
    $mastodon_post_post_type_options = array(
    'labels'                => array(
    'name'                  => __('Mastodon Posts', 'sync-mastodon'),
    'singular_name'         => __('Mastodon Post', 'sync-mastodon'),
    'all_items'             => __('All Mastodon Posts', 'sync-mastodon'),
    'archives'              => __('Mastodon Post Archives', 'sync-mastodon'),
    'attributes'            => __('Mastodon Post Attributes', 'sync-mastodon'),
    'insert_into_item'      => __('Insert into Mastodon Post', 'sync-mastodon'),
    'uploaded_to_this_item' => __('Uploaded to this Mastodon Post', 'sync-mastodon'),
    'featured_image'        => _x('Featured Image', Sync_Mastodon_Options::get_post_type(), 'sync-mastodon'),
    'set_featured_image'    => _x('Set featured image', Sync_Mastodon_Options::get_post_type(), 'sync-mastodon'),
    'remove_featured_image' => _x('Remove featured image', Sync_Mastodon_Options::get_post_type(), 'sync-mastodon'),
    'use_featured_image'    => _x('Use as featured image', Sync_Mastodon_Options::get_post_type(), 'sync-mastodon'),
    'filter_items_list'     => __('Filter Mastodon Posts list', 'sync-mastodon'),
    'items_list_navigation' => __('Mastodon Posts list navigation', 'sync-mastodon'),
    'items_list'            => __('Mastodon Posts list', 'sync-mastodon'),
    'new_item'              => __('New Mastodon Post', 'sync-mastodon'),
    'add_new'               => __('Add New', 'sync-mastodon'),
    'add_new_item'          => __('Add New Mastodon Post', 'sync-mastodon'),
    'edit_item'             => __('Edit Mastodon Post', 'sync-mastodon'),
    'view_item'             => __('View Mastodon Post', 'sync-mastodon'),
    'view_items'            => __('View Mastodon Posts', 'sync-mastodon'),
    'search_items'          => __('Search Mastodon Posts', 'sync-mastodon'),
    'not_found'             => __('No Mastodon Posts found', 'sync-mastodon'),
    'not_found_in_trash'    => __('No Mastodon Posts found in trash', 'sync-mastodon'),
    'parent_item_colon'     => __('Parent Mastodon Post:', 'sync-mastodon'),
    'menu_name'             => __('Mastodon Posts', 'sync-mastodon'),
    ),
    'public'                => true,
    'hierarchical'          => false,
    'show_ui'               => true,
    'show_in_nav_menus'     => true,
    'supports'              => array( 'title', 'editor', 'author', 'custom-fields', 'excerpt' ),
    'has_archive'           => true,
    'rewrite'               => true,
    'exclude_from_search'   => true,
    'query_var'             => true,
    'menu_position'         => null,
    'menu_icon'             => 'dashicons-pressthis',
    'show_in_rest'          => true,
    'rest_base'             => 'mastodon-post',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    );

    $mastodon_post_post_type_options = apply_filters('sync-mastodon-post-post-type-options', $mastodon_post_post_type_options);

    register_post_type('mastodon-post', $mastodon_post_post_type_options);

}
add_action('init', 'mastodon_post_init');

/**
 * Sets the post updated messages for the `mastodon_post` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `mastodon_post` post type.
 */
function mastodon_post_updated_messages( $messages )
{
    global $post;

    $permalink = get_permalink($post);

    $messages[Sync_Mastodon_Options::get_post_type()] = array(
    0  => '', // Unused. Messages start at index 1.
    /* translators: %s: post permalink */
    1  => sprintf(__('Mastodon Post updated. <a target="_blank" href="%s">View Mastodon Post</a>', 'sync-mastodon'), esc_url($permalink)),
    2  => __('Custom field updated.', 'sync-mastodon'),
    3  => __('Custom field deleted.', 'sync-mastodon'),
    4  => __('Mastodon Post updated.', 'sync-mastodon'),
    /* translators: %s: date and time of the revision */
    5  => isset($_GET['revision']) ? sprintf(__('Mastodon Post restored to revision from %s', 'sync-mastodon'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
    /* translators: %s: post permalink */
    6  => sprintf(__('Mastodon Post published. <a href="%s">View Mastodon Post</a>', 'sync-mastodon'), esc_url($permalink)),
    7  => __('Mastodon Post saved.', 'sync-mastodon'),
    /* translators: %s: post permalink */
    8  => sprintf(__('Mastodon Post submitted. <a target="_blank" href="%s">Preview Mastodon Post</a>', 'sync-mastodon'), esc_url(add_query_arg('preview', 'true', $permalink))),
    /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
    9  => sprintf(
        __('Mastodon Post scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Mastodon Post</a>', 'sync-mastodon'),
        date_i18n(__('M j, Y @ G:i', 'sync-mastodon'), strtotime($post->post_date)), esc_url($permalink) 
    ),
    /* translators: %s: post permalink */
    10 => sprintf(__('Mastodon Post draft updated. <a target="_blank" href="%s">Preview Mastodon Post</a>', 'sync-mastodon'), esc_url(add_query_arg('preview', 'true', $permalink))),
    );

    return $messages;
}
add_filter('post_updated_messages', 'mastodon_post_updated_messages');
