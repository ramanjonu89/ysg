@extends('frontend.layouts.app')



@section('content')


 <section class="slice-xs sct-color-2 border-bottom">

            <div class="container container-sm">

                <div class="row cols-delimited justify-content-center">

                    <div class="col">

                        <div class="icon-block icon-block--style-1-v5 text-center ">

                            <div class="block-icon c-gray-light mb-0">

                                <i class="la la-shopping-cart"></i>

                            </div>

                            <div class="block-content d-none d-md-block">

                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('1. My Cart') }}</h3>

                            </div>

                        </div>

                    </div>



                    <div class="col">

                        <div class="icon-block icon-block--style-1-v5 text-center ">

                            <div class="block-icon mb-0 c-gray-light">

                                <i class="la la-map-o"></i>

                            </div>

                            <div class="block-content d-none d-md-block">

                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('2. Shipping info') }}</h3>

                            </div>

                        </div>

                    </div>



                    <div class="col">

                        <div class="icon-block icon-block--style-1-v5 text-center ">

                            <div class="block-icon mb-0 c-gray-light">

                                <i class="la la-truck"></i>

                            </div>

                            <div class="block-content d-none d-md-block">

                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('3. Delivery info') }}</h3>

                            </div>

                        </div>

                    </div>



                    <div class="col">

                        <div class="icon-block icon-block--style-1-v5 text-center active">

                            <div class="block-icon mb-0">

                                <i class="la la-credit-card"></i>

                            </div>

                            <div class="block-content d-none d-md-block">

                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('4. Payment') }}</h3>

                            </div>

                        </div>

                    </div>



                    <div class="col">

                        <div class="icon-block icon-block--style-1-v5 text-center">

                            <div class="block-icon c-gray-light mb-0">

                                <i class="la la-check-circle"></i>

                            </div>

                            <div class="block-content d-none d-md-block">

                                <h3 class="heading heading-sm strong-300 c-gray-light text-capitalize">{{ __('5. Confirmation') }}</h3>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>




<section class="gry-bg py-4">

    <div class="container">

        <div class="row">

            <div class="col-lg-6 offset-lg-2">

                <div class="card">

                    <div class="align-items-center card-header d-flex justify-content-center text-center" >

                        

                       
                        <div class="row">
                        <div class="col-lg-6 ">
                        <h3 class="d-inline-block heading-4 mb-0 mr-3 strong-600" >{{ __('Payment Details') }}</h3>
                        </div>
                        <div class="col-lg-6">
                         <img loading="lazy"  class="img-fluid" src="../frontend/images/accepted_c22e0.png" height="30">
                        </div>
                        </div>

                    </div>

                    <div class="card-body">

                        <form role="form" action="{{ route('stripe.post') }}" method="post" class="require-validation"

                        data-cc-on-file="false"

                        data-stripe-publishable-key="{{ env('STRIPE_KEY') }}"

                        id="payment-form">

                            @csrf

                            

                            <div class='form-row'>

                                <div class='col-12 form-group required'>

                                    <label class='control-label'>{{ __('Name on Card') }}</label>

                                    <input class='form-control inputname' size='4' type='text'>

                                </div>

                            </div>

                            

                            <div class='form-row'>

                                <div class='col-12 form-group required'>

                                    <label class='control-label'>{{ __('Card Number') }}</label>

                                 

                                     <input class="form-control number card-number" type="text" ng-model="ncard" maxlength="19" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>

                                </div>

                            </div>

                            

                            <div class='form-row'>

                                <div class='col-12 col-md-4 form-group cvc required'>

                                    <label class='control-label'>{{ __('CVC') }}</label>

                                    <input class="form-control ccv " type="text" placeholder="CVC" maxlength="4" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>

                                </div>

                                <div class='col-12 col-md-4 form-group expiration required'>

                                    <label class='control-label'>{{ __('Expiration Month') }}</label>

                                    <input class='form-control card-expiry-month' placeholder='MM' maxlength='2' type='text'>

                                </div>

                                <div class='col-12 col-md-4 form-group expiration required'>

                                    <label class='control-label'>{{ __('Expiration Year') }}</label>

                                    <input class='form-control card-expiry-year' placeholder='YYYY' maxlength='4' type='text'>

                                </div>

                            </div>

                            

                            <div class='form-row'>

                                <div class='col-12 error form-group d-none'>

                                    <div class='alert-danger alert'>{{ __('Please correct the errors and try again.') }}</div>

                                </div>

                            </div>



                            <div class="row">

                                <div class="col-12">

                                    @if (Session::get('payment_type') == 'cart_payment')

                                    <button class="btn btn-base-1 btn-block" type="submit">{{ __('Place Order') }} (${{ number_format(convert_to_usd(\App\Order::findOrFail(Session::get('order_id'))->grand_total), 2) }})</button>

                                    @elseif(Session::get('payment_type') == 'wallet_payment')

                                    <button class="btn btn-base-1 btn-block" type="submit">{{ __('Place Order') }} (${{ number_format(convert_to_usd(Session::get('payment_data')['amount']), 2) }})</button>

                                    @elseif(Session::get('payment_type') == 'customer_package_payment')

                                    <button class="btn btn-base-1 btn-block" type="submit">{{ __('Place Order') }} (${{ number_format(convert_to_usd($customer_package->amount,2)) }})</button>

                                    @elseif(Session::get('payment_type') == 'seller_package_payment')

                                    <button class="btn btn-base-1 btn-block" type="submit">{{ __('Place Order') }} (${{ number_format(convert_to_usd($seller_package->amount,2)) }})</button>

                                    @endif

                                </div>

                            </div>

                            

                        </form>

                    </div>

                </div>

            </div>

            <div class="col-lg-4 ml-lg-auto">

                        @include('frontend.partials.cart_summary')

                    </div>

        </div>

    </div>

