<?php
    //Part 2 - Logic Test
    for ($x=1; $x<=100; $x++) {
        if ($x % 15 == 0 ) {
            echo "foobar, ";
        } elseif ($x % 5 == 0 ) {
            //not really needed - just didn't like the extra comma on the last number
            if ($x == 100) {
                echo "bar";
            } else {
                echo "bar, ";
            }
        } elseif ($x % 3 == 0 ) {
            echo "f00, ";
        } else {
            echo $x. ", ";
        }
    }

    echo "\n";

?>