!function($,t,e){$(function(){"use strict";var t=$("#js-mobile-menu").unbind();$("#js-navigation-menu").removeClass("show"),t.on("click",function(t){t.preventDefault(),$("#js-navigation-menu").slideToggle(function(){$("#js-navigation-menu").is(":hidden")&&$("#js-navigation-menu").removeAttr("style")})}),$(".share-fb").attr("href","https://www.facebook.com/sharer/sharer.php?u="+window.location.href),$(".share-twitter").attr("href","https://twitter.com/home?status="+window.location.href),$(".share-google").attr("href","https://plus.google.com/share?url="+window.location.href)})}(jQuery,this);