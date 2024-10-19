<form class="blast_qr_form" action="https://www.blastnessbooking.com/reservations/risultato.html?" id="qr-form" method="get" target="">
            
    <input type="hidden" id="id_stile" class="stile" name="id_stile" value="<?= $id_stile ?>"/>
    <input type="hidden" id="id_albergo" class="albergo" name="id_albergo" value="<?= $id_albergo ?>">
    <input type="hidden" id="dc" class="dc" name="dc" value="<?= $dc ?>">

    <input type="hidden" name="lingua_int" value="ita"/>
    <input type="hidden" id="gg" name="gg" value=""/>
    <input type="hidden" id="mm" name="mm" value=""/>
    <input type="hidden" id="aa" name="aa" value=""/>
    <input type="hidden" id="notti_1" name="notti_1" value="1"/>


    <div class="qr_container">
        
        <?php // ----- Calendari ----- // ?>
        <div class="qr_item__calendar">

            <input type="text" readOnly id="dario" class="qr_item__calendar__input" />

            <div class="qr_item__calendar__book_dates">

                <div class="qr_item__calendar__dates__element">
                    <div class="qr_item__calendar__dates__element__arrive">
                        <?= $this->__("qr-arrivo") ?>
                    </div>
                    <div class="qr_item__calendar__dates__element__arrive__data-numero" id="data-arrivo"></div>     
                </div>

                <div class="layover-prenota__cont__form__list__row__calendari__element">
                    <div class="qr_item__calendar__dates__element__departure">
                        <?= $this->__("qr-partenza") ?>
                    </div>
                    <div class="qr_item__calendar__dates__element__departure__data-numero" id="data-partenza"></div>      
                </div>

            </div>

        </div>

        <?php // ----- Adulti ----- // ?>
        <div class="qr_item__item">
            <div class="qr_item__item__dicitura">
                <?= $this->__("qr-adulti") ?>
            </div>
            <div class="qr_item__item-sel">
                <select name="tot_adulti" id="adulti" class="qr_item__item__select">
                    <?php for ($i=1; $i<=9; $i++){ ?>
                        <option <?= ($i==2) ? 'selected="selected"' : '' ?> value="<?= $i ?>"><?= $i ?></option>
                    <?php } ?>                            
                </select>                            
            </div> 
        </div>

        <?php // ----- Camere ----- // ?>
        <div class="qr_item__item">
            <div class="qr_item__item__dicitura">
                <?= $this->__("qr-camere") ?>
            </div>
            <div class="qr_item__item-sel">
                <select name="tot_camere" id="camere" class="qr_item__item__select">
                    <?php for ($i=1; $i<=9; $i++){ ?>
                        <option <?= ($i==1) ? 'selected="selected"' : '' ?> value="<?= $i ?>"><?= $i ?></option>
                    <?php } ?>                            
                </select>                        
            </div>
        </div> 

        <?php // ----- Bambini ----- // ?>
        <div class="qr_item__item">
            <div class="qr_item__item__dicitura">
                <?= $this->__("qr-bambini") ?>
            </div>
            <div class="qr_item__item-sel">
                <select name="tot_bambini" id="bambini" class="qr_item__item-sel__select">
                    <?php for ($i=0; $i<=9; $i++){ ?>
                        <option <?= ($i==0) ? 'selected="selected"' : '' ?> value="<?= $i ?>"><?= $i ?></option>
                    <?php } ?>                            
                </select>                          
            </div>
        </div>                     
                   

        <?php // ----- Verifica ----- // ?>
        <div class="qr_item__item">
            <input type="submit" class="qr_item__item__button-submit" value="">           
        </div>  
        
        <?php // ----- Cancella ----- // ?>
        <a class="qr_item__item modifica" href="">
            <?= $modifica_cancella_link ?>   
        </a>               

    </div>

</form>   