<?php
/*
Plugin Name: Choco Pops
Description: This plugin permit to build your own popup (ligthweight system)
Version:0.9
Author: Willy Bahuaud
Author URI: http://wabeo.fr
*/
/**
INIT
*/
function pop_init() {
    register_post_type( 'popup', array(
        'public'             => false,
        'label'              => 'Popup',
        'show_ui'            => true,
        'show_in_menu'       => true,
        'publicly_queryable' => true,
        'supports'            => array( 'title', 'editor', 'thumbnail' )
        ) );
}
add_action( 'init', 'pop_init' );

/**
BACK
*/

//metaboxes
function pop_box() {
        add_meta_box( 'pop_url', 'URL de la Popup', 'pop_url', 'popup', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'pop_box' );

function pop_url( $post ) {
    wp_nonce_field('update-popup_'.$post->ID, '_wpnonce_popup');
    echo '<input type="url" name="popup" style="width:100%;" value="' . ( false !== ( $titre = get_post_meta( $post->ID, '_popup', true ) ) ? $titre : '' ) . '" placeholder="Url">';
}

function pop_save( $id ) {
    if( ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) && isset( $_POST[ 'popup' ] ) ) {
        check_admin_referer( 'update-popup_' . $_POST[ 'post_ID' ], '_wpnonce_popup' );
        update_post_meta( $id, '_popup', $_POST[ 'popup' ] );
    }
}
add_action( 'save_post', 'pop_save' );

//choix d'une popup à afficher
function pop_dashboard() {
    wp_add_dashboard_widget( 'pop_select', 'Afficher une popup', 'pop_select' );  
} 
add_action('wp_dashboard_setup', 'pop_dashboard' );

function pop_select() {
    if( isset( $_POST[ 'choco-pop' ] ) ) {
        update_option( 'choco-pop' , $_POST[ 'choco-pop' ] );
    }
    $saved_popup = get_option( 'choco-pop' );
    echo '<form method="post" action="">';
        $popup = get_posts( array(
            'post_type'        => 'popup',
            'posts_per_page'   => -1,
            'status'           => 'publish',
            'suppress_filters' => false
            ) );
        if( ! empty( $popup ) ) :
            echo '<p class="sub">Choix de la popup</p>';
            echo '<select name="choco-pop[popup-select]" id="popup-select" style="width:100%;">';
            echo '<option value="">Aucune</option>';
            foreach( $popup as $p )
                echo '<option value="' . esc_attr( $p->ID ) . '" ' . selected( $p->ID, $saved_popup[ 'popup-select' ] ) . '>' . esc_html( apply_filters( 'the_title', $p->post_title ) ) . '</option>';
            echo '</select>';

            echo '<p class="sub">Planification de la popup</p>';
            echo 'Date de départ : <input type="date" name="choco-pop[start-date]" value="' . $saved_popup[ 'start-date' ] . '"><br>';
            echo 'Date de fin : <input type="date" name="choco-pop[end-date]" value="' . $saved_popup[ 'end-date' ] . '">';
        endif;
    echo '<br><button type="submit" class="button button-primary">Enregistrer</button>';
    echo '</form><style>.sub{font-weight:bold;}</style>';
}


/**
FRONT
*/

function choco_pops_scripts() {
    if( is_front_page() ){
        //pop up now ?
        if( false !== ( $pop = get_option( 'choco-pop' ) ) ) {
            $date = time();
            if( strtotime( $pop[ 'start-date' ] ) <= $date && ( strtotime( $pop[ 'end-date' ] ) + 86400 ) >= $date && ( ! is_null( $popup = get_post( $pop[ 'popup-select' ] ) ) ) ) {

                $synockaimechoco = array(
                    'lien'  => get_permalink( $popup->ID ),
                    'image' => wp_get_attachment_image_src( get_post_thumbnail_id( $popup->ID ), 'full' )
                     );

                wp_enqueue_style( 'choco-style', plugins_url( 'pop.css', __FILE__ ), false, '0.9', 'all' );
                wp_enqueue_script( 'jquery' );
                wp_register_script( 'choco-pops', plugins_url( 'pop.js', __FILE__ ), array( 'jquery' ), '0.9', true );
                wp_enqueue_script( 'choco-pops' );
                wp_localize_script( 'choco-pops', 'popup', $synockaimechoco );
            }
        }
    }
}
add_action('wp_enqueue_scripts','choco_pops_scripts');