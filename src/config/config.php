<?php

    return array(

        /*
        |--------------------------------------------------------------------------
        | PayPal Environment
        |--------------------------------------------------------------------------
        |
        | Whether or not the IPN verification should respond to the PayPal
        | production or sandbox server.
        |
        | Supported: 'production', 'sandbox'
        */
        'environment' => 'production',

        /*
        |--------------------------------------------------------------------------
        | Request Handler
        |--------------------------------------------------------------------------
        |
        | To verify an IPN message, a POST request must be made to the PalPal
        | server. The default, 'auto' will usually work best, as it uses cURL when
        | available and falls back to fsock.
        |
        | Supported: 'auto', 'curl', 'fsock'
        |
        */
        'request_handler' => 'auto',

        'table_name_orders' => 'ipn_orders',
        'table_name_order_items' => 'ipn_order_items',
        'table_name_order_item_option' => 'ipn_order_item_options'

    );