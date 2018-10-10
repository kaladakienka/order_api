<?php declare(strict_types = 1);

namespace App\Services;

use Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Interfaces\OrderRepositoryInterface;

class OrderService
{
    /** @var Client  */
    private $client;

    /** @var OrderRepositoryInterface  */
    private $orderRepo;

    public function __construct(Client $client, OrderRepositoryInterface $orderRepo)
    {
        $this->client = $client;
        $this->orderRepo = $orderRepo;
    }

    /**
     * @param array $origin
     * @param array $destination
     * @return mixed|null
     * @throws \Exception
     */
    public function createOrder(array $origin, array $destination) : array
    {
        Log::info('Creating order with origin ' . json_encode($origin) . ' and destination ' .
            json_encode($destination));
        $distance = $this->fetchDistance($origin, $destination);
        $order = [
            'distance' => $distance['value']
        ];
        $data = $this->orderRepo->createOrder($order);
        Log::info('Successfully created order - ' . json_encode($data));
        return $data;
    }

    /**
     * @param string $orderId
     * @param string $status
     * @return bool
     * @throws \Exception
     */
    public function editOrder(string $orderId, string $status) : bool
    {
        Log::info('Editing order with id ' . $orderId . ' with status ' . $status);
        $params = [
            'status' => $status
        ];
        $updated = $this->orderRepo->editOrder($orderId, $params);
        Log::info('Successfully edited order ' . $orderId);
        return $updated;
    }

    public function listOrders(int $page, int $limit) : array
    {
        $orders = $this->orderRepo->listOrders($page, $limit);
        Log::info('Successfully fetched orders for page ' . $page . ' with limit ' . $limit);
        return $orders;
    }

    /**
     * @param array $origin
     * @param array $destination
     * @return mixed|null
     * @throws \Exception
     */
    public function fetchDistance(array $origin, array $destination) : array
    {
        $requestParams = [
            'origins' => $origin[0] . ',' . $origin[1],
            'destinations' => $destination[0] . ',' . $destination[1]
        ];
        $url = env('GOOGLE_MAPS_DISTANCE_MATRIX');

        try {
            Log::info('Querying distance from Google API - ' . json_encode($requestParams));

            $requestParams['key'] = env('GOOGLE_API_KEY');
            $response = $this->client->request('GET', $url, [
                'query' => $requestParams
            ]);

            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents());

                if (!empty($data)) {
                    $distance = $data->rows[0]->elements[0]->distance;
                    return (array) $distance;
                }
            }
        } catch (GuzzleException $exception) {
            $errorMessage = "Error occurred while contacting Google API";
            Log::error($errorMessage . " - " . $url . " : " . $exception->getMessage());

            throw new \Exception($errorMessage, $exception->getCode());
        }

        return null;
    }
}