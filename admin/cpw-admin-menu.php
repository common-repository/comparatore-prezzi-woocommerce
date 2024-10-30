<?php


if ( ! function_exists( 'cpw_menu' ) ) {
	add_action( 'admin_menu', 'cpw_menu' );
	
	function cpw_menu() {
		add_menu_page( 'CPW', 'CPW [FREE]', 'manage_options', 'cpw', 'cpw_options', dirname( plugin_dir_url( __FILE__ ) ) . '/assets/img/moneybag.png' );
	}
	
	function cpw_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
		?>
		<?php cpw_loader(); ?>
        <div class="wrap">
            <h2>Comparatore Prezzi WooCommerce</h2>
            <div class="grid">
                <table class="table striped">
                    <thead>
                    <tr>
                        <th>SERVIZIO</th>
                        <th>REQUISITI</th>
                        <th>FEED XML & DOWNLOAD CSV</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php echo cpw_render_info_service( 'trovaprezzi' ); ?>
                    </tbody>
                </table>
            </div>
        </div>
		<?php
	}
}


if ( ! function_exists( 'cpw_create_submenu_pages' ) ) {
// sub menu
	add_action( 'admin_menu', 'cpw_create_submenu_pages' );
	
	function cpw_create_submenu_pages() {
		
		// trovaprezzi
		add_submenu_page( 'cpw', 'Trovaprezzi', 'Trovaprezzi', 'manage_options', 'cpw_trovaprezzi', 'cpw_menu_trovaprezzi' );
	}
}