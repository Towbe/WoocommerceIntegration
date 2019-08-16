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
        switch ($new_status) {
            case 'completed':
                $this->handle_completed_order($order_id);
                break;

            case 'cancelled':
            case 'failed':
            case 'on-hold':
                $this->handle_cancelled_order($order_id);
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

    public function handle_completed_order($order_id)
    {
        $order = wc_get_order($order_id);
            
        $store_destination = new Destination();
        $store_destination->type = "pickup";
        $store_destination->location = new LinkitLocation();
        $store_destination->address = get_option("linkit_store_address", "");
        $store_destination->location->lat = get_option("linkit_store_latitude", 0);
        $store_destination->location->lng = get_option('linkit_store_longitude', 0);
        

        $client_destination = new Destination();
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
        $client->reference_id = $order->get_user_id();

        $job = new LinkitJob();
        $job->cancelled = false;
        $job->destinations = array(
            0 => $store_destination,
            1 => $client_destination,
        );
        $job->reference_id = (string)$order_id;
        $job->clients = array(
            0 => $client,
        );
        $job->phone_number = $client->phone_number;
        $job->service = "Fast Store Request";

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
            
                $res = array(
                    "product" => $item->get_name(),
                    "label" => $item->get_product()->get_sku(),
                    "type"=>$item->get_product()->get_categories();
                    "quantity" => $item->get_quantity()
                );

                try {
                    $product = wc_get_product($id);
                    if ($product !== false && $product !== null) {
                        $images = $product->get_gallery_image_ids();
                        $imageurls = array();
                        for ($i = 0; $i < count($images); $i++) {
                            array_push($imageurls,wp_get_attachment_url($images[0]));
                        }
                        $res["image_uris"] = $imageurls;
                    } else {
                        error_log("Product with id " . $id . " does not exist");
                    }
                } catch (Exception $e) {
                    error_log($e);
                }

                if ($field !== '') {
                    $res['label'] = $item->get_meta($field);
                }

                print_r($res);

                array_push($linkit_items, $res);

                return true;
            }
        });
        
                
        $job->extra = array(
            "expected_picking_time" => $order->get_meta("expected_picking_time")
        );        

        $client_destination->extra = array(
            "parcels" => $linkit_items,
            "type" => "dropoff",
            "client_uid" => $order->get_user_id()
        );



        $id = $job->create();

        update_post_meta($order_id, 'linkit_job_id', $id);
    }
}
