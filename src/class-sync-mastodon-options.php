<?php
/**
 * Mastodon Sync Options
 *
 * @package SyncMastodon;
 */

namespace SyncMastodon;

class Sync_Mastodon_Options
{

    public static function get_rss_url()
    {
        return get_option('sync-mastodon-rss-url');
    }

    public static function set_rss_url( $value )
    {
        return update_option('sync-mastodon-rss-url', $value);
    }

    public static function get_post_author()
    {
        return get_option('sync-mastodon-author');
    }

    public static function set_post_author( $value )
    {
        return update_option('sync-mastodon-author', $value);
    }

    public static function get_post_type()
    {
        $type = get_option('sync-mastodon-post-type');
        return $type ? $type : 'mastodon-post';
    }

    public static function set_post_type( $value )
    {
        return update_option('sync-mastodon-post-type', $value);
    }

    public static function get_post_sync_status()
    {
        return (int)get_option('sync-mastodon-status');
    }

    public static function set_post_sync_status( $value )
    {
        return update_option('sync-mastodon-status', $value);
    }

}
