<?php

/***************SECCION PARA LA GRILLA DE ORDENES**************/
add_filter( 'manage_edit-shop_order_columns', 'diu_wc_tracking_order_column', 20 );
function diu_wc_tracking_order_column($columns){
    $reordered_columns = array();
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key == 'order_status' ){
            // Inserting after "WC Tracking" column
            $reordered_columns['status_wc_tracking'] = __( 'WC Tracking','diu-wc-tracking-status');
    }
    }
    return $reordered_columns;
}
add_action( 'manage_shop_order_posts_custom_column' , 'diu_wc_tracking_list_column_content', 20, 2 );
function diu_wc_tracking_list_column_content( $column, $post_id ){
    switch ( $column )
    {
        case 'status_wc_tracking' :
        // Get custom post meta data
		$current_status = get_post_meta( $post_id, 'status_tracking', true );
        if(!empty($current_status)){
		 	$current_status_value = get_term_by('id', $current_status, 'diurvan_wctracking_state');
            echo '<mark class="order-status status-completed"><span>'.$current_status_value->name.'</span></mark>';
		}
        else
			echo '';
        break;
    }
}
/***************SECCION PARA LA GRILLA DE ORDENES**************/

/***************SECCION PARA EL BULK DEL CAMBIO DE ESTADO**************/
//Añade la action Bulk para las Ordenes
add_filter( 'bulk_actions-edit-shop_order', 'diu_wc_tracking_actions_orders', 20, 1 );
function diu_wc_tracking_actions_orders( $actions ) {
	$cat_args = array( 'orderby' => 'diurvan_wc_tracking_orden', 'order' => 'asc', 'hide_empty' => false );
	$diurvan_wctracking_state = get_terms( 'diurvan_wctracking_state', $cat_args );
	foreach ( $diurvan_wctracking_state as $wc_tracking_state ) {
		$actions['status_wc_tracking_'.$wc_tracking_state->term_id] = __( 'WC Tracking:'.$wc_tracking_state->name, 'woocommerce' );
	}
    return $actions;
}
// Hace acciones Bulks en Ordenes
add_filter( 'handle_bulk_actions-edit-shop_order', 'diu_wc_tracking_bulk_edit_shop_order', 10, 3 );
function diu_wc_tracking_bulk_edit_shop_order( $redirect_to, $action, $post_ids ) {
	$len = strlen('status_wc_tracking_');
	$lenaction = strlen($action)-$len;
	$contains_status = substr($action, -$lenaction);

    if ( !$contains_status )
        return $redirect_to; // Exit

    $processed_ids = array();

    foreach ( $post_ids as $post_id ) {
		$current_status_history = get_post_meta( $post_id, 'status_tracking_history', true );
		$current_status = get_post_meta( $post_id, 'status_tracking', true );
		
		if($current_status != $contains_status && $contains_status != '0'){
			if($current_status_history=='')
				$string_history = $contains_status;
			else
				$string_history = $current_status_history.','. PHP_EOL .$contains_status;
			update_post_meta( $post_id, 'status_tracking_history', $string_history );
			update_post_meta( $post_id, 'status_tracking', $contains_status );
		}        
        $processed_ids[] = $post_id;
    }
    return add_query_arg( array(
        'status_wc_tracking' => '1',
        'processed_count' => count( $processed_ids ),
        'processed_ids' => implode( ',', $processed_ids ),
    ), $redirect_to );
}

// Al final de las acciones Bulks en Ordenes, indica el nro de Ordenes procesadas
add_action( 'admin_notices', 'diu_wc_tracking_admin_notice' );
function diu_wc_tracking_admin_notice() {
	// $message = sprintf( _n( 'WC Tracking Order status changed.', '%s order statuses changed.', $_REQUEST['processed_count'] ), number_format_i18n( $_REQUEST['processed_count'] ) );
	// echo "<div class=\"updated\"><p>{$message}</p></div>";

    if ( empty( $_REQUEST['wc_tracking_updates'] ) ) return; // Exit

    $count = intval( $_REQUEST['processed_count'] );
	$message = intval( $_REQUEST['processed_message'] );
	$action = intval( $_REQUEST['action_id'] );

    printf( '<div id="message" class="updated fade"><p>' .
        _n( 'Processed %s Order for WC Tracking.',
        $message,
        $count,
        $action
    ) . '</p></div>', $count );
}
/***************SECCION PARA EL BULK DEL CAMBIO DE ESTADO**************/







