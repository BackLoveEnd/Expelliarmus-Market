<?php

if (! function_exists('randomNumber')) {
    function randomNumber(int $length = 8): int
    {
        $output = random_int(1, 9);

        for ($i = 1; $i < $length; $i++) {
            $output .= random_int(0, 9);
        }

        return (int) $output;
    }
}

if (! function_exists('randomString')) {
    function randomString($length = 10, bool $upper = false): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $upper ? strtoupper($randomString) : $randomString;
    }
}
