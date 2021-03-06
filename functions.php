<?php

/*
DISABLE GUTENBERG
Disable Gutenberg with Code
*/
add_filter('use_block_editor_for_post', '__return_false', 10);

/*
DISABLE WPAUTOP
Wpautop is the name of the function that automatically encloses double-line breaks with a <p> and </ p>. WordPress uses this feature in all post types (posts, pages and custom post types)- for both content and shortcode generated texts. Hence, some of the editing jobs such as working with a shortcode-driven grid system in the WYSIWYG editor can be quite problematic.
The wpautop filter can be disabled with this line.
*/
remove_filter( 'the_content', 'wpautop' );

/*
ACF FIELDS TO WP REST API
Automatically expose all the ACF fields to the Wordpress REST API in Pages and in your custom post types.
*/

function create_ACF_meta_in_REST() {
    $postypes_to_exclude = ['acf-field-group','acf-field'];
    $extra_postypes_to_include = ["page"];
    $post_types = array_diff(get_post_types(["_builtin" => false], 'names'),$postypes_to_exclude);

    array_push($post_types, $extra_postypes_to_include);

    foreach ($post_types as $post_type) {
        register_rest_field( $post_type, 'ACF', [
            'get_callback'    => 'expose_ACF_fields',
            'schema'          => null,
       ]
     );
    }

}

function expose_ACF_fields( $object ) {
    $ID = $object['id'];
    return get_fields($ID);
}

add_action( 'rest_api_init', 'create_ACF_meta_in_REST' );

/*
Custom GET endpoint
Add custom GET endpoints to the WP REST API
*/

add_action( 'rest_api_init', function() {
  register_rest_route( 'my/v1', '/projects', [
    'methods' => 'GET',
    'callback' => 'get_projects',
  ] );
} );

// Get recent projects
function get_projects( $params ) {
  $projects =  [
    'project_1' => 'project_1',
    'project_2' => 'project_2'
  ];

  return $projects;
}

/*
Allow SVG
Enable your WordPress site to accept SVG files
*/
add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

  global $wp_version;
  if ( $wp_version !== '4.7.1' ) {
     return $data;
  }

  $filetype = wp_check_filetype( $filename, $mimes );

  return [
      'ext'             => $filetype['ext'],
      'type'            => $filetype['type'],
      'proper_filename' => $data['proper_filename']
  ];

}, 10, 4 );

function cc_mime_types( $mimes ){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

function fix_svg() {
  echo '<style type="text/css">
        .attachment-266x266, .thumbnail img {
             width: 100% !important;
             height: auto !important;
        }
        </style>';
}
add_action( 'admin_head', 'fix_svg' );

function my_correct_filetypes( $data, $file, $filename, $mimes, $real_mime ) {

    if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
      return $data;
    }

    $wp_file_type = wp_check_filetype( $filename, $mimes );

  	// Check for the file type you want to enable, e.g. 'svg'.
    if ( 'svg' === $wp_file_type['ext'] ) {
      $data['ext']  = 'svg';
      $data['type'] = 'image/svg+xml';
    }

    return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'my_correct_filetypes', 10, 5 );

?>
