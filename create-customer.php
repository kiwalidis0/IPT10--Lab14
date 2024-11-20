<?php

require "init.php";

$customer = $stripe->customers->create([
    'name' => 'Nene De Leon',
    'email' => 'Nene@DeLeon.ph',
    'phone' => '+639123456789',
    'address' => [
        'line1' => 'Mabuhay St.',
        'line2' => 'Masaya Village',
        'state' => '',
        'city' => 'Angeles City',
        'country' => 'Philippines',
        'postal_code' => 2019
    ]
]);

print_r($customer);