</section>



@endsection



@section('script')

<script type="text/javascript" src="https://js.stripe.com/v2/"></script>



<script type="text/javascript">
    $('form').submit(function(){
              $(this).find('button[type=submit]').prop('disabled', true);
           
   
});
    $(function() {

        var $form         = $(".require-validation");

        $('form.require-validation').bind('submit', function(e) {
        
     


            var $form         = $(".require-validation"),

            inputSelector = ['input[type=email]', 'input[type=password]',

            'input[type=text]', 'input[type=file]',

            'textarea'].join(', '),

            $inputs       = $form.find('.required').find(inputSelector),

            $errorMessage = $form.find('div.error'),

            valid         = true;

            $errorMessage.addClass('d-none');

            

            $('.has-error').removeClass('has-error');

            $inputs.each(function(i, el) {

                var $input = $(el);

                if ($input.val() === '') {

                    $input.parent().addClass('has-error');

                    $errorMessage.removeClass('d-none');

                    e.preventDefault();

                }

            });

            

            if (!$form.data('cc-on-file')) {

                e.preventDefault();

                Stripe.setPublishableKey($form.data('stripe-publishable-key'));

                Stripe.createToken({

                    number: $('.card-number').val(),

                    cvc: $('.card-cvc').val(),

                    exp_month: $('.card-expiry-month').val(),

                    exp_year: $('.card-expiry-year').val()

                }, stripeResponseHandler);

            }

            

        });

        

        function stripeResponseHandler(status, response) {

            if (response.error) {

                $('.error')

                .removeClass('d-none')

                .find('.alert')

                .text(response.error.message);
     $form.find('button[type=submit]').prop('disabled', false);
                } else {

                // token contains id, last4, and card type

                var token = response['id'];

                // insert the token into the form so it gets submitted to the server

                $form.find('input[type=text]').empty();

                $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");

                $form.get(0).submit();

            }

        }

    });

</script>



