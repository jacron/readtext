/*
 * Author: jan
 * Date: 17-mei-2014
 */
'use strict';

$(function(){

   // reconstruct the host for images and links
   function getLinkUrl() {
       var originalLink = $('.originallink a').attr('href'),
            pos = originalLink.lastIndexOf('/');

       return originalLink.substr(0, pos);
   }

   function addPrefix(link) {
       var prefix;
        if (link.indexOf('/') !== 0) {
            prefix = getLinkUrl() + '/';
        }
        else {
            prefix = 'http://' + host;
        }
        return prefix + link;
   }

   // Get UTF8-encoded content.
   $('.utf8').click(function(e){
       e.preventDefault();
       document.location.href = document.location.href + '&utf8=1';
   });

    // Avoid caching.
    $('.refresh').click(function(e){
        e.preventDefault();
        document.location.href = document.location.href + '&refresh=1';
    });

    // Adjust src attribute of images.
   $.each($('img'), function() {
       var $this = $(this),
            src = $this.attr('src');

       if (src.length && src.indexOf('http') !== 0 && !$this.hasClass('rw-logo')) {
           $this.attr('src', addPrefix(src));
       }
   });

    function hasSpecialPrefix(href) {
        return href.indexOf('http') === 0 ||
            href.indexOf('javascript:') === 0 ||
            href.indexOf('mailto:') === 0;
    }

   // Adjust href attribute of hyperlinks
   $.each($('a'), function() {
       var $this = $(this),
            href = $this.attr('href');

       if (!href) {
           return true;
       }
       if (href.length && !hasSpecialPrefix(href)) {
           $this.attr('href', addPrefix(href));
       }
   });

});
