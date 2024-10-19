<?php

function blastqr_scarica_offerte() {
    global $blastqr_lingua_int;
    
    // Recupera i valori delle opzioni (parametri dell'URL)
    $id_albergo = get_option('blastqr_id_albergo', '12575');
    $dc = get_option('blastqr_dc', '8810');

    // Crea l'URL per richiedere il file XML
    $url = "https://xml.verticalbooking.com/htng/prices/Offers.htm?id_albergo=" . $id_albergo .
           "&lingua_int=" . $blastqr_lingua_int . "&user=albergo_" . $id_albergo . "&pass=" . $dc;
        
    $data = file_get_contents($url);
    $data = unserialize($data);

    update_option('offerte', $data);
    update_option('offerte_scaricate', time());

}

?>
