jQuery(document).ready(function () {

    var ajaxurl = cpw_ajax.adminajax;

    jQuery('.cpw-selectize').selectize();

    jQuery('.cpw-save-button').on('click', function () {

        jQuery('.cpw-loader').fadeIn();

        var service = jQuery(this).data('service');
        var taxonomies_value = {};
        var shipping_cond_on_price = {};
        var shipping_cond_on_weight = {};

        var sale = jQuery('input[name="_sale"]').is(':checked');
        var other_images = jQuery('input[name="other_images"]').is(':checked');
        var featured = jQuery('input[name="_featured"]').is(':checked');
        var tree_tax = jQuery('.cpw-service-tree-tax').val();
        var tree_tax_exc = jQuery('.cpw-service-tree-tax-exc').val();
        var cpw_brand = jQuery('.cpw-service-brand').val();
        var cpw_brand_cpm = jQuery('.cpw-service-brand-cpm').val();
        var desc = jQuery('.cpw-service-desc').val();
        var stock = jQuery('.cpw-service-stock').val();
        var ean = jQuery('.cpw-service-ean').val();
        var ean_cpm = jQuery('.cpw-service-ean-cpm').val();
        var partnbr = jQuery('.cpw-service-partnbr').val();
        var partnbr_cpm = jQuery('.cpw-service-partnbr-cpm').val();
        var shipping = jQuery('.cpw-service-shipping').val();
        var shipping_fixed_value = jQuery('.cpw-service-shipping-fixed-value').val();
        var filter_max_price = jQuery('.cpw-service-filter-max-price').val();
        var filter_min_price = jQuery('.cpw-service-filter-min-price').val();
        var filter_min_qta = jQuery('.cpw-service-filter-min-qta').val();

        jQuery('.cpw-service-taxonomy').each(function () {
            if (jQuery(this).val()) {
                taxonomies_value[jQuery(this).attr('name')] = jQuery(this).val();
            }
        });
        jQuery('.cpw-service-shipping-combinations-price .cpw-service-shipping-combinations-single').each(function () {
            if (jQuery(this).find('input[name="min"]').val() != ''
                && jQuery(this).find('input[name="max"]').val() != ''
                && jQuery(this).find('input[name="shipping_cost"]').val() != ''
            ) {

                shipping_cond_on_price[jQuery(this).data('cycle')] = [
                    jQuery(this).find('input[name="min"]').val(),
                    jQuery(this).find('input[name="max"]').val(),
                    jQuery(this).find('input[name="shipping_cost"]').val()
                ]
            }

        });
        jQuery('.cpw-service-shipping-combinations-weight .cpw-service-shipping-combinations-single').each(function () {
            if (jQuery(this).find('input[name="min"]').val() != ''
                && jQuery(this).find('input[name="max"]').val() != ''
                && jQuery(this).find('input[name="shipping_cost"]').val() != ''
            ) {

                shipping_cond_on_weight[jQuery(this).data('cycle')] = [
                    jQuery(this).find('input[name="min"]').val(),
                    jQuery(this).find('input[name="max"]').val(),
                    jQuery(this).find('input[name="shipping_cost"]').val()
                ]
            }

        });


        var data = {
            action: 'cpw_save_service_settings',
            taxonomies_value: taxonomies_value,
            service: service,
            other_images: other_images,
            sale: sale,
            featured: featured,
            tree_tax: tree_tax,
            tree_tax_exc: tree_tax_exc,
            cpw_brand: cpw_brand,
            cpw_brand_cpm: cpw_brand_cpm,
            desc: desc,
            stock: stock,
            ean: ean,
            ean_cpm: ean_cpm,
            partnbr: partnbr,
            partnbr_cpm: partnbr_cpm,
            shipping: shipping,
            shipping_fixed_value: shipping_fixed_value,
            shipping_cond_on_price: shipping_cond_on_price,
            shipping_cond_on_weight: shipping_cond_on_weight,
            filter_max_price: filter_max_price,
            filter_min_price: filter_min_price,
            filter_min_qta: filter_min_qta
        };


        jQuery.post(ajaxurl, data, function (response) {
            cpw_download_csv_click();
            var resp = JSON.parse(response);
            if (resp.number_of_products) {
                jQuery('#cpw-number-of-products').html('<div class="cell">' + resp.number_of_products + '</div><div class="cell">' + resp.number_of_variations + '</div><div class="cell">' + resp.number_of_products_ok + '</div><div class="cell">' + resp.buttons + '</div>');
            }
            jQuery('.cpw-loader').fadeOut();
        });

        return false;
    });

    jQuery('a[href="#feed_products"]').on('click', function () {
        jQuery('.cpw-loader').fadeIn();
        var service = jQuery(this).data('service');


        var data = {
            action: 'cpw_load_products',
            service: service
        };


        jQuery.post(ajaxurl, data, function (response) {
            jQuery('.cpw-loader').fadeOut();
            cpw_download_csv_click();
            if (response && response != '') {


                var products = jQuery.makeArray(JSON.parse(response));
                if (service == 'trovaprezzi') {
                    jQuery('#cpw_products_trovaprezzi').DataTable({
                        destroy: true,
                        pageLength: 20,
                        "bJQueryUI": true, "sPaginationType": "full_numbers",
                        data: products,
                        columns: [
                            {title: "Imm"},
                            {title: "ID"},
                            {title: "Nome"},
                            {title: "Desc"},
                            {title: "Albero Categorie"},
                            {title: "Brand"},
                            {title: "Ean"},
                            {title: "Part Number"},
                            {title: "Variante"},
                            {title: "Disponibilità"},
                            {title: "Prezzo"},
                            {title: "Prezzo Originale"},
                            {title: "Costo Spedizione"},
                            {title: "Altre Immmagini"},
                            {title: "Status"},
                        ]
                    });
                    cpw_more();
                }
            }
        });

    });

    jQuery('select[name="cpw_tree_tax"]').on('change', function () {

        if (jQuery('select[name="cpw_tree_tax_exc"]').length > 0) {

            jQuery('.cpw-loader').fadeIn();
            var data = {
                action: 'cpw_reload_tree_tax_exc',
                tax_value: jQuery(this).val()
            };

            jQuery.post(ajaxurl, data, function (response) {
                jQuery('.cpw-loader').fadeOut();
                if (response && response != '') {
                    jQuery('.cpw_tree_tax_exc_container').html(response);
                    jQuery('select[name="cpw_tree_tax_exc"]').selectize();
                }
            });
        }
    });

    jQuery('.cpw-service-shipping').on('change', function () {

        jQuery('.cpw-service-shipping-combinations').slideUp();
        jQuery('.cpw-service-shipping-fixed-value-container').slideUp();

        if (jQuery(this).val() == 'cpw_shipping_custom_on_weight') {
            jQuery('.cpw-service-shipping-combinations-weight').slideDown();
        }
        else if (jQuery(this).val() == 'cpw_shipping_custom_on_price') {
            jQuery('.cpw-service-shipping-combinations-price').slideDown();
        }
        else if (jQuery(this).val() == 'fixed') {
            jQuery('.cpw-service-shipping-fixed-value-container').slideDown();
        }

    });

    jQuery('#cpw_preview').scrollToFixed({bottom: 0});

    cpw_download_csv_click();


});


