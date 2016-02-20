<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Tests;

use KleijnWeb\SwaggerBundle\Test\ApiTestCase;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class ServiceDeskApiV1Test extends WebTestCase
{
    // @codingStandardsIgnoreStart
    const PSK_TOKEN = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImRlZmF1bHQifQ.eyJpc3MiOiJ0ZXN0aW5nX2lzc3VlciIsInBybiI6ImFwaSJ9.o4tBedoktxALvXKRR3_M3Hq2XUMAwHiUTr2sK85yehQ';
    // @codingStandardsIgnoreEnd

    use ApiTestCase;

    /**
     * Init response validation, point to your spec
     */
    public static function setUpBeforeClass()
    {
        static::initSchemaManager(__DIR__ . '/../../../web/swagger/service-desk/v1.yml');
    }

    protected function setUp()
    {
        $server = ['HTTP_AUTHORIZATION' => 'Bearer ' . self::PSK_TOKEN];
        $this->client = static::createClient([], $server);
        static::bootKernel();
        $this->loadFixtures([]);
    }

    /**
     * @test
     */
    public function canCreateTicket()
    {
        $data = [
            'title'       => 'Help',
            'description' => 'It doesn\'t work',
            'type'        => 'bug-report'
        ];
        $response = $this->post('/service-desk/v1/ticket', $data);

        $this->assertInternalType('integer', $response->id);

        return $response;
    }

    /**
     * @test
     */
    public function createdTicketWillHaveTicketNumber()
    {
        $postResponse = $this->canCreateTicket();
        $this->assertInternalType('string', $postResponse->ticketNumber);
        $this->assertRegExp("/T" . date('Y') . "\\.\\d{2}\\.0{4}$postResponse->id/", $postResponse->ticketNumber);
    }

    /**
     * @test
     */
    public function createdTicketWillHaveTimestamp()
    {
        $postResponse = $this->canCreateTicket();
        $this->assertInternalType('string', $postResponse->createdAt);
    }

    /**
     * @test
     */
    public function canUpdateTicket()
    {
        $postResponse = $this->canCreateTicket();
        $postResponse->title = "Updated title";

        $response = $this->put('/service-desk/v1/ticket/' . $postResponse->id, (array)$postResponse);

        $this->assertSame($postResponse->title, $response->title);
    }

    /**
     * @test
     */
    public function updatedTicketWillHaveTimestamps()
    {
        $postResponse = $this->canCreateTicket();
        $postResponse->title = "Updated title";

        sleep(1);
        $response = $this->put('/service-desk/v1/ticket/' . $postResponse->id, (array)$postResponse);

        $this->assertInternalType('string', $response->updatedAt);
        $this->assertNotSame($response->updatedAt, $response->createdAt);
    }

    /**
     * @test
     */
    public function canGetTicketById()
    {
        $postResponse = $this->canCreateTicket();

        $response = $this->get('/service-desk/v1/ticket/' . $postResponse->id);

        $this->assertSame($postResponse->id, $response->id);
        $this->assertSame($postResponse->title, $response->title);
    }

    /**
     * @test
     */
    public function canGetTicketByTicketNumber()
    {
        $postResponse = $this->canCreateTicket();

        $response = $this->get('/service-desk/v1/ticket/findByTicketNumber/' . $postResponse->ticketNumber);

        $this->assertSame($postResponse->id, $response->id);
        $this->assertSame($postResponse->ticketNumber, $response->ticketNumber);
    }

    /**
     * @test
     */
    public function canFindTicketByDescription()
    {
        $postResponse = $this->canCreateTicket();

        /** @var array $response */
        $response = $this->get('/service-desk/v1/ticket', ['description'=> 'work']);

        $this->assertArrayHasKey(0, $response);
        $this->assertSame($postResponse->id, $response[0]->id);
        $this->assertSame($postResponse->description, $response[0]->description);
    }

    /**
     * @test
     */
    public function canFindTicketByStatus()
    {
        $postResponse = $this->canCreateTicket();

        $response = $this->get('/service-desk/v1/ticket', ['status'=> 'open']);

        /** @var array $response */
        $this->assertArrayHasKey(0, $response);
        $this->assertSame($postResponse->id, $response[0]->id);
        $this->assertSame($postResponse->status, $response[0]->status);
    }
}
