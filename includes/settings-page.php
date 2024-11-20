<?php

// Funzione per aggiungere il menu nel backend
function blastqr_add_admin_menu() {
    add_menu_page(
        'Configurazioni modulo QR Blastness',
        'Blastness',
        'manage_options',
        'blastqr-settings',
        'blastqr_settings_page',
        plugin_dir_url(dirname(__FILE__)) . 'assets/logo-blastness.png',
        20
    );
    add_action('admin_enqueue_scripts', 'blastqr_admin_enqueue_scripts');
}

function blastqr_admin_enqueue_scripts($hook) {
    // Controlla se siamo sulla pagina del plugin
    if ($hook != 'toplevel_page_blastqr-settings') {
        return; // Esci se non siamo nella pagina delle impostazioni del plugin
    }
    
    // Carica il file CSS da /assets/admin.css
    wp_enqueue_style('blastqr_admin_css', plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin.css');
    wp_enqueue_script(
        'blastqr_admin_js', // Handle dello script
        plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin.js', // Percorso dello script
        array(), // Nessuna dipendenza
        '1.0.0', // Versione dello script
        true // Carica il file nel footer
    );
}

// Funzione per creare la pagina di impostazioni
function blastqr_settings_page() {
        // Definisci i campi del form come array
        $form_fields = [
            'rooms' => [
                'label' => 'Camere',
                'key' => 'blastqr_show_rooms',
                'value' => 1
            ],
            'adults' => [
                'label' => 'Adulti',
                'key' => 'blastqr_show_adult',
                'value' => 1
            ],
            'children' => [
                'label' => 'Bambini',
                'key' => 'blastqr_show_children',
                'value' => 1
            ],
            'discount_code' => [
                'label' => 'Codice Sconto',
                'key' => 'blastqr_show_discount_code',
                'value' => 1
            ]
        ];
    
        $color_fields = [
            'button_color' => [
                'label' => 'Pulsante Prenota',
                'key' => 'blastqr_button_color',
                'color' => '#ffffff'
            ],
            'button_text_color' => [
                'label' => 'Testo Pulsante Prenota',
                'key' => 'blastqr_button_text_color',
                'color' => '#000000'
            ],
            'qr_background_color' => [
                'label' => 'Sfondo Qr',
                'key' => 'blastqr_qr_background_color',
                'color' => '#000000'
            ],
            'modify_cancel_color' => [
                'label' => 'Pulsante Modifica/Cancella',
                'key' => 'blastqr_modify_cancel_color',
                'color' => '#ffffff'
            ],
        ];
        
        $color_calendar = [
            'calendario_sfondo' => [
                'label' => 'Sfondo calendario',
                'key' => 'blastqr_calendario_sfondo',
                'color' => '#ffffff'
            ],
            'calendario_text' => [
                'label' => 'Testo date calendario',
                'key' => 'blastqr_calendario_text',
                'color' => '#000000'
            ],
            'sfondo_seleziona_date' => [
                'label' => 'Background date selezionate calendario',
                'key' => 'blastqr_sfondo_seleziona_date',
                'color' => '#000000'
            ],            
            'text_seleziona_date' => [
                'label' => 'Testo date selezionate calendario',
                'key' => 'blastqr_text_seleziona_date',
                'color' => '#ffffff'
            ]
        ];

        // **Define the position arrays before the if block**
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
    

    // Salva le impostazioni quando il form viene sottomesso
    if (isset($_POST['submit'])) {
        update_option('blastqr_id_albergo', sanitize_text_field($_POST['id_albergo']));
        update_option('blastqr_id_stile', sanitize_text_field($_POST['id_stile']));
        update_option('blastqr_dc', sanitize_text_field($_POST['dc']));
        update_option('blastqr_action_type', sanitize_text_field($_POST['action_type']));
        update_option('blastqr_enable_qr', isset($_POST['enable_qr']) ? 1 : 0);
        update_option('blastqr_enable_qr_preview', isset($_POST['enable_qr_preview']) ? 1 : 0);
        update_option('blastqr_use_anchor_class', isset($_POST['use_anchor_class']) ? 1 : 0);
        update_option('blastqr_anchor_class', sanitize_text_field($_POST['anchor_class']));
        update_option('blastqr_enable_qr_shortcode', isset($_POST['enable_qr_shortcode']) ? 1 : 0);
        update_option('blastqr_shortcode_name', sanitize_text_field($_POST['shortcode_name']));
        update_option('blastqr_custom_css', wp_strip_all_tags($_POST['custom_css']));
        update_option('blastqr_disable_base_css', isset($_POST['disable_base_css']) ? 1 : 0);
        update_option('blastqr_type_qr', sanitize_text_field($_POST['type_qr']));
        update_option('blastqr_enable_cookie', isset($_POST['enable_cookie']) ? 1 : 0);
        update_option('blastqr_enable_fixed_discount_code', isset($_POST['enable_fixed_discount_code']) ? 1 : 0);
        update_option('blastqr_fixed_discount_code', sanitize_text_field($_POST['fixed_discount_code']));
        update_option('blastqr_discount_code_visibility', sanitize_text_field($_POST['discount_code_visibility']));


        // Salva le impostazioni dei campi
        foreach ($form_fields as $key => $field) {
            update_option('blastqr_show_' . $key, isset($_POST['show_' . $key]) ? 1 : 0);
        }

        // Salva i valori dei colori
        foreach ($color_fields as $key => $field) {
            update_option('blastqr_' . $key, sanitize_hex_color($_POST[$key]));
        }
    
        foreach ($color_calendar as $key => $field) {
            update_option('blastqr_' . $key, sanitize_hex_color($_POST[$key]));
        }

        foreach (['fullwidth', 'box'] as $type) {
            $positions = ($type === 'fullwidth') ? $fullwidth_positions : $box_positions;
            foreach ($positions as $pos => $value) {
                $field_name = $type . '_' . $pos;
                $input_value = isset($_POST[$field_name]) ? trim($_POST[$field_name]) : '';
                if ($input_value === '') {
                    // If empty, save as empty string
                    update_option('blastqr_' . $field_name, '');
                } else {
                    // Ensure it's a number
                    $numeric_value = is_numeric($input_value) ? $input_value : '';
                    update_option('blastqr_' . $field_name, $numeric_value);
                }
            }
        }


        echo '<div class="updated"><p>Impostazioni salvate.</p></div>';
    }

    
    // Recupera i valori delle opzioni salvate
    $id_albergo = get_option('blastqr_id_albergo', '');
    $id_stile = get_option('blastqr_id_stile', '');
    $dc = get_option('blastqr_dc', '');
    $action_type = get_option('blastqr_action_type', 'premium');
    $enable_qr = get_option('blastqr_enable_qr', 0);
    $enable_qr_preview = get_option('blastqr_enable_qr_preview', 0);
    $use_anchor_class = get_option('blastqr_use_anchor_class', 0);
    $anchor_class = get_option('blastqr_anchor_class', '');
    $enable_qr_shortcode = get_option('blastqr_enable_qr_shortcode', 0);
    $shortcode_name = get_option('blastqr_shortcode_name', 'quick-reserve');
    $custom_css = get_option('blastqr_custom_css', '');
    $disable_base_css = get_option('blastqr_disable_base_css', 0);
    $tipo_qr = get_option('blastqr_type_qr', 'full-width');
    $enable_cookie = get_option('blastqr_enable_cookie', 0);
    $enable_fixed_discount_code = get_option('blastqr_enable_fixed_discount_code', 0);
    $fixed_discount_code = get_option('blastqr_fixed_discount_code', '');
    $discount_code_visibility = get_option('blastqr_discount_code_visibility', 'both');

    $fullwidth_positions = [
        'top' => get_option('blastqr_fullwidth_top', 'unset'),
        'bottom' => get_option('blastqr_fullwidth_bottom', '0'),
        'left' => get_option('blastqr_fullwidth_left', 'unset'),
        'right' => get_option('blastqr_fullwidth_right', 'unset'),
    ];
    
    $box_positions = [
        'top' => get_option('blastqr_box_top', '20'),
        'bottom' => get_option('blastqr_box_bottom', 'unset'),
        'left' => get_option('blastqr_box_left', 'unset'),
        'right' => get_option('blastqr_box_right', '20'),
    ];

    $form_fields = [
        'rooms' => [
            'label' => 'Camere',
            'key' => 'blastqr_show_rooms',
            'value' => get_option('blastqr_show_rooms', 1) 
        ],
        'adults' => [
            'label' => 'Adulti',
            'key' => 'blastqr_show_adults',
            'value' => get_option('blastqr_show_adults', 1)
        ],
        'children' => [
            'label' => 'Bambini',
            'key' => 'blastqr_show_children',
            'value' => get_option('blastqr_show_children', 1)
        ],
        'discount_code' => [
            'label' => 'Codice Sconto',
            'key'=> 'blastqr_show_discount_code',
            'value' => get_option('blastqr_show_discount_code', 0)
        ]
    ];

    $color_fields = [
        'button_color' => [
            'label' => 'Pulsante Prenota',
            'key' => 'blastqr_button_color',
            'color' => get_option('blastqr_button_color', '#ffffff')
        ],
        'button_text_color' => [
            'label' => 'Testo Pulsante Prenota',
            'key' => 'blastqr_button_text_color',
            'color' => get_option('blastqr_button_text_color', '#000000')
        ],
        'qr_background_color' => [
            'label' => 'Sfondo Qr',
            'key' => 'blastqr_qr_background_color',
            'color' => get_option('blastqr_qr_background_color', '#000000')
        ],
        'modify_cancel_color' => [
            'label' => 'Pulsante Modifica/Cancella',
            'key' => 'blastqr_modify_cancel_color',
            'color' => get_option('blastqr_modify_cancel_color', '#ffffff')
        ],
    ];

    $color_calendar = [
        'calendario_sfondo' => [
            'label' => 'Sfondo calendario',
            'key' => 'blastqr_calendario_sfondo',
            'color' => get_option('blastqr_calendario_sfondo', '#ffffff')
        ],
        'calendario_text' => [
            'label' => 'Testo date calendario',
            'key' => 'blastqr_calendario_text',
            'color' => get_option('blastqr_calendario_text', '#000000')
        ],
        'sfondo_seleziona_date' => [
            'label' => 'Background date selezionate calendario',
            'key' => 'blastqr_sfondo_seleziona_date',
            'color' => get_option('blastqr_sfondo_seleziona_date', '#000000')
        ],            
        'text_seleziona_date' => [
            'label' => 'Testo date selezionate calendario',
            'key' => 'blastqr_text_seleziona_date',
            'color' => get_option('blastqr_text_seleziona_date', '#ffffff')
        ]
    ];

    ?>
    <div class="wrap">
        <h1>Impostazioni modulo QR Blastness</h1>
        <form method="post" action="#">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row" style="color: #E11997">Tipologia BE</th>
                    <td>
                        <select name="action_type">
                            <option value="premium" <?php selected($action_type, 'premium'); ?>>Premium</option>
                            <option value="advanced" <?php selected($action_type, 'advanced'); ?>>Advanced</option>
                        </select>
                        <div class="question tipo_be">
                        <i class="far fa-question-circle"></i>
                            <div class="question__text">
                            <?= __('Questa voce viene comunicata da Blastness al momento della fornitura, in base alla tipologia di booking scelta insieme al cliente. Prima di modificare questo parametro accertarsi con Blastness della presenza di uno stile adatto alla nuova tipologia.', 'blastqr') ?>
                        </div>
                    </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Parametri BE') ?></th>
                    <td>
                        <div style="display: flex; gap: 20px">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="id_albergo"><?= __('ID Albergo', 'blastqr')  ?></label>
                                <input style="border: 1px solid #E11997" type="text" name="id_albergo" value="<?php echo esc_attr($id_albergo); ?>" />
                            </div>
                            
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="id_stile"><?= __('ID Stile', 'blastqr') ?></label>
                                <input style="border: 1px solid #E11997" type="text" name="id_stile" value="<?php echo esc_attr($id_stile); ?>" />
                            </div>

                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="dc"><?= __('DC', 'blastqr') ?></label>
                                <input style="border: 1px solid #E11997" type="text" name="dc" value="<?php echo esc_attr($dc); ?>" />
                            </div>
                        </div>
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                                <?= __('Questi parametri vengono forniti da Blastness e sono necessari per il corretto funzionamento del modulo Quick Reserve', 'blastqr') ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Tipologia QR', 'blastqr') ?></th>
                    <td>
                        <select name="type_qr">
                            <option value="full-width" <?php selected($tipo_qr, 'full-width'); ?>>Full Width</option>
                            <option value="box" <?php selected($tipo_qr, 'box'); ?>>Box</option>
                        </select>
                        <div class="question tipo_be">
                        <i class="far fa-question-circle"></i>
                            <div class="question__text">
                            <?= __('Tipologia QR', 'blastqr') ?>
                        </div>
                    </div>
                    </td>
                </tr>
                <tr valign="top" class="position-settings" id="position-full-width">
                    <th scope="row" style="color: #E11997"><?= __('Posizione QR Full-Width', 'blastqr')?></th>
                    <td>
                        <label for="fullwidth_top">Top:</label>
                        <input type="number" name="fullwidth_top" value="<?php echo esc_attr($fullwidth_positions['top']); ?>" />
                        <label for="fullwidth_bottom">Bottom:</label>
                        <input type="number" name="fullwidth_bottom" value="<?php echo esc_attr($fullwidth_positions['bottom']); ?>" />
                        <label for="fullwidth_left">Left:</label>
                        <input type="number" name="fullwidth_left" value="<?php echo esc_attr($fullwidth_positions['left']); ?>" />
                        <label for="fullwidth_right">Right:</label>
                        <input type="number" name="fullwidth_right" value="<?php echo esc_attr($fullwidth_positions['right']); ?>" />
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                               <?= __(' Impostando i valori nei campi può essere determinata la posizione del modulo Quick Reserve. Lasciando vuoto il campo "Left" e impostando un valore nel campo "Right" il modulo verrà posizionato a destra, viceversa se si lascia vuoto il campo "Right" e si imposta un valore nel campo "Left" il modulo verrà posizionato a sinistra. Allo stesso modo impostando "Top" e "Bottom" si può determinare la posizione verticale del modulo.', 'blastqr') ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr valign="top" class="position-settings" id="position-box">
                    <th scope="row" style="color: #E11997"><?= __('Posizione QR Box', 'blastqr') ?></th>
                    <td>
                        <label for="box_top">Top:</label>
                        <input type="number" name="box_top" value="<?php echo esc_attr($box_positions['top']); ?>" />
                        <label for="box_bottom">Bottom:</label>
                        <input type="number" name="box_bottom" value="<?php echo esc_attr($box_positions['bottom']); ?>" />
                        <label for="box_left">Left:</label>
                        <input type="number" name="box_left" value="<?php echo esc_attr($box_positions['left']); ?>" />
                        <label for="box_right">Right:</label>
                        <input type="number" name="box_right" value="<?php echo esc_attr($box_positions['right']); ?>" />
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                                <?= __('Impostando i valori nei campi può essere determinata la posizione del modulo Quick Reserve. Lasciando vuoto il campo "Left" e impostando un valore nel campo "Right" il modulo verrà posizionato a destra discostando il box dal bordo destro dello stesso numero di px impostati nel valore relativo. Viceversa se si lascia vuoto il campo "Right" e si imposta un valore nel campo "Left" il modulo verrà posizionato a sinistra con la distanza dal bordo impostata. Allo stesso modo impostando "Top" e "Bottom" si può determinare la posizione verticale del modulo.', 'blastqr') ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Abilita QR','blastqr') ?></th>
                    <td>
                        <input type="checkbox" name="enable_qr" value="1" <?php checked($enable_qr, 1); ?>>
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                                <?= __('Abilitando questo campo, il modulo Quick Reserve verrà mostrato sul sito web.', 'blastqr') ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Abilita Preview QR (Solo per utenti loggati)', 'blastqr') ?></th>
                    <td>
                        <input type="checkbox" name="enable_qr_preview" value="1" <?php checked($enable_qr_preview, 1); ?>>
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                                <?= __('Abilitando questo campo, il modulo Quick Reserve verrà mostrato in modalità di prova, visibile solamente per gli utenti loggati.', 'blastqr') ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Codice Sconto Fisso', 'blastqr')?></th>
                    <td>
                        <div style="display: flex; align-items: center; gap: 20px">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="enable_fixed_discount_code"><?= __('Abilita codice sconto fisso', 'blastqr') ?></label>
                                <input type="checkbox" name="enable_fixed_discount_code" value="1" <?php checked($enable_fixed_discount_code, 1); ?>>
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        <?= __('Abilitando questo campo, può essere impostato un codice sconto di default', 'blastqr') ?>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="fixed_discount_code"><?= __('Codice sconto attivo:','blastqr') ?></label>
                                <input type="text" name="fixed_discount_code" value="<?php echo esc_attr($fixed_discount_code); ?>" />
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <label for="discount_code_visibility"><?= __('Visibilità Codice Sconto', 'blastqr') ?></label>
                            <select name="discount_code_visibility">
                                <option value="mobile" <?php selected($discount_code_visibility, 'mobile'); ?>><?= __('Solo Mobile', 'blastqr') ?></option>
                                <option value="desktop" <?php selected($discount_code_visibility, 'desktop'); ?>><?= __('Solo Desktop', 'blastqr') ?></option>
                                <option value="both" <?php selected($discount_code_visibility, 'both'); ?>><?= __('Entrambi', 'blastqr') ?></option>
                            </select>
                            <div class="question abilita_qr">
                                <i class="far fa-question-circle"></i>
                                <div class="question__text">
                                    <?= __('Il codice sconto può essere impostato per essere sia visibile da desktop / mobile', 'blastqr') ?>
                                </div>
                            </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Classe QR','blastqr') ?></th>
                    <td>
                        <div style="display: flex; gap: 20px">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="use_anchor_class"><?= __("Usa QR all'interno di una classe di ancoraggio", 'blastqr') ?></label>
                                <input type="checkbox" name="use_anchor_class" value="1" <?php checked($use_anchor_class, 1); ?>>
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        <?= __("Abilitando questo campo, il modulo Quick Reserve verrà mostrato all'interno di un classe", 'blastqr') ?>
                                    </div>
                                </div>
                            </div>
                
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="anchor_class"><?= __('Classe di ancoraggio del QR', 'blastqr') ?></label>
                                <input style="border: 1px solid #E11997" type="text" name="anchor_class" value="<?php echo esc_attr($anchor_class); ?>" />
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        <?= __("La classe all'interno di cui viene aggiunto il modulo Quick Reserve, se l'opzione è stata selezionata", 'blastqr') ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </td>


                </tr>

                <tr valign="top">
                    <th scope="row" style="color: #E11997">Shortcode</th>
                    <td>
                        <div style="display: flex; align-items: center; gap: 20px">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="enable_qr_shortcode"><?= __('Abilita QR tramite Shortcode', 'blastqr') ?></label>
                                <input type="checkbox" name="enable_qr_shortcode" value="1" <?php checked($enable_qr_shortcode, 1); ?>>
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                       <?= __('Abilitando questo campo, il modulo Quick Reserve verrà mostrato all\'interno di una pagina o di un post utilizzando uno shortcode', 'blastqr') ?>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">  
                                <label for="shortcode_name"><?= __('Nome dello shortcode', 'blastqr') ?></label>
                                <input style="border: 1px solid #E11997" type="text" name="shortcode_name" value="<?php echo esc_attr($shortcode_name); ?>" />
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        <?= __('Nome dello shortcode che desideri utilizzare', 'blastqr') ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>



                <!-- Campi del form -->

                    <tr valign="top">
                        <th scope="row" style="color: #E11997"><?= __('Campi form', 'blastqr') ?></th>
                        <td>
                            <div>
                            <?php foreach ($form_fields as $key => $field): ?>
                                <label for="show_<?php echo $key ?>"><?php echo esc_html($field['label']); ?></label>
                                <input type="checkbox" name="show_<?php echo esc_attr($key); ?>" value="1" <?php checked($field['value'], 1); ?>>
                            <?php endforeach; ?>
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        <?= __("Puoi scegliere i campi da mostrare all'interno del modulo Quick Reserve", 'blastqr') ?>
                                    </div>
                                </div>
                            </div>
                        
                        </td>
                    </tr>


                <!-- Campi per i selettori di colore -->
    
                    <tr valign="top">
                        <th scope="row" style="color: #E11997"><?= __('Colori QR', 'blastqr') ?></th>
                        <td>
                            <div>
                            <?php foreach ($color_fields as $key => $field): ?>
                                <label for="<?php echo $key; ?>"><?php echo esc_html($field['label']); ?></label>
                                <input type="color" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($field['color']); ?>">
                            <?php endforeach; ?>
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        <?= __('Colori del modulo Quick Reserve', 'blastqr') ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row" style="color: #E11997"><?= __('Colori calendarioo', 'blastqr') ?></th>
                        <td>
                            <div>
                            <?php foreach ($color_calendar as $key => $field): ?>
                                <label for="<?php echo $key; ?>"><?php echo esc_html($field['label']); ?></label>
                                <input type="color" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($field['color']); ?>">
                            <?php endforeach; ?>
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        <?= __('Colori del calendario', 'blastqr') ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Offerte', 'blastqr') ?></th>
                    <td>
                        <div style="border:1px solid #E11997; padding:10px; width:70%; border-radius:5px;"><?= __("La funzionalità che stampa l'elenco delle Offerte speciali disponibili per la struttura è atutomatica, e si attiva inserendo nella pagina o nel luogo desiderato il segnaposto <b>[offerte]</b><br />
                        Solitamente viene inserito in una pagina 'Offerte' adibita alla visualizzazione di tale Elenco, che si aggiorna in tempo reale e in modo automatico secondo i settaggi presenti sull'extranet di blastness per la relativa struttura ricettiva.", 'blastqr'); ?></div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('CSS Personalizzato', 'blastqr') ?></th>
                    <td>
                        <textarea name="custom_css" rows="10" cols="50" class="large-text"><?php echo stripslashes(esc_html($custom_css)); ?></textarea>
                        <p class="description"><?= __('Inserisci qui il tuo CSS personalizzato che verrà applicato al frontend.', 'blastqr') ?></p>
                    </td>
                </tr>
                 
                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Disabilita CSS QR', 'blastqr') ?></th>
                    <td>
                        <input type="checkbox" name="disable_base_css" value="1" <?php checked($disable_base_css, 1); ?>>
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                                <?= __('Abilitando questo campo, il CSS base del modulo Quick Reserve verrà disabilitato', 'blastqr') ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Abilita banner cookie', 'blastqr') ?></th>
                    <td>
                        <input type="checkbox" name="enable_cookie" value="1" <?php checked($enable_cookie, 1); ?>>
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                                <?= __('Abilitando questo campo viene aggiunto il banner cookie','blastqr') ?>
                            </div>
                        </div>
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
?>