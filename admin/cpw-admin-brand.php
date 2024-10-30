<?php

if (!function_exists('function_cpw_brand')) {

// Register Custom Taxonomy
  function function_cpw_brand()
  {
    
    $labels = array(
        'name' => _x('CPW Brands', 'Taxonomy General Name', 'cpw'),
        'singular_name' => _x('CPW Brand', 'Taxonomy Singular Name', 'cpw'),
        'menu_name' => __('CPW Brand', 'cpw'),
        'all_items' => __('All CPW Brands', 'cpw'),
        'parent_item' => __('Parent CPW Brand', 'cpw'),
        'parent_item_colon' => __('Parent CPW Brand:', 'cpw'),
        'new_item_name' => __('New CPW Brand', 'cpw'),
        'add_new_item' => __('Add New CPW Brand', 'cpw'),
        'edit_item' => __('Edit CPW Brand', 'cpw'),
        'update_item' => __('Update CPW Brand', 'cpw'),
        'view_item' => __('View CPW Brand', 'cpw'),
        'separate_items_with_commas' => __('Separate CPW Brands with commas', 'cpw'),
        'add_or_remove_items' => __('Add or remove CPW Brands', 'cpw'),
        'choose_from_most_used' => __('Choose from the most used', 'cpw'),
        'popular_items' => __('Popular CPW Brands', 'cpw'),
        'search_items' => __('Search CPW Brands', 'cpw'),
        'not_found' => __('Not Found', 'cpw'),
        'no_terms' => __('No CPW Brands', 'cpw'),
        'items_list' => __('CPW Brands list', 'cpw'),
        'items_list_navigation' => __('CPW Brands list navigation', 'cpw'),
    );
    $rewrite = array(
        'slug' => 'cpw-brands',
        'with_front' => true,
        'hierarchical' => false,
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
        'rewrite' => $rewrite,
    );
    register_taxonomy('cpw_brand', array('product'), $args);
    
  }
  
  add_action('init', 'function_cpw_brand', 0);
  
}