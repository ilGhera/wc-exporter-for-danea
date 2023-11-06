<?php
/**
 * Currency exchange class
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 * @since 1.5.0
 */

class WCEXD_Currency_Exchange {

    /**
     * Check if the currency exchange option is active
     *
     * @var bool
     */
    private $is_active;


    /**
     * Check if the order has currency = USD
     *
     * @var bool
     */
    private $is_usd;


    /**
     * The constructor
     *
     * @param object $order the WC order.
     *
     * @return void
     */
    public function __construct( $order ) {

        $this->is_active = get_option( 'wcexd-currency-exchange' );
        $this->is_usd    = 'USD' === $order->get_currency() ? true : false;

    }


    /**
     * Get latest rates from the endpoint
     *
     * @return array
     */
    private function get_latest_rates() {

        $base_url = 'https://tassidicambio.bancaditalia.it/terzevalute-wf-web/rest/v1.0/';
        $response = wp_remote_request(
            $base_url . 'latestRates',
            array(
                'method' => 'GET',
                'headers' => array(
                    'Accept' => 'application/json',
                ),
                'timeout' => 20,
            )
        );

        /* error_log( 'RESPONSE: ' . print_r( json_decode( $response['body'] ), true ) ); */

        $body = isset( $response['body'] ) ? json_decode( $response['body'] ) : null;

        if ( is_object( $body ) && isset( $body->latestRates ) ) {

            return $body->latestRates;

        }

    }


    /**
     * Get the URD rate
     *
     * @return float
     */
    private function get_usd_rate() {

        $transient = get_transient( 'wcexd-usd-rate' );

        if ( $transient ) {

            return $transient;

        } else {

            $rates = $this->get_latest_rates(); 

            if ( is_array( $rates ) ) {

                foreach ( $rates as $rate ) {

                    if ( isset( $rate->isoCode ) && 'USD' === $rate->isoCode ) {

                        if ( isset( $rate->eurRate ) ) {

                            /* Set the transient */
                            set_transient( 'wcexd-usd-rate', $rate->eurRate, DAY_IN_SECONDS );

                            return $rate->eurRate;

                        }

                    }

                }

            }

        }

    }


    /**
     * Exchange dollars in euros
     *
     * @param float $price the value to transform.
     *
     * @return float
     *
     */
    public function filter_price( $price ) {

        if ( ! $this->is_active || ! $this->is_usd ) {

            return number_format( $price, 2, '.', '' );

        } else {

            $usd_rate = $this->get_usd_rate();

            return number_format( $price / $usd_rate, 2, '.', '' );

        }

    }


    /**
     * Display the USD exchange rate
     *
     * @return string
     */
    public function the_usd_exchange_rate() {

        if ( $this->is_active && $this->is_usd ) {

            printf( wp_kses_post( 'Tasso di cambio applicato: 1 USD (dollaro USA $) = %f EURO (€)' ), $this->get_usd_rate() );

        }

    }

}

