<?php

if ( ! function_exists( 'cpw_admin_enqueue' ) ) {
	add_action( 'admin_enqueue_scripts', 'cpw_admin_enqueue' );
	
	function cpw_admin_enqueue( $hook ) {
		
		if ( $hook == 'cpw-free_page_cpw_trovaprezzi'
		     || $hook == 'toplevel_page_cpw'
		) {
			wp_enqueue_script( 'selectizejs', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/selectize.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'cpwjs', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/cpw.js', array( 'jquery' ) );
			wp_enqueue_script( 'metro_js', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/metro.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'jquery.dataTables', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/jquery.dataTables.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'jquery-scrolltofixed', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/jquery-scrolltofixed-min.js', array( 'jquery' ) );
			
			
			wp_localize_script( 'cpwjs', 'cpw_ajax', array( 'adminajax' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_style( 'cpw_style', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/cpw.css' );
			wp_enqueue_style( 'selectizecss', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/selectize.css' );
			wp_enqueue_style( 'metro', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/metro.min.css' );
			wp_enqueue_style( 'metro_icon', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/metro-icons.min.css' );
		}
	}
}
if ( ! function_exists( 'cpw_new_product' ) ) {
	function cpw_new_product( $product_id, $product_variations ) {
		if ( get_post_type( $product_id ) == 'product' ):
			$product = new WC_Product( $product_id );
		endif;
		
		return $product;
	}
}
if ( ! function_exists( 'cpw_render_select_terms' ) ) {
	
	function cpw_render_select_terms( $service ) {
		
		$main_settings = array();
		$saved_values  = array();
		
		$main_option = get_option( 'cpw_' . $service );
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		$taxonomies = get_object_taxonomies( 'product', 'objects' );
		
		foreach ( $taxonomies as $key => $single_taxonomy ):
			
			$return = '<div class="grid"><div class="row cells4 cpw-admin-block-item">';
			
			$terms = get_terms( array(
				'taxonomy'   => $key,
				'hide_empty' => true,
			) );
			
			
			if ( ! is_wp_error( $terms ) ):
				if ( is_array( $terms ) && count( $terms ) > 0 ):
					
					
					if ( isset( $single_taxonomy->labels ) ):
						if ( isset( $single_taxonomy->labels->name ) ):
							$return .= '<div class="cell">';
							$return .= '<h5>' . esc_html( $single_taxonomy->labels->name ) . '<br /><small>' . esc_html( $single_taxonomy->name ) . '</small></h5>';
							$return .= '</div>';
						endif;
					endif;
					
					if ( array_key_exists( 'taxonomies', $main_settings ) ):
						$saved_values = $main_settings['taxonomies'];
					endif;
					$selected_terms = array();
					
					if ( $saved_values ):
						if ( array_key_exists( $key, $saved_values ) ):
							$selected_terms = $saved_values[ $key ];
						endif;
					endif;
					$return .= '<div class="cell colspan3">';
					$return .= '<select multiple name="' . esc_attr( $key ) . '" class="cpw-service-taxonomy cpw-selectize">';
					foreach ( $terms as $single_term ):
						$selected = '';
						if ( in_array( $single_term->term_id, $selected_terms ) ):
							$selected = 'selected="selected"';
						endif;
						$return .= '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $single_term->term_id ) . '">' . esc_html( $single_term->name ) . '</option>';
					endforeach;
					$return .= '</select>';
					$return .= '</div>';
				endif;
			endif;
			
			$return .= '</div></div>';
			
			echo $return;
		
		endforeach;
		
	}
	
}
if ( ! function_exists( 'cpw_render_variations' ) ) {
	
	function cpw_render_variations( $service ) {
		?>
        <div class="grid">
            <div class="row cells4 cpw-admin-block-item">
                <div class="cell"><h5>Varianti dei Prodotti</h5></div>
                <div class="cell colspan3"><input type="checkbox" name="" value="false" disabled="disabled"/>
                    Includi le Varianti dei Prodotti ( Consigliato )
					<?php echo cpw_render_premium_link(); ?>
                </div>

            </div>
        </div>
		<?php
	}
}
if ( ! function_exists( 'cpw_render_sale' ) ) {
	
	function cpw_render_sale( $service ) {
		?>
        <div class="grid">
            <div class="row cells4 cpw-admin-block-item">
                <div class="cell"><h5>Prodotti in Promozione</h5></div>
                <div class="cell colspan3">
                    <input type="checkbox" name="" value="false" disabled="disabled"/> Filtra per prodotti in
                    promozione
					<?php echo cpw_render_premium_link(); ?>
                </div>

            </div>
        </div>
		<?php
	}
}
if ( ! function_exists( 'cpw_render_featured' ) ) {
	
	function cpw_render_featured( $service ) {
		?>
        <div class="grid">
            <div class="row cells4 cpw-admin-block-item">
                <div class="cell"><h5>Prodotti in Evidenza</h5></div>
                <div class="cell colspan3">
                    <input type="checkbox" disabled="disabled" name="" value="false"/> Filtra per prodotti
                    in evidenza
					<?php echo cpw_render_premium_link(); ?>
                </div>

            </div>
        </div>
		<?php
	}
}
if ( ! function_exists( 'cpw_render_price_filter' ) ) {
	
	function cpw_render_price_filter( $service ) {
		
		?>
        <div class="grid">
            <div class="row cells4 cpw-admin-block-item">
                <div class="cell"><h5>Filtra per Prezzo</h5></div>

                <div class="cell colspan3">
                    <div class="grid">
                        <div class="row cells7">
                            <div class="cell colspan2">
                                <label>Prodotti con Prezzo inferiore a:</label>
                            </div>
                            <div class="cell">
                                <input type="number" value="" class="cpw-service-filter-max-price"
                                       disabled="disabled"/>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="row cells7">
                            <div class="cell colspan2">
                                <label>Prodotti con Prezzo superiore a:</label>
                            </div>
                            <div class="cell">
                                <input type="number" value="" class="cpw-service-filter-min-price"
                                       disabled="disabled"/>
                            </div>
                        </div>
                    </div>
					
					<?php echo cpw_render_premium_link(); ?>
                </div>
            </div>
        </div>
		
		<?php
		
	}
}
if ( ! function_exists( 'cpw_render_qta_filter' ) ) {
	
	function cpw_render_qta_filter( $service ) {
		?>
        <div class="grid">
            <div class="row cells4 cpw-admin-block-item">
                <div class="cell"><h5>Filtra per Quantità</h5></div>
                <div class="cell colspan3">
                    <div class="grid">
                        <div class="row cells7">
                            <div class="cell colspan2">
                                <label>Prodotti con quantità superiore a: ( attenzione alla gestione
                                    del magazzino )</label>
                            </div>
                            <div class="cell colspan2">
                                <input type="number" min="-1" value="" disabled="disabled"
                                       class="cpw-service-filter-min-qta"/>
                            </div>
                            <div class="cell colspan3">-1 DISATTIVATO<br/>0 TUTTI I PRODOTTI
                                DISPONIBILI
                            </div>
                        </div>
                    </div>
					<?php echo cpw_render_premium_link(); ?>
                </div>
            </div>
        </div>
		<?php
		
	}
}
if ( ! function_exists( 'cpw_render_brand' ) ) {
	
	function cpw_render_brand( $service ) {
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		
		$return            = '<div class="grid">
        <div class="row cells4 cpw-admin-block-item">
            <div class="cell"><h5>Brand ( Facoltativo )</h5></div>';
		$taxonomies        = get_object_taxonomies( 'product', 'objects' );
		$brand_saved_value = 'cpw_brand';
		
		if ( is_array( $main_settings ) && array_key_exists( 'cpw_brand', $main_settings ) ):
			$brand_saved_value = $main_settings['cpw_brand'];
		endif;
		
		$return .= '<div class="cell colspan3">';
		$return .= '<select name="cpw_brand" class="cpw-service-brand cpw-selectize">';
		if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ):
			
			foreach ( $taxonomies as $key => $value ):
				if ( $key != 'product_shipping_class' ):
					
					$return .= '<option value="' . esc_attr( $key ) . '" ' . selected( $brand_saved_value, $key, false ) . '>' . esc_html( $value->labels->name ) . ' -  ' . esc_html( $value->name ) . '</option>';
				
				endif;
			endforeach;
		
		endif;
		
		$return .= '<option value="cpw_brand_cpm" ' . selected( $brand_saved_value, 'cpw_brand_cpm', false ) . '>Custom post meta</option>';
		$return .= '<option value="none" ' . selected( $brand_saved_value, 'none', false ) . '>Nessuno ( Sconsigliato )</option>';
		$return .= '</select>';
		$return .= 'Custom Post Meta Name <br /> <input type="text" class="cpw-service-brand-cpm" value="" disabled="disabled"/>' . cpw_render_premium_link();
		$return .= '</div>';
		$return .= '</div></div>';
		
		echo $return;
		
	}
}
if ( ! function_exists( 'cpw_render_desc' ) ) {
	
	function cpw_render_desc( $service ) {
		
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		$desc_saved_value = 'short';
		if ( is_array( $main_settings ) && array_key_exists( 'desc', $main_settings ) ):
			$desc_saved_value = $main_settings['desc'];
		endif;
		
		$return = '<div class="grid">';
		$return .= '<div class="row cells4 cpw-admin-block-item">';
		$return .= '<div class="cell"><h5>Descrizione ( Facoltativo )</h5></div>';
		$return .= '<div class="cell colspan3">';
		
		
		$return .= '<select name="cpw_desc" class="cpw-service-desc cpw-selectize">';
		$return .= '<option value="short" ' . selected( $desc_saved_value, 'short', false ) . '>Descrizione Breve</option>';
		$return .= '<option value="long" ' . selected( $desc_saved_value, 'long', false ) . '>Descrizione Estesa</option>';
		$return .= '<option value="none" ' . selected( $desc_saved_value, 'none', false ) . '>Nessuna ( Sconsigliato )</option>';
		$return .= '</select>';
		$return .= '</div></div></div>';
		
		echo $return;
		
	}
}
if ( ! function_exists( 'cpw_render_original_price' ) ) {
	
	function cpw_render_original_price( $service ) {
		
		?>
        <div class="grid">
            <div class="row cells4 cpw-admin-block-item">
                <div class="cell"><h5>Prezzo Originale ( Facoltativo )</h5></div>
                <div class="cell colspan3">
                    <input type="checkbox" name="original_price"
                           value="" disabled="disabled"/>
                    Mostra anche Prezzo Originale
					<?php echo cpw_render_premium_link(); ?>
                </div>

            </div>
        </div>
		<?php
	}
}
if ( ! function_exists( 'cpw_render_stock' ) ) {
	
	function cpw_render_stock( $service ) {
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		$stock_saved_value = 'text';
		if ( is_array( $main_settings ) && array_key_exists( 'stock', $main_settings ) ):
			$stock_saved_value = $main_settings['stock'];
		endif;
		
		$return = '<div class="grid">';
		$return .= '<div class="row cells4 cpw-admin-block-item">';
		$return .= '<div class="cell"><h5>Disponibilità ( Facoltativo )</h5></div>';
		$return .= '<div class="cell colspan3">';
		
		
		$return .= '<select name="cpw_desc" class="cpw-service-stock cpw-selectize">';
		$return .= '<option value="text" ' . selected( $stock_saved_value, 'text', false ) . '>Testuale (Disponibile / Disponibilità limitata / Non disponibile)</option>';
		$return .= '<option value="number" ' . selected( $stock_saved_value, 'number', false ) . '>Numerica ( ove possibile )</option>';
		$return .= '<option value="none" ' . selected( $stock_saved_value, 'none', false ) . '>Nessuna ( Sconsigliato )</option>';
		$return .= '</select>';
		$return .= '</div></div></div>';
		
		echo $return;
		
	}
}
if ( ! function_exists( 'cpw_render_tree_tax' ) ) {
	
	function cpw_render_tree_tax( $service ) {
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		
		$return               = '<div class="grid">
        <div class="row cells4 cpw-admin-block-item">
            <div class="cell"><h5>Albero Categorie ( Obbligatorio )</h5></div>';
		$taxonomies           = get_object_taxonomies( 'product', 'objects' );
		$tree_tax_saved_value = 'product_cat';
		if ( is_array( $main_settings ) && array_key_exists( 'tree_tax', $main_settings ) ):
			$tree_tax_saved_value = $main_settings['tree_tax'];
		endif;
		
		if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ):
			$return .= '<div class="cell colspan3">';
			$return .= '<select name="cpw_tree_tax" class="cpw-service-tree-tax cpw-selectize">';
			foreach ( $taxonomies as $key => $value ):
				if ( $key != 'product_shipping_class' ):
					$selected = '';
					if ( $key == $tree_tax_saved_value ):
						$selected = 'selected = "selected"';
					endif;
					$return .= '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $value->labels->name ) . ' - ' . esc_html( $value->name ) . '</option>';
				endif;
			endforeach;
			$return .= '</select>';
			$return .= '</div></div></div>';
		endif;
		
		echo $return;
		
	}
}
if ( ! function_exists( 'cpw_render_tree_tax_exc' ) ) {
	
	function cpw_render_tree_tax_exc( $service ) {
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		
		$return               = '<div class="grid">
        <div class="row cells4 cpw-admin-block-item">
            <div class="cell"><h5>Escludi da Albero Tassonomia</h5></div>';
		$tree_tax_saved_value = 'product_cat';
		if ( is_array( $main_settings ) && array_key_exists( 'tree_tax', $main_settings ) ):
			$tree_tax_saved_value = $main_settings['tree_tax'];
		endif;
		
		$tree_tax_exc_saved_value = array();
		if ( is_array( $main_settings ) && array_key_exists( 'tree_tax_exc', $main_settings ) ):
			$tree_tax_exc_saved_value = $main_settings['tree_tax_exc'];
		endif;
		
		
		$terms = get_terms( array(
			'taxonomy'   => $tree_tax_saved_value,
			'hide_empty' => true,
		) );
		
		
		if ( ! is_wp_error( $terms ) ):
			if ( is_array( $terms ) && count( $terms ) > 0 ):
				$return .= '<div class="cell colspan3 cpw_tree_tax_exc_container">';
				$return .= '<select multiple name="cpw_tree_tax_exc" class="cpw-service-tree-tax-exc cpw-selectize">';
				foreach ( $terms as $single_term ):
					$selected = '';
					if ( is_array( $tree_tax_exc_saved_value ) && in_array( $single_term->term_id, $tree_tax_exc_saved_value ) ):
						$selected = 'selected="selected"';
					endif;
					$return .= '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $single_term->term_id ) . '">' . esc_html( $single_term->name ) . '</option>';
				endforeach;
				$return .= '</select>';
				$return .= '</div>';
			endif;
		endif;
		
		$return .= '</div></div>';
		
		echo $return;
		
	}
}
if ( ! function_exists( 'cpw_render_shipping' ) ) {
	
	function cpw_render_shipping( $service ) {
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		$return                               = '<div class="grid">
        <div class="row cells4 cpw-admin-block-item">
            <div class="cell"><h5>Spese di Spedizione ( Obbligatorio )</h5></div>';
		$return                               .= '<div class="cell colspan3">';
		$shipping_saved_value                 = 'fixed';
		$shipping_fixed_value                 = '';
		$shipping_cond_saved_values_on_price  = array();
		$shipping_cond_saved_values_on_weight = array();
		
		
		if ( $main_settings && array_key_exists( 'shipping_fixed_value', $main_settings ) ):
			$shipping_fixed_value = $main_settings['shipping_fixed_value'];
		endif;
		
		if ( is_array( $main_settings ) && array_key_exists( 'shipping', $main_settings ) ):
			$shipping_saved_value = $main_settings['shipping'];
		endif;
		
		if ( is_array( $main_settings ) && array_key_exists( 'shipping_cond_on_price', $main_settings ) ):
			
			
			$shipping_cond_saved_values_on_price = $main_settings['shipping_cond_on_price'];
		
		
		endif;
		if ( is_array( $main_settings ) && array_key_exists( 'shipping_cond_on_weight', $main_settings ) ):
			
			
			$shipping_cond_saved_values_on_weight = $main_settings['shipping_cond_on_weight'];
		
		
		endif;
		
		$return .= '<select name="cpw_shipping" class="cpw-service-shipping cpw-selectize">';
		$return .= '<option value="fixed" ' . selected( $shipping_saved_value, 'fixed', false ) . '>Fixed Shipping Cost</option>';
		$return .= '<option value="free" ' . selected( $shipping_saved_value, 'free', false ) . '>Free</option>';
		$return .= '<option value="cpw_shipping_custom_on_price" ' . selected( $shipping_saved_value, 'cpw_shipping_custom_on_price', false ) . '>Custom Shipping Cost ( based on PRICE )</option>';
		$return .= '<option value="cpw_shipping_custom_on_weight" ' . selected( $shipping_saved_value, 'cpw_shipping_custom_on_weight', false ) . '>Custom Shipping Cost ( based on WEIGHT )</option>';
		$return .= '</select>';
		
		$style_shipping_fixed     = 'style="display:none;"';
		$style_shipping_on_price  = 'style="display:none;"';
		$style_shipping_on_weight = 'style="display:none;"';
		
		if ( $shipping_saved_value == 'cpw_shipping_custom_on_weight' ):
			$style_shipping_on_weight = 'style="display:block;"';
        elseif ( $shipping_saved_value == 'cpw_shipping_custom_on_price' ):
			$style_shipping_on_price = 'style="display:block;"';
        elseif ( $shipping_saved_value == 'fixed' ):
			$style_shipping_fixed = 'style="display:block;"';
		endif;
		
		
		$return .= '<div class="cpw-service-shipping-fixed-value-container" ' . esc_attr( $style_shipping_fixed ) . '>';
		$return .= 'Fixed Shipping Cost <br /> <input type="text" class="cpw-service-shipping-fixed-value" value="' . esc_attr( $shipping_fixed_value ) . '"/>';
		$return .= '</div>';
		
		
		$return .= '<div class="cpw-service-shipping-combinations cpw-service-shipping-combinations-price" ' . esc_attr( $style_shipping_on_price ) . '>';
		$return .= '<h4>Custom Shipping Cost based on Product Price</h4>';
		for ( $i = 0; $i < 3; $i ++ ):
			
			$saved_value_min_on_price           = '';
			$saved_value_max_on_price           = '';
			$saved_value_shipping_cost_on_price = '';
			
			if ( array_key_exists( $i, $shipping_cond_saved_values_on_price ) ):
				
				if ( is_array( $shipping_cond_saved_values_on_price[ $i ] ) && count( $shipping_cond_saved_values_on_price[ $i ] ) > 0 ):
					
					$saved_value_min_on_price           = $shipping_cond_saved_values_on_price[ $i ][0];
					$saved_value_max_on_price           = $shipping_cond_saved_values_on_price[ $i ][1];
					$saved_value_shipping_cost_on_price = $shipping_cond_saved_values_on_price[ $i ][2];
				
				endif;
			
			
			endif;
			
			$return .= '<div class="cpw-service-shipping-combinations-single row cells3" data-cycle="' . $i . '">';
			$return .= '<div class="cell">';
			$return .= ' If <b>product price</b> is > of <br />';
			$return .= '<input type="number" value="' . esc_attr( $saved_value_min_on_price ) . '" class="cpw-shipping-cond"  name="min" />';
			$return .= '</div>';
			$return .= '<div class="cell">';
			$return .= ' and < of <br />';
			$return .= '<input type="number" value="' . esc_attr( $saved_value_max_on_price ) . '" class="cpw-shipping-cond" name="max" />';
			$return .= '</div>';
			$return .= '<div class="cell">';
			$return .= ' <b>Shipping Cost is: </b><br />';
			$return .= '<input type="number" value="' . esc_attr( $saved_value_shipping_cost_on_price ) . '" class="cpw-shipping-cond" name="shipping_cost" />';
			$return .= '</div>';
			$return .= '</div>';
		endfor;
		$return .= '</div>';
		
		
		$return .= '<div class="cpw-service-shipping-combinations cpw-service-shipping-combinations-weight" ' . esc_attr( $style_shipping_on_weight ) . '>';
		$return .= '<h4>Custom Shipping Cost based on Product Weight</h4>';
		for ( $i = 0; $i < 3; $i ++ ):
			
			$saved_value_min_on_weight           = '';
			$saved_value_max_on_weight           = '';
			$saved_value_shipping_cost_on_weight = '';
			
			if ( array_key_exists( $i, $shipping_cond_saved_values_on_weight ) ):
				
				if ( is_array( $shipping_cond_saved_values_on_weight[ $i ] ) && count( $shipping_cond_saved_values_on_weight[ $i ] ) > 0 ):
					
					$saved_value_min_on_weight           = $shipping_cond_saved_values_on_weight[ $i ][0];
					$saved_value_max_on_weight           = $shipping_cond_saved_values_on_weight[ $i ][1];
					$saved_value_shipping_cost_on_weight = $shipping_cond_saved_values_on_weight[ $i ][2];
				
				endif;
			
			
			endif;
			
			$return .= '<div class="cpw-service-shipping-combinations-single row cells3" data-cycle="' . $i . '">';
			$return .= '<div class="cell">';
			$return .= ' If <b>product weight</b> is > of <br />';
			$return .= '<input type="number" value="' . esc_attr( $saved_value_min_on_weight ) . '" class="cpw-shipping-cond"  name="min" />';
			$return .= '</div>';
			$return .= '<div class="cell">';
			$return .= ' and < of <br />';
			$return .= '<input type="number" value="' . esc_attr( $saved_value_max_on_weight ) . '" class="cpw-shipping-cond" name="max" />';
			$return .= '</div>';
			$return .= '<div class="cell">';
			$return .= ' <b>Shipping Cost is: </b><br />';
			$return .= '<input type="number" value="' . esc_attr( $saved_value_shipping_cost_on_weight ) . '" class="cpw-shipping-cond" name="shipping_cost" />';
			$return .= '</div>';
			$return .= '</div>';
		endfor;
		$return .= '</div>';
		
		$return .= '</div></div></div>';
		
		
		echo $return;
	}
}
if ( ! function_exists( 'cpw_render_partnbr' ) ) {
	
	function cpw_render_partnbr( $service ) {
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		$return              = '<div class="grid">
        <div class="row cells4 cpw-admin-block-item">
            <div class="cell"><h5>Codice Produttore ( Facoltativo )</h5></div>';
		$return              .= '<div class="cell colspan3">';
		$partnbr_saved_value = '_sku';
		if ( is_array( $main_settings ) && array_key_exists( 'partnbr', $main_settings ) ):
			$partnbr_saved_value = $main_settings['partnbr'];
		endif;
		
		$return .= '<select name="partnbr" class="cpw-service-partnbr cpw-selectize">';
		$return .= '<option value="_sku" ' . selected( $partnbr_saved_value, '_sku', false ) . '>SKU</option>';
		$return .= '<option value="id_product" ' . selected( $partnbr_saved_value, 'id_product', false ) . '>ID Prodotto</option>';
		$return .= '<option value="partnbr_cpm" ' . selected( $partnbr_saved_value, 'partnbr_cpm', false ) . '>Custom post meta</option>';
		$return .= '<option value="none" ' . selected( $partnbr_saved_value, 'none', false ) . '>Nessuno ( Sconsigliato )</option>';
		$return .= '</select>';
		$return .= 'Custom Post Meta Name <br /> <input type="text" class="cpw-service-partnbr-cpm" value="" disabled="disabled"/>' . cpw_render_premium_link();
		
		$return .= '</div></div></div>';
		
		echo $return;
		
	}
}
if ( ! function_exists( 'cpw_render_ean' ) ) {
	
	function cpw_render_ean( $service ) {
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		$return          = '<div class="grid">
        <div class="row cells4 cpw-admin-block-item">
            <div class="cell"><h5>Codice Ean ( Altamente Raccomandato )</h5></div>';
		$return          .= '<div class="cell colspan3">';
		$ean_saved_value = '_sku';
		
		if ( is_array( $main_settings ) && array_key_exists( 'ean', $main_settings ) ):
			$ean_saved_value = $main_settings['ean'];
		endif;
		
		$return .= '<select name="cpw_ean" class="cpw-service-ean cpw-selectize">';
		$return .= '<option value="_sku" ' . selected( $ean_saved_value, '_sku', false ) . '>SKU</option>';
		$return .= '<option value="id_product" ' . selected( $ean_saved_value, 'id_product', false ) . '>ID Prodotto</option>';
		
		$return .= '<option value="ean_cpm" ' . selected( $ean_saved_value, 'ean_cpm', false ) . '>Custom post meta</option>';
		$return .= '<option value="none" ' . selected( $ean_saved_value, 'none', false ) . '>Nessuno ( Sconsigliato )</option>';
		$return .= '</select>';
		$return .= 'Custom Post Meta Name <br /> <input type="text" class="cpw-service-ean-cpm" value="" disabled="disabled"/>' . cpw_render_premium_link();
		
		$return .= '</div></div></div>';
		
		
		echo $return;
		
	}
}
if ( ! function_exists( 'cpw_render_other_images' ) ) {
	
	function cpw_render_other_images( $service ) {
		?>
        <div class="grid">
            <div class="row cells4 cpw-admin-block-item">
                <div class="cell"><h5>Ulteriori Immagini (
                        Facoltativo )</h5></div>
                <div class="cell colspan3">
                    <input type="checkbox" name="" value=""
                           disabled="disabled"/>
                    Link ad altre immagini ( galleria
                    immagini prodotto ) - solo in Feed XML
					<?php echo cpw_render_premium_link(); ?>
                </div>

            </div>

        </div>
		<?php
	}
}
if ( ! function_exists( 'cpw_render_save_button' ) ) {
	
	function cpw_render_save_button( $service ) {
		echo '<div class="cpw-save-button-container"><a data-service="' . esc_attr( $service ) . '" class="button cpw-save-button" href="#">Salva</a></div>';
	}
}
if ( ! function_exists( 'cpw_render_debug_legend' ) ) {
	
	function cpw_render_debug_legend() {
		$return = '<div class="row cpw-legend">';
		$return .= '<h5>LEGENDA</h5>';
		$return .= '<div class="cell"><span class="cpw-warning"><span class="mif-warning"></span></span> CONSIGLIATO</div>';
		$return .= '<div class="cell"><span class="cpw-alert"><span class="mif-blocked"></span></span> OBBLIGATORIO</div>';
		$return .= '<div class="cell"><span class="mif-thumbs-up fg-emerald">OK</span> VERRÀ PUBBLICATO</div>';
		$return .= '<div class="cell"><span class="mif-thumbs-down fg-red">KO</span> NON VERRÀ PUBBLICATO</div>';
		$return .= '</div>';
		
		echo $return;
	}
}
if ( ! function_exists( 'cpw_render_premium_link' ) ) {
	
	function cpw_render_premium_link() {
		$return = '<div class="row"><span class="cpw-premium-only"><a style="color: inherit;" target="_blank" href="http://drive.rsaweb.com/prodotto/cpw-comparatore-prezzi-woocommerce/">Opzione disponibile solo nella versione premium</a></span></div>';
		
		return $return;
	}
}

/* cpw get functions */

if ( ! function_exists( 'cpw_get_desc' ) ) {
	
	function cpw_get_desc( $service, $product_id, $limit ) {
		$return = '';
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		if ( get_post_type( $product_id ) == 'product' ):
			$product = new WC_Product( $product_id );
		endif;
		
		
		if ( array_key_exists( 'desc', $main_settings ) ):
			$value = $main_settings['desc'];
			
			switch ( $value ):
				case 'short':
					if ( $product->get_short_description() && $product->get_short_description() != '' ):
						$return = esc_html( substr( wp_strip_all_tags( $product->get_short_description() ), 0, $limit ) );
					endif;
					break;
				case 'long':
					if ( $product->get_description() && $product->get_description() != '' ):
						$return = esc_html( substr( wp_strip_all_tags( $product->get_description() ), 0, $limit ) );
					endif;
					break;
				case 'none':
					$return = '';
					break;
			endswitch;
		endif;
		
		return sanitize_text_field( $return );
	}
}
if ( ! function_exists( 'cpw_get_tree_tax' ) ) {
	
	function cpw_get_tree_tax( $service, $product_id, $separator, $exclude ) {
		
		$terms_array   = array();
		$main_settings = array();
		
		if ( empty( $exclude ) ):
			$exclude = array();
		endif;
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		if ( array_key_exists( 'tree_tax', $main_settings ) ):
			
			$value = $main_settings['tree_tax'];
			
			if ( get_post_type( $product_id ) == 'product_variation' ):
				$product_id = wp_get_post_parent_id( $product_id );
			endif;
			
			if ( ! is_wp_error( $terms = get_the_terms( $product_id, $value ) ) ):
				if ( is_array( $terms ) && count( $terms ) > 0 ):
					foreach ( $terms as $single_term ):
						if ( ! in_array( $single_term->term_id, $exclude ) ):
							$terms_array[] = $single_term->name;
						endif;
					endforeach;
				endif;
			endif;
			
			if ( is_array( $terms_array ) && count( $terms_array ) > 0 ):
				$return_value = esc_html( implode( $separator, $terms_array ) );
			else:
				$return_value = '';
			endif;
		
		endif;
		
		return $return_value;
	}
}
if ( ! function_exists( 'cpw_get_brand' ) ) {
	
	function cpw_get_brand( $service, $product_id ) {
		
		$return = '';
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		if ( array_key_exists( 'cpw_brand', $main_settings ) ):
			$value = $main_settings['cpw_brand'];
			
			
			if ( ! is_wp_error( $terms = get_the_terms( $product_id, $value ) ) ):
				if ( is_array( $terms ) && count( $terms ) > 0 ):
					$return = esc_html( $terms[0]->name );
				endif;
			endif;
		endif;
		
		return $return;
	}
}
if ( ! function_exists( 'cpw_get_ean' ) ) {
	
	function cpw_get_ean( $service, $product_id ) {
		$return        = '';
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		
		if ( array_key_exists( 'ean', $main_settings ) ):
			$value = $main_settings['ean'];
			if ( $value == 'none' ):
				$return = '';
			else:
				switch ( $value ):
					case 'id_product':
						$return = $product_id;
						break;
					default:
						if ( get_post_meta( $product_id, $main_settings['ean'], true ) && get_post_meta( $product_id, $main_settings['ean'], true ) != '' ):
							$return = esc_html( get_post_meta( $product_id, $main_settings['ean'], true ) );
						endif;
						break;
				endswitch;
				if ( ! is_wp_error( $terms = get_the_terms( $product_id, $value ) ) ):
					if ( is_array( $terms ) && count( $terms ) > 0 ):
						$return = esc_html( $terms[0]->name );
					endif;
				endif;
			endif;
		endif;
		
		return $return;
	}
}
if ( ! function_exists( 'cpw_get_partnbr' ) ) {
	
	function cpw_get_partnbr( $service, $product_id ) {
		$return        = '';
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		if ( array_key_exists( 'partnbr', $main_settings ) ):
			
			$value = $main_settings['partnbr'];
			
			if ( $value == 'none' ):
				$return = '';
			else:
				switch ( $value ):
					case 'id_product':
						$return = $product_id;
						break;
					default:
						if ( get_post_meta( $product_id, $main_settings['partnbr'], true ) && get_post_meta( $product_id, $main_settings['partnbr'], true ) != '' ):
							$return = esc_html( get_post_meta( $product_id, $main_settings['partnbr'], true ) );
						endif;
						break;
				endswitch;
				if ( ! is_wp_error( $terms = get_the_terms( $product_id, $value ) ) ):
					if ( is_array( $terms ) && count( $terms ) > 0 ):
						$return = esc_html( $terms[0]->name );
					endif;
				endif;
			endif;
		endif;
		
		return $return;
	}
}
if ( ! function_exists( 'cpw_get_stock' ) ) {
	
	
	function cpw_get_stock( $service, $product_id ) {
		$return        = '';
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		if ( array_key_exists( 'stock', $main_settings ) ):
			$value = $main_settings['stock'];
			if ( get_post_type( $product_id ) == 'product' ):
				$product = new WC_Product( $product_id );
			endif;
			$stock_quantity = $product->get_stock_quantity();
			$stock_status   = $product->get_stock_status();
			
			switch ( $service ):
				case 'trovaprezzi':
					switch ( $value ):
						case 'text':
							if ( $stock_quantity && $stock_quantity != '' ):
								if ( $stock_quantity == 0 ):
									$return = 'Non Disponibile';
                                elseif ( $stock_quantity == 1 ):
									$return = 'Disponibilità limitata';
                                elseif ( $stock_quantity >= 2 ):
									$return = 'Disponibile';
								endif;
							else:
								if ( $stock_status == 'instock' ):
									$return = 'Disponibile';
								else:
									$return = 'Non Disponibile';
								endif;
							endif;
							
							
							break;
						case 'number':
							if ( $stock_quantity && $stock_quantity != '' ):
								$return = $stock_quantity;
							else:
								if ( $stock_status == 'instock' ):
									$return = 'Disponibile';
								else:
									$return = 'Non Disponibile';
								endif;
							endif;
							break;
						case 'none':
							$return = '';
							break;
					endswitch;
					break;
			endswitch;
		
		
		endif;
		
		return $return;
	}
}
if ( ! function_exists( 'cpw_get_shipping_cost' ) ) {
	
	function cpw_get_shipping_cost( $service, $product_id ) {
		
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		
		$shipping_cost = '';
		
		if ( array_key_exists( 'shipping', $main_settings ) ):
			
			$value = $main_settings['shipping'];
			
			$product = cpw_new_product( $product_id, false );
			
			switch ( $value ):
				case 'fixed':
					if ( $main_settings && array_key_exists( 'shipping_fixed_value', $main_settings ) ):
						$shipping_cost = esc_html( $main_settings['shipping_fixed_value'] );
					endif;
					break;
				case 'none':
					$shipping_cost = 'n.d.';
					break;
				case 'free':
					$shipping_cost = '0';
					break;
				
				case 'cpw_shipping_custom_on_price':
					if ( is_array( $main_settings ) && array_key_exists( 'shipping_cond_on_price', $main_settings ) ):
						$shipping_cond_saved_values_on_price = $main_settings['shipping_cond_on_price'];
						if ( is_array( $shipping_cond_saved_values_on_price ) && count( $shipping_cond_saved_values_on_price ) > 0 ):
							$price = $product->get_price();
							foreach ( $shipping_cond_saved_values_on_price as $single_shipping_cond ):
								if ( ( $price >= $single_shipping_cond[0] )
								     && $price <= $single_shipping_cond[1]
								):
									$shipping_cost = esc_html( $single_shipping_cond[2] );
								endif;
							
							endforeach;
						
						
						endif;
					
					endif;
					
					break;
				case 'cpw_shipping_custom_on_weight':
					if ( is_array( $main_settings ) && array_key_exists( 'shipping_cond_on_weight', $main_settings ) ):
						$shipping_cond_saved_values_on_weight = $main_settings['shipping_cond_on_weight'];
						if ( is_array( $shipping_cond_saved_values_on_weight ) && count( $shipping_cond_saved_values_on_weight ) > 0 ):
							$weight = $product->get_weight();
							foreach ( $shipping_cond_saved_values_on_weight as $single_shipping_cond ):
								if ( ( $weight >= $single_shipping_cond[0] )
								     && $weight <= $single_shipping_cond[1]
								):
									$shipping_cost = esc_html( $single_shipping_cond[2] );
								endif;
							
							endforeach;
						
						
						endif;
					
					endif;
					break;
			endswitch;
		endif;
		
		
		return $shipping_cost;
	}
}
if ( ! function_exists( 'cpw_calc_number_products_by_source' ) ) {
	
	function cpw_calc_number_products_by_source( $service ) {
		
		
		$result = '<span class="cpw-number-products-tosave">Salva impostazioni</span>';
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'products_by_source', $main_option ) ):
				$products = $main_option['products_by_source'];
				if ( is_array( $products ) ):
					$result = '<div class="cpw-number-products"><h6>';
					$result .= '<span class="cpw-number-products-number">';
					$result .= '<span class="fg-darkBlue cpw-number-products-value">' . esc_html( count( $products ) ) . '</span>';
					$result .= '</span>';
					$result .= '<span class="cpw-number-products-service"> prodotti selezionati</span></h6>';
					$result .= '</div>';
				endif;
			endif;
		endif;
		
		
		return $result;
		
		
	}
}
if ( ! function_exists( 'cpw_calc_number_products_variations_by_source' ) ) {
	
	function cpw_calc_number_products_variations_by_source( $service ) {
		
		$result = '<div class="cpw-number-variations"><h6>';
		$result .= '<span class="cpw-number-products-number">';
		$result .= '<span class="fg-pink cpw-number-products-value">0</span>';
		$result .= '</span>';
		$result .= '<span class="cpw-number-products-service"> varianti</span></h6>';
		$result .= '</div>';
		
		
		return $result;
		
		
	}
}
if ( ! function_exists( 'cpw_calc_number_products_to_export' ) ) {
	
	function cpw_calc_number_products_to_export( $service ) {
		$result      = '';
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'products_to_export', $main_option ) ):
				$products = $main_option['products_to_export'];
				if ( is_array( $products ) ):
					$result = '<div class="cpw-number-products-ok"><h6>';
					$result .= '<span class="cpw-number-products-number"><span class="fg-emerald cpw-number-products-value">' . esc_html( count( $products ) ) . '</span></span>';
					$result .= '<span class="cpw-number-products-service"> verranno pubblicati</span></h6>';
					$result .= '</div>';
				endif;
			endif;
		endif;
		
		return $result;
		
		
	}
}
if ( ! function_exists( 'cpw_check_filter_is_ok' ) ) {
	
	
	function cpw_check_filter_is_ok( $service, $product_id ) {
		
		$return = false;
		
		$main_settings = array();
		
		$main_option = get_option( 'cpw_' . $service );
		
		if ( is_array( $main_option ) && count( $main_option ) > 0 ):
			if ( array_key_exists( 'settings', $main_option ) ):
				$main_settings = $main_option['settings'];
			endif;
		endif;
		
		$product = cpw_new_product( $product_id, true );
		$name    = $product->get_name();
		$price   = $product->get_price();
		
		
		switch ( $service ):
			case 'trovaprezzi':
				$tree_tax_exclude = array();
				if ( is_array( $main_settings ) && count( $main_settings ) > 0 && array_key_exists( 'tree_tax_exc', $main_settings ) ):
					$tree_tax_exclude = $main_settings['tree_tax_exc'];
				endif;
				$tree_tax = cpw_get_tree_tax( $service, $product_id, '>', $tree_tax_exclude );
				
				if (
					$name && ! empty( $name )
					&& $price && ! empty( $price )
					&& $tree_tax && ! empty( $tree_tax )
				):
					$return = true;
				endif;
				
				
				break;
		endswitch;
		
		return $return;
	}
}
if ( ! function_exists( 'cpw_render_feed_csv_button' ) ) {
	
	
	function cpw_render_feed_csv_button( $service ) {
		$return = '';
		
		
		$return .= '<div class="row cells2 cpw-feed-csv-buttons">';
		$return .= '<div class="cell">';
		$return .= '<a  href="#" onclick="alert(\'Disponibile in versione PREMIUM\')" class="image-button small-button bg-cyan fg-white text-shadow"> Feed XML <span class="icon mif-embed2 bg-darkCyan fg-white text-shadow"></span> </a>';
		$return .= '</div>';
		$return .= '<div class="cell">';
		$return .= '<a class="cpw-download-csv image-button  small-button bg-green fg-white text-shadow" data-service="' . esc_attr( $service ) . '"> CSV <span class="icon mif-file-download bg-darkGreen fg-white text-shadow"></span> </a>';
		$return .= '</div>';
		$return .= '</div>';
		
		return $return;
	}
}
if ( ! function_exists( 'cpw_render_info_service' ) ) {
	
	function cpw_render_info_service( $service ) {
		
		$dialog = '';
		switch ( $service ):
			case 'trovaprezzi':
				$dialog = '<div data-role="dialog" data-overlay-color="op-dark" data-overlay-click-close="true" data-close-button="true" id="dialog' . $service . '" class="padding20 dialog"  data-overlay="true" style="width: auto; height: auto; visibility: hidden;max-width: 60%; max-height: 80vh;overflow-y: scroll;">
                            <h3>Requisiti Trovaprezzi</h3>
                            <table class="table striped">
       <tr>
       <th>Campo</th>
       <th>Contenuto</th>
</tr>
<tr class="align-left">
       <th>Nome</th>
       <th>OBBLIGATORIO. Modello/Titolo. No slogan o frasi promozionali.</th>
</tr>
<tr class="align-left">
       <th>Marca</th>
       <th>FACOLTATIVO. Marca del produttore, suggeriamo di indicarla sempre.</th>
</tr>
<tr class="align-left">
       <th>Descrizione</th>
       <th>FACOLTATIVO. Descrizione breve dell\'offerta. Deve indicare le caratteristiche principali. Per una corretta visualizzazione non dovrebbe contenere codice HTML. Max 255 caratteri. No Slogan o frasi promozionali.</th>
</tr>
<tr class="align-left">
       <th>Prezzo Vendita</th>
       <th>OBBLIGATORIO. Prezzo di vendita: numerico, comprensivo di Iva e di ogni altra tassa o contributo previsto, senza separatore delle migliaia e nessun altro testo. No simbolo dell\'Euro.</th>
</tr>
<tr class="align-left">
       <th>Prezzo Originale</th>
       <th>FACOLTATIVO. Prezzo originale dell\'offerta: numerico, comprensivo di Iva, senza separatore delle migliaia e nessun altro testo. No simbolo dell\'Euro.</th>
</tr>
<tr class="align-left">
       <th>Codice Interno (ID Offerta o SKU)</th>
       <th>OBBLIGATORIO. UNICO PER CIASCUN OFFERTA; solitamente è il codice interno dell\'inserzionista. Il sistema considera questo campo "Case Insensitive", cioè i caratteri in maiuscolo e in minuscolo saranno considerati identici. Max 50 caratteri.</th>
</tr>
<tr class="align-left">
       <th>Link all\'offerta</th>
       <th>OBBLIGATORIO. Link alla pagina dell\'offerta sul sito dell\'inserzionista (deve essere completa di http://).</th>
</tr>
<tr class="align-left">
       <th>Disponibilità</th>
       <th>FACOLTATIVO. I valori utilizzabili possono essere numerici o testuali. Varietà di valori: Disponibile: nel caso in cui il campo riporti valori numerici maggiori o uguali a 2 Disponibilità limitata: nel caso in cui il campo riporti il valore 1 Non disponibile: nel caso in cui il campo riporti il valore 0 Vedi negozio per disponibilità: nel caso in cui il campo sia vuoto (oppure riporti diciture testuali non riconosciute dal nostro sistema)</th>
</tr>
<tr class="align-left">
       <th>Albero categorie</th>
       <th>OBBLIGATORIO. Categorie del sito dell\'inserzionista. E\' preferibile che siano riportati tutti i livelli, dalla macro-categoria all\'ultima sotto-categoria, con un separatore diverso rispetto a quello utilizzato come separatore di campo: consigliamo di utilizzare il carattere ";" oppure ",". I caratteri dovrebbero essere tutti in minuscolo (esempio: “fotografia;macchine digitali”).</th>
</tr>
<tr class="align-left">
       <th>Link all\'Immagine</th>
       <th>FACOLTATIVO. Link all\'immagine dell\'offerta. No link a immagine vuota o link a immagine “non disponibile”. Dovranno essere possibilmente pulite e trasparenti, in particolare NON dovranno essere bordate e NON dovranno riportare slogan o parole promozionali. Consigliamo di fornirci immagini della dimensione più grande a disposizione.</th>
</tr>
<tr class="align-left">
       <th>Spese di Spedizione</th>
       <th>OBBLIGATORIO. Numerico, comprensivo di Iva, senza separatore delle migliaia e senza testo o simboli (NO simbolo dell’euro). Se le spese sono incluse mettere 0. N.B.: usare lo stesso separatore decimale del prezzo.</th>
</tr>
<tr class="align-left">
       <th>Codice Produttore</th>
       <th>FACOLTATIVO. Chiamato anche Part-Number o ManufacturerSKU. E\' il codice univoco che le case produttrici attribuiscono ai propri prodotti.</th>
</tr>
<tr class="align-left">
       <th>Codice EAN</th>
       <th>ALTAMENTE RACCOMANDATO. Codice EAN del prodotto, univoco e definito a livello Europeo. Utile al fine del riconoscimento e relativa associazione offerta/scheda tecnica.</th>
</tr>
<tr class="align-left">
       <th>Ulteriori Link Immagine</th>
       <th>FACOLTATIVI. Per chi ne disponesse, si richiede di riportare più link ad immagini diverse della stessa offerta. N.B.: ogni ulteriore link immagine deve essere riportato in un campo aggiuntivo.</th>
</tr>

</table>
<span class="dialog-close-button"></span>
                            </div>
                            
                        <button onclick="metroDialog.toggle(\'#dialog' . esc_attr( $service ) . '\')"
                                class="image-button bg-white fg-black">
                            <span class="icon mif-info bg-white fg-black"></span>
                            Requisiti Trovaprezzi.it
                        </button>';
				break;
		endswitch;
		
		$return = '<tr>';
		$return .= '<td><img class="cpw-image-service" src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/logo_tp.png"/></td>';
		$return .= '<td>' . esc_html( $dialog ) . '</td>';
		$return .= '<td>' . cpw_render_feed_csv_button( 'trovaprezzi' ) . '</td>';
		$return .= '</tr>';
		
		return $return;
	}
}
if ( ! function_exists( 'cpw_loader' ) ) {
	
	function cpw_loader() {
		echo '<div class="cpw-loader">
        <div class="cpw-loader-spinner">
            <div class="cpw-loader-double-bounce1"></div>
            <div class="cpw-loader-double-bounce2"></div>
        </div>
        <div class="cpw-loader-text">
        Attendere, l\' elaborazione potrebbe richiedere parecchio tempo..
</div>
    </div>';
	}
}
if ( ! function_exists( 'cpw_utf8_json_encode' ) ) {
	
	function cpw_utf8_json_encode( $d ) {
		if ( is_array( $d ) ) {
			foreach ( $d as $k => $v ) {
				$d[ $k ] = cpw_utf8_json_encode( $v );
			}
		} else if ( is_string( $d ) ) {
			return utf8_encode( $d );
		}
		
		return $d;
	}
}
if ( ! function_exists( 'cpw_sanitize_array' ) ) {
	function cpw_sanitize_array( &$array ) {
		
		foreach ( $array as &$value ) {
			
			if ( ! is_array( $value ) ) {
				$value = sanitize_text_field( $value );
				
			} else {
				cpw_sanitize_array( $value );
			}
		}
		
		return $array;
		
	}
}
if ( ! function_exists( 'cpw_sanitize_int_array' ) ) {
	function cpw_sanitize_int_array( &$array ) {
		
		foreach ( $array as &$value ) {
			
			if ( ! is_array( $value ) ) {
				$value = intval( $value );
				
			} else {
				cpw_sanitize_int_array( $value );
			}
		}
		
		return $array;
		
	}
}