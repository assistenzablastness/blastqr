<?php

/*
Plugin Name: BlastQR
Plugin URI: https://github.com/assistenzablastness/blastqr
Description: Modulo Quick Reserve collegato al Booking Engine Blastness
Version: 1.0.2
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

add_action('plugins_loaded', 'blastqr_load_textdomain');

function blastqr_dario_library() {
    wp_enqueue_script('dario', 'https://cdn.blastness.biz/assets/libraries/dario/13/index.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'blastqr_dario_library');

add_action('wp_enqueue_scripts', 'blastqr_enqueue_scripts');

// Funzioni


// Funzione per stampare il form
function stampaForm(){
    echo aggiungi_form_html();
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
        echo '<style type="text/css">' . esc_html($custom_css) . '</style>';
    }
}


// Funzione per caricare i colori del Quick Reserve
function carica_colori_qr() {
    // Recupera i valori dei colori dalle impostazioni
    $button_color = get_option('blastqr_button_color', '#fff');
    $button_text_color = get_option('blastqr_button_text_color', '#000');
    $qr_background_color = get_option('blastqr_qr_background_color', '#000');
    $modify_cancel_color = get_option('blastqr_modify_cancel_color', '#fff');

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
        .dario-cell--selected, .dario-cell--inner, .dario-cell--hover{
            background-color: {$button_color};
            color: {$button_text_color};
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
    $mofile = dirname(__FILE__) . '/languages/blastqr-' . $locale . '.mo';

    // Verifica se il file di traduzione esiste e lo carica
    if (file_exists($mofile)) {
        load_textdomain('blastqr', $mofile);
    }
}

// Funzione per caricare il CSS del QR
function aggiungi_qr_style() {
    // Usa plugin_dir_url per ottenere il percorso corretto
    wp_enqueue_style('qr-style', plugin_dir_url(__FILE__) . 'assets/css/qr-style.css');
    wp_enqueue_style('qr-style-dario', plugin_dir_url(__FILE__) . 'assets/css/dario.css');
}

// Funzione per caricare il file JavaScript nel frontend
function blastqr_enqueue_scripts() {
    // Registra il file JavaScript (true significa che sarà caricato nel footer)
    wp_enqueue_script(
        'quick-reserve-js', // Handle univoco
        plugin_dir_url(__FILE__) . 'assets/js/quick-reserve.js', // Percorso del file
        array('jquery'), // Dipendenze (es. jQuery)
        '1.0.0', // Versione del file
        true // Carica nel footer
    );
}


function blastqr_visualizza_offerte() {
    // Controlla se le offerte sono state scaricate
    $offerte_scaricate = get_option('offerte_scaricate', 0);

    $url_finale = '';
    $tipo_be = get_option('blastqr_action_type', 'premium');
    if($tipo_be == 'advanced'){
        $url_finale = '/reservations/pacchetti.html?';
    }else{
        $url_finale = '/premium/index_pacchetti_2.html';
    }

    if (time() - $offerte_scaricate > 7200) {
        blastqr_scarica_offerte();
    }


    $offerte = get_option('offerte', []);
    // Recupera le offerte salvate

    if (empty($offerte)) {
        return '<p>Non ci sono offerte disponibili.aa</p>';
    }

  
    // Crea l'output HTML per le offerte
    $output = '<div class="offerte-list">';

    foreach ($offerte['rate_plans'] as $offerta) {
        $link_pren = str_replace ('prenota_new.htm?', $url_finale.'?id_stile='.get_option('blastqr_id_stile', 0).'&id_albergo='.get_option('blastqr_id_albergo', 0).'&dc='.get_option('blastqr_dc', 0).'&id_prodotto_sel='.$offerta['id_prodotto'],$offerta['link_prenotazione']);
        $output .= '<div class="offerta">';
        $output .= '<img src="'. (!empty($offerta['img_file']) ? esc_url($offerta['img_file']) : plugin_dir_url(__FILE__) . 'assets/offer_img_no.jpg') .'">';
        $output .= '<h3>' . sanitize_text_field($offerta['nome']) . '</h3>';
        $output .= '<p>' . sanitize_text_field($offerta['descrizione']) . '</p>';
        $output .= '<a href="'.$link_pren.'">'. __('Prenota') .'</a>';
        $output .= '</div>';
    }

    $output .= '</div>';

    return $output;
}
add_shortcode('offerte', 'blastqr_visualizza_offerte');

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

// Controlla gli aggiornamenti del plugin da GitHub

add_filter('pre_set_site_transient_update_plugins', 'blastqr_check_for_plugin_update');

function blastqr_check_for_plugin_update($transient) {
    // Definisci il nome del plugin e la versione corrente
    $plugin_slug = 'blastqr/blastqr.php'; // Il percorso del file principale del plugin
    $current_version = '1.0.2'; // La versione corrente del plugin
    echo $current_version;
    // Fai una richiesta all'API di GitHub per ottenere l'ultima versione
    $response = wp_remote_get('https://api.github.com/repos/assistenzablastness/blastqr/releases/latest');
    
    // Se ci sono errori nella richiesta, ritorna il transient senza modifiche
    if (is_wp_error($response)) {
        error_log('Errore nella richiesta API di GitHub: ' . $response->get_error_message());
        return $transient;
    }

    // Decodifica il corpo della risposta JSON
    $release = json_decode(wp_remote_retrieve_body($response));

    // Aggiungi un controllo per verificare cosa contiene la risposta
    if (empty($release)) {
        error_log('Risposta API vuota o non valida: ' . wp_remote_retrieve_body($response));
        return $transient;
    }

    // Verifica se 'tag_name' è presente e stampa l'output per capire cosa sta accadendo
    if (!isset($release->tag_name)) {
        error_log('Campo tag_name non presente nella risposta API. Risposta completa: ' . print_r($release, true));
        return $transient;
    }

    // Verifica se la versione su GitHub è maggiore della versione corrente
    if (version_compare($release->tag_name, $current_version, '>')) {
        // Crea un oggetto con le informazioni dell'aggiornamento
        $plugin_info = array(
            'slug' => $plugin_slug,
            'new_version' => $release->tag_name,
            'url' => $release->html_url,
            'package' => $release->zipball_url // Link al file zip dell'ultima release
        );
        
        // Aggiungi l'aggiornamento al transient
        $transient->response[$plugin_slug] = (object) $plugin_info;
    }
    
    echo "_---";
    return $transient;
}

// Mostra le informazioni dell'aggiornamento nel pannello plugin di WordPress
add_filter('plugins_api', 'blastqr_plugin_information', 20, 3);

function blastqr_plugin_information($false, $action, $args) {
    // Definisci il nome del plugin
    $plugin_slug = 'blastqr.php';
    
    // Verifica che l'azione sia 'plugin_information' e che il plugin slug sia corretto
    if ($action === 'plugin_information' && $args->slug === $plugin_slug) {
        // Fai una richiesta all'API di GitHub per ottenere le informazioni sull'ultima release
        $response = wp_remote_get('https://api.github.com/repos/assistenzablastness/blastqr/releases/latest');
        
        // Se ci sono errori nella richiesta, ritorna false
        if (is_wp_error($response)) {
            return $false;
        }

        // Decodifica il corpo della risposta JSON
        $release = json_decode(wp_remote_retrieve_body($response));
        
        // Crea un oggetto con le informazioni del plugin
        $info = new stdClass();
        $info->name = 'BlastQR'; // Nome del plugin
        $info->slug = $plugin_slug;
        $info->version = $release->tag_name;
        $info->author = 'Blastness';
        $info->homepage = 'https://github.com/assistenzablastness/blastqr';
        $info->download_link = $release->zipball_url;
        $info->requires = '5.0'; // Versione minima di WordPress richiesta
        $info->tested = '5.8'; // Ultima versione di WordPress testata
        $info->last_updated = $release->published_at;

        return $info;
    }
    
    return $false;
}