/*
 * Author: jan
 * Date: 28-mei-2014
 */


function pop_me_up(pURL, features){
    new_window = window.open(pURL, "popup_window", features);
    new_window.focus();
}

function pop_me_up2(pURL,name, features){
    new_window = window.open(pURL, name, features);
    new_window.focus();
}