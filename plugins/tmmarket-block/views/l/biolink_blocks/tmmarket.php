<?php defined('ALTUMCODE') || die() ?>
    <style>
body {
  padding-top: 80px;
}

.show-cart li {
  display: flex;
}
.card {
  margin-bottom: 20px;
}
.card-img-top {
  width: 200px;
  height: 200px;
  align-self: center;
}

       .modal-backdrop {
    position: inherit;
        }
        
        .modal-backdrop.show {
    opacity: 0;
    </style>
    

<style>
    .button {
  --background: <?= $data->link->settings->border_color ?>;
  --text: #fff;
  --cart: #fff;
  --tick: var(--background);
  position: relative;
  border: none;
  background: none;
  padding: 8px 28px;
  border-radius: 2px;
  -webkit-appearance: none;
  -webkit-tap-highlight-color: transparent;
  -webkit-mask-image: -webkit-radial-gradient(white, black);
  overflow: hidden;
  cursor: pointer;
  text-align: center;
  min-width: 144px;
  color: var(--text);
  background: var(--background);
  transform: scale(var(--scale, 1));
  transition: transform 0.4s cubic-bezier(0.36, 1.01, 0.32, 1.27);
}
.button:active {
  --scale: 0.95;
}
.button span {
  font-size: 14px;
  font-weight: 500;
  display: block;
  position: relative;
  padding-left: 24px;
  margin-left: -8px;
  line-height: 26px;
  transform: translateY(var(--span-y, 0));
  transition: transform 0.7s ease;
}
.button span:before,
.button span:after {
  content: "";
  width: var(--w, 2px);
  height: var(--h, 14px);
  border-radius: 1px;
  position: absolute;
  left: var(--l, 8px);
  top: var(--t, 6px);
  background: currentColor;
  transform: scale(0.75) rotate(var(--icon-r, 0deg))
    translateY(var(--icon-y, 0));
  transition: transform 0.65s ease 0.05s;
}
.button span:after {
  --w: 14px;
  --h: 2px;
  --l: 2px;
  --t: 12px;
}
.button .cart {
  position: absolute;
  left: 50%;
  top: 50%;
  margin: -13px 0 0 -18px;
  transform-origin: 12px 23px;
  transform: translateX(-120px) rotate(-18deg);
}
.button .cart:before,
.button .cart:after {
  content: "";
  position: absolute;
}
.button .cart:before {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  box-shadow: inset 0 0 0 2px var(--cart);
  bottom: 0;
  left: 9px;
  filter: drop-shadow(11px 0 0 var(--cart));
}
.button .cart:after {
  width: 16px;
  height: 9px;
  background: var(--cart);
  left: 9px;
  bottom: 7px;
  transform-origin: 50% 100%;
  transform: perspective(4px) rotateX(-6deg) scaleY(var(--fill, 0));
  transition: transform 1.2s ease var(--fill-d);
}
.button .cart svg {
  z-index: 1;
  width: 36px;
  height: 26px;
  display: block;
  position: relative;
  fill: none;
  stroke: var(--cart);
  stroke-width: 2px;
  stroke-linecap: round;
  stroke-linejoin: round;
}
.button .cart svg polyline:last-child {
  stroke: var(--tick);
  stroke-dasharray: 10px;
  stroke-dashoffset: var(--offset, 10px);
  transition: stroke-dashoffset 0.4s ease var(--offset-d);
}
.button.loading {
  --scale: 0.95;
  --span-y: -32px;
  --icon-r: 180deg;
  --fill: 1;
  --fill-d: 0.8s;
  --offset: 0;
  --offset-d: 1.73s;
}
.button.loading .cart {
  animation: cart 3.4s linear forwards 0.2s;
}
@keyframes cart {
  12.5% {
    transform: translateX(-60px) rotate(-18deg);
  }
  25%,
  45%,
  55%,
  75% {
    transform: none;
  }
  50% {
    transform: scale(0.9);
  }
  44%,
  56% {
    transform-origin: 12px 23px;
  }
  45%,
  55% {
    transform-origin: 50% 50%;
  }
  87.5% {
    transform: translateX(70px) rotate(-18deg);
  }
  100% {
    transform: translateX(140px) rotate(-18deg);
  }
}

.bdisplay {
padding: 0.1rem 0.75rem;
}

@media screen and (max-width: 768px) {
 .bdisplay {   
  display: none;
}  
}
</style>


<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="col-12 my-2">


<!-- Nav -->
<nav id="carticon" class="navbar navbar-inverse bg-inverse fixed-top bg-faded">
    <div class="row">
        <div class="col">
          <button type="button" class="btn btn-dark" style="margin-right: 5px; background-color:<?= $data->link->settings->border_color ?>; border-color: <?= $data->link->settings->border_color ?>;" data-toggle="modal" data-target="#cart"><i class="fas fa-shopping-cart"></i> <span style="font-size:70%; vertical-align: super;"><span class="total-count"></span></span></button><button class="clear-cart btn btn-danger"><i class="fas fa-trash-alt"></i></button></div>
    </div>
</nav>


<!-- Main -->
    <div class="row">
    
           <?php foreach($data->link->settings->items as $key => $item): ?>
            <?php if($item->enable): ?>
          <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
        <div class="card shadow" style="background:<?= $data->link->settings->background_color ?>; color:<?= $data->link->settings->text_color ?>; border: 0px;">
         <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
             <div class="carousel-inner">
                 <div class="carousel-item active">
      <img class="d-block w-100" style="aspect-ratio: 1/1; border-radius: 0.25rem 0.25rem 0 0;" src="/uploads/block_images/<?= $item->image ?>" alt="<?= $item->title ?>">
    </div>
    </div>
    </div>
                               
                <div class="card-footer p-4" style="background:<?= $data->link->settings->background_color ?>; border-top: 0px solid <?= $data->link->settings->text_color ?>;">
                <div class="text-right"><span style="font-size:70%; opacity: 0.4;">ID <?= $item->id ?></span></div>
                <h6><?= $item->title ?></h6>
                <p style="font-size: 85%; line-height: 1.2" class="fw-light text-gray mt-2"><?= $item->description ?></p>
 <div class="text-right">
 <span class="h5 mb-0 text-gray"><span style="margin-right:0.1rem; font-size: 80%;"><?= $data->link->settings->currency ?></span><?= $item->cost ?></span>    
</div>  
               
                       <button style="margin-top: 1rem;" class="button add-to-cart" data-name="<?= $item->id ?>" data-id="<?= $item->title ?>" data-price="<?= $item->cost ?>">
            <span><?= $data->link->settings->button_text ?></span>
          
            <div class="cart">
                <svg viewBox="0 0 36 26">
                    <polyline
                        points="1 2.5 6 2.5 10 18.5 25.5 18.5 28.5 7.5 7.5 7.5"
                    ></polyline>
                    <polyline points="15 13.5 17 15.5 22 10.5"></polyline>
                </svg>
            </div>
        </button>
        
            </div>
            
                    </div> 
    </div>
            <?php endif ?>
             <?php endforeach ?>
</div>

  
 <!-- Modal -->
<div class="modal fade" id="cart" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="background: #000000bf;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?= l('create_biolink_tmmarket_modal.cart') ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="show-cart table">
          
        </table>
        <div class="text-right"><?= l('create_biolink_tmmarket_modal.total_price') ?> <span style="margin-right:0.1rem; font-size: 80%;"><?= $data->link->settings->currency ?></span><span class="total-cart"></span></div>
      </div>
      <div class="modal-footer" style="display:block">

        <div id="carter">
        <form id="<?= 'mail_form_' . $data->link->biolink_block_id ?>" method="post" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="biolink_block_id" value="<?= $data->link->biolink_block_id ?>" />
                    <input type="hidden" name="currency" value="<?= $data->link->settings->currency ?>" required="required" />
                    <div class="titles"> </div>
                    
                    <div class="counter"></div>
                    
                    <div class="totalOrder"> </div>
                    <div class="totalCurrency"> </div>
                     
                    <div class="notification-container"></div>
                    <div class="row">
                    <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                        <input type="text" class="form-control form-control-lg" name="phone" pattern="^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){10,14}(\s*)?$" maxlength="32" required="required" placeholder="<?= $data->link->settings->phone_placeholder ?>" aria-label="<?= $data->link->settings->phone_placeholder ?>" />
                    </div>

                    <div class="form-group col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                        <input type="text" class="form-control form-control-lg" name="name" pattern="^[\w'\-,.][^0-9_!¡?÷?¿/\\+=@#$%ˆ&*(){}|~<>;:[\]]{2,}$" maxlength="64" required="required" placeholder="<?= $data->link->settings->name_placeholder ?>" aria-label="<?= $data->link->settings->name_placeholder ?>" />
                    </div>
                    
                    <?php if($data->link->settings->show_agreement): ?>
                        <div class="d-flex align-items-center">
                            <input type="checkbox" id="agreement" name="agreement" class="mr-3" required="required" />
                            <label for="agreement" class="text-muted mb-0">
                                <a href="<?= $data->link->settings->agreement_url ?>">
                                    <?= $data->link->settings->agreement_text ?>
                                </a>
                            </label>
                        </div>

                    <?php endif ?>

                   <!-- <?php if(settings()->captcha->biolink_is_enabled && settings()->captcha->type != 'basic'): ?>
                    <div class="form-group">
                        <?php (new \Altum\Captcha())->display() ?>
                    </div>
                    <?php endif ?>-->

                    <div style="display:block;" class="text-center col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                        <button  type="submit" name="submit" class="btn btn-block btn-md btn-primary" style="background:<?= $data->link->settings->border_color ?> !important; border-color: <?= $data->link->settings->border_color ?> !important;" data-is-ajax><?= l('create_biolink_tmmarket_modal.order_now') ?></button>
                    </div>
                    </div>
                </form>
                </div>
                
      </div>
    </div>
  </div>
</div> 


</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF"
        crossorigin="anonymous"></script>
<script src="https://unpkg.com/cart-localstorage@1.1.4/dist/cart-localstorage.min.js" type="text/javascript"></script>
<script>
// ************************************************
// Shopping Cart API
// ************************************************

var shoppingCart = (function() {
  // =============================
  // Private methods and propeties
  // =============================
  cart = [];
  
  // Constructor
  function Item(name, price, count, id) {
    this.name = name;
    this.id = id;
    this.price = price;
    this.count = count;
  }
  
  // Save cart
  function saveCart() {
    sessionStorage.setItem('shoppingCart', JSON.stringify(cart));
  }
  
    // Load cart
  function loadCart() {
    cart = JSON.parse(sessionStorage.getItem('shoppingCart'));
  }
  if (sessionStorage.getItem("shoppingCart") != null) {
    loadCart();
  }
  

  // =============================
  // Public methods and propeties
  // =============================
  var obj = {};
  
  // Add to cart
  obj.addItemToCart = function(name, price, id, count) {
    for(var item in cart) {
      if(cart[item].name === name) {
        cart[item].count ++;
        saveCart();
        return;
      }
    }
    var item = new Item(name, price, count, id);
    cart.push(item);
    saveCart();
  }
  // Set count from item
  obj.setCountForItem = function(name, count) {
    for(var i in cart) {
      if (cart[i].name === name) {
        cart[i].count = count;
        break;
      }
    }
  };
  // Remove item from cart
  obj.removeItemFromCart = function(name) {
      for(var item in cart) {
        if(cart[item].name === name) {
          cart[item].count --;
          if(cart[item].count === 0) {
            cart.splice(item, 1);
          }
          break;
        }
    }
    saveCart();
  }

  // Remove all items from cart
  obj.removeItemFromCartAll = function(name) {
    for(var item in cart) {
      if(cart[item].name === name) {
        cart.splice(item, 1);
        break;
      }
    }
    saveCart();
  }

  // Clear cart
  obj.clearCart = function() {
    cart = [];
    saveCart();
  }

  // Count cart 
  obj.totalCount = function() {
    var totalCount = 0;
    for(var item in cart) {
      totalCount += cart[item].count;
    }
    return totalCount;
  }

  // Total cart
  obj.totalCart = function() {
    var totalCart = 0;
    for(var item in cart) {
      totalCart += cart[item].price * cart[item].count;
    }
    return Number(totalCart.toFixed(2));
  }

  // List cart
  obj.listCart = function() {
    var cartCopy = [];
    for(i in cart) {
      item = cart[i];
      itemCopy = {};
      for(p in item) {
        itemCopy[p] = item[p];

      }
      itemCopy.total = Number(item.price * item.count).toFixed(2);
      cartCopy.push(itemCopy)
    }
    return cartCopy;
  }

  // cart : Array
  // Item : Object/Class
  // addItemToCart : Function
  // removeItemFromCart : Function
  // removeItemFromCartAll : Function
  // clearCart : Function
  // countCart : Function
  // totalCart : Function
  // listCart : Function
  // saveCart : Function
  // loadCart : Function
  return obj;
})();


// *****************************************
// Triggers / Events
// ***************************************** 
// Add item
$('.add-to-cart').click(function(event) {
  event.preventDefault();
  var name = $(this).data('name');
  var id = $(this).data('id');
  var price = Number($(this).data('price'));
  shoppingCart.addItemToCart(name, price, id, 1);
  displayCart();
  $("#carter").show(); 
  $("#carticon").show(); 
});

// Clear items
$('.clear-cart').click(function() {
  shoppingCart.clearCart();
  displayCart();
$("#carter").hide(); 
 $("#carticon").hide(); 
});


function displayCart() {
  var cartArray = shoppingCart.listCart();
  var output = "";
  var titles = "";
  var counter = "";
  for(var i in cartArray) {
    output += "<tr>"
      + "<td style='font-size:90%; line-height: 1.2; width:40%;'>" + cartArray[i].id + "</td>" 
      /*+ "<td style='font-size:90%; line-height: 1.2;'>(" + cartArray[i].price + ")</td>"*/
      /*+ "<td><div class='input-group'><button class='minus-item input-group-addon btn btn-primary bdisplay' data-name=" + cartArray[i].name + ">-</button>"*/
      + "<td style='width:30%;'><div class='input-group'><input type='number' class='item-count form-control' style='border: 1px solid #c1c1c1; margin: 0 1rem; border-radius: 0.2rem; min-width: 3rem; text-align: center;' min='0' data-name='" + cartArray[i].name + "' value='" + cartArray[i].count + "'></div></td>"
      /*+ "<button class='plus-item btn btn-primary input-group-addon bdisplay' data-name=" + cartArray[i].name + ">+</button></div></td>"*/
      + "<td style='width:10%;'><button class='delete-item btn btn-danger' style='padding: 0rem 0.45rem; border-radius: 0.2rem; vertical-align: sub;' data-name=" + cartArray[i].name + "><i class='fas fa-trash-alt'></i></button></td>"
      + " = " 
      + "<td style='padding-top: 1.2em; width:20%;'>" + cartArray[i].total + "</td>" 
      +  "</tr>";
      titles += "<input type=\"hidden\" name=\"item_title[" + i +"]\" value=" + cartArray[i].name +" required=\"required\" />";
      counter += "<input type=\"hidden\" name=\"item_count[" + i +"]\" value=" + cartArray[i].count +" required=\"required\" />";
  }
  var totalOrder = "<input type=\"hidden\" name=\"total\" value=" + shoppingCart.totalCart() +" required=\"required\" />";
  $('.titles').html(titles);
  $('.counter').html(counter);
  $('.totalOrder').html(totalOrder);
  $('.totalCurrency').html(totalOrder);
  $('.show-cart').html(output);
  $('.total-cart').html(shoppingCart.totalCart());
  $('.total-count').html(shoppingCart.totalCount());
  
}

if (shoppingCart.totalCart() < 1) {
   $("#carter").hide(); // hide
    $("#carticon").hide(); 
} 

if (shoppingCart.totalCart() > 1) {
   $("#carter").show(); // hide
   $("#carticon").show(); 
} 

// Delete item button

$('.show-cart').on("click", ".delete-item", function(event) {
  var name = $(this).data('name')
  shoppingCart.removeItemFromCartAll(name);
  displayCart();
  if (shoppingCart.totalCart() < 1) {
   $("#carter").hide(); // hide
   $("#carticon").hide(); 
}
})


// -1
$('.show-cart').on("click", ".minus-item", function(event) {
  var name = $(this).data('name')
  shoppingCart.removeItemFromCart(name);
  displayCart();
})
// +1
$('.show-cart').on("click", ".plus-item", function(event) {
  var name = $(this).data('name')
  shoppingCart.addItemToCart(name);
  displayCart();
})

// Item count input
$('.show-cart').on("change", ".item-count", function(event) {
   var name = $(this).data('name');
   var count = Number($(this).val());
  shoppingCart.setCountForItem(name, count);
  displayCart();
});

displayCart();
</script>

 <script>
        'use strict';

        /* Go over all mail buttons to make sure the user can still submit mail */
        $('form[id^="mail_"]').each((index, element) => {
            let biolink_block_id = $(element).find('input[name="biolink_block_id"]').val();
            let is_converted = localStorage.getItem(`mail_${biolink_block_id}`);

            /*if(is_converted) {
                $(element).find('button[type="submit"]').attr('disabled', 'disabled');
            }*/
        });
        /* Form handling for mail submissions if any */
        $('form[id^="mail_"]').on('submit', event => {
            let biolink_block_id = $(event.currentTarget).find('input[name="biolink_block_id"]').val();
            let is_converted = localStorage.getItem(`mail_${biolink_block_id}`);

            let notification_container = event.currentTarget.querySelector('.notification-container');
            notification_container.innerHTML = '';
            pause_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

            if(is_converted || !is_converted) {
                $.ajax({
                    type: 'POST',
                    url: `${site_url}l/link/tmmarket`,
                    data: $(event.currentTarget).serialize(),
                    dataType: 'json',
                    success: (data) => {
                        enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));

                        if (data.status == 'error') {
                            display_notifications(data.message, 'error', notification_container);
                        } else if (data.status == 'success') {

                            display_notifications(data.message, 'success', notification_container);

                            setTimeout(() => {

                                /* Hide modal */
                                $(event.currentTarget).closest('.modal').modal('hide');

                                /* Remove the notification */
                                notification_container.innerHTML = '';

                                /* Set the localstorage to mention that the user was converted */
                                localStorage.setItem(`mail_${biolink_block_id}`, true);

                                /* Set the submit button to disabled */
                                $(event.currentTarget).find('button[type="submit"]').attr('disabled', 'disabled');

                                if(data.details.thank_you_url) {
                                    window.location.replace(data.details.thank_you_url);
                                }

                            }, 1500);
                         shoppingCart.clearCart();
                         displayCart();
                         
                        }

                        /* Reset captcha */
                        try {
                            grecaptcha.reset();
                            hcaptcha.reset();
                            turnstile.reset();
                        } catch (error) {}
                    },
                    error: () => {
                        enable_submit_button(event.currentTarget.querySelector('[type="submit"][name="submit"]'));
                        display_notifications(<?= json_encode(l('global.error_message.basic')) ?>, 'error', notification_container);
                    },
                });

            }

            event.preventDefault();
        })
    </script>
    
    <script>
    document.querySelectorAll(".button").forEach((button) =>
    button.addEventListener("click", (e) => {
        if (!button.classList.contains("loading")) {
            button.classList.add("loading");
            setTimeout(() => button.classList.remove("loading"), 3500);
        }
        e.preventDefault();
    })
);
</script>
