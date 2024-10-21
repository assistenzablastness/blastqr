<?php 

// Funzione per aggiungere il form nel body
function aggiungi_form_html() {
    global $blastqr_lingua_int;
    // Recupera i valori dalle opzioni salvate
    $id_albergo = get_option('blastqr_id_albergo', '12575');
    $id_stile = get_option('blastqr_id_stile', '18535');
    $dc = get_option('blastqr_dc', '8810');
    $action_type = get_option('blastqr_action_type', 'premium');
    $enable_qr = get_option('blastqr_enable_qr', 0);
    $enable_qr_preview = get_option('blastqr_enable_qr_preview', 0);
    $use_anchor_class = get_option('blastqr_use_anchor_class', 0);
    $anchor_class = get_option('blastqr_anchor_class', '');
    $enable_qr_shortcode = get_option('blastqr_enable_qr_shortcode', 0);
    $shortcode_name = get_option('blastqr_shortcode_name', 'qr_form_shortcode');

    if (!function_exists('generate_options')) {
        function generate_options($max, $default_value) {
            $options = '';
            for ($i = 0; $i <= $max; $i++) {
                $selected = ($i == $default_value) ? 'selected' : '';
                $options .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
            }
            return $options;
        }
    }
    

    // Array associativo per i campi del form
    $form_fields = [
        'rooms' => [
            'label' => 'Camere',
            'html' => '<div class="qr_item">' . __('Camere', 'blastqr') . ': <span id="numero_camere"></span>
                        <select id="tot_camere" name="tot_camere" class="qr_item__select">' . generate_options(9, 1) . '</select>
                    </div>',
            'option' => get_option('blastqr_show_rooms', 1),
        ],
        'adults' => [
            'label' => 'Adulti',
            'html' => '<div class="qr_item">' . __('Adulti', 'blastqr') . ': <span id="numero_adulti"></span>
                        <select id="tot_adulti" name="tot_adulti" class="qr_item__select">' . generate_options(9, 2) . '</select>
                    </div>',
            'option' => get_option('blastqr_show_adults', 1),
        ],
        'children' => [
            'label' => 'Bambini',
            'html' => '<div class="qr_item">' . __('Bambini', 'blastqr') . ': <span id="numero_bambini"></span>
                        <select id="tot_bambini" name="tot_bambini" class="qr_item__select">' . generate_options(9, 0) . '</select>
                    </div>',
            'option' => get_option('blastqr_show_children', 1),
        ],
    ];

    $prenota_diretto = '';
    
    if($action_type == 'advanced'){
        $prenota_diretto = 'https://www.blastnessbooking.com/reservations/index.html?lingua_int='. $blastqr_lingua_int .'&id_stile=' . esc_attr($id_stile) . '&id_albergo=' . esc_attr($id_albergo) . '&dc=' . esc_attr($dc).'';
    } else{
        $prenota_diretto = 'https://www.blastnessbooking.com/premium/index.html?id_stile='. esc_attr($id_stile) .'&lingua_int='. $blastqr_lingua_int .'&id_albergo='. esc_attr($id_albergo) .'&dc='. esc_attr($dc) .'';
    }
    
    // Determina il form action e il link di modifica/cancella
    $form_action = ($action_type == 'advanced') 
        ? 'https://www.blastnessbooking.com/reservations/risultato.html' 
        : 'https://www.blastnessbooking.com/premium/index2.html';

    $modifica_cancella_link = ($action_type == 'advanced')
        ? 'https://www.blastnessbooking.com/reservations/cancel_modify.html?id_albergo=' . esc_attr($id_albergo) . '&dc=' . esc_attr($dc) . '&id_stile=' . esc_attr($id_stile) . '&lingua_int=ita'
        : 'https://www.blastnessbooking.com/premium/cancel_modify.html?id_albergo=' . esc_attr($id_albergo) . '&dc=' . esc_attr($dc) . '&id_stile=' . esc_attr($id_stile) . '&lingua_int=ita';


    $add_into_class = ($use_anchor_class && !empty($anchor_class)) ? ' anchor-class' : '';

    $add_to_shortcode = ($enable_qr_shortcode && !empty($shortcode_name)) ? ' shortcode-class' : '';
    // Verifica se mostrare il form pubblicamente
    if ($enable_qr || ($enable_qr_preview && is_user_logged_in())) {

        // Form HTML
        $form_html = '
        <a class="prenota' . esc_attr($add_into_class) . esc_attr($add_to_shortcode) .'" href="'. $prenota_diretto .'">'. __('Prenota', 'blastqr').'</a>
        <form class="blast_qr_form' . esc_attr($add_into_class) . esc_attr($add_to_shortcode) . '" action="' . esc_url($form_action) . '" id="qr-form" method="get" target="">
            <input type="hidden" id="id_stile" class="stile" name="id_stile" value="' . esc_attr($id_stile) . '"/>
            <input type="hidden" id="id_albergo" class="albergo" name="id_albergo" value="' . esc_attr($id_albergo) . '"/>
            <input type="hidden" id="dc" class="dc" name="dc" value="' . esc_attr($dc) . '"/>
            <input type="hidden" name="lingua_int" value="'. $blastqr_lingua_int .'"/>
            <input type="hidden" id="gg" name="gg" value=""/>
            <input type="hidden" id="mm" name="mm" value=""/>
            <input type="hidden" id="aa" name="aa" value=""/>
            <input type="hidden" id="notti_1" name="notti_1" value="1"/>
        
            <div class="qr_container">
                <div class="qr_item__calendar">
                    <input type="text" readOnly id="dario" class="qr_item__calendar__input" />
                    <div class="qr_item__calendar__book_dates">
                        <div class="qr_item__calendar__dates__element">
                            <div class="qr_item__calendar__dates__element__arrive">'. __('checkin', 'blastqr') .'</div>
                            <div class="qr_item__calendar__dates__element__arrive__data-numero" id="data-arrivo"></div>     
                        </div>
                        <div class="layover-prenota__cont__form__list__row__calendari__element">
                            <div class="qr_item__calendar__dates__element__departure">'. __('checkout', 'blastqr') .'</div>
                            <div class="qr_item__calendar__dates__element__departure__data-numero" id="data-partenza"></div>      
                        </div>
                    </div>
                </div>';

        // Aggiungi i campi del form
        foreach ($form_fields as $field) {
            if ($field['option']) {
                $form_html .= $field['html'];
            }
        }

        $form_html .= '
                <div class="qr_item__submit">
                    <input type="submit" class="qr_item__item__button-submit" value="'. __('Verifica', 'blastqr') .'">
                </div>
                <a class="modifica" href="'. esc_url($modifica_cancella_link) .'">'. __('Modifica/Cancella prenotazione', 'blastqr') .'</a>
            </div>
        </form>';

        // Verifica se usare la classe di ancoraggio
        if ($use_anchor_class && !empty($anchor_class)) {
            // Aggiungi il form all'interno della classe di ancoraggio specificata
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var anchor = document.querySelector(".' . esc_js($anchor_class) . '");
                    if (anchor) {
                        anchor.innerHTML = `' . addslashes($form_html) . '`;
                    }
                });
            </script>';
        } else {
            // Mostra il form normalmente
            return $form_html;
        }
    }
}
?>