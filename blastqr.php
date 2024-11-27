<?php

/*
Plugin Name: BlastQR
Plugin URI: https://github.com/assistenzablastness/blastqr
Description: Modulo Quick Reserve collegato al Booking Engine Blastness
Version: 0.9.7
Author: Blastness
Author URI: https://blastness.com
Text Domain: blastqr
Domain Path: /languages
GitHub Plugin URI: https://github.com/assistenzablastness/blastqr
GitHub Branch: main
*/

if (!defined('ABSPATH')) {
    exit;
}


global $blastqr_lingua_int;
$blastqr_lingua_int = get_lingua();


// Includi i file che gestiscono la pagina delle impostazioni e il form
include_once plugin_dir_path(__FILE__) . 'includes/settings-page.php';
include_once plugin_dir_path(__FILE__) . 'includes/form-output.php';
include_once plugin_dir_path(__FILE__) . 'includes/download_offers.php';

// Hook per aggiungere il menu delle impostazioni
add_action('admin_menu', 'blastqr_add_admin_menu');

// Hook per caricare il CSS del QR
add_action('wp_enqueue_scripts', 'aggiungi_qr_style');

// Hook per caricare i colori del QR
add_action('wp_head', 'carica_colori_qr');

// Hook per caricare il CSS Custom
add_action('wp_head', 'blastqr_custom_css');

$enable_qr_shortcode = get_option('blastqr_enable_qr_shortcode', 0);

if($enable_qr_shortcode){
    // Hook per registrare lo shortcode al caricamento del plugin
    registra_shortcode_dinamico();
} else{
    // Hook per aggiungere il form nel frontend
    add_action('wp_footer', 'stampaForm');

}

// Hook per registrare [offerte] come shortcode
add_shortcode('offerte', 'blastqr_visualizza_offerte');

// Hook per caricare il textdomain
add_action('plugins_loaded', 'blastqr_load_textdomain');

// Hook per caricare Dario 
// Hook per caricare il codice JS
add_action('wp_enqueue_scripts', 'blastqr_enqueue_scripts');



// Funzioni


// Funzione per stampare il form
function stampaForm(){
    echo aggiungi_form_html();
}

// Funzione banner cookie
function bannerCookie(){
    global $blastqr_lingua_int;

    $banner = "<script id='cookieScriptInclusion' defer type='text/javascript' src='https://bcm-public.blastness.com/init.js?v=2&l=".$blastqr_lingua_int."'></script>";
    
    echo $banner;
}


// Funzione per registrare lo shortcode dinamicamente
function registra_shortcode_dinamico() {
    // Recupera il nome dello shortcode dalle impostazioni
    $shortcode_name = get_option('blastqr_shortcode_name', 'qr_form_shortcode');
    // Funzione che genera il form quando viene chiamato lo shortcode
    add_shortcode($shortcode_name, 'aggiungi_form_html');
}


// Funzione per caricare il Custom CSS 
function blastqr_custom_css() {
    $custom_css = get_option('blastqr_custom_css', '');
    if (!empty($custom_css)) {
        echo '<style type="text/css">' . stripslashes($custom_css) . '</style>';
    }
}


// Funzione per caricare i colori del Quick Reserve
function carica_colori_qr() {
    // Recupera i valori dei colori dalle impostazioni
    $button_color = get_option('blastqr_button_color', '#ffffff');
    $button_text_color = get_option('blastqr_button_text_color', '#000000');
    $qr_background_color = get_option('blastqr_qr_background_color', '#000000');
    $modify_cancel_color = get_option('blastqr_modify_cancel_color', '#ffffff');

    $sfondo_calendario = get_option('blastqr_calendario_sfondo', '#ffffff');
    $testo_calendario = get_option('blastqr_calendario_text', '#000000');
    $sfondo_date_selezionate = get_option('blastqr_sfondo_seleziona_date', '#000000');
    $testo_date_selezionate = get_option('blastqr_text_seleziona_date', '#ffffff');
    
    // Retrieve position settings
    $fullwidth_positions = [
        'top' => get_option('blastqr_fullwidth_top', ''),
        'bottom' => get_option('blastqr_fullwidth_bottom', ''),
        'left' => get_option('blastqr_fullwidth_left', ''),
        'right' => get_option('blastqr_fullwidth_right', ''),
    ];

    $box_positions = [
        'top' => get_option('blastqr_box_top', '20'),
        'bottom' => get_option('blastqr_box_bottom', ''),
        'left' => get_option('blastqr_box_left', ''),
        'right' => get_option('blastqr_box_right', '20'),
    ];

    // Function to process position values
    function process_position_value($value) {
        if ($value === '') {
            return 'unset';
        }
        if (is_numeric($value)) {
            return $value . 'px';
        }
        // Since we're only accepting numbers, any non-numeric value defaults to 'unset'
        return 'unset';
    }

    // Process positions for full-width
    foreach ($fullwidth_positions as $key => $value) {
        $fullwidth_positions[$key] = process_position_value($value);
    }

    // Process positions for box
    foreach ($box_positions as $key => $value) {
        $box_positions[$key] = process_position_value($value);
    }
    // Genera il CSS dinamico
    $custom_css = "
        .blast_qr_form {
            background-color: {$qr_background_color};
        }
        .qr_item__item__button-submit {
            background-color: {$button_color};
            color: {$button_text_color};
        }
        .modifica {
            color: {$modify_cancel_color};
        }
        .prenota{
            background-color: {$button_color};
            color: {$button_text_color};
        }
        .dario{
          background-color: {$sfondo_calendario};
        }  
        .dario-cell{
            color: {$testo_calendario};
        }
        .dario-cell--disable{
            color: #cccccc;
        }
        .dario-cell--selected, .dario-cell--inner, .dario-cell--hover{
            background-color: {$sfondo_date_selezionate};
            color: {$testo_date_selezionate};
        }   
        ";
    
        $custom_css .= "
            .blast_qr_form.full-width {
                top: {$fullwidth_positions['top']};
                bottom: {$fullwidth_positions['bottom']};
                left: {$fullwidth_positions['left']};
                right: {$fullwidth_positions['right']};
            }
            .blast_qr_form.box {
                top: {$box_positions['top']};
                bottom: {$box_positions['bottom']};
                left: {$box_positions['left']};
                right: {$box_positions['right']};
            }
        ";

    // Stampa il CSS direttamente nel frontend
    echo '<style type="text/css">' . $custom_css . '</style>';
}


