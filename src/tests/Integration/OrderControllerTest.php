<?php declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

final class OrderControllerTest extends \TestCase
{
    public function testShouldCreateOrderFromValidData()
    {
        $parameters = [
            "origin" => ["6.433738", "3.432619"],
            "destination" => ["6.451487", "3.428404"],
        ];
        $this->json('POST', '/api/v1/order', $parameters)
            ->seeJson([
                'distance' => 5560,
                'status' => 'unassigned',
            ])
            ->assertResponseStatus(Response::HTTP_OK);
    }

    /**
     * @depends testCreateRecipeFromValidData
     * @param $orderId
     */
    public function testShouldEditOrderWithValidData($orderId)
    {
        $parameters = [
            "status" => "taken"
        ];
        $this->json('PUT', '/api/v1/order' . $orderId, $parameters)
            ->seeJson([
                'status' => 'SUCCESS',
            ])
            ->assertResponseStatus(Response::HTTP_OK);
    }

    public function testShouldNotCreateOrderFromInvalidData()
    {
        $parameters = [
            "origin" => ["6.433738", "3.432619"],
        ];
        $this->json('POST', '/api/v1/order', $parameters)
            ->seeJson([
                'error' => 'The destination field is required. ',
            ])
            ->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @depends testCreateRecipeFromValidData
     * @param $orderId
     */
    public function testShouldNotEditOrderWithInvalidData($orderId)
    {
        $parameters = [
            "statuss" => "taken"
        ];
        $this->json('POST', '/api/v1/order' . $orderId, $parameters)
            ->seeJson([
                'error' => 'The status field is required. ',
            ])
            ->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @depends testShouldNotEditOrderWithInvalidData
     * @param $orderId
     */
    public function testShouldNotEditOrderThatHasAlreadyBeenTaken($orderId)
    {
        $parameters = [
            "status" => "taken"
        ];
        $this->json('POST', '/api/v1/order' . $orderId, $parameters)
            ->seeJson([
                'error' => 'order_already_been_taken',
            ])
            ->assertResponseStatus(Response::HTTP_CONFLICT);
    }
}