add_action( 'init', 'diurvan_taxonomy_wc_tracking' );
function diurvan_taxonomy_wc_tracking()  {
    /** TAXONOMIAS DE ESTADOS **/
    $labels = array(
        'name'                       => 'WC_Tracking_States',
        'singular_name'              => 'WcTracking State',
        'menu_name'                  => 'WcTracking State',
        'all_items'                  => 'All WcTracking States',
        'parent_item'                => 'Parent WcTracking State',
        'parent_item_colon'          => 'Parent WcTracking State:',
        'new_item_name'              => 'New WcTracking State Name',
        'add_new_item'               => 'Add WcTracking State Item',
        'edit_item'                  => 'Edit WcTracking State',
        'update_item'                => 'Update WcTracking State',
        'separate_items_with_commas' => 'Separate WcTracking State with commas',
        'search_items'               => 'Search WcTracking States',
        'add_or_remove_items'        => 'Add or remove WcTracking States',
        'choose_from_most_used'      => 'Choose from the most used WcTracking States',
    );
    $args = array( 'labels' => $labels, 'hierarchical' => false, 'public' => true, 'show_ui' => true, 'show_admin_column' => false, 'show_in_nav_menus' => true, 'supports' => array( 'title', 'thumbnail' ) );
    register_taxonomy( 'diurvan_wctracking_state', 'product', $args );
    register_taxonomy_for_object_type( 'diurvan_wctracking_state', 'product' );
    /** TAXONOMIAS DE ESTADOS **/
}
add_action( 'diurvan_wctracking_state_add_form_fields', 'diurvan_wc_tracking_add_fields' );
function diurvan_wc_tracking_add_fields( $taxonomy ) {
	echo '<div class="form-field">
	<label for="diurvan_wc_tracking_orden">Orden de presentación</label>
	<input type="number" name="diurvan_wc_tracking_orden" id="diurvan_wc_tracking_orden" />
	</div>';
}
add_action( 'diurvan_wctracking_state_edit_form_fields', 'diurvan_wc_tracking_edit_fields', 10, 2 );
function diurvan_wc_tracking_edit_fields( $term, $taxonomy ) {
    $value_orden = get_term_meta( $term->term_id, 'diurvan_wc_tracking_orden', true );
    echo '<tr class="form-field">
        <th>
            <label for="diurvan_wc_tracking_orden">Orden de presentación</label>
        </th>
        <td>
            <input name="diurvan_wc_tracking_orden" id="diurvan_wc_tracking_orden" type="number" value="' . esc_attr( $value_orden ) .'" />
        </td>
    </tr>';
}
add_action( 'created_diurvan_wctracking_state', 'diurvan_wctracking_state_save_term_fields' );
add_action( 'edited_diurvan_wctracking_state', 'diurvan_wctracking_state_save_term_fields' );
function diurvan_wctracking_state_save_term_fields( $term_id ) {
    update_term_meta( $term_id, 'diurvan_wc_tracking_orden', sanitize_text_field( $_POST[ 'diurvan_wc_tracking_orden' ] ) );
}



