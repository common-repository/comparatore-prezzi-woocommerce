<?php


if ( ! function_exists( 'cpw_save_service_settings_callback' ) ) {
	add_action( 'wp_ajax_cpw_save_service_settings', 'cpw_save_service_settings_callback' );
	add_action( 'wp_ajax_nopriv_cpw_save_service_settings', 'cpw_save_service_settings_callback' );
	
	function cpw_save_service_settings_callback() {
		
		
		$products                      = array();
		$cpw_service                   = array();
		$products_to_export            = array();
		$products_variations_by_source = array();
		$products_by_source            = array();
		
		
		$taxonomies = array();
		if ( isset( $_POST['taxonomies_value'] ) ):
			$taxonomies = $_POST['taxonomies_value'];
		endif;
		$service                 = $_POST['service'];
		$sale                    = $_POST['sale'];
		$featured                = $_POST['featured'];
		$tree_tax                = $_POST['tree_tax'];
		$tree_tax_exc            = $_POST['tree_tax_exc'];
		$cpw_brand               = $_POST['cpw_brand'];
		$cpw_brand_cpm           = $_POST['cpw_brand_cpm'];
		$desc                    = $_POST['desc'];
		$stock                   = $_POST['stock'];
		$ean                     = $_POST['ean'];
		$ean_cpm                 = $_POST['ean_cpm'];
		$partnbr                 = $_POST['partnbr'];
		$partnbr_cpm             = $_POST['partnbr_cpm'];
		$shipping                = $_POST['shipping'];
		$shipping_fixed_value    = $_POST['shipping_fixed_value'];
		$shipping_cond_on_price  = $_POST['shipping_cond_on_price'];
		$shipping_cond_on_weight = $_POST['shipping_cond_on_weight'];
		$filter_max_price        = $_POST['filter_max_price'];
		$filter_min_price        = $_POST['filter_min_price'];
		$filter_min_qta          = (int) $_POST['filter_min_qta'];
		
		
		if ( $filter_max_price == - 1 ):
			$filter_max_price = 1000 * 999; // highest price
		endif;
		
		
		$args_query = array(
			'post_type'      => array( 'product' ),
			'posts_per_page' => 20,
		);
		
		
		if ( ! get_option( 'cpw_' . $service ) ):
			add_option( 'cpw_' . $service, array() );
		endif;
		$main_option = get_option( 'cpw_' . $service );
		
		
		if ( get_option( $main_option['settings'] ) && is_array( get_option( $main_option['settings'] ) ) ):
			$cpw_service = get_option( $main_option['settings'] );
		endif;
		
		if ( is_array( $taxonomies ) ):
			$cpw_service['taxonomies'] = $taxonomies;
		endif;
		
		
		$cpw_service['sale']                 = $sale;
		$cpw_service['featured']             = $featured;
		$cpw_service['tree_tax']             = $tree_tax;
		$cpw_service['tree_tax_exc']         = $tree_tax_exc;
		$cpw_service['cpw_brand']            = $cpw_brand;
		$cpw_service['cpw_brand_cpm']        = $cpw_brand_cpm;
		$cpw_service['desc']                 = $desc;
		$cpw_service['stock']                = $stock;
		$cpw_service['ean']                  = $ean;
		$cpw_service['ean_cpm']              = $ean_cpm;
		$cpw_service['partnbr']              = $partnbr;
		$cpw_service['partnbr_cpm']          = $partnbr_cpm;
		$cpw_service['shipping']             = $shipping;
		$cpw_service['shipping_fixed_value'] = $shipping_fixed_value;
		if ( is_array( $shipping_cond_on_price ) && count( $shipping_cond_on_price ) > 0 ):
			$cpw_service['shipping_cond_on_price'] = $shipping_cond_on_price;
		endif;
		if ( is_array( $shipping_cond_on_weight ) && count( $shipping_cond_on_weight ) > 0 ):
			$cpw_service['shipping_cond_on_weight'] = $shipping_cond_on_weight;
		endif;
		$cpw_service['filter_max_price'] = $filter_max_price;
		$cpw_service['filter_min_price'] = $filter_min_price;
		$cpw_service['filter_min_qta']   = $filter_min_qta;
		
		
		$main_option['settings'] = $cpw_service;
		
		update_option( 'cpw_' . $service, $main_option );
		
		
		if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ):
			$args_query['tax_query'] = array(
				'relation' => 'OR'
			);
			
			foreach ( $taxonomies as $key => $single_taxonomy ):
				array_push( $args_query['tax_query'], array(
					'taxonomy' => $key,
					'field'    => 'term_id',
					'terms'    => $single_taxonomy,
					'operator' => 'IN'
				) );
			endforeach;
		endif;
		
		$products_by_source_uncleaned = get_posts( $args_query );
		if ( is_array( $products_by_source_uncleaned ) && count( $products_by_source_uncleaned ) > 0 ):
			$limit = 0;
			foreach ( $products_by_source_uncleaned as $single_product_by_source ):
				if ( ++ $limit < 21 ):
					array_push( $products_by_source, $single_product_by_source->ID );
				endif;
			endforeach;
		endif;
		
		$limit = 0;
		foreach ( $products_by_source as $single_product_by_source ) :
			if ( cpw_check_filter_is_ok( $service, $single_product_by_source ) ):
				if ( ++ $limit < 21 ):
					array_push( $products_to_export, $single_product_by_source );
				endif;
			endif;
		endforeach;
		
		$main_option['products_by_source']            = $products_by_source;
		$main_option['products_variations_by_source'] = $products_variations_by_source;
		$main_option['products_to_export']            = $products_to_export;
		
		update_option( 'cpw_' . $service, $main_option );
		
		$number_of_products    = cpw_calc_number_products_by_source( $service );
		$number_of_products_ok = cpw_calc_number_products_to_export( $service );
		$buttons               = cpw_render_feed_csv_button( $service );
		$number_of_variations  = cpw_calc_number_products_variations_by_source( $service );
		
		echo json_encode( array(
			'number_of_products'    => $number_of_products,
			'number_of_products_ok' => $number_of_products_ok,
			'number_of_variations'  => $number_of_variations,
			'buttons'               => $buttons
		) );
		
		
		die();
	}
}

