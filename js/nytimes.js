/*
 * Author: jan
 * Date: 28-mei-2014
 */


//noinspection JSUnusedGlobalSymbols
function pop_me_up(pURL, features){
    var new_window = window.open(pURL, "popup_window", features);
    new_window.focus();
}

//noinspection JSUnusedGlobalSymbols
function pop_me_up2(pURL,name, features){
    var new_window = window.open(pURL, name, features);
    new_window.focus();
}