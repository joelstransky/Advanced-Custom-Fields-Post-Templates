<?php
/**
 * Include a Post Template rule type
 */
function acf_post_template_rule_type( $rule_types ) {
  if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
      return $rule_types;
  }
  $rule_types['Post']['post_template'] =  __("Post Template",'acf');
  return $rule_types;
}
add_filter('acf/location/rule_types', 'acf_post_template_rule_type', 10, 1);

/**
 * Supply values for the Post Template rule type
 */
function acf_post_template_rule_values ( $choices ) {
  if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
      return $choices;
  }
  $args = array('public' => true, 'capability_type' => 'post');
  $post_types = get_post_types( $args );
  $post_templates = array( 'none' => 'None' );
  foreach ($post_types as $key => $post_type) {
    $post_templates = array_merge($post_templates, get_page_templates(null, $post_type) );
  }
  foreach( $post_templates as $k => $v ) {
    $choices[ $v ] = $k;
  }
  return $choices;
}
add_filter( 'acf/location/rule_values/post_template', 'acf_post_template_rule_values', 10, 1 );

/**
 * Match the rule type and edit screen
 */
add_filter('acf/location/rule_match/post_template', 'acf_location_rules_match_post_template', 10, 3);
function acf_location_rules_match_post_template( $match, $rule, $options ) {
  if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
      return $match;
  }
  // copied from acf_location::rule_match_page_template (advanced-custom-fields-pro/core/location.php)

  // bail early if not a post
  if( !$options['post_id'] ) return false;
  // vars
  $page_template = $options['page_template'];
  // get page template
  if( !$page_template ) {
    $page_template = get_post_meta( $options['post_id'], '_wp_page_template', true );
  }

  // get page template again
  if( !$page_template ) {
    $post_type = $options['post_type'];
    if( !$post_type ) {
      $post_type = get_post_type( $options['post_id'] );
    }
    if( $post_type === 'page' ) {
      $page_template = "default";
    }
  }

  // compare
  if( $rule['operator'] == "==" ) {
    $match = ( $page_template === $rule['value'] );
  } elseif( $rule['operator'] == "!=" ) {
    $match = ( $page_template !== $rule['value'] );
  }

  // return
  return $match;
}
