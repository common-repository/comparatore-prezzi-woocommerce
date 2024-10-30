<?php

if ( ! function_exists( 'function_cpw_csv_trovaprezzi_callback' ) ) {
	
	add_action( 'wp_ajax_function_cpw_csv_trovaprezzi', 'function_cpw_csv_trovaprezzi_callback' );
	add_action( 'wp_ajax_nopriv_function_cpw_csv_trovaprezzi', 'function_cpw_csv_trovaprezzi_callback' );
	
	function function_cpw_csv_trovaprezzi_callback() {
		
		header( 'Content-type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="demo.csv"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		
		
		$service       = $_POST['service'];
		$file_name     = 'csv_' . $service . '_' . date( 'YmdHi' ) . '.csv';
		$path          = plugin_dir_path( __FILE__ ) . $service;
		$absolute_path = plugin_dir_url( __FILE__ ) . $service . '/';
		$array_header  = array(
			'Name',
			'Brand',
			'Description',
			'Price',
			'Code',
			'Link',
			'Stock',
			'Categories',
			'Image',
			'ShippingCost',
			'PartNumber',
			'EanCode'
		);
		$array_data    = array();
		
		
		if ( ! file_exists( $path ) ):
			mkdir( $path, 0777 );
		else:
			$file = fopen( $path . '/' . $file_name, 'w' );
			
			fprintf( $file, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
			
			$main_settings = array();
			
			$main_option = get_option( 'cpw_' . $service );
			
			if ( is_array( $main_option ) && count( $main_option ) > 0 ):
				if ( array_key_exists( 'settings', $main_option ) ):
					$main_settings = $main_option['settings'];
				endif;
			endif;
			
			if ( is_array( $main_option ) && count( $main_option ) > 0 ):
				if ( array_key_exists( 'products_to_export', $main_option ) ):
					$products         = $main_option['products_to_export'];
					$tree_tax_exclude = array();
					
					if ( is_array( get_option( 'cpw_service_' . $service ) )
					     && count( get_option( 'cpw_service_' . $service ) ) > 0
					     && array_key_exists( 'tree_tax_exc', get_option( 'cpw_service_' . $service ) )
					     && is_array( get_option( 'cpw_service_' . $service )['tree_tax_exc'] )
					     && count( ( get_option( 'cpw_service_' . $service )['tree_tax_exc'] ) ) > 0
					):
						$tree_tax_exclude = get_option( 'cpw_service_' . $service )['tree_tax_exc'];
					endif;
					
					
					$query = new WP_Query( array(
						'post_type'      => array( 'product' ),
						'post__in'       => $products,
						'posts_per_page' => - 1
					) );
					
					if ( $query->have_posts() ) :
						$limit = 0;
						while ( $query->have_posts() ) :
							$query->the_post();
							if ( ++ $limit < 21 ):
								$product_id  = get_the_ID();
								$product     = new WC_Product( $product_id );
								$name        = $product->get_name();
								$brand       = cpw_get_brand( $service, $product_id );
								$description = cpw_get_desc( $service, $product_id, 225 );
								$price       = $product->get_price();
								
								$stock         = cpw_get_stock( $service, $product_id );
								$code          = $product_id;
								$product_url   = $product->get_permalink();
								$categories    = strtolower( cpw_get_tree_tax( $service, $product_id, ',', $tree_tax_exclude ) );
								$shipping_cost = cpw_get_shipping_cost( $service, $product_id );
								$image         = get_the_post_thumbnail_url( $product_id, 'large' );
								$ean           = cpw_get_ean( $service, $product_id );
								$part_number   = cpw_get_partnbr( $service, $product_id );
								
								array_push( $array_data, array(
									html_entity_decode( $name ),
									html_entity_decode( $brand ),
									wp_strip_all_tags( html_entity_decode( $description ) ),
									$price,
									$code,
									$product_url,
									$stock,
									html_entity_decode( $categories ),
									$image,
									$shipping_cost,
									$part_number,
									$ean
								) );
							endif;
						endwhile;
					endif;
					
					
					fputcsv( $file, $array_header, '|' );
					foreach ( $array_data as $row ) {
						fputcsv( $file, $row, '|' );
					}
					fclose( $file );
					
					echo $absolute_path . $file_name;
				endif;
			endif;
		endif;
		
		die();
	}
}