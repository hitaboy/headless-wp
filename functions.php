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


?>
