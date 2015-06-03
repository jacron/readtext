/*
 * Author: jan
 * Date: 26-mei-2014
 */

$(function(){
    //$('h3').html($('h3 a').html());
    var header = $('h3 a').html();
    //$('h3').append($('<h1>').html());
    $('h3').html('').append($('<h1>').html(header));
});

