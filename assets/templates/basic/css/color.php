<?php
header("Content-Type:text/css");
$color = "#f0f"; // Change your Color Here
$secondColor = "#ff8"; // Change your Color Here

function checkhexcolor($color){
    return preg_match('/^#[a-f0-9]{6}$/i', $color);
}

if (isset($_GET['color']) AND $_GET['color'] != '') {
    $color = "#" . $_GET['color'];
} 

if (!$color OR !checkhexcolor($color)) {
    $color = "#336699";
}


function checkhexcolor2($secondColor){
    return preg_match('/^#[a-f0-9]{6}$/i', $secondColor);
}

if (isset($_GET['secondColor']) AND $_GET['secondColor'] != '') {
    $secondColor = "#" . $_GET['secondColor'];
}

if (!$secondColor OR !checkhexcolor2($secondColor)) {
    $secondColor = "#336699";
}
?>

.login-button, .header-top,.category__header,.read__more,.subscribe-form button,.widget .ui-slider-range,.view-number li.active .bar,.widget-check-group input:checked~label::before,.qv-btn,.pagination .page-item a, .pagination .page-item span,.cmn--btn.theme,.dashboard-item:hover,.file-input-btn,.bill-button,.cmn-btn,.submit-button,.contact-group .cmn--btn,.form--check input:checked:before,.contact-group .cmn--btn:hover,.shortcut-icons li a .amount,.product-share a, .product-details-wishlist a,.nav-tabs li a.active,.wish-react li a, .wish-react li button,.cart-plus-minus .cart-decrease:hover, .cart-plus-minus .cart-decrease.active, .cart-plus-minus .cart-increase:hover, .cart-plus-minus .cart-increase.active,.cart-table tr th,.apply-coupon-code button,.order-track-form-group button,.order-track-item .thumb,.filter-in,.left-category .categories li:hover > a{
    background: <?php echo $color  ?>
}


.wish-react li a.active, .wish-react li button, .wish-react li a.active:hover, .wish-react li button.active:hover{
    background: #071636!important
}

.contact-group .select-bar .list .option.selected, .contact-group .select-bar .list .option:hover{
    background: <?php echo $color  ?>!important
}

.view-style li a.active{
    background: <?php echo $color  ?> !important
}

.view-number li .bar{
    background: <?php echo $color.'4d'  ?>
}

.pagination .page-item.disabled span{
    background: <?php echo $color.'4d'  ?>
}

.user-profile .content{
    background: <?php echo $color.'4d'  ?>
}

.widget .title{
    border-bottom : 2px dashed <?php echo $color.'33'  ?>
}

.header-category-area .cmn--btn,.btn--base, .badge--base, .bg--base,.widget .ui-state-default,.preloader,*::selection,body *::-webkit-scrollbar-button,body *::-webkit-scrollbar-thumb,.product-details .product-size-area .product-single-size.active{
    background-color: <?php echo $color ?>!important
}
.cmn--btn.white,.widget-check-group input:checked~label::before,.cmn--btn.theme,.dashboard-menu ul li a:hover, .dashboard-menu ul li a.active,.cart-plus-minus .cart-decrease:hover, .cart-plus-minus .cart-decrease.active, .cart-plus-minus .cart-increase:hover, .cart-plus-minus .cart-increase.active,.product-details .product-color-area .product-single-color.active,.product-details .product-size-area .product-single-size.active, .header-category-area .cmn--btn{
    border-color : <?php echo $color ?>
}

.form-control:focus{
    border-color : <?php echo $color.'33'  ?>
}

.product__item:hover .d-price,.section-header-2 a,.footer__widget .contact__info .icon,.footer__widget .footer__links li a:hover,.footer__widget .footer__links li a::before,.filter-category li a.active,.filter-category li a:hover,.product-item-2-inner .product-content .price,.pagination .page-item.active span, .pagination .page-item.active a, .pagination .page-item:hover span, .pagination .page-item:hover a,.info__item .info__content .info__title a,.dashboard-item:hover .dashboard-icon,.acc a,.contact-group .account-alt a,.contact-group a,.product-details-content .price,h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,.total .amount,.vendor__item .vendor__info li i,.vendor__item .read-more,.vendor__single__author .content-area ul li i,.best-sell-item .best-sell-inner .read-more,.order-track-item .thumb i{
    color: <?php echo $color ?>
}

.text--base{
    color: <?php echo $color ?>!important
}

.vendor__item .vendor__bottom .vendor-author{
    box-shadow:0 0 4px <?php echo $color.'e6'  ?>
}

.modal-content{
    border: 3px solid <?php echo $color ?>
}

.order-track-item::after{
    border-top : 2px dashed <?php echo $color ?>
}