//Añade el campo de Estado en el Admin de la Orden
add_action( 'woocommerce_admin_order_data_after_order_details', 'diurvan_editable_order_meta_general' );
function diurvan_editable_order_meta_general( $order ) {
	$order_status = wc_get_order_status_name($order->get_status());
	$current_status_setting = get_option('diu_wc_tracking_status');

	$woo_status_ordered = array();
	foreach(wc_get_order_statuses() as $key=> $value) {
		$woo_status_ordered[] = array(
			'id' => count($woo_status_ordered) + 1,
			'key' => $key,
			'value' => $value
		);
	}
	$order_status_order = 0;$order_status_setting = 0;
	foreach($woo_status_ordered as $key=> $value) {
		if($value['value'] == $order_status) $order_status_order = $value['id'];
		if($value['key'] == $current_status_setting) $order_status_setting = $value['id'];
	}

	if ($order_status_order >= $order_status_setting) {
		$current_status = get_post_meta( $order->get_id(), 'status_tracking', true );
		$current_status_history = get_post_meta( $order->get_id(), 'status_tracking_history', true );

		$cat_args = array( 'orderby' => 'diurvan_wc_tracking_orden', 'order' => 'asc', 'hide_empty' => false );
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
		
		if(count($diurvan_wctracking_state) > 0){
			$array_options = array();
			$array_options[0] = __( 'Select Status Tracking', 'diu-wc-tracking-status');
			foreach($diurvan_wctracking_state_final as $wc_tracking_state){
				$array_options[$wc_tracking_state->term_id] = $wc_tracking_state->name;
			}
		}
			
		woocommerce_wp_select( array(
				'id' => 'status_tracking',
				'name' => 'status_tracking',
				'label' => 'Status Tracking:  (<a href="'.home_url(  ).'/wp-admin/edit-tags.php?taxonomy=diurvan_wctracking_state&post_type=product)"> '.__( 'Add items', 'diu-wc-tracking-status').' →</a>)',
				'value' => $current_status,
				'options' => $array_options,
				'wrapper_class' => 'form-field-wide'
			) );
	}
}

