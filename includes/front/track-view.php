<?php

//Add Tracking to Order View Account
add_action( 'woocommerce_view_order', 'diurvan_custom_tracking',5 , 1 );
function diurvan_custom_tracking( $order_id ){
    $current_status_history = get_post_meta( $order_id, 'status_tracking_history', true );
	if($current_status_history!=''){
		$split_status_history = explode(',', $current_status_history);
		// Display the order status 
    	echo '<h2>'.__("Your Tracking Status History", "diu-wc-tracking-status").'</h2>';
		echo '<table><td>' . implode('<br/>', $split_status_history) . '</td></table>';
	}
}

add_shortcode('diurvan_custom_tracking', 'diurvan_custom_tracking_function');
function diurvan_custom_tracking_function(){
	ob_start();
	global $wp;

	$use_bootstrap = get_option('diu_wc_use_bootstrap');
	$include_notes = get_option('diu_wc_include_notes');
 $custom_number_tracking = get_option('diu_wc_custom_number_tracking');

	if(empty( $use_bootstrap )){
		wp_enqueue_style( 'diur-bootstrap-min', plugin_dir_url( __DIR__ ) . '../css/bootstrap.min.css' );
	}
	wp_enqueue_style( 'diur-bundle', plugin_dir_url( __DIR__ ) . '../css/style.bundle.css' );

	$order_id = 0;
	if(isset($_POST["wc_tracking_order"]) && $_POST["wc_tracking_order"]!=''){
		$order_id = $_POST["wc_tracking_order"];
	}
	if(isset($_GET["wc_tracking_order"]) && $_GET["wc_tracking_order"]!=''){
		$order_id = $_GET["wc_tracking_order"];
	}
	
	// USE OWN CUSTOM NUMBER TRACKING FROM WC-TRACKING-STATUS
	if( $custom_number_tracking ){
		global $wpdb;
		$meta = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='wc_tracking_number_tracking' AND meta_value='".$order_id."'");
		if (is_array($meta) && !empty($meta) && isset($meta[0])) {
			$meta = $meta[0];
		}		
		if (is_object($meta)) {
			$order_id = $meta->post_id;
		}
	}
	// USE OWN CUSTOM NUMBER TRACKING FROM WC-TRACKING-STATUS
	
	if($order_id != 0){
		//$order_id = $_POST["wc_tracking_order"];
		global $woocommerce;
		$orden = wc_get_order($order_id);
		/* REQUIERED FOR https://kolmite.com/numeros-de-orden-secuencial-de-woocommerce */
		/* Se reemplaza el ID de la Orden, por el Secuencial Number del plugin */
		//$order_id = $orden->get_order_number();
		
		if($orden){
			$current_status = get_post_meta( $order_id, 'status_tracking', true );
			$cat_args = array( 'orderby' => 'name', 'order' => 'asc', 'hide_empty' => false );
			$diurvan_wctracking_state = get_terms( 'diurvan_wctracking_state', $cat_args );
			$diurvan_wctracking_state_final = array();
			foreach ( $diurvan_wctracking_state as $wc_tracking_state ) {
				$orden_tracking_cat = get_term_meta( $wc_tracking_state->term_id, 'diurvan_wc_tracking_orden', true );
				$diurvan_wctracking_state_final[$orden_tracking_cat] = (object) array(
					'name' => $wc_tracking_state->name,
					'slug' => $wc_tracking_state->slug,
					'term_id' => $wc_tracking_state->term_id
				);
			}
			ksort( $diurvan_wctracking_state_final, SORT_NUMERIC );
			if(count($diurvan_wctracking_state_final) > 0){
				$estados = array(); $valor = 0; $indice = 1; $totalindice = 100/count($diurvan_wctracking_state_final);
				foreach($diurvan_wctracking_state_final as $wc_tracking_state){
					$estados[$totalindice * $indice] = $wc_tracking_state->name;
					if($current_status == $wc_tracking_state->term_id){
						$valor = $totalindice * $indice;
					}
					$indice++;
				}
			}

			?>

<script>
	jQuery( document ).ready(function() {
		var x = document.getElementsByClassName("tracking-submit");
		for (var i = 0; i < x.length; i++) {
		x[i].addEventListener('click', function() {
			location.href = "<?php echo home_url( $wp->request );?>";
		});
		};
	});
</script>

		<ul class="stepper__list">
			<?php
			if(count($diurvan_wctracking_state_final) > 0){
				$step = 0;
				$current_indice = 0;
				foreach($diurvan_wctracking_state_final as $wc_tracking_state){
					$step +=1;
					if($current_status == $wc_tracking_state->term_id){
						$current_indice = 100/count($diurvan_wctracking_state_final) * $step;
					}
				}
				$step = 0;
				foreach($diurvan_wctracking_state_final as $wc_tracking_state){
					$step +=1;
					$indice = 100/count($diurvan_wctracking_state_final) * $step;
			?>
			<li class="stepper__list__item stepper__list__item--<?php if($indice <= $current_indice) echo "done"; else echo "pending"; ?>">
				<?php if($indice <= $current_indice) { ?>
					<svg class="stepper__list__icon" viewBox="0 0 24 24">
					<path class="st0" d="M12 20c4.4 0 8-3.6 8-8s-3.6-8-8-8-8 3.6-8 8 3.6 8 8 8zm0 1.5c-5.2 0-9.5-4.3-9.5-9.5S6.8 2.5 12 2.5s9.5 4.3 9.5 9.5-4.3 9.5-9.5 9.5z"></path>
					<path class="st0" d="M11.1 12.9l-1.2-1.1c-.4-.3-.9-.3-1.3 0-.3.3-.4.8-.1 1.1l.1.1 1.8 1.6c.1.1.4.3.7.3.2 0 .5-.1.7-.3l3.6-4.1c.3-.3.4-.8.1-1.1l-.1-.1c-.4-.3-1-.3-1.3 0l-3 3.6z"></path>
					</svg>
				<?php }else{ ?>
					<svg class="stepper__list__icon stepper__list__icon--current" width="24" height="24" viewBox="0 0 24 24">
					<path d="M12 16.1c1.8 0 3.3-1.4 3.3-3.2 0-1.8-1.5-3.2-3.3-3.2s-3.3 1.4-3.3 3.2c0 1.7 1.5 3.2 3.3 3.2zm0 1.7c-2.8 0-5-2.2-5-4.9S9.2 8 12 8s5 2.2 5 4.9-2.2 4.9-5 4.9z"></path>
					</svg>
				<?php } ?>
				<span class="stepper__list__title"><?php echo $wc_tracking_state->name ?></span>
			</li>
			<?php } } ?>
		</ul>
		<br/>
			<?php

			$shipping_total = $orden->get_shipping_total();
			$shipping_tax   = $orden->get_shipping_tax();
			$shipping_total_cost = $shipping_total + $shipping_tax;

			if(!empty( $include_notes )){
				$order_notes = wc_get_order_notes([
					'order_id' => $order_id,
					'type' => 'customer',
				 ]);
				$content_nota = '<ul>';
				foreach($order_notes as $note){
					$content_nota .= '<li><span style="text-decoration: underline;">'.date("Y-m-d", strtotime($note->date_created) ).':</span> '.$note->content.'</li>';
				}
				$content_nota .= '</ul>';
			}

			?>
			<div class="form-group">
				<h5><b><?php echo __( 'ORDER INFORMATION', 'diu-wc-tracking-status');?></b></h5>
			</div>
			<div class="row">
				<div class="col-4"><label><?php echo __( 'Order number', 'diu-wc-tracking-status')?>:</label></div>
				<div class="col-8"><label><?php echo $order_id?></label></div>
			</div>
			<div class="row">
				<div class="col-4"><label><?php echo __( 'Company name', 'diu-wc-tracking-status')?>:</label></div>
				<div class="col-8"><label><?php echo $orden->get_billing_company()?></label></div>
			</div>
			<div class="row">
				<div class="col-4"><label><?php echo __( 'Contact name', 'diu-wc-tracking-status')?>:</label></div>
				<div class="col-8"><label><?php echo $orden->get_billing_first_name().' '.$orden->get_billing_last_name()?></label></div>
			</div>
			<div class="row">
				<div class="col-4"><label><?php echo __( 'Contact telephone', 'diu-wc-tracking-status')?>:</label></div>
				<div class="col-8"><label><?php echo $orden->get_billing_phone()?></label></div>
			</div>
			<?php if(strlen($content_nota)>0){ ?>
			<div class="row">
				<div class="col-4"><label><?php echo __( 'Order notes', 'diu-wc-tracking-status')?>:</label></div>
				<div class="col-8"><?php echo $content_nota?></div>
			</div>
			<?php }	?>

			<?php if(get_option('diu_wc_include_plugin_seguimiento') == 1 ){
				$opciones = get_post_meta ($order_id, 'seguimiento', true);
				$args = array('slug' => $opciones['transportista'], 'post_type' => 'agencias');
				$agencias = new WP_Query( $args );
				$url_agencia = '';
				if ($agencias->posts){
					foreach ( $agencias->posts as $agencia ) {
						if($agencia->post_name == $opciones['transportista']){
							$url_agencia = get_field('url_seguimiento', $agencia->ID);
							$url_agencia = str_replace('%ref%',$opciones['codigo'],$url_agencia);
							break;
						}						
					}
				}
				?>
			<div class="row">
				<div class="col-4"><label><?php echo __( 'Tracking Number', 'diu-wc-tracking-status')?>:</label></div>			
				<div class="col-8"><label><?php echo $opciones['codigo']; ?></label>&nbsp;&nbsp;&nbsp;(<a target="_blank" href="<?php echo $url_agencia; ?>"><span class="product-title"><?php echo __('Ver tracking', 'diu-wc-tracking-status') ?></span></a>)</div>			
			</div>
			</div>
			<div class="row">
				<div class="col-4"><label><?php echo __( 'Agency', 'diu-wc-tracking-status')?>:</label></div>
				<div class="col-8"><label><?php echo $opciones['transportista']; ?></label></div>
			</div>
			<div class="row">
				<div class="col-4"><label><?php echo __( 'Shipping Date', 'diu-wc-tracking-status')?>:</label></div>
				<div class="col-8"><label><?php echo $opciones['fecha']; ?></label></div>
			</div>
			<?php }	?>

			<div class="row">
				<div class="col-4"><label><b><?php echo __( 'Order Detail', 'diu-wc-tracking-status')?></b></label></div>
			</div>
			<div class="row">
				<div class="table-responsive">
					<table class="tracking-table table table-striped">
					<thead>
						<tr>
							<th scope="col"><?php echo __("Imagen", "diu-wc-tracking-status")?></th>
							<th scope="col"><?php echo __("Nombre Producto", "diu-wc-tracking-status")?></th>
							<th scope="col"><?php echo __("Cantidad", "diu-wc-tracking-status")?></th>
							<th scope="col"><?php echo __("Sub-Total", "diu-wc-tracking-status")?></th>
							<th scope="col"><?php echo __("Impuesto", "diu-wc-tracking-status")?></th>
							<th scope="col"><?php echo __("Total", "diu-wc-tracking-status")?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( count( $orden->get_items() ) > 0 ) {
							foreach ( $orden->get_items() as $item_id => $item ) {
								//$product      = $item->get_product();
								$item_name    = $item->get_name();
								$quantity     = $item->get_quantity();
								$line_subtotal     = $item->get_subtotal();
								$line_total        = $item->get_total()+$item->get_total_tax();
								$line_fee = $item->get_total_tax();
								$image_product = wp_get_attachment_image_src( get_post_thumbnail_id( $item->get_product_id() ), array('20', '20') );
								?>
								<tr>
									<td><img src="<?php echo $image_product[0]; ?>" data-id="<?php echo $item_id; ?>"></td>
									<td><?php echo $item_name?></td>
									<td><?php echo $quantity?></td>
									<td class="text-right"><?php echo number_format($line_subtotal, 2)?></td>
									<td class="text-right"><?php echo number_format($line_fee,2)?></td>
									<td class="text-right"><?php echo number_format($line_total,2)?></td>
								</tr>
								<?php
							}
							if($shipping_total_cost>0){
								?>
								<tr>
									<td colspan="5" class="text-right"><b><?php echo __("Costo de envío", "diu-wc-tracking-status")?></b></td>
									<td class="text-right"><b><?php echo number_format($shipping_total_cost,2)?></b></td>
								</tr>
								<?php
							}
							?>
							<tr>
								<td colspan="5" class="text-right"><b><?php echo __("TOTAL", "diu-wc-tracking-status")?></b></td>
								<td class="text-right"><b><?php echo number_format($orden->get_total(),2)?></b></td>
							</tr>
							<?php
						}
						else{
							?>
							<tr>
								<td><?php echo __("Orden no tiene items", "diu-wc-tracking-status")?></td>
							</tr>
							<?php
						}
						?>
					</tbody>
					</table>
				</div>
			</div>
			<button type="submit" class="tracking-submit btn btn-primary"><?php echo __("Volver", "diu-wc-tracking-status") ?></button>
			<?php
		}
		else{
			?>
			<form method="post" class="text-center;">
				<div class="form-group">
					<span style="height: 50px;"><b><?php echo __("No hay información para la orden ingresada No.", "diu-wc-tracking-status")?></b></span>
				</div>
				<button class="tracking-submit btn btn-primary"><?php echo __("Volver", "diu-wc-tracking-status") ?></button>
			</form>
			<?php
		}
	}
	else{
		?>
		<form method="post">
			<div class="form-group row">
				<label class="col-sm-3 col-form-label"><?php echo __( 'Número de orden', 'diu-wc-tracking-status')?></label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="wc_tracking_order" name="wc_tracking_order" placeholder="<?php echo __( 'Número de orden', 'diu-wc-tracking-status')?>">
				</div>
			</div>
			<div class="form-group row">
				<div class="col-sm-10 offset-3">
					<button class="tracking-submit btn btn-primary"><?php echo __("Tracking", "diu-wc-tracking-status") ?></button>
				</div>
			</div>
		</form>

		<?php 
	}
	return ob_get_clean();
}

