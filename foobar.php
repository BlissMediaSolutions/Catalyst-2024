<?php
    //Part 2 - Logic Test
    $result = "";
    for ($x=1; $x<=100; $x++) {
        if ($x % 15 == 0 ) {
            $result = "foobar";
        } elseif ($x % 5 == 0 ) {
            $result = "bar";
        } elseif ($x % 3 == 0 ) {
            $result = "foo";
        } else {
            $result = $x;
        }
        echo ($x != 100) ? $result.=", " : $result;
    }

    echo "\n";

?>