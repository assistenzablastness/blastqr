<?php

function blastqr_scarica_offerte($lingua_tre_caratteri) {
    // Recupera i valori delle opzioni (parametri dell'URL)
    $id_albergo = get_option('blastqr_id_albergo', '');
    $dc = get_option('blastqr_dc', '');

    // Crea l'URL per richiedere il file XML
    $url = "https://xml.verticalbooking.com/htng/prices/Offers.htm?id_albergo=" . $id_albergo .
           "&lingua_int=" . $lingua_tre_caratteri . "&user=albergo_" . $id_albergo . "&pass=" . $dc;

    // Scarica i dati XML
    $data = file_get_contents($url);
    $data = unserialize($data);

    // Salva le offerte per la lingua specifica
    update_option('offerte_' . $lingua_tre_caratteri, $data);
    update_option('offerte_scaricate_' . $lingua_tre_caratteri, time());
}
