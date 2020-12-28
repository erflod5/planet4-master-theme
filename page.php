<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

/**
 * Category : Issue
 * Tag      : Campaign
 * Post     : Action
 */

use P4\MasterTheme\Context;
use P4\MasterTheme\Post;
use Timber\Timber;

$context        = Timber::get_context();
$post           = new Post(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$page_meta_data = get_post_meta( $post->ID );
$page_meta_data = array_map( 'reset', $page_meta_data );

// Set Navigation Issues links.
$post->set_issues_links();

// Get Navigation Campaigns links.
$page_tags = wp_get_post_tags( $post->ID );
$tags      = [];

if ( is_array( $page_tags ) && $page_tags ) {
	foreach ( $page_tags as $page_tag ) {
		$tags[] = [
			'name' => $page_tag->name,
			'link' => get_tag_link( $page_tag ),
		];
	}
	$context['campaigns'] = $tags;
}

// Set GTM Data Layer values.
$post->set_data_layer();
$data_layer = $post->get_data_layer();

Context::set_header( $context, $page_meta_data, $post->title );
Context::set_background_image( $context );
Context::set_og_meta_fields( $context, $post );
Context::set_campaign_datalayer( $context, $page_meta_data );

$context['post']                = $post;
$context['social_accounts']     = $post->get_social_accounts( $context['footer_social_menu'] );
$context['page_category']       = $data_layer['page_category'];
$context['post_tags']           = implode( ', ', $post->tags() );
$context['custom_body_classes'] = 'brown-bg ';

if ( post_password_required( $post->ID ) ) {

	// Check if page url has a unique id(custom hash), appended with it, if not add one.
	$custom_hash = filter_input( INPUT_GET, 'ch', FILTER_SANITIZE_STRING );
	if ( ! $custom_hash ) {
		wp_safe_redirect( add_query_arg( 'ch', md5( uniqid( '', true ) ), get_permalink() ) );
		exit();
	}

	/**
	 * Password protected form validation:
	 * The latest entered password is stored as a secure hash in a cookie named 'wp-postpass_' . COOKIEHASH.
	 * When the password form is called, that cookie has been validated already by WordPress.
	 */
	if ( isset( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] ) ) {
		$old_cookie = get_transient( 'p4-postpass_' . $custom_hash );
		if ( false === $old_cookie ) {
			// This code runs when there is no valid transient set.
			$current_cookie = wp_unslash( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
			set_transient( 'p4-postpass_' . $custom_hash, $current_cookie, $expiration = 60 * 5 ); // Transient cache expires in 5 mins.
		} else {
			$current_cookie = wp_unslash( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
			set_transient( 'p4-postpass_' . $custom_hash, $current_cookie, $expiration = 60 * 5 );
			if ( $current_cookie !== $old_cookie ) {
				$context['validation_error'] = __( 'Sorry, Invalide password.', 'planet4-master-theme' );
			}
		}
	}

	// Hide the page title from links to the extra feeds.
	remove_action( 'wp_head', 'feed_links_extra', 3 );

	$context['login_url'] = wp_login_url();

	Timber::render( 'single-page.twig', $context );
} else {
	Timber::render( [ 'page-' . $post->post_name . '.twig', 'page.twig' ], $context );
}
