# headless-wp
A Headless Wordpress theme compatible with ACF and WPML

## style.css
Only necessary for theme's basic information.
```
/*
Theme Name: Headless WP Theme
Description: Headless WP Theme compatible with ACF and WPML
Author: Pere Esteve @hitaboy
Version: 1.0
*/
```

## index.php
Meant to don't execute any wp or ph function. Just a plain html.
```
<html>
  <head>
    <title>Headless WP</title>
  </head>
  <body>
    <h1>Headless WP Theme</h1>
  </body>
</html>

```
## functions.php

### Disable Gutenberg Editor
```
add_filter('use_block_editor_for_post', '__return_false', 10);
```

Wpautop is the name of the function that automatically encloses double-line breaks with a <p> and </ p>. WordPress uses this feature in all post types (posts, pages and custom post types)- for both content and shortcode generated texts. Hence, some of the editing jobs such as working with a shortcode-driven grid system in the WYSIWYG editor can be quite problematic.
The wpautop filter can be disabled with this line.
```
remove_filter( 'the_content', 'wpautop' );
```

### Adding ACF fields to the WP REST API
If using ACF plugin and having the need to get the fields trought the WP REST API, use this code that automatically expose all the ACF fields to the Wordpress REST API in Pages and in your custom post types.
```
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
```

### Custom GET endpoint
Add custom GET endpoints to the WP REST API
```
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
```

### Enabled SVG Support for WordPress
You’ll be able to upload SVG images to your Media Library. There, you can view and interact with them just like with other image file types.
```
function cc_mime_types( $mimes ){
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );
```
