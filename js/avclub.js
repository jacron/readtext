/**
 * Created by orion on 07-06-15.
 */
'use strict';

$(function() {
    $.each($('img'), function(){
        this.onload = function() {
            //console.log(this.width);
            if (this.width < 300) {
                $(this).hide();
            }
        };
    });
});