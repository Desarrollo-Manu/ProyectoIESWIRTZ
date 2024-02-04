<?php
function estarLogeado(){
    if (isset($_COOKIE['PHPSESSID'])) {
        if($_COOKIE['PHPSESSID']===session_id()){
            return true;
        }else{
            session_start();
            return false;
        }
    } else {
        return false;
    }
}