window.addEventListener('DOMContentLoaded', function (event) {
  var selectors = {
    $currency: document.getElementById('currency'),
    $amount: document.getElementById('amount'),
    $amountInUSD: document.getElementById('amount_in_usd')
  };
  var modeUSD = false;
  var rateUSD = Number(currencies[selectors.$currency.value].rate_usd);

  selectors.$amountInUSD.addEventListener('change', function (event) {
    modeUSD = event.target.checked;
    var currSelected = currencies[selectors.$currency.value];
    var value = Number(selectors.$amount.value);
    if (modeUSD) {
      selectors.$amount.setAttribute('name', 'amount_usd');
      if (value && currSelected) {
        selectors.$amount.value = (Number(selectors.$amount.value) / currSelected.rate_usd).toFixed(2);
      }
    } else {
      selectors.$amount.setAttribute('name', 'amount');
      if (value && currSelected) {
        rateUSD = currSelected.rate_usd;
        selectors.$amount.value = (Number(selectors.$amount.value) * currSelected.rate_usd).toFixed(8);
      }
    }
  });

  selectors.$currency.addEventListener('change', function (event) {
    selectors.$amount.value = '';
  });

});
