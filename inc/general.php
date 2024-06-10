<?php
//cart url
function get_cart_url() {
    if (WP_ENV === 'production') {
        return 'https://melodyraejones.com/contact/cart/';
    } else {
        return 'http://melodyraejones.local/shop/cart/';
    }
}