// Funzione per caricare il textdomain per le lingue
function blastqr_load_textdomain() {
    // Recupera la lingua corrente impostata in WordPress
    $locale = get_locale();
  
    // Percorso della directory delle traduzioni
    $mofile = dirname(__FILE__) . '/languages/blastqr_' . $locale . '.mo';

   
    // Verifica se il file di traduzione esiste e lo carica
    if (file_exists($mofile)) {
        load_textdomain('blastqr', $mofile);
    }
}


// Funzione per caricare il CSS del QR
function aggiungi_qr_style() {
    // Usa plugin_dir_url per ottenere il percorso corretto
    if(get_option('blastqr_disable_base_css', 0) == false){
        wp_enqueue_style('qr-style', plugin_dir_url(__FILE__) . 'assets/css/qr-style.css');
    } 
    wp_enqueue_style('qr-style-dario', plugin_dir_url(__FILE__) . 'assets/css/dario.css');
}


//
// function blastqr_dario_library() {
//     wp_enqueue_script('dario', 'https://cdn.blastness.biz/assets/libraries/dario/13/index.js', array(), null, true);
// }


// Funzione per caricare il file JavaScript nel frontend
function blastqr_enqueue_scripts() {
    // Registra il file JavaScript (true significa che sarà caricato nel footer)
    wp_enqueue_script(
        'quick-reserve-js',
        plugin_dir_url(__FILE__) . 'assets/js/quick-reserve.js',
        '1.0.0',
        true
    );
    wp_enqueue_script('dario', 'https://cdn.blastness.biz/assets/libraries/dario/13/index.js', array(), null, true);
    
    $enable_cookie = get_option('blastqr_enable_cookie', 0);

    if($enable_cookie){
        // Aggiungere banner cookie
        add_action('wp_footer', 'bannerCookie');
    }


}


function blastqr_visualizza_offerte() {
        global $blastqr_lingua_int;
        $translations = blastqr_get_translations();
        $lingua = get_lingua();

        // Controlla se le offerte sono state scaricate per la lingua corrente
        $offerte_scaricate = get_option('offerte_scaricate_' . $lingua, 0);
    
        // Se le offerte sono state scaricate più di 2 ore fa, scarica di nuovo
        if (time() - $offerte_scaricate > 7200) {
            blastqr_scarica_offerte($lingua);
        }
    
        // Recupera le offerte per la lingua corrente
        $offerte = get_option('offerte_' . $lingua, []);
    
        // Se non ci sono offerte, mostra un messaggio
        if (empty($offerte)) {
            return '<p>'. $translations['non_ci_sono_offerte_disponibili'] .'</p>';
        }
    
        // Genera l'output HTML per le offerte
        $output = '<div class="offerte-list">';
        foreach ($offerte['rate_plans'] as $offerta) {
            $url_finale = (get_option('blastqr_action_type', 'premium') == 'advanced') ? 
                          '/reservations/pacchetti.html?' : '/premium/index_pacchetti_2.html';
    
            $link_pren = str_replace(
                'prenota_new.htm?', 
                $url_finale.'?id_stile='.get_option('blastqr_id_stile', 0).'&id_albergo='.get_option('blastqr_id_albergo', 0).'&dc='.get_option('blastqr_dc', 0).'&id_prodotto_sel='.$offerta['id_prodotto'], 
                $offerta['link_prenotazione']
            );
    
            $output .= '<div class="offerta">';
            $output .= '<img src="'. (!empty($offerta['img_file']) ? esc_url($offerta['img_file']) : plugin_dir_url(__FILE__) . 'assets/offer_img_no.jpg') .'">';
            $output .= '<h3>' . sanitize_text_field($offerta['nome']) . '</h3>';
            $output .= '<p>' . sanitize_text_field($offerta['descrizione']) . '</p>';
            $output .= '<a href="'.$link_pren.'">'. $translations['prenota'] .'</a>';
            $output .= '</div>';
        }
    
        $output .= '</div>';
    
        return $output;
    }
    

