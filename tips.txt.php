<?php

//box collision
if( $dest->x < $box->x + $box->w && 
    $dest->x + $dest->w > $box->x &&
    $dest->y < $box->y + $box->h &&        
    $dest->y + $dest->h > $box->y) {
        // echo 'kolizka';
        if($dest->y + $dest->h > $box->y) {echo 'cos';}
        // $y_pos = $box->y - 32;
        // $x_pos = $box->x - 32;
    }