<script>

    // 4: VISA, 51 -> 55: MasterCard, 36-38-39: DinersClub, 34-37: American Express, 65: Discover, 5019: dankort

    

    

    $(function(){

        

        var cards = [{

            nome: "mastercard",

            colore: "#0061A8",

            src: "https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png"

            }, {

            nome: "visa",

            colore: "#E2CB38",

            src: "https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/2000px-Visa_Inc._logo.svg.png"

            }, {

            nome: "dinersclub",

            colore: "#888",

            src: "http://www.worldsultimatetravels.com/wp-content/uploads/2016/07/Diners-Club-Logo-1920x512.png"

            }, {

            nome: "americanExpress",

            colore: "#108168",

            src: "https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/American_Express_logo.svg/600px-American_Express_logo.svg.png"

            }, {

            nome: "discover",

            colore: "#86B8CF",

            src: "https://lendedu.com/wp-content/uploads/2016/03/discover-it-for-students-credit-card.jpg"

            }, {

            nome: "dankort",

            colore: "#0061A8",

            src: "https://upload.wikimedia.org/wikipedia/commons/5/51/Dankort_logo.png"

        }];

        

        var month = 0;

        var html = document.getElementsByTagName('html')[0];

        var number = "";

        

        var selected_card = -1;

        

        $(document).click(function(e){

            

            if(!$(e.target).is(".expire") || !$(e.target).closest(".expire").length){

                $(".date_value").css("color", "var(--text-color)");

            }

            if(!$(e.target).is(".number") || !$(e.target).closest(".number").length){

                $(".card_number").css("color", "var(--text-color)");

            }

            if(!$(e.target).is(".inputname") || !$(e.target).closest(".inputname").length){

                $(".fullname").css("color", "var(--text-color)");

            }

        });

        

        

        //Card number input

        $(".number").keyup(function(event){

            $(".card_number").text($(this).val());

            number = $(this).val();

            

            if(parseInt(number.substring(0, 2)) > 50 && parseInt(number.substring(0, 2)) < 56){

                selected_card = 0;

                }else if(parseInt(number.substring(0, 1)) == 4){

                selected_card = 1;  

                }else if(parseInt(number.substring(0, 2)) == 36 || parseInt(number.substring(0, 2)) == 38 || parseInt(number.substring(0, 2)) == 39){

                selected_card = 2;     

                }else if(parseInt(number.substring(0, 2)) == 34 || parseInt(number.substring(0, 2)) == 37){

                selected_card = 3; 

                }else if(parseInt(number.substring(0, 2)) == 65){

                selected_card = 4; 

                }else if(parseInt(number.substring(0, 4)) == 5019){

                selected_card = 5; 

                }else{

                selected_card = -1; 

            }

            

            if(selected_card != -1){

                html.setAttribute("style", "--card-color: " + cards[selected_card].colore);  

                $(".bankid").attr("src", cards[selected_card].src).show();

                }else{

                html.setAttribute("style", "--card-color: #cecece");

                $(".bankid").attr("src", "").hide();

            }

            

            if($(".card_number").text().length === 0){

                $(".card_number").html("&#x25CF;&#x25CF;&#x25CF;&#x25CF; &#x25CF;&#x25CF;&#x25CF;&#x25CF; &#x25CF;&#x25CF;&#x25CF;&#x25CF; &#x25CF;&#x25CF;&#x25CF;&#x25CF;");

            }

            

            }).focus(function(){

            $(".card_number").css("color", "white");

            }).on("keydown input", function(){

            

            $(".card_number").text($(this).val());

            

            if(event.key >= 0 && event.key <= 9){

                if($(this).val().length === 4 || $(this).val().length === 9 || $(this).val().length === 14){

                    $(this).val($(this).val() +  " ");

                }

            }

        })

        

        //Name Input

        $(".inputname").keyup(function(){  

            $(".fullname").text($(this).val());  

            if($(".inputname").val().length === 0){

                $(".fullname").text("FULL NAME");

            }

            return event.charCode;

            }).focus(function(){

            $(".fullname").css("color", "white");

        });

        

        //Security code Input

        $(".ccv").focus(function(){

          

            $(".seccode").css("color", "white");

            }).keyup(function(){

            $(".seccode").text($(this).val());

            if($(this).val().length === 0){

                $(".seccode").html("&#x25CF;&#x25CF;&#x25CF;");

            }

            }).focusout(function() {

            $(".card").css("transform", "rotatey(0deg)");

            $(".seccode").css("color", "var(--text-color)");

        });

        

        

        //Date expire input

        $(".expire").keypress(function(event){

            if(event.charCode >= 48 && event.charCode <= 57){

                if($(this).val().length === 1){

                    $(this).val($(this).val() + event.key + " / ");

                    }else if($(this).val().length === 0){

                    if(event.key == 1 || event.key == 0){

                        month = event.key;

                        return event.charCode;

                        }else{

                        $(this).val(0 + event.key + " / ");

                    }

                    }else if($(this).val().length > 2 && $(this).val().length < 9){

                    return event.charCode;

                }

            }

            return false;

            }).keyup(function(event){

            $(".date_value").html($(this).val());

            if(event.keyCode == 8 && $(".expire").val().length == 4){

                $(this).val(month);

            }

            

            if($(this).val().length === 0){

                $(".date_value").text("MM / YYYY");

            }

            }).keydown(function(){

            $(".date_value").html($(this).val());

            }).focus(function(){

            $(".date_value").css("color", "white");

        });

    });

