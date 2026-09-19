<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Point Earning Actions
    |--------------------------------------------------------------------------
    |
    | Define the amount of points a user receives for various community actions.
    |
    */
    'earn' => [
        'identity_verification' => 50,
        'free_listing'          => 15,
        'meetup_host'           => 20,
        'positive_review'       => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Point Spending Actions
    |--------------------------------------------------------------------------
    |
    | Define the cost of various promotions and perks.
    |
    */
    'spend' => [
        'featured_promotion'  => 100,
        'sponsored_promotion' => 300,
    ],
];
