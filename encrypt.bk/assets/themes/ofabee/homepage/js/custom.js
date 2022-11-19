$(document).ready(function(){
$('.event-carousel').owlCarousel({
    loop:false,
    margin:30,
    nav:false,
    autoPlay: true,
    autoPlay: 1000,
    responsive:{
        0:{
            items:1,
            margin:30
        },
        600:{
            items:3
        },
        760:{
            items:3
        },
        1000:{
            items:3
        }
    }
})

$('.testimonial-carousel').owlCarousel({
    loop:true,
    margin:30,
    nav:false,
    autoPlay: true,
    autoPlay: 1000,
    responsive:{
        0:{
            items:1,
            margin:10
        },
        600:{
            items:2
        },
        760:{
            items:2
        },
        1000:{
            items:2
        }
    }
})

});


    