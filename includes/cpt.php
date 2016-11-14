<?php

	/**
	 * Add custom post type for forms
	 */
	function gmt_donations_add_custom_post_type_forms() {

		$labels = array(
			'name'               => _x( 'Donation Forms', 'post type general name', 'gmt_donations' ),
			'singular_name'      => _x( 'Donation Form', 'post type singular name', 'gmt_donations' ),
			'add_new'            => _x( 'Add New', 'keel-pets', 'gmt_donations' ),
			'add_new_item'       => __( 'Add New Form', 'gmt_donations' ),
			'edit_item'          => __( 'Edit Form', 'gmt_donations' ),
			'new_item'           => __( 'New Form', 'gmt_donations' ),
			'all_items'          => __( 'All Forms', 'gmt_donations' ),
			'view_item'          => __( 'View Form', 'gmt_donations' ),
			'search_items'       => __( 'Search Forms', 'gmt_donations' ),
			'not_found'          => __( 'No forms found', 'gmt_donations' ),
			'not_found_in_trash' => __( 'No forms found in the Trash', 'gmt_donations' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Donations', 'gmt_donations' ),
		);
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our donation forms and form-specific data',
			'public'        => true,
			// 'menu_position' => 5,
			'menu_icon'     => 'dashicons-money',
			'hierarchical'  => false,
			'supports'      => array(
				'title',
				// 'editor',
				// 'thumbnail',
				'excerpt',
				'revisions',
				// 'page-attributes',
			),
			'has_archive'   => false,
			// 'rewrite' => array(
			// 	'slug' => 'courses',
			// ),
			// 'map_meta_cap'  => true,
			// 'capabilities' => array(
			// 	'create_posts' => false,
			// 	'edit_published_posts' => false,
			// 	'delete_posts' => false,
			// 	'delete_published_posts' => false,
			// )
		);
		register_post_type( 'gmt_donation_forms', $args );
	}
	add_action( 'init', 'gmt_donations_add_custom_post_type_forms' );



	/**
	 * Add custom post type for donations
	 */
	function gmt_donations_add_custom_post_type_donations() {

		$labels = array(
			'name'               => _x( 'Donations', 'post type general name', 'gmt_donations' ),
			'singular_name'      => _x( 'Donation', 'post type singular name', 'gmt_donations' ),
			'add_new'            => _x( 'Add New', 'keel-pets', 'gmt_donations' ),
			'add_new_item'       => __( 'Add New Donation', 'gmt_donations' ),
			'edit_item'          => __( 'Edit Donation', 'gmt_donations' ),
			'new_item'           => __( 'New Donation', 'gmt_donations' ),
			'all_items'          => __( 'Donations', 'gmt_donations' ),
			'view_item'          => __( 'View Donation', 'gmt_donations' ),
			'search_items'       => __( 'Search Donations', 'gmt_donations' ),
			'not_found'          => __( 'No donations found', 'gmt_donations' ),
			'not_found_in_trash' => __( 'No donations found in the Trash', 'gmt_donations' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Donations', 'gmt_donations' ),
		);
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our donation data',
			'public'        => true,
			// 'menu_position' => 5,
			'menu_icon'     => 'dashicons-money',
			'hierarchical'  => false,
			'supports'      => array(
				// 'title',
				// 'editor',
				// 'thumbnail',
				// 'excerpt',
				'revisions',
				// 'page-attributes',
			),
			'has_archive'   => false,
			'show_in_menu'  => 'edit.php?post_type=gmt_donation_forms',
			// 'rewrite' => array(
			// 	'slug' => 'courses',
			// ),
			'map_meta_cap'  => true,
			'capabilities' => array(
				'create_posts' => false,
				// 'edit_published_posts' => false,
				// 'delete_posts' => false,
				// 'delete_published_posts' => false,
			)
		);
		register_post_type( 'gmt_donations', $args );
	}
	add_action( 'init', 'gmt_donations_add_custom_post_type_donations' );



	/**
	 * Add custom post type for donors
	 */
	function gmt_donations_add_custom_post_type_donors() {

		$labels = array(
			'name'               => _x( 'Donors', 'post type general name', 'gmt_donations' ),
			'singular_name'      => _x( 'Donor', 'post type singular name', 'gmt_donations' ),
			'add_new'            => _x( 'Add New', 'keel-pets', 'gmt_donations' ),
			'add_new_item'       => __( 'Add New Donor', 'gmt_donations' ),
			'edit_item'          => __( 'Edit Donor', 'gmt_donations' ),
			'new_item'           => __( 'New Donor', 'gmt_donations' ),
			'all_items'          => __( 'Donors', 'gmt_donations' ),
			'view_item'          => __( 'View Donor', 'gmt_donations' ),
			'search_items'       => __( 'Search Donors', 'gmt_donations' ),
			'not_found'          => __( 'No donors found', 'gmt_donations' ),
			'not_found_in_trash' => __( 'No donors found in the Trash', 'gmt_donations' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Donors', 'gmt_donations' ),
		);
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our donor data',
			'public'        => true,
			// 'menu_position' => 5,
			'menu_icon'     => 'dashicons-money',
			'hierarchical'  => false,
			'supports'      => array(
				// 'title',
				// 'editor',
				// 'thumbnail',
				// 'excerpt',
				'revisions',
				// 'page-attributes',
			),
			'has_archive'   => false,
			'show_in_menu'  => 'edit.php?post_type=gmt_donation_forms',
			// 'rewrite' => array(
			// 	'slug' => 'courses',
			// ),
			'map_meta_cap'  => true,
			'map_meta_cap'  => true,
			'capabilities' => array(
				'create_posts' => false,
				// 'edit_published_posts' => false,
				// 'delete_posts' => false,
				// 'delete_published_posts' => false,
			)
		);
		register_post_type( 'gmt_donors', $args );
	}
	add_action( 'init', 'gmt_donations_add_custom_post_type_donors' );