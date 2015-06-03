/*
 * Author: jan
 * Date: 24-mei-2014
 */
$(function(){

    // Toggle big/small image on readwrite.com
   $('.readwrite-com .avatar img').click(function(){
       var $img = $(this);

       if ($img.width() == 30) {
           $img.width('initial');
           $img.height('initial');
       }
       else {
           $img.width(30);
           $img.height(30);
       }
   });
});



