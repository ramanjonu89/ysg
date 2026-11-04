class Currency {
  constructor() {
    this.defNoOfDecimals = 2;
    this.defCurrency = {
      'name' : 'Singapore',
      'code' : 'SGD',
      'symbol' : '$',
      'exchangeRate' : 1,
    };
  }

  get currency() {
    return this.defCurrency;
  }

  get noOfDecimals() {
    return this.defNoOfDecimals;
  }

  // format
  formatPrice(price) {
    if(price === null || price === undefined){
      return this.currency.symbol + '0';
    }
    return this.currency.symbol + this.convertPrice(price).toFixed(this.noOfDecimals); 
  }

  // convert price base on exchange rate of currency
  convertPrice(price) {
    return parseFloat(price) * parseFloat(this.currency.exchangeRate);
  }
}

export default new Currency()