// Funzione per recuperare la lingua corrente
function get_lingua() {
    $locale = get_locale(); // Recupera il locale corrente di WordPress

    switch ($locale) {
        case 'it-IT':
        case 'it_IT':
            return 'ita'; // Italiano
        case 'en_US':
        case 'en_GB':
            return 'eng'; // Inglese
        case 'fr_FR':
            return 'fra'; // Francese
        case 'de_DE':
            return 'deu'; // Tedesco
        case 'ru_RU':
            return 'rus'; // Russo
        case 'es_ES':
            return 'esp'; // Spagnolo
        case 'zh_CN':
            return 'chi'; // Cinese
        case 'pt_PT':
            return 'por'; // Portoghese
        case 'ja':
            return 'jpn'; // Giapponese
        default:
            return 'eng'; // Default Inglese
    }
}

function blastqr_get_translations() {
    static $translations = null;

    if ( $translations !== null ) {
        return $translations;
    }

    // Utilizza la tua funzione personalizzata per ottenere il codice lingua a 3 caratteri
    global $blastqr_lingua_int;
    if ( empty( $blastqr_lingua_int ) ) {
        $blastqr_lingua_int = get_lingua();
    }

    // Percorso del file JSON basato sul codice lingua a 3 caratteri
    $json_file = plugin_dir_path( __FILE__ ) . 'languages/' . $blastqr_lingua_int . '.json';

    // Se il file non esiste, utilizza la lingua predefinita (ad esempio, 'eng')
    if ( ! file_exists( $json_file ) ) {
        $json_file = plugin_dir_path( __FILE__ ) . 'languages/eng.json';
    }

    // Carica e decodifica il file JSON
    $json_content = file_get_contents( $json_file );
    $translations = json_decode( $json_content, true );

    // Se la decodifica fallisce, utilizza un array vuoto
    if ( ! is_array( $translations ) ) {
        $translations = array();
    }

    return $translations;
}

// Aggiornamenti

// Controlla gli aggiornamenti del plugin da GitHub
add_filter('pre_set_site_transient_update_plugins', 'blastqr_check_for_plugin_update');

// Mostra le informazioni dell'aggiornamento nel pannello plugin di WordPress
add_filter('plugins_api', 'blastqr_plugin_information', 20, 3);


function blastqr_check_for_plugin_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    // Fetch release info from GitHub
    $response = wp_remote_get('https://api.github.com/repos/assistenzablastness/blastqr/releases/latest');
    
    if (is_wp_error($response)) {
        return $transient;
    }

    $release = json_decode(wp_remote_retrieve_body($response));

    if (!isset($release->tag_name)) {
        return $transient;
    }

    $current_version = '0.9.7';
    
    if (version_compare($release->tag_name, $current_version, '>')) {
        $plugin_info = array(
            'slug' => 'blastqr-main/blastqr.php',
            'new_version' => $release->tag_name,
            'package' => 'https://github.com/assistenzablastness/blastqr/releases/download/'.$release->tag_name.'/blastqr-main.zip'
        );

        $transient->response['blastqr-main/blastqr.php'] = (object) $plugin_info;
    }

    return $transient;
}


function blastqr_plugin_information($false, $action, $args) {
    if ($action === 'plugin_information' && $args->slug === 'blastqr-main/blastqr.php') {
        $response = wp_remote_get('https://api.github.com/repos/assistenzablastness/blastqr/releases/latest');
        if (is_wp_error($response)) {
            return $false;
        }

        $release = json_decode(wp_remote_retrieve_body($response));

        $info = new stdClass();
        $info->name = 'BlastQR';
        $info->slug = 'blastqr-main/blastqr.php';
        $info->version = $release->tag_name;
        $info->author = 'BlastQR';
        $info->homepage = 'https://github.com/assistenzablastness/blastqr';
        $info->download_link = $release->zipball_url;
        $info->requires = '5.0';
        $info->tested = '5.8'; 
        $info->last_updated = $release->published_at;

        return $info;
    }

    return $false;
}
