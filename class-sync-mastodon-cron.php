<?php

namespace SyncMastodon;

use SyncMastodon\Sync_Mastodon_Core;
use SyncMastodon\Sync_Mastodon_Options;

class Sync_Mastodon_Cron {

	public $hook_name = 'sync_mastodon_cron_hook';

	public function __construct() {
		add_filter( 'cron_schedules', [ $this, 'sync_mastodon_cron_interval' ] );
		add_action( $this->hook_name, [ $this, 'sync' ] );

		if ( ! wp_next_scheduled( $this->hook_name ) ) {
 			wp_schedule_event( time(), 'five_minutes', $this->hook_name );
		}

		add_action( 'sync_mastodon_deactivate', [ $this, 'remove_cron' ] );
 	}

	public function sync_mastodon_cron_interval( $schedules ) {
    	$schedules['five_minutes'] = [
        	'interval' => 5 * 60,
        	'display'  => esc_html__( 'Every Five Minutes' ),
    	];

    	return $schedules;
    }

	public function remove_cron() {
		echo "Removing cron";
		$timestamp = wp_next_scheduled( $this->hook_name );
   		wp_unschedule_event( $timestamp, $this->hook_name );
	}

    public function sync() {
    	if (0 == Sync_Mastodon_Options::get_post_sync_status()) {
    		return;
    	}

    	$core = new Sync_Mastodon_Core();
    	$core->sync();
    }

    public function next_sync_time() {
    	$timestamp = wp_next_scheduled( $this->hook_name );
    	// There MUST be a WP function to format a time and take into account the offset, but I can't find it.
    	return date_i18n('H:i:s', $timestamp + (get_option('gmt_offset') * 60 * 60));
    }

}
