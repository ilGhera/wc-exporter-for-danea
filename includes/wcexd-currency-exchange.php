<?php
/**
 * Currency exchange class
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 * @since 1.4.14
 */

class WCEXD_Currency_Exchange {

    public function __construct() {

        $is_active = get_option( 'wcexd-currency-exchange' );

    }


    private function get_latest_rates() {

        $base_url = 'https://tassidicambio.bancaditalia.it/terzevalute-wf-web/rest/v1.0/';
        $response = wp_remote_request(
            $base_url . 'latestRates',
            /* $base_url . 'dailyRates?referenceDate=2023-03-19&baseCurrencyIsoCode=USD&currencyIsoCode=EUR', */
            array(
                'method' => 'GET',
                'headers' => array(
                    'Accept' => 'application/json',
                ),
                'timeout' => 20,
                /* 'body'    => $body, */
            )
        );
        error_log( 'RESPONSE: ' . print_r( json_decode( $response['body'] ), true ) );

    }

}
new WCEXD_Currency_Exchange();

