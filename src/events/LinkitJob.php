<?php

require_once 'Destination.php';
require_once 'LinkitClient.php';
require_once 'LinkitLocation.php';

class LinkitJob
{
    public $id;
    public $service;
    public $destinations;
    public $cancelled;
    public $extra;
    public $clients;
    public $reference_id;
    public $phone_number;

    private function send_request($path, $method, $data) {
        $api_key = get_option('linkit_api_key', "");
        if ($api_key == "") {
            error_log('Linkit api key is not set');
            return null;
        }

        $url = "https://api.tbtest.net/v1/" . $path;

        $response = wp_remote_post ($url, array(
            'method' => $method,
            'timeout' => 30,
            'headers' => array(
                "Authorization" => $api_key,
                "Cache-Control" => "no-cache",
                "Content-Type" => "application/json",
            ),
            'body' => $data,
        ));

        return $response;
    }

    public function create() {
        $serialized = $this->json_serialize();
        $result = $this->send_request("job-request", "PUT", $serialized);

        if ($result !== null) {
            return json_decode($result['body'])->id;
        } else {
            return json_encode($result);
        }

        return '';
    }

    public function cancel() {
        $result = $this->send_request("company/jobs/cancel", "POST", json_encode(array(
            "id" => $this->id,
        )));
        error_log(json_encode($result));
    }

    public function finish() {
        $result = $this->send_request("job-request/finish", "POST", json_encode(array(
            "id" => $this->id,
        )));
        error_log(json_encode($result));
    }

    private function json_serialize() {
        return json_encode($this);
    }
}
