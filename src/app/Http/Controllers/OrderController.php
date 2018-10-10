<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use Log;
use Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

                return $this->sendResponse([ 'error' => $errorString ], Response::HTTP_BAD_REQUEST);
            }

            $requestData = $request->all();
            $response = $this->orderService
                ->createOrder($requestData['origin'], $requestData['destination']);

            return $this->sendResponse($response, Response::HTTP_OK);
        } catch (\Exception $ex) {
            Log::error('Error occurred while creating order - ', $ex->getMessage());
            return $this->sendResponse([ 'error' => $ex->getMessage() ], Response::HTTP_INTERNAL_SERVER_ERROR);
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

                return $this->sendResponse([ 'error' => $errorString ], Response::HTTP_BAD_REQUEST);
            }

            $requestData = $request->all();
            $response = $this->orderService->editOrder($orderId, $requestData['status']);

            if ($response) {
                return $this->sendResponse([ 'status' => 'SUCCESS' ], Response::HTTP_OK);
            } else {
                return $this->sendResponse([ 'error' => 'Failed to update order' ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Throwable $ex) {
            Log::error('Error occurred while editing order - ' . $ex->getMessage());
            if ($ex->getCode() != 0) {
                return $this->sendResponse([ 'error' => $ex->getMessage() ], $ex->getCode());
            }
            return $this->sendResponse([ 'error' => $ex->getMessage() ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
            Log::error('Error occurred while listing orders - ' . $ex->getMessage());
            return $this->sendResponse([ 'error' => $ex->getMessage() ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
