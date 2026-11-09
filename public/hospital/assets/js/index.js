$(document).ready(function(){
    //CREDIT CARD PREVIEW INPUT
    initCreditCardPreview();
    //NUMERIC INPUTS
    var cleave = new Cleave('.numeric1', {
      numeral: true
    });
    var cleave = new Cleave('.numeric2', {
      numeral: true
    });
    var cleave = new Cleave('.numeric3', {
      numeral: true
    });
    //CVC NUMBER HELP MODAL
    $('[data-toggle="tooltip"]').tooltip();
});

function submitPayment(){ 
  $('#messageSuccess').show();
  $('#messageError').show();
}

function initCreditCardPreview(){
  var path_to_icon='assets/images/credit-cards/';
    var icon='generic-mono.svg';
    $('.form-control-cc img').attr('src',path_to_icon+icon);
    var cleaveCreditCard = new Cleave('#cc_number', {
      creditCard: true,
      onCreditCardTypeChanged: function(type) {
          switch(type){
            case 'visa':
              icon='visa.svg';
            break;
            case 'mastercard':
              icon='mastercard.svg';
            break;
            case 'diners':
              icon='diners.svg';
            break;
            case 'discover':
              icon='discover.svg';
            break;
            case 'maestro':
              icon='maestro.svg';
            break;
            case 'jcb':
              icon='jcb.svg';
            break;
            case 'alipay':
              icon='alipay.svg';
            break;
            case 'amex':
              icon='amex.svg';
            break;
            case 'elo':
              icon='elo.svg';
            break;
            case 'hipercard':
              icon='hipercard.svg';
            break;
            case 'paypal':
              icon='paypal.svg';
            break;
            case 'unionpay':
              icon='unionpay.svg';
            break;
            case 'unknown':
              icon='generic-mono.svg';
            break;
          }      
          $('.form-control-cc img').attr('src',path_to_icon+icon);          
      }
    });
}