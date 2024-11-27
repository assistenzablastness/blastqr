<?php 

// Funzione per aggiungere il form nel body
function aggiungi_form_html() {
    global $blastqr_lingua_int;
    $translations = blastqr_get_translations();

    // Recupera i valori dalle opzioni salvate
    $id_albergo = get_option('blastqr_id_albergo', '');
    $id_stile = get_option('blastqr_id_stile', '');
    $dc = get_option('blastqr_dc', '');
    $action_type = get_option('blastqr_action_type', 'premium');
    $enable_qr = get_option('blastqr_enable_qr', 0);
    $enable_qr_preview = get_option('blastqr_enable_qr_preview', 0);
    $use_anchor_class = get_option('blastqr_use_anchor_class', 0);
    $anchor_class = get_option('blastqr_anchor_class', '');
    $enable_qr_shortcode = get_option('blastqr_enable_qr_shortcode', 0);
    $shortcode_name = get_option('blastqr_shortcode_name', 'qr_form_shortcode');
    $tipo_qr = get_option('blastqr_type_qr', 'full-width');
    $enable_fixed_discount_code = get_option('blastqr_enable_fixed_discount_code', 0);
    $fixed_discount_code = get_option('blastqr_fixed_discount_code', '');
    $discount_code_visibility = get_option('blastqr_discount_code_visibility', 'both');
    $data_apertura = get_option('blastqr_data_apertura', '');

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
            'label' => $translations['camere'],
            'html' => '<div class="qr_item">' . $translations['camere'] . ': <span id="numero_camere"></span>
                        <select id="tot_camere" name="tot_camere" class="qr_item__select">' . generate_options(9, 1) . '</select>
                    </div>',
            'option' => get_option('blastqr_show_rooms', 1),
        ],
        'adults' => [
            'label' => $translations['adulti'],
            'html' => '<div class="qr_item">' . $translations['adulti'] . ': <span id="numero_adulti"></span>
                        <select id="tot_adulti" name="tot_adulti" class="qr_item__select">' . generate_options(9, 2) . '</select>
                    </div>',
            'option' => get_option('blastqr_show_adults', 1),
        ],
        'children' => [
            'label' => $translations['bambini'],
            'html' => '<div class="qr_item">' . $translations['bambini'] . ': <span id="numero_bambini"></span>
                        <select id="tot_bambini" name="tot_bambini" class="qr_item__select">' . generate_options(9, 0) . '</select>
                    </div>',
            'option' => get_option('blastqr_show_children', 1),
        ],
        'discount_code' => [
            'label' => $translations['codice_sconto'],
            'html' => '<div class="qr_item">
                        <input type="text" placeholder="'. $translations['codice_sconto'] .'" id="generic_codice" name="generic_codice" class="qr_item__sconto" />
                    </div>',
            'option' => get_option('blastqr_show_discount_code', 1),
        ]
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


    $generic_codice_mobile = '';
    $generic_codice_desktop = '';

    $sconto_height = '';
    if($form_fields['discount_code']['option'] == 1){
        $sconto_height = 'sconto-height';
    }
    
    if($enable_fixed_discount_code && ($discount_code_visibility == 'mobile' || $discount_code_visibility == 'both')){
        $generic_codice_mobile = '&generic_codice='.$fixed_discount_code;
    } 
    if($enable_fixed_discount_code && ($discount_code_visibility == 'desktop' || $discount_code_visibility == 'both')){
        if($form_fields['discount_code']['option'] == 1){
            $form_fields['discount_code']['html'] = '<div class="qr_item">
                        <input type="text" placeholder="'. $translations['codice_sconto_placeholder'] .'" id="generic_codice" name="generic_codice" class="qr_item__sconto" value="'.$fixed_discount_code.'" />
                    </div>';
        } else{
            $generic_codice_desktop = '<input type="hidden" id="generic_codice" name="generic_codice" value="'.$fixed_discount_code.'"/>';
        }
    }


    // Verifica se mostrare il form pubblicamente
    if ($enable_qr || ($enable_qr_preview && is_user_logged_in())) {

        // Form HTML
        $form_html = '
        <a class="prenota' . esc_attr($add_into_class) . esc_attr($add_to_shortcode) .'" href="'. $prenota_diretto. $generic_codice_mobile .'">'. $translations['prenota'] .'</a>
        <form class="blast_qr_form '. $sconto_height ." " . $tipo_qr .' '. esc_attr($add_into_class) . esc_attr($add_to_shortcode) . '" action="' . esc_url($form_action) . '" id="qr-form" method="get" target="">
            <input type="hidden" id="id_stile" class="stile" name="id_stile" value="' . esc_attr($id_stile) . '"/>
            <input type="hidden" id="id_albergo" class="albergo" name="id_albergo" value="' . esc_attr($id_albergo) . '"/>
            <input type="hidden" id="dc" class="dc" name="dc" value="' . esc_attr($dc) . '"/>
            <input type="hidden" id="lingua_int" name="lingua_int" value="'. $blastqr_lingua_int .'"/>
            <input type="hidden" id="gg" name="gg" value=""/>
            <input type="hidden" id="mm" name="mm" value=""/>
            <input type="hidden" id="aa" name="aa" value=""/>
            <input type="hidden" id="notti_1" name="notti_1" value="1"/>
            '.$generic_codice_desktop.'
        
            <div class="qr_container '. $tipo_qr .'">
                <div class="qr_item__calendar">
                    <input type="text" placeholder="'. $translations['apri_calendario_placeholder'] .'" readOnly id="dario" data-mindate="'. $data_apertura . '" class="qr_item__calendar__input" />
                    <div class="qr_item__calendar__book_dates">
                        <div class="qr_item__calendar__dates__element">
                            <div class="qr_item__calendar__dates__element__arrive">'. $translations['check_in_label'] .'</div>
                            <div class="qr_item__calendar__dates__element__arrive__data-numero" id="data-arrivo"></div>     
                        </div>
                        <div class="layover-prenota__cont__form__list__row__calendari__element">
                            <div class="qr_item__calendar__dates__element__departure">'. $translations['check_out_label'] .'</div>
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
                    <input type="submit" class="qr_item__item__button-submit" value="'. $translations['prenota'] .'">
                </div>
                <a class="modifica" href="'. esc_url($modifica_cancella_link) .'">'. $translations['modifica_cancella_prenotazione'] .'</a>
            </div>
        </form>';

        // Verifica se usare la classe di ancoraggio
        if ($use_anchor_class && !empty($anchor_class)) {
            // Aggiungi il form all'interno della classe di ancoraggio specificata
            echo '<script>
                    var anchor = document.querySelector(".' . esc_js($anchor_class) . '");
                    if (anchor) {
                        anchor.innerHTML = `' . addslashes($form_html) . '`;
                    }
            </script>';
        } else {
            // Mostra il form normalmente
            return $form_html;
        }
    }
}
?>