<?php

if (! function_exists('randomNumber')) {
    function randomNumber(int $length = 8): int
    {
        $output = random_int(1, 9);

        for ($i = 1; $i < $length; ++$i) {
            $output .= random_int(0, 9);
        }

        return (int)$output;
    }
}