<?php

function kyxucf_setup() {
	
	// Reister the Menus
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation' ),
	) );

}

add_action( 'after_setup_theme', 'kyxucf_setup' );

function kyxucf_enqueue_scripts() {
	wp_enqueue_style( 'modularity-lite', get_template_directory_uri() .'/style.css' );
	wp_enqueue_style( 'kyxucf', get_stylesheet_directory_uri() .'/css/kyxucf.css' );
}
add_action( 'wp_enqueue_scripts', 'kyxucf_enqueue_scripts' );
