=== Sync Mastodon ===
Contributors: magicroundabout
Tags: mastodon, sync, fediverse
Requires at least: 5.1
Tested up to: 6.1
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Fetch posts from a Mastodon RSS feed and add them to a custom post type

== Description ==

This plugin syncs Mastodon posts from a Mastodon RSS feed into a custom post type.

This plugin:

* runs automatically using either wp-cron or manually using [wp-cli](https://wp-cli.org/)
* uses Mastodon RSS feeds and needs no authentication
* rates-limits fetches
* allows you to choose an author for synced posts

Note that this plugin does a one-way sync from Mastodon to your WordPress install. You can add your own posts in WordPress but they will not be cross-posted to Mastodon.

= Instructions =

Once you have installed the plugin you will need to go to Settings -> Mastodon Sync and enter your RSS feed URL

If you want to do automatic sync then you can then also turn on the Auto-sync option.

= WP-CLI command =

If you can use [WP-CLI](https://wp-cli.org/) then you can make use of the `wp-cli sync-mastodon` command to
do an import from Mastodon. This works particularly well for large first-time imports before you enable the automatic sync. But you could also use the system cron to run this command instead of WP cron.

= Wish list / Roadmap =

Things I have in mind for future development:

* Better front-end validation in admin screens and meta boxes
* Better error logging, and logging in general, including WP-CLI-specific output

== Installation ==

Once you have installed and activated the plugin, follow the instructions in the description.

== Screenshots ==

1. Options screen

== Changelog ==

= 1.2.1 =
* Fix RSS feed caching

= 1.2.0 =
* Changes sync interval to five minutes

= 1.1.0 =
* Adds ability to select a post type to sync to

= 1.0.0 =
* Initial version based on [Sync Pinboard](https://wordpress.org/plugins/sync-pinboard/)

== Upgrade Notice ==
