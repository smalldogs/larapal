<?php namespace Smalldogs\LaraPal;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

use PayPal\Ipn\Listener;
use PayPal\Ipn\Message;
use PayPal\Ipn\Verifier\CurlVerifier;

use Smalldogs\LaraPal\Exception\InvalidIpnException;
use Smalldogs\LaraPal\Models\IpnOrder;
use Smalldogs\LaraPal\Models\IpnOrderItem;
use Smalldogs\LaraPal\Models\IpnOrderItemOption;

/**
 * Class PayPalIpn
 * @package Smalldogs\LaraPal
 *
 * References:
 * https://github.com/mike182uk/paypal-ipn-listener
 * https://github.com/orderly/symfony2-paypal-ipn/blob/master/src/Orderly/PayPalIpnBundle/Ipn.php
 */
class LaraPal {

    /**
     * Listens for and stores PayPal IPN requests.
     *
     * @return IpnOrder
     * @throws InvalidIpnException
     * @throws UnexpectedResponseBodyException
     * @throws UnexpectedResponseStatusException
     */
    public function storeOrder()
    {

        $data = $this->verifyIpn();

        return $this->store($data);

    }

    /**
     * Get the PayPal environment configuration value.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return Config::get('larapal::table_name_orders', 'production');
    }

    /**
     * Set the PayPal environment runtime configuration value.
     *
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        Config::set('larapal::environment', $environment);
    }

    /**
     * Stores the IPN contents and returns the IpnOrder object.
     *
     * @param array $data
     * @return IpnOrder
     */
    private function store($data)
    {
        Log::info('LaraPal->Store called');

        // If `Transaction Id` exists, we are updating order status
        $order = IpnOrder::where('txn_id', $data['txn_id'])->first();

        // If the order does exist, update it and quit
        if ($order)
        {
            $order->fill($data);
            $order->save();
            return $order;
        }

        // No order exists, begin a new one
        $order = new IpnOrder((array) $data);

        $order->save();

        // Add inividual items to order
        $this->storeOrderItems($order, $data);

        return $order;
    }

    /**
     * Stores the order items from the IPN contents.
     *
     * @param IpnOrder $order
     * @param array $data
     */
    private function storeOrderItems($order, $data)
    {
        $cart = isset($data['num_cart_items']);
        $numItems = (isset($data['num_cart_items'])) ? $data['num_cart_items'] : 1;

        // Loop through each item
        for ($i = 0; $i < $numItems; $i++)
        {
            $suffix = ($numItems > 1 || $cart) ? ($i + 1) : '';

            $itemAttributes = [
                'item_name',
                'item_number',
                'quantity',
                'mc_gross',
                'mc_handling',
                'mc_shipping',
                'tax'
            ];

            $item = new IpnOrderItem();

            // Loop through each attribute for each item
            foreach ($itemAttributes as $attributeName)
            {
                if (isset($data[$attributeName . $suffix]))
                {
                    $item->{$attributeName} = $data[$attributeName . $suffix];
                }
            }

            $order->items()->save($item);

            // Set the order item options if any
            // $count = 7 because PayPal allows you to set a maximum of 7 options per item
            // Reference: https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_html_Appx_websitestandard_htmlvariables
            for ($ii = 1, $count = 7; $ii < $count; $ii++) {
                if (isset($data['option_name' . $ii . '_' . $suffix])) {
                    $option = new IpnOrderItemOption();
                    $option->option_name = $data['option_name' . $ii . '_' . $suffix];
                    if (isset($data['option_selection' . $ii . '_' . $suffix])) {
                        $option->option_selection = $data['option_selection' . $ii . '_' . $suffix];
                    }
                    $item->options()->save($option);
                }
            }
        }
    }

    /**
     * Validates the IPN request returns the IPN data on success
     *
     * @return array $ipnMessage
     */
    public function verifyIpn ()
    {
        $listener = new Listener;
        $verifier = new CurlVerifier;

        $ipnMessage = Message::createFromGlobals(); // uses php://input

        $verifier->setIpnMessage($ipnMessage);
        $verifier->forceSSLv3(FALSE);
        $verifier->setEnvironment('production'); // can either be sandbox or production

        $listener->setVerifier($verifier);

        // If IPN does not validate
        if (!$listener->processIpn()) throw new InvalidIpnException($listener->getReport());

        // On verified IPN
        return $ipnMessage;

    }

}