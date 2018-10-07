<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use Log;
use Validator;
use Illuminate\Http\Request;

use App\Services\OrderService;

class OrderController extends BaseController
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'origin' => 'required',
                'destination' => 'required'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                Log::error('Validation failed ' . json_encode($errors));

                $errorString = "";
                foreach ($errors->all() as $error) {
                    $errorString .=  $error . " ";
                }

                return $this->sendResponse([ 'error' => $errorString ], 400);
            }

            $requestData = $request->all();
            $response = $this->orderService
                ->createOrder($requestData['origin'], $requestData['destination']);

            return $this->sendResponse($response, 200);
        } catch (\Exception $ex) {
            return $this->sendResponse([ 'error' => $ex->getMessage() ], 500);
        }
    }

    public function editOrder(Request $request, $orderId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                Log::error('Validation failed ' . json_encode($errors));

                $errorString = "";
                foreach ($errors->all() as $error) {
                    $errorString .=  $error . " ";
                }

                return $this->sendResponse([ 'error' => $errorString ], 400);
            }

            $requestData = $request->all();
            $response = $this->orderService->editOrder($orderId, $requestData);

            if ($response) {
                return $this->sendResponse([ 'status' => 'SUCCESS' ], 200);
            } else {
                return $this->sendResponse([ 'error' => 'Failed to update order' ], 500);
            }
        } catch (\Exception $ex) {
            if ($ex->getCode() != 0) {
                return $this->sendResponse([ 'error' => $ex->getMessage() ], $ex->getCode());
            }
            return $this->sendResponse([ 'error' => $ex->getMessage() ], 500);
        }
    }

    public function listOrders(Request $request)
    {
        try {
            $page = (int) $request->query('page', 1);
            $limit = (int) $request->query('limit', 10);
            $orders = $this->orderService->listOrders($page, $limit);
            return $this->sendResponse($orders, 200);
        } catch (\Exception $ex) {
            return $this->sendResponse([ 'error' => $ex->getMessage() ], 500);
        }
    }
}
