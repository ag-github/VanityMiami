jQuery(document).ready(function () {
    jQuery(".owl-carousel").owlCarousel({
        items: 4,
        lazyLoad: true,
        navigation: true
    });
});
jQuery(document).ready(function () {
    jQuery(".owl-carousel").mouseover(function () {
        jQuery(".owl-carousel").owlCarousel({
            autoPlay: 3000,
            items: 4,
            lazyLoad: true,
            navigation: true
        });
    });
});