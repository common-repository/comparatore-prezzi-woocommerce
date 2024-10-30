<?php


if ( ! function_exists( 'cpw_menu_trovaprezzi' ) ) {
	function cpw_menu_trovaprezzi() {
		?>
		<?php cpw_loader(); ?>
        <div class="wrap cpw-wrap cpw-wrap-trovaprezzi">
        <img class="cpw-image-service"
             src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>assets/img/logo_tp.png"/>
        <div class="grid">

        <div class="row">
            <div class="cell">
                <div class="tabcontrol2" data-role="tabcontrol">
                    <ul class="tabs">
                        <li><a href="#feed_settings">
                                <h5>Impostazioni</h5>
                            </a>
                        </li>
                        <li>
                            <a href="#feed_products" data-service="trovaprezzi">
                                <h5>DEBUG Prodotti su Trovaprezzi</h5>
                            </a>
                        </li>
                    </ul>
                    <div class="frames">
                        <div class="frame" id="feed_settings">
                            <div class="cpw-admin-block">
                                <h4 class="cpw-admin-block-title">Source</h4>
                                <p class="margin40 no-margin-left">
                                    Seleziona , sfogliando i vari tab, i prodotti che vuoi esportare su TrovaPrezzi.
                                    Puoi selezionarli per categorie, tag, tag prodotto, classi spedizione, attributi ed
                                    eventuali nuove tassonomie.
                                    Inoltre puoi selezionare i prodotti per Brands grazie a "Brand Trovaprezzi". Puoi
                                    impostare
                                    il Brand all' interno di ogni prodotto
                                    Puoi scegliere anche di aggiungere i prodotti In Saldo e/o In Vetrina
                                </p>
								<?php
								cpw_render_select_terms( 'trovaprezzi' );
								cpw_render_variations( 'trovaprezzi' );
								?>
								<?php cpw_render_save_button( 'trovaprezzi' ); ?>
                            </div>
                            <div class="cpw-admin-block">
                                <h4 class="cpw-admin-block-title">Filtri</h4>
                                <p class="margin40 no-margin-left">
                                    Da questa sezione sarà possibile, se lo si ritiene necessario, effettuare una
                                    raffinazione ed un filtro dei prodotti da esportare nel Feed ( o CSV ) di
                                    Trovaprezzi.
                                </p>
								<?php
								cpw_render_sale( 'trovaprezzi' );
								cpw_render_featured( 'trovaprezzi' );
								
								cpw_render_price_filter( 'trovaprezzi' );
								cpw_render_qta_filter( 'trovaprezzi' );
								?>
								<?php cpw_render_save_button( 'trovaprezzi' ); ?>
                            </div>
                            <div class="cpw-admin-block">
                                <h4 class="cpw-admin-block-title">Dettagli Trovaprezzi</h4>
                                <p class="margin40 no-margin-left">
                                    Impostazioni per la creazione del Feed. Seleziona i valori che vuoi mostrare.
                                    Il Feed ed il CSV saranno costituiti da paramentri a cui è necessario associare dei
                                    valori.
                                    Da questa sezione è possibile scegliere quale valore associare a ciascun parametro.
                                </p>
								<?php
								cpw_render_brand( 'trovaprezzi' );
								cpw_render_desc( 'trovaprezzi' );
								cpw_render_original_price( 'trovaprezzi' );
								cpw_render_stock( 'trovaprezzi' );
								cpw_render_tree_tax( 'trovaprezzi' );
								cpw_render_tree_tax_exc( 'trovaprezzi' );
								cpw_render_shipping( 'trovaprezzi' );
								cpw_render_partnbr( 'trovaprezzi' );
								cpw_render_ean( 'trovaprezzi' );
								cpw_render_other_images( 'trovaprezzi' );
								
								
								?>
								<?php cpw_render_save_button( 'trovaprezzi' ); ?>
                            </div>


                        </div>
                        <div class="frame" id="feed_products">
                            <table id="cpw_products_trovaprezzi"
                                   class="cpw_products_on_service display border bordered striped"
                                   cellspacing="0" width="100%"></table>
							<?php cpw_render_debug_legend(); ?>
                        </div>

                    </div>
                </div>
            </div>
			<?php /*
        
        */ ?>
            <div id="cpw_preview" class="row">
                <div class="cell">
                    <div class="cpw-sidebar">
                        <div class="cpw-admin-block">
                            <div id="cpw-number-of-products" class="row cells4">
                                <div class="cell"><?php echo cpw_calc_number_products_by_source( 'trovaprezzi' ); ?></div>
                                <div class="cell"><?php echo cpw_calc_number_products_variations_by_source( 'trovaprezzi' ); ?></div>
                                <div class="cell"><?php echo cpw_calc_number_products_to_export( 'trovaprezzi' ); ?></div>
                                <div class="cell"><?php echo cpw_render_feed_csv_button( 'trovaprezzi' ); ?></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}