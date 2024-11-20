
document.addEventListener("DOMContentLoaded", function(event) {


  let lingua = 'eng';
  if(document.getElementById('lingua_int')){
    lingua = document.getElementById('lingua_int').value;
  }

  if(typeof(document.getElementById("dario")) != 'undefined' && document.getElementById("dario") != null){
      const dario = new Dario('#dario', {
          classes: 'dario--mod',
          inline: false,
          range: true,
          minDate: new Date(),
          lang: lingua,
          onSelect: (result) => {
              console.log(result);
              document.getElementById('data-arrivo').innerHTML = result.startDate.date+"&nbsp;"+result.startMonthShort+"&nbsp;"+result.startDate.year;
              document.getElementById('data-partenza').innerHTML = result.endDate.date+"&nbsp;"+result.endMonthShort+"&nbsp;"+result.endDate.year; 
              document.getElementById('notti_1').value = result.nights;   
              document.getElementById('gg').value = result.startDate.fullDate;   
              document.getElementById('mm').value = result.startDate.fullMonth;     
              document.getElementById('aa').value = result.startDate.year;                                                        
          }
      });    
  }

  aggiornaSelect('tot_camere', 'numero_camere');
  aggiornaSelect('tot_adulti', 'numero_adulti');
  aggiornaSelect('tot_bambini', 'numero_bambini');

});


function aggiornaSelect(selectId, spanId) {
  const selectElement = document.getElementById(selectId);
  const spanElement = document.getElementById(spanId);

  if (selectElement && spanElement) {
    spanElement.textContent = selectElement.value;

    selectElement.addEventListener('change', function (event) {
      spanElement.textContent = event.target.value;
    });
  }
}

