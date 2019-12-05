<?php

require_once 'LinkitJob.php';


class LinkitWoocommerce_Events
{

    protected $plugin_name;

    protected $version;

    private $barcode_field;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->barcode_field = '';
    }

    public function on_order_status_changed($order_id, $old_status, $new_status)
    {
        $send_picker = get_option('linkit_send_picker');
        $send_driver = get_option('linkit_send_driver');
        $cancel = get_option('linkit_cancel');
        $finish = get_option('linkit_finish');

        if (strpos($send_picker, $new_status) !== false) {
            $this->handle_dispatch_order($order_id, 'Picker');
            return;
        }
        if (strpos($send_driver, $new_status) !== false) {
            $this->handle_dispatch_order($order_id);

            return;
        }
        if (strpos($cancel, $new_status) !== false) {
            $this->handle_cancelled_order($order_id);
            return;
        }
        if (strpos($finish, $new_status) !== false) {
            $this->handle_finish_order($order_id);
            return;
        }
    }

    public function handle_cancelled_order($order_id)
    {
        $id = (string)get_post_meta($order_id, 'linkit_job_id', true);
        if (strlen($id) == 0) {
            error_log("Could not cancel the order " . $order_id . " because it's job id is not defined");
            return;
        }

        $job = new LinkitJob();
        $job->id = $id;
        $job->cancel();
    }

    public function handle_dispatch_order($order_id, $service='')
    {
        $order = wc_get_order($order_id);
        $store_destination = new Destination();
        $store_destination->extra=array("type" => "pickup");
        $store_destination->location = new LinkitLocation();
        $store_destination->address = get_option("linkit_store_address", "");
        $store_destination->location->lat = get_option("linkit_store_latitude", 0);
        $store_destination->location->lng = get_option('linkit_store_longitude', 0);
        $store_destination->extra = array(
            "hide_map" => true,
        );

        $store_destination_2 = new Destination();
        $store_destination_2->extra=array("type" => "pickup");
        $store_destination_2->location = new LinkitLocation();
        $store_destination_2->address = get_option("linkit_store_address", "");
        $store_destination_2->location->lat = get_option("linkit_store_latitude", 0);
        $store_destination_2->location->lng = get_option('linkit_store_longitude', 0);

        $client_destination = new Destination();
        $client_destination ->extra = array("type" => "dropoff");
        $client_destination->location = new LinkitLocation();
        $client_destination->address = $order->get_formatted_shipping_address();
        $client_latitude_meta = get_option('linkit_latitude_meta', '');
        $client_longitude_meta = get_option('linkit_longitude_meta', '');
        if ($client_latitude_meta !== '') {
            $client_destination->location->lat = (float)$order->get_meta($client_latitude_meta);
        }

        if ($client_longitude_meta !== '') {
            $client_destination->location->lng = (float)$order->get_meta($client_longitude_meta);
        }

        $client = new LinkitClient();
        $client->name = $order->get_shipping_first_name();
        $client->name .= ' ' . $order->get_shipping_last_name();
        $client->phone_number = $order->get_billing_phone();
        $client->extra = array("reference_uid" => $order->get_user_id()) ;

        $job = new LinkitJob();
        $job->cancelled = false;
        $job->reference_id = (string)$order_id;
        $job->clients = array(
            0 => $client,
        );
        $job->phone_number = $client->phone_number;
        $job->job_number = (int)preg_replace('/[^0-9]/', '', $order->get_order_number());

        if ($service === '') {
            $job_type_meta = get_option('linkit_job_type_meta', '');
            if ($job_type_meta === '') {
                $job->service = 'Fast Store Request';
            }  else {
                $job->service = $order->get_meta($job_type_meta);
            }
        } else {
            $job->service = $service;
        }

        $items = $order->get_items();
        $linkit_items = array();

        array_walk($items, function ($item, $id) use (&$linkit_items) {
            {
                $barcode_field = $client_longitude_meta = get_option('linkit_barcode_meta', '');

                $product = $item->get_product();

                $imageurls = array();
                try {
                    $product = $item->get_product();
                    if ($product !== false && $product !== null) {

                        $images = wp_get_attachment_image_url($product->get_image_id(), 'full');
                        $imageurls = $images;
                    } else {
                        error_log("Product with id " . $id . " does not exist");
                    }
                } catch (Exception $e) {
                    error_log($e);
                }

                $res = array(
                    "product" => $item->get_name(),
                    "label" => $product ->get_sku(),
                    "type"=> wc_get_product_category_list($product->get_id()),
                    "quantity" => $item->get_quantity(),
                    "image_uris" => $imageurls,
                );

                if ($barcode_field !== '') {
                    $res['label'] = $product->get_meta($barcode_field);
                }

                array_push($linkit_items, $res);

                return true;
            }
        });

        $job->extra = array(
            "expected_picking_time" => $order->get_meta("expected_picking_time"),
            "pickedStatus" => "Not Processed",
            "job_type" => "picker",
            "woocommerce_order_id" => $order_id,
        );

        $client_destination->extra = array(
            "parcels" => $linkit_items,
            "type" => "dropoff",
            "client_uid" => $order->get_user_id(),
        );

        $job->destinations = array(
            0 => $store_destination,
            1 => $client_destination,
            2 => $store_destination_2,
        );

        if ($service != 'Picker') {
            $api_key = get_option('linkit_api_key', "");
            if ($api_key == "") {
                error_log('Linkit api key is not set');
                return null;
            }
            $stage = $order->get_meta('stage');
            if ($stage != 0) {
                $times = wp_remote_post('https://api.towbe.com/v1/timeslots/get-timeslots', array(
                    'method'=> 'POST',
                    'timeout'=> 30,
                    'headers' => array(
                        "Authorization" => $api_key,
                        "Cache-Control" => "no-cache",
                        "Content-Type" => "application/json",
                    ),
                    'body' => json_encode(array(
                        "time"=> $order->get_date_created()->format(DATE_RFC3339),
                        "vehicle_type"=> "moto",
                        "number_of_timeslots"=> 4,
                    ))
                ));
                $job->schedule = (new DateTime(json_decode($times['body'])->timeslots[$stage - 1]->time_end))->getTimestamp();
            }
        }

        $id = $job->create();

        error_log($id);

        update_post_meta($order_id, 'linkit_job_id', $id);
    }

    public function handle_finish_order($order_id) {
        $id = (string)get_post_meta($order_id, 'linkit_job_id', true);
        if (strlen($id) == 0) {
            error_log("Could not finish the order " . $order_id . " because it's job id is not defined");
            return;
        }

        $job = new LinkitJob();
        $job->id = $id;
        $job->finish();
    }
}