if ( ! function_exists( 'cpw_load_products_callback' ) ) {
	add_action( 'wp_ajax_cpw_load_products', 'cpw_load_products_callback' );
	add_action( 'wp_ajax_nopriv_cpw_load_products', 'cpw_load_products_callback' );
	
	function cpw_load_products_callback() {
		
		$return  = array();
		$service = $_POST['service'];
		
		$products_saved     = array();
		$products_to_export = array();
		$separator_tree_tax = ',';
		$tree_tax_exclude   = array();
		$main_settings      = array();
		
		
		$main_option = get_option( 'cpw_' . $service );
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				if ( is_array( $main_option['settings'] ) && count( $main_option['settings'] ) > 0 ):
					$main_settings = $main_option['settings'];
				endif;
			endif;
			if ( array_key_exists( 'products_by_source', $main_option ) ):
				if ( is_array( $main_option['products_by_source'] ) && count( $main_option['products_by_source'] ) > 0 ):
					$products_saved = $main_option['products_by_source'];
				endif;
			endif;
			if ( array_key_exists( 'products_to_export', $main_option ) ):
				if ( is_array( $main_option['products_to_export'] ) && count( $main_option['products_to_export'] ) > 0 ):
					$products_to_export = $main_option['products_to_export'];
				endif;
			endif;
		endif;
		
		
		if ( is_array( $main_settings ) && count( $main_settings ) > 0 && array_key_exists( 'tree_tax_exc', $main_settings ) ):
			$tree_tax_exclude = $main_settings['tree_tax_exc'];
		endif;
		
		switch ( $service ):
			case 'trovaprezzi':
				$separator_tree_tax = ';';
				break;
		endswitch;
		
		
		if ( $products_saved && is_array( $products_saved ) && count( $products_saved ) > 0 ):
			$limit = 0;
			foreach ( $products_saved as $single_product ) :
				$var = '<span class="mif-cross fg-darkCyan"></span>';
				if ( get_post_type( $single_product ) == 'product' ):
					$product   = new WC_Product( $single_product );
					$edit_link = get_edit_post_link( $single_product );
				endif;
				
				
				$image          = $product->get_image( array( 50, 50 ) );
				$name           = $product->get_name();
				$price          = $product->get_price();
				$stock          = cpw_get_stock( 'trovaprezzi', $single_product );
				$original_price = '';
				$tree_tax       = cpw_get_tree_tax( 'trovaprezzi', $single_product, $separator_tree_tax, $tree_tax_exclude );
				$brand          = cpw_get_brand( 'trovaprezzi', $single_product );
				$desc           = cpw_get_desc( 'trovaprezzi', $single_product, 225 );
				$ean            = cpw_get_ean( 'trovaprezzi', $single_product );
				$partnbr        = cpw_get_partnbr( 'trovaprezzi', $single_product );
				$shipping_cost  = cpw_get_shipping_cost( 'trovaprezzi', $single_product );
				$status         = '<span class="mif-thumbs-down fg-red">KO</span>';
				
				
				$other_images_tag = '';
				
				
				if ( $name == '' ):
					$name = '<span class="cpw-alert"><span class="mif-blocked"></span></span>';
				else:
					$name = '<a href="' . $edit_link . '" target="_blank">' . $name . '</a>';
				endif;
				
				if ( $desc == '' ):
					$desc = '<span class="cpw-warning"><span class="mif-warning"></span></span>';
				else:
					$desc = '<span class="cpw_more">' . $desc . '</span>';
				endif;
				
				if ( $price == '' ):
					$price = '<span class="cpw-alert"><span class="mif-blocked"></span></span>';
				else:
					$price = get_woocommerce_currency_symbol() . ' ' . $price . '</span>';
				endif;
				
				
				if ( $shipping_cost == '' ):
					$shipping_cost = '<span class="cpw-alert"><span class="mif-blocked"></span></span>';
				else:
					$shipping_cost = get_woocommerce_currency_symbol() . ' ' . $shipping_cost . '</span>';
				endif;
				
				$original_price = '<span class="cpw-warning"><span class="mif-warning"></span></span>';
				
				if ( $brand == '' ):
					$brand = '<span class="cpw-warning"><span class="mif-warning"></span></span>';
				endif;
				
				if ( $image == '' ):
					$image = '<span class="cpw-warning"><span class="mif-warning"></span></span>';
				endif;
				
				if ( $tree_tax == '' ):
					$tree_tax = '<span class="cpw-alert"><span class="mif-blocked"></span></span>';
				endif;
				
				if ( $ean == '' ):
					$ean = '<span class="cpw-warning"><span class="mif-warning"></span></span>';
				endif;
				
				if ( $partnbr == '' ):
					$partnbr = '<span class="cpw-warning"><span class="mif-warning"></span></span>';
				endif;
				
				if ( in_array( $single_product, $products_to_export ) ):
					$status = '<span class="mif-thumbs-up fg-emerald">OK</span>';
				endif;
				
				
				switch ( $service ):
					case 'trovaprezzi':
						
						$return[] = array(
							$image,
							$single_product,
							$name,
							$desc,
							$tree_tax,
							$brand,
							$ean,
							$partnbr,
							$var,
							$stock,
							$price,
							$original_price,
							$shipping_cost,
							$other_images_tag,
							$status
						);
						
						break;
				endswitch;
				/*
				if ( ++ $limit > 5 ) :
					break;
				endif;
				*/
			endforeach;
		
		endif;
		
		
		echo json_encode( cpw_utf8_json_encode( $return ) );
		
		die();
	}
}
if ( ! function_exists( 'cpw_reload_tree_tax_exc_callback' ) ) {
	add_action( 'wp_ajax_cpw_reload_tree_tax_exc', 'cpw_reload_tree_tax_exc_callback' );
	add_action( 'wp_ajax_nopriv_cpw_reload_tree_tax_exc', 'cpw_reload_tree_tax_exc_callback' );
	
	
	function cpw_reload_tree_tax_exc_callback() {
		
		$return = '';
		
		$tax_value = $_POST['tax_value'];
		$terms     = get_terms( array(
			'taxonomy'   => $tax_value,
			'hide_empty' => true,
		) );
		
		
		if ( ! is_wp_error( $terms ) ):
			if ( is_array( $terms ) && count( $terms ) > 0 ):
				$return .= '<select multiple name="cpw_tree_tax_exc" class="cpw-service-tree-tax-exc cpw-selectize">';
				foreach ( $terms as $single_term ):
					$return .= '<option value="' . $single_term->term_id . '">' . $single_term->name . '</option>';
				endforeach;
				$return .= '</select>';
			endif;
		endif;
		
		echo $return;
		
		die();
		
	}
}