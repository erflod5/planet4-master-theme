<?php

if ( ! class_exists( 'P4_Sitemap' ) ) {

	/**
	 * Class P4_Sitemap
	 */
	class P4_Sitemap {

		/**
		 * Gets data for the Action pages.
		 *
		 * @return array
		 */
		public function get_actions() : array {
			$options   = get_option( 'planet4_options' );
			$parent_id = $options['act_page'];
			$actions   = [];

			if ( 0 !== absint( $parent_id ) ) {
				$args = [
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_parent' => $parent_id,
					'orderby'     => 'post_title',
					'order'       => 'ASC',
					'numberposts' => -1,
				];
				$actions = get_posts( $args );
			}

			if ( is_array( $actions ) && $actions ) {
				foreach ( $actions as $action ) {
					$actions_data[] = [
						'title' => $action->post_title,
						'link'  => get_permalink( $action->ID ),
					];
				}
			}

			return $actions_data;
		}

		/**
		 * Gets data for the Issue pages.
		 *
		 * @return array
		 */
		public function get_issues() : array {
			$options   = get_option( 'planet4_options' );
			$parent_id = $options['explore_page'];
			$issues    = [];

			if ( 0 !== absint( $parent_id ) ) {
				$args = [
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_parent' => $parent_id,
					'orderby'     => 'post_title',
					'order'       => 'ASC',
					'numberposts' => -1,
				];
				$issues = get_posts( $args );
			}

			if ( is_array( $issues ) && $issues ) {
				foreach ( $issues as $issue ) {

					// Get campaigns for this issue.
					$page_tags = wp_get_post_tags( $issue->ID );
					$tags      = [];

					if ( is_array( $page_tags ) && $page_tags ) {
						foreach ( $page_tags as $page_tag ) {
							$tags[] = [
								'name' => $page_tag->name,
								'link' => get_tag_link( $page_tag ),
							];
						}
					}

					$issues_data[] = [
						'title'     => $issue->post_title,
						'link'      => get_permalink( $issue->ID ),
						'campaigns' => $tags,
					];
				}
			}

			return $issues_data;
		}

		/**
		 * Gets data for the Evergreen pages.
		 *
		 * @return array
		 */
		public function get_evergreen_pages() : array {

			$args = [
				'post_type'   => 'page',
				'post_status' => 'publish',
				'meta_key'    => '_wp_page_template',
				'meta_value'  => 'page-templates/evergreen.php',
			];
			$pages = get_posts( $args );

			if ( is_array( $pages ) && $pages ) {
				foreach ( $pages as $page ) {
					$evergreen_data[] = [
						'title' => $page->post_title,
						'link'  => get_permalink( $page->ID ),
					];
				}
			}

			return $evergreen_data;
		}

		/**
		 * Gets data for the custom article types.
		 *
		 * @return array
		 */
		public function get_page_types() : array {

			$article_types = get_terms(
				[
					'hide_empty' => false,
					'orderby'    => 'name',
					'taxonomy'   => 'p4-page-type',
				]
			);

			if ( is_array( $article_types ) && $article_types ) {
				foreach ( $article_types as $article_type ) {
					$article_types_data[] = [
						'name' => $article_type->name,
						'link' => get_site_url() . '/?s=&orderby=post_date&f[ptype][' . rawurlencode( $article_type->name ) . ']=' . $article_type->term_id,
					];
				}
			}

			return $article_types_data;
		}
	}
}