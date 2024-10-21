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
            ]
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
        update_option('blastqr_disable_base_css', sanitize_text_field($_POST['disable_base_css']));
        update_option('blastqr_type_qr', sanitize_text_field($_POST['type_qr']));


        // Salva le impostazioni dei campi
        foreach ($form_fields as $key => $field) {
            update_option('blastqr_show_' . $key, isset($_POST['show_' . $key]) ? 1 : 0);
        }

        // Salva i valori dei colori
        foreach ($color_fields as $key => $field) {
            update_option('blastqr_' . $key, sanitize_hex_color($_POST[$key]));
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
    $shortcode_name = get_option('blastqr_shortcode_name', 'qr_form_shortcode');
    $custom_css = get_option('blastqr_custom_css', '');
    $disable_base_css = get_option('blastqr_disable_base_css', 0);
    $tipo_qr = get_option('blastqr_type_qr', 'full-width');

    $form_fields = [
        'rooms' => [
            'label' => 'Camere',
            'key' => 'blastqr_show_rooms',
            'value' => get_option('blastqr_show_rooms', 1) // Usa 'value' come valore predefinito
        ],
        'adults' => [
            'label' => 'Adulti',
            'key' => 'blastqr_show_adults',
            'value' => get_option('blastqr_show_adults', 1) // Usa 'value' come valore predefinito
        ],
        'children' => [
            'label' => 'Bambini',
            'key' => 'blastqr_show_children',
            'value' => get_option('blastqr_show_children', 1) // Usa 'value' come valore predefinito
        ],
    ];

    $color_fields = [
        'button_color' => [
            'label' => 'Pulsante Prenota',
            'key' => 'blastqr_button_color',
            'color' => get_option('blastqr_button_color', '#ffffff') // Usa 'color' come valore predefinito
        ],
        'button_text_color' => [
            'label' => 'Testo Pulsante Prenota',
            'key' => 'blastqr_button_text_color',
            'color' => get_option('blastqr_button_text_color', '#000000') // Usa 'color' come valore predefinito
        ],
        'qr_background_color' => [
            'label' => 'Sfondo Qr',
            'key' => 'blastqr_qr_background_color',
            'color' => get_option('blastqr_qr_background_color', '#000000') // Usa 'color' come valore predefinito
        ],
        'modify_cancel_color' => [
            'label' => 'Pulsante Modifica/Cancella',
            'key' => 'blastqr_modify_cancel_color',
            'color' => get_option('blastqr_modify_cancel_color', '#ffffff') // Usa 'color' come valore predefinito
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
                            Questa voce viene comunicata da Blastness al momento della fornitura, in base alla tipologia di booking scelta insieme al cliente. Prima di modificare questo parametro accertarsi con Blastness della presenza di uno stile adatto alla nuova tipologia.
                        </div>
                    </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997">Parametri BE</th>
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
                                Parametri di configurazione per la connesione con il Booking Engine. Questi parametri vengono comunicati nella mail con le istruzioni di integrazione
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
                            Tipologia QR
                        </div>
                    </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997">Abilita QR</th>
                    <td>
                        <input type="checkbox" name="enable_qr" value="1" <?php checked($enable_qr, 1); ?>>
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                                Abilitando questo campo, il modulo Quick Reserve verrà mostrato pubblicamente. 
                            </div>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997">Abilita Preview QR (Solo per utenti loggati)</th>
                    <td>
                        <input type="checkbox" name="enable_qr_preview" value="1" <?php checked($enable_qr_preview, 1); ?>>
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                                Abilitando questo campo, il modulo Quick Reserve verrà mostrato in modalità di prova, visibile solamente per gli utenti loggati.
                            </div>
                        </div>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row" style="color: #E11997">Classe QR</th>
                    <td>
                        <div style="display: flex; gap: 20px">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="use_anchor_class"><?= __("Usa QR all'interno di una classe di ancoraggio", 'blastqr') ?></label>
                                <input type="checkbox" name="use_anchor_class" value="1" <?php checked($use_anchor_class, 1); ?>>
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        Abilitando questo campo, il modulo Quick Reserve verrà mostrato all'interno di un classe
                                    </div>
                                </div>
                            </div>
                
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <label for="anchor_class"><?= __('Classe di ancoraggio del QR', 'blastqr') ?></label>
                                <input style="border: 1px solid #E11997" type="text" name="anchor_class" value="<?php echo esc_attr($anchor_class); ?>" />
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        La classe all'interno di cui viene aggiunto il modulo Quick Reserve, se l'opzione è stata selezionata
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
                                        Abilitando questo campo, il modulo Quick Reserve verrà posizionato utilizzando uno shortcode
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">  
                                <label for="shortcode_name"><?= __('Nome dello shortcode', 'blastqr') ?></label>
                                <input style="border: 1px solid #E11997" type="text" name="shortcode_name" value="<?php echo esc_attr($shortcode_name); ?>" />
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        Nome dello shortcode che desideri utilizzare
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
                                <label for="show_<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?></label>
                                <input type="checkbox" name="show_<?php echo esc_attr($key); ?>" value="1" <?php checked($field['value'], 1); ?>>
                            <?php endforeach; ?>
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        Puoi scegliere i campi da mostrare all'interno del modulo Quick Reserve
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
                                <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($field['label']); ?></label>
                                <input type="color" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($field['color']); ?>">
                            <?php endforeach; ?>
                                <div class="question abilita_qr">
                                    <i class="far fa-question-circle"></i>
                                    <div class="question__text">
                                        Colori del modulo Quick Reserve e calendario
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Offerte', 'blastqr') ?></th>
                    <td>
                        <div style="border:1px solid #E11997; padding:10px; width:70%; border-radius:5px;">La funzionalità che stampa l'elenco delle Offerte speciali disponibili per la struttura è atutomatica, e si attiva inserendo nella pagina o nel luogo desiderato il segnaposto <b>[offerte]</b><br />
                        Solitamente viene inserito in una pagina "Offerte" adibita alla visualizzazione di tale Elenco, che si aggiorna in tempo reale e in modo automatico secondo i settaggi presenti sull'extranet di blastness per la relativa struttura ricettiva.</div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('CSS Personalizzato', 'blastqr') ?></th>
                    <td>
                        <textarea name="custom_css" rows="10" cols="50" class="large-text"><?php echo esc_textarea($custom_css); ?></textarea>
                        <p class="description">Inserisci qui il tuo CSS personalizzato che verrà applicato al frontend.</p>
                    </td>
                </tr>
                 
                <tr valign="top">
                    <th scope="row" style="color: #E11997"><?= __('Disabilità CSS QR', 'blastqr') ?></th>
                    <td>
                        <input type="checkbox" name="disable_base_css" value="1" <?php checked($disable_base_css, 1); ?>>
                        <div class="question abilita_qr">
                            <i class="far fa-question-circle"></i>
                            <div class="question__text">
                                Abilitando questo campo, il codice CSS di base del modulo Quick Reserve verrà disabilitato.
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