/* Update States to Users */
add_filter ( 'woocommerce_account_menu_items', 'wc_tracking_update_link', 40 );
function wc_tracking_update_link( $menu_links ){
    $user = wp_get_current_user();
    if ( in_array( 'wc_courier', (array) $user->roles ) ) {
        $menu_links = array_slice( $menu_links, 0, 5, true ) 
        + array( 'wc-tracking-status' => __('Actualizar Estados','diu-wc-tracking-status') )
        + array_slice( $menu_links, 5, NULL, true );
    }
    return $menu_links;
}
add_action( 'init', 'wc_tracking_add_endpoint' );
function wc_tracking_add_endpoint() {
	add_rewrite_endpoint( 'wc-tracking-status', EP_PAGES );
}
add_action( 'woocommerce_account_wc-tracking-status_endpoint', 'wc_tracking_my_account_endpoint_content' );
function wc_tracking_my_account_endpoint_content() {
    $order_ids = wc_get_orders( array(
        'limit'        => -1, // Query all orders
        'return' => 'ids'
    ));
    if(count($order_ids) > 0 ){
        ?>
        <div class="woocommerce-notices-wrapper"></div>
        <p><?php echo __('Estas son tus órdenes asignadas: ', 'diu-wc-tracking-status'); ?></p>

        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
            <thead>
                <tr>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php echo __('Nro', 'diu-wc-tracking-status'); ?></span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php echo __('Orden', 'diu-wc-tracking-status'); ?></span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php echo __('Estado', 'diu-wc-tracking-status'); ?></span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php echo __('Fecha', 'diu-wc-tracking-status'); ?></span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php echo __('Total', 'diu-wc-tracking-status'); ?></span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?php echo __('Nuevo Estado', 'diu-wc-tracking-status'); ?></span></th>
                </tr>
            </thead>
            <tbody>
        <?php

        $calc_comm_total = 0; $indice = 0;
        foreach ( $order_ids as $order_id ) {
            $indice++;
            $order = wc_get_order( $order_id );
            $order_subtotal = $order->get_subtotal();
            ?>
            <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-completed order">
                <th class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"><?php echo $indice; ?></th>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number">Orden: <?php echo $order_id; ?></td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"><?php echo $order->get_status(); ?></td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number"><?php echo $order->order_date; ?></td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" style="text-align:right"><?php echo wc_price($order->get_total()); ?></td>
                <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" style="text-align:right">Hello</td>
            </tr>
            <?php
        }
        ?>
            </tbody>
        </table>
        <?php
    }
}
/* Update States to Users */