add_action( 'woocommerce_process_shop_order_meta', 'diurvan_save_general_details' );
function diurvan_save_general_details( $ord_id ){
	$current_status_history = get_post_meta( $ord_id, 'status_tracking_history', true );
	$current_status = get_post_meta( $ord_id, 'status_tracking', true );
	$new_status = wc_clean( $_POST[ 'status_tracking' ] );
	$send_email = get_option('diu_wc_send_email');

	if($send_email && $current_status != $new_status && $new_status != '0'){
		if($current_status_history=='')
			$string_history = $new_status;
		else
			$string_history = $current_status_history.','. PHP_EOL .$new_status;
		update_post_meta( $ord_id, 'status_tracking_history', $string_history );
		global $woocommerce;
		$orden = wc_get_order($ord_id);
		
		/*****/
		$html .= '<div>
			<div style="width:100%;display:inline-block;float:left">
				<h5><b>'.__( 'ORDER INFORMATION', 'diu-wc-tracking-status').'</b></h5>
			</div>
			<div style="width:100%;display:inline-block;float:left;padding-top: 30px;">
				<div style="width:20%;display:inline-block;float:left">
						<span style="height: 50px;"><b>'.__( 'Current status', 'diu-wc-tracking-status').':</b></span>
				</div>
				<div style="width:40%;display:inline-block;float:left">
						<span style="height: 50px;">'.$new_status.'</span>
				</div>
			</div>
			<div style="width:100%;display:inline-block;float:left;padding-top: 30px;">
				<div style="width:20%;display:inline-block;float:left">
						<span style="height: 50px;"><b>'.__( 'Order number', 'diu-wc-tracking-status').':</b></span>
				</div>
				<div style="width:40%;display:inline-block;float:left">
						<span style="height: 50px;">'.$order_id.'</span>
				</div>
			</div>
			<div style="width:100%;display:inline-block;float:left;">
				<div style="width:20%;display:inline-block;float:left">
						<span style="height: 50px;"><b>'.__( 'Company name', 'diu-wc-tracking-status').':</b></span>
				</div>
				<div style="width:40%;display:inline-block;float:left">
						<span style="height: 50px;">'.$orden->get_billing_company().'</span>
				</div>
			</div>
			<div style="width:100%;display:inline-block;float:left;">
				<div style="width:20%;display:inline-block;float:left">
						<span style="height: 50px;"><b>'.__( 'Contact name', 'diu-wc-tracking-status').':</b></span>
				</div>
				<div style="width:40%;display:inline-block;float:left">
						<span style="height: 50px;">'.$orden->get_billing_first_name().' '.$orden->get_billing_last_name().'</span>
				</div>
			</div>
			<div style="width:100%;display:inline-block;float:left;">
				<div style="width:20%;display:inline-block;float:left">
						<span style="height: 50px;"><b>'.__( 'Contact phone', 'diu-wc-tracking-status').':</b></span>
				</div>
				<div style="width:40%;display:inline-block;float:left">
						<span style="height: 50px;">'.$orden->get_billing_phone().'</span>
				</div>
			</div>
			<div style="width:100%;display:inline-block;float:left;">
				<div style="width:20%;display:inline-block;float:left">
						<span style="height: 50px;"><b>'.__( 'Order detail', 'diu-wc-tracking-status').':</b></span>
				</div>
				<div style="width:40%;display:inline-block;float:left">';
		
			$html.='<table>
					<tr>
						<th>'.__("Product Name", "diu-wc-tracking-status").'</th>
						<th>'.__("Quantity", "diu-wc-tracking-status").'</th>
						<th>'.__("Sub Total", "diu-wc-tracking-status").'</th>
						<th>'.__("Total", "diu-wc-tracking-status").'</th>
					</tr>';
			if ( count( $orden->get_items() ) > 0 ) {
				foreach ( $orden->get_items() as $item_id => $item ) {
					$product      = $item->get_product();
					$item_name    = $item->get_name();
					$quantity     = $item->get_quantity();
					$line_subtotal     = $item->get_subtotal();
					$line_total        = $item->get_total();
					$html.='<tr>
							<td>'.$item_name.'</td>
							<td>'.$quantity.'</td>
							<td>'.$line_subtotal.'</td>
							<td>'.$line_total.'</td>
						</tr>';	
				}
			}
			else{
				$html.='<tr>
							<td>'.__("No items", "diu-wc-tracking-status").'</td>
						</tr>';	
			}
		$html.='</table>';

		$html .= '</div>
			</div>				
		</div>';		
		/*****/
		$cabeceras= array('Content-Type: text/html; charset=UTF-8');
		wp_mail($orden->get_billing_email(), __( 'Update status order', 'diu-wc-tracking-status'), $html, $cabeceras);
	}
	update_post_meta( $ord_id, 'status_tracking', $new_status );
}


/* SECCION PARA CONFIGURACIÓN DEL PLUGIN */
function diu_wc_tracking_submenu(){
	add_submenu_page( 'woocommerce', 'WC Tracking Settings', 'WC Tracking Settings', 'manage_options', 'diu_wc_tracking_submenu_settings', 'diu_wc_tracking_submenu_settings');
}
add_action('admin_menu','diu_wc_tracking_submenu');
function diu_wc_tracking_submenu_settings(){
	?>
	<div class="wrap">
		<h2><?php echo 'WC Tracking Settings'; ?></h2>

		<div class="tab-content">
			<form method="POST" action="options.php">
				<?php
					settings_fields('diu_wc_tracking_settings_group');
					do_settings_sections('diu_wc_tracking_settings_group');
				?>
				<label><?php echo __('Add this shortcode into a Page or Post: ', 'diu-wc-tracking-status'); ?><b> [diurvan_custom_tracking]</b></label>
				<table class="form-table">
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php echo __('Status to be tracked from', 'diu-wc-tracking-status'); ?></label>
						</th>
						<td class="forminp forminp-text">
							<select id="diu_wc_tracking_status" name="diu_wc_tracking_status">
								<?php
								
								$woo_status_ordered = array();
								foreach(wc_get_order_statuses() as $key=> $value) {
									$woo_status_ordered[] = array(
										'id' => count($woo_status_ordered) + 1,
										'key' => $key,
										'value' => $value
									);
								}
							foreach($woo_status_ordered as $key=> $value) {
								$selected = $value['key'] == get_option('diu_wc_tracking_status')?"selected":"";
								echo "<option data='".$value['id']."' value='".$value['key']."' ".$selected. " >".$value['value']."</option>";
							}
							?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php echo __('Send email when status change', 'diu-wc-tracking-status'); ?></label>
						</th>
						<td scope="row" colspan="4">
							<?php
							$send_email = get_option('diu_wc_send_email');
							if( empty( $send_email ) ) $send_email = '';
							
							woocommerce_wp_checkbox(array(
								'id'            => 'diu_wc_send_email',
								'label'         => __('', 'woocommerce' ),
								'description'   => __( 'Check to send a client email aditional to Woo emails', 'woocommerce' ),
								'value'         => $send_email,
							));
							?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php echo __('Own styles on Tracking Page', 'diu-wc-tracking-status'); ?></label>
						</th>
						<td scope="row" cplspan="4">
							<?php
							$use_bootstrap = get_option('diu_wc_use_bootstrap');
							if( empty( $use_bootstrap ) ) $use_bootstrap = '';
							
							woocommerce_wp_checkbox(array(
								'id'            => 'diu_wc_use_bootstrap',
								'label'         => __('', 'woocommerce' ),
								'description'   => __( 'Check to use your own styles on tracking page using bootstrap class.', 'woocommerce' ),
								'value'         => $use_bootstrap,
							));
							?>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php echo __("Don't include notes on track", 'diu-wc-tracking-status'); ?></label>
						</th>
						<td scope="row" cplspan="4">
							<?php
							$include_notes = get_option('diu_wc_include_notes');
							if( empty( $include_notes ) ) $include_notes = '';
							
							woocommerce_wp_checkbox(array(
								'id'            => 'diu_wc_include_notes',
								'label'         => __('', 'woocommerce' ),
								'description'   => __( "Check to doesn't include notes on track view.", 'woocommerce' ),
								'value'         => $include_notes,
							));
							?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row" class="titledesc">
							<label><?php echo __("Use Custom Number Tracking", 'diu-wc-tracking-status'); ?></label>
						</th>
						<td scope="row" cplspan="4">
							<?php
							$custom_number_trackink = get_option('diu_wc_custom_number_tracking');
							
							woocommerce_wp_checkbox(array(
								'id'            => 'diu_wc_custom_number_tracking',
								'label'         => __('', 'woocommerce' ),
								'description'   => __( "Check to use custom Number Tracking on Order.", 'woocommerce' ),
								'value'         => $custom_number_trackink,
							));
							?>
						</td>
					</tr>
					

					<?php if(get_option('diu_wc_include_plugin_seguimiento') == 1){ ?>
					<tr valign="top">
						<th scope="row" class="titledesc" colspan="2">
							<label><?php echo __('Include information from plugin "SEGUIMIENTO DE PEDIDO"', 'diu-wc-tracking-status'); ?></label><br/>
							<label>https://www.enriquejros.com/plugins/seguimiento-envios-woocommerce/</label>
						</th>
					</tr>
					<tr>
						<td class="forminp forminp-text">
							<select id="diu_wc_include_plugin_seguimiento" name="diu_wc_include_plugin_seguimiento">
								<option value='0' <?php echo get_option('diu_wc_include_plugin_seguimiento') == 0?"selected":"";  ?> ><?php echo __('No', 'diu-wc-tracking-status') ?></option>
								<option value='1' <?php echo get_option('diu_wc_include_plugin_seguimiento') == 1?"selected":"";  ?> ><?php echo __('Yes', 'diu-wc-tracking-status') ?></option>
							</select>
						</td>
					</tr>
					<?php } ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>

	</div>
	<?php
}
function diu_wc_tracking_save_settings(){
	register_setting('diu_wc_tracking_settings_group', 'diu_wc_include_plugin_seguimiento');
	register_setting('diu_wc_tracking_settings_group', 'diu_wc_tracking_status');
	register_setting('diu_wc_tracking_settings_group', 'diu_wc_send_email');
	register_setting('diu_wc_tracking_settings_group', 'diu_wc_use_bootstrap');
	register_setting('diu_wc_tracking_settings_group', 'diu_wc_include_notes');
	register_setting('diu_wc_tracking_settings_group', 'diu_wc_custom_number_tracking');
	$initial_terms = array (
		'0' => array (
			'name'          => 'Orden recepcionada',
			'slug'          => 'orden-recepcionada',
			'description'   => 'Orden recepcionada',
		)
	);
	$cat_args = array( 'orderby' => 'diurvan_wc_tracking_orden', 'order' => 'asc', 'hide_empty' => false );
	$diurvan_wctracking_state = get_terms( 'diurvan_wctracking_state', $cat_args );
	if(count($diurvan_wctracking_state)==0){
		foreach ( $initial_terms as $term_key=>$term) {
			wp_insert_term(
				$term['name'],
				'diurvan_wctracking_state', 
				array(
					'description'   => $term['description'],
					'slug'          => $term['slug'],
				)
			);
		}
	}
}
add_action('admin_init', 'diu_wc_tracking_save_settings');
/* SECCION PARA CONFIGURACIÓN DEL PLUGIN */



/* SECCION PARA AGREGAR EL ROL DE COURIER */
register_activation_hook( __FILE__, 'wc_tracking_status_add_role_activation' );
function wc_tracking_status_add_role_activation() {
	add_role('wc_courier', 'WC Tracking Courier', get_role( 'contributor' )->capabilities );
}
add_action( 'wp_roles_init', static function ( \WP_Roles $roles ) {
	$roles->roles['wc_courier']['name'] = 'WC Tracking Courier';
	$roles->role_names['wc_courier'] = 'WC Tracking Courier';
});
/* SECCION PARA AGREGAR EL ROL DE COURIER */

/* AÑADE METABOX A LA ORDEN */
add_action( 'add_meta_boxes', 'diu_add_meta_boxes_number_tracking' );
if ( ! function_exists( 'diu_add_meta_boxes_number_tracking' ) )
{
    function diu_add_meta_boxes_number_tracking()
    {
					if( get_option('diu_wc_custom_number_tracking') ){
        add_meta_box( 'wc_tracking_metabox', __('Number Tracking','diu-wc-tracking'), 'diu_add_meta_boxes_tracking_for_prepare', 'shop_order', 'side', 'core' );
					}
    }
}

if ( ! function_exists( 'diu_add_meta_boxes_tracking_for_prepare' ) )
{
    function diu_add_meta_boxes_tracking_for_prepare()
    {
        global $post;

        $meta_field_data = get_post_meta( $post->ID, 'wc_tracking_number_tracking', true ) ? get_post_meta( $post->ID, 'wc_tracking_number_tracking', true ) : '';

        echo '<input type="hidden" name="diu_wc_tracking_field_nonce" value="' . wp_create_nonce() . '">
        <p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
            <input type="text" style="width:250px;" name="wc_tracking_number_tracking" placeholder="' . $meta_field_data . '" value="' . $meta_field_data . '"></p>';
    }
}

// Save the data of the Meta field
add_action( 'save_post', 'diu_wc_tracking_save_order', 10, 1 );
if ( ! function_exists( 'diu_wc_tracking_save_order' ) )
{
    function diu_wc_tracking_save_order( $post_id ) {
        // Check if our nonce is set.
        if ( ! isset( $_POST[ 'diu_wc_tracking_field_nonce' ] ) ) {
            return $post_id;
        }
        $nonce = $_REQUEST[ 'diu_wc_tracking_field_nonce' ];
        if ( ! wp_verify_nonce( $nonce ) ) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST[ 'post_type' ] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        // --- Its safe for us to save the data ! --- //

        // Sanitize user input  and update the meta field in the database.
        update_post_meta( $post_id, 'wc_tracking_number_tracking', $_POST[ 'wc_tracking_number_tracking' ] );
    }
}
/* AÑADE METABOX A LA ORDEN */