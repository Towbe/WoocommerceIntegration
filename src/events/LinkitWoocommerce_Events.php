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

    public function handle_send_to_picker($order_id)
    {

    }

    public function handle_send_to_driver($order_id)
    {

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
            $client_destination->location->lat = (float)$order->get_meta($client_longitude_meta);
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
                $field = '';
                if ($this->barcode_field === '') {
                    $field = (string)get_option("linkit_barcode_field", "");
                } else {
                    $field = $this->barcode_field;
                }

                $product = $item->get_product();

                $imageurls = array();
                try {
                    $product = $item->get_product();
                    if ($product !== false && $product !== null) {

                        $images = $product->get_gallery_image_ids();

                        for ($i = 0; $i < count($images); $i++) {

                            array_push($imageurls,wp_get_attachment_image_url($images[0]));
                        }

                     error_log($imageurls);
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
                    "image_uris" => $imageurls
                );

                if ($field !== '') {
                    $res['label'] = $item->get_meta($field);
                }

                print_r($res);

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


        $job->organization = "Test Org";
        $job->dispatching_organization = "Test Org";
        $job->driver_uid = $order->get_meta("driver_uid");


        $client_destination->extra = array(
            "parcels" => $linkit_items,
            "type" => "dropoff",
            "client_uid" => $order->get_user_id(),
        );

        $job->destinations = array(
            0 => $store_destination,
            1 => $client_destination,
            0 => $store_destination,
        );

        $id = $job->create();

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