</script>

<style>

    @import url("https://fonts.googleapis.com/css?family=Roboto:300,400,500");

    * {

    margin: 0;

    box-sizing: border-box;

    }

    

    html {

    --card-color: #cacaca;

    --text-color: #e1e1e1;

    }

    

    

    

    .tips {

    position: fixed;

    bottom: 0;

    width: 100%;

    height: 30px;

    background: #f1f1f1;

    line-height: 30px;

    font-size: 14px;

    padding: 2px 15px;

    }

    

    .containerx {

    

    display: -webkit-box;

    display: flex;

    -webkit-box-orient: horizontal;

    -webkit-box-direction: normal;

    flex-direction: row;

    -webkit-box-align: center;

    align-items: center;

    margin: auto;

    top: 0;

    bottom: 0;

    left: 0;

    right: 0;

    width: 680px;

    }

    .containerx .col1 {

    -webkit-perspective: 1000;

    perspective: 1000;

    -webkit-transform-style: preserve-3d;

    transform-style: preserve-3d;

    }

    .containerx .col1 .card {

    position: relative;

    width: 420px;

    height: 250px;

    margin-bottom: 85px;

    margin-right: 10px;

    border-radius: 17px;

    box-shadow: 0 5px 20px -5px rgba(0, 0, 0, 0.1);

    -webkit-transition: all 1s;

    transition: all 1s;

    -webkit-transform-style: preserve-3d;

    transform-style: preserve-3d;

    }

    .containerx .col1 .card .front {

    position: absolute;

    background: var(--card-color);

    border-radius: 17px;

    padding: 50px;

    width: 100%;

    height: 100%;

    transform: translateZ(1px);

    -webkit-transform: translateZ(1px);

    -webkit-transition: background 0.3s;

    transition: background 0.3s;

    z-index: 50;

    background-image: repeating-linear-gradient(45deg, rgba(255, 255, 255, 0) 1px, rgba(255, 255, 255, 0.03) 2px, rgba(255, 255, 255, 0.04) 3px, rgba(255, 255, 255, 0.05) 4px), -webkit-linear-gradient(-245deg, rgba(255, 255, 255, 0) 40%, rgba(255, 255, 255, 0.2) 70%, rgba(255, 255, 255, 0) 90%);

    -webkit-backface-visibility: hidden;

    backface-visibility: hidden;

    }

    .containerx .col1 .card .front .type {

    position: absolute;

    width: 75px;

    height: 45px;

    top: 20px;

    right: 20px;

    }

    .containerx .col1 .card .front .type img {

    width: 100%;

    float: right;

    }

    .containerx .col1 .card .front .card_number {

    position: absolute;

    font-size: 26px;

    font-weight: 500;

    letter-spacing: -1px;

    top: 110px;

    color: var(--text-color);

    word-spacing: 3px;

    -webkit-transition: color 0.5s;

    transition: color 0.5s;

    }

    .containerx .col1 .card .front .date {

    position: absolute;

    bottom: 40px;

    right: 55px;

    width: 90px;

    height: 35px;

    color: var(--text-color);

    -webkit-transition: color 0.5s;

    transition: color 0.5s;

    }

    .containerx .col1 .card .front .date .date_value {

    font-size: 12px;

    position: absolute;

    margin-left: 22px;

    margin-top: 12px;

    color: var(--text-color);

    font-weight: 500;

    -webkit-transition: color 0.5s;

    transition: color 0.5s;

    }

    .containerx .col1 .card .front .date:after {

    content: 'MONTH / YEAR';

    position: absolute;

    display: block;

    font-size: 7px;

    margin-left: 20px;

    }

    .containerx .col1 .card .front .date:before {

    content: 'valid \a thru';

    position: absolute;

    display: block;

    font-size: 8px;

    white-space: pre;

    margin-top: 8px;

    }

    .containerx .col1 .card .front .fullname {

    position: absolute;

    font-size: 20px;

    bottom: 40px;

    color: var(--text-color);

    -webkit-transition: color 0.5s;

    transition: color 0.5s;

    }

    .containerx .col1 .card .back {

    position: absolute;

    width: 100%;

    border-radius: 17px;

    height: 100%;

    background: var(--card-color);

    -webkit-transform: rotateY(180deg);

    transform: rotateY(180deg);

    }

    .containerx .col1 .card .back .magnetic {

    position: absolute;

    width: 100%;

    height: 50px;

    background: rgba(0, 0, 0, 0.7);

    margin-top: 25px;

    }

    .containerx .col1 .card .back .bar {

    position: absolute;

    width: 80%;

    height: 37px;

    background: rgba(255, 255, 255, 0.7);

    left: 10px;

    margin-top: 100px;

    }

    .containerx .col1 .card .back .seccode {

    font-size: 13px;

    color: var(--text-color);

    font-weight: 500;

    position: absolute;

    top: 100px;

    right: 40px;

    }

    .containerx .col1 .card .back .chip {

    bottom: 45px;

    left: 10px;

    }

    .containerx .col1 .card .back .disclaimer {

    position: absolute;

    width: 65%;

    left: 80px;

    color: #f1f1f1;

    font-size: 8px;

    bottom: 55px;

    }

    .containerx .col2 input {

    display: block;

    width: 260px;

    height: 30px;

    padding-left: 10px;

    padding-top: 3px;

    padding-bottom: 3px;

    margin: 7px;

    font-size: 17px;

    border-radius: 20px;

    background: rgba(0, 0, 0, 0.05);

    border: none;

    -webkit-transition: background 0.5s;

    transition: background 0.5s;

    }

    .containerx .col2 input:focus {

    outline-width: 0;

    background: rgba(31, 134, 252, 0.15);

    -webkit-transition: background 0.5s;

    transition: background 0.5s;

    }

    .containerx .col2 label {

    padding-left: 8px;

    font-size: 15px;

    color: #444;

    }

    .containerx .col2 .ccv {

    width: 40%;

    }

    .containerx .col2 .buy {

    width: 260px;

    height: 50px;

    position: relative;

    display: block;

    margin: 20px auto;

    border-radius: 10px;

    border: none;

    background: #42C2DF;

    color: white;

    font-size: 20px;

    -webkit-transition: background 0.4s;

    transition: background 0.4s;

    cursor: pointer;

    }

    .containerx .col2 .buy i {

    font-size: 20px;

    }

    .containerx .col2 .buy:hover {

    background: #3594A9;

    -webkit-transition: background 0.4s;

    transition: background 0.4s;

    }

    

    .chip {

    position: absolute;

    width: 55px;

    height: 40px;

    background: #bbb;

    border-radius: 7px;

    }

    .chip:after {

    content: '';

    display: block;

    width: 35px;

    height: 25px;

    border-radius: 4px;

    position: absolute;

    top: 0;

    bottom: 0;

    margin: auto;

    background: #ddd;

    }

    

</style>

@endsection