function cpw_more() {
    var showChar = 20;  // How many characters are shown by default
    var ellipsestext = "...";
    var moretext = '<i class="mif-expand-more"></i>';
    var lesstext = '<i class="mif-expand-less"></i>';


    jQuery('.cpw_more').each(function () {
        var content = jQuery(this).html();

        if (content.length > showChar) {

            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);

            var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="cpw_morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="cpw_morelink">' + moretext + '</a></span>';

            jQuery(this).html(html);
        }

    });

    jQuery(".cpw_morelink").click(function () {
        if (jQuery(this).hasClass("less")) {
            jQuery(this).removeClass("less");
            jQuery(this).html(moretext);
        } else {
            jQuery(this).addClass("less");
            jQuery(this).html(lesstext);
        }
        jQuery(this).parent().prev().toggle();
        jQuery(this).prev().toggle();
        return false;
    });
}

function cpw_download_csv_click() {
    jQuery('.cpw-download-csv').on('click', function () {
        jQuery('.cpw-loader').fadeIn();
        var service = jQuery(this).data('service');

        var data = {
            action: 'function_cpw_csv_trovaprezzi',
            service: service
        };

        jQuery.post(ajaxurl, data, function (response) {
            jQuery('.cpw-loader').fadeOut();
            location.href = response;
            //console.log(response);
        });


    });
}