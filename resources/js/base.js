$(document).ready( function () {
    $('li.m-menu__item > div > ul > li.m-menu__item--active').parents('li.m-menu__item').addClass('m-menu__item--open m-menu__item--expanded');
});