<?php
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Tests;

use JsonSchema\RefResolver;
use KleijnWeb\SwaggerBundle\Test\ApiTestCase;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Collection;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class ServiceDeskApiV2Test extends WebTestCase
{
    // @codingStandardsIgnoreStart
    const PSK_TOKEN = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6ImRlZmF1bHQifQ.eyJpc3MiOiJ0ZXN0aW5nX2lzc3VlciIsInBybiI6ImFwaSJ9.o4tBedoktxALvXKRR3_M3Hq2XUMAwHiUTr2sK85yehQ';
    // @codingStandardsIgnoreEnd

    use ApiTestCase;

    protected $defaultServerVars = ['CONTENT_TYPE' => 'application/vnd.api+json'];

    /**
     * Init response validation, point to your spec
     */
    public static function setUpBeforeClass()
    {
        static::initSchemaManager(__DIR__ . '/../../../web/swagger/service-desk/v2.yml');
    }

    protected function setUp()
    {
        // Ugh...
        RefResolver::$maxDepth = 20;
        $server = ['HTTP_AUTHORIZATION' => 'Bearer ' . self::PSK_TOKEN];
        $this->client = static::createClient([], $server);
        static::bootKernel();
        $this->loadFixtures([]);
    }

    /**
     * @test
     *
     * @param string $title
     *
     * @return object
     */
    public function canCreateTicket($title = 'Help')
    {
        $data = [
            'data' => [
                'type'       => 'ticket',
                'attributes' => [
                    'title'       => $title,
                    'description' => 'It doesn\'t work',
                    'type'        => 'bug-report'
                ]
            ]
        ];
        $response = $this->post('/service-desk/v2/ticket', $data);

        $this->assertInternalType('integer', $response->data->attributes->id);

        return $response;
    }

    /**
     * @test
     */
    public function createdTicketWillHaveTicketNumber()
    {
        $postResponse = $this->canCreateTicket();
        $attributes = $postResponse->data->attributes;
        $this->assertInternalType('string', $attributes->ticketNumber);
        $this->assertRegExp("/T" . date('Y') . "\\.\\d{2}\\.0{4}$attributes->id/", $attributes->ticketNumber);
    }

    /**
     * @test
     */
    public function createdTicketWillHaveTimestamp()
    {
        $postResponse = $this->canCreateTicket();
        $attributes = $postResponse->data->attributes;
        $this->assertInternalType('string', $attributes->createdAt);
    }

    /**
     * @test
     */
    public function canReplaceTicket()
    {
        $title = "Updated title";
        $postResponse = $this->canCreateTicket();
        $attributes = $postResponse->data->attributes;
        $attributes->title = $title;
        $data = [
            'data' => [
                'id'         => (string)$attributes->id,
                'type'       => 'ticket',
                'attributes' => $postResponse->data->attributes
            ]
        ];

        $response = $this->put('/service-desk/v2/ticket/' . $attributes->id, $data);

        $this->assertSame($title, $response->data->attributes->title);
    }

    /**
     * @test
     */
    public function canUpdateTicket()
    {
        $postResponse = $this->canCreateTicket();
        $attributes = $postResponse->data->attributes;
        $data = [
            'data' => [
                'id'         => (string)$attributes->id,
                'type'       => 'ticket',
                'attributes' => [
                    'title' => "Updated title"
                ]
            ]
        ];

        $response = $this->patch('/service-desk/v2/ticket/' . $attributes->id, $data);

        $this->assertSame("Updated title", $response->data->attributes->title);
    }

    /**
     * @test
     */
    public function updatedTicketWillHaveTimestamps()
    {
        $postResponse = $this->canCreateTicket();
        $postAttributes = $postResponse->data->attributes;
        $postResponse->title = "Updated title";

        sleep(1);
        $data = [
            'data' => [
                'type'       => 'ticket',
                'attributes' => $postAttributes
            ]
        ];
        $response = $this->put('/service-desk/v2/ticket/' . $postAttributes->id, $data);
        $attributes = $response->data->attributes;

        $this->assertInternalType('string', $attributes->updatedAt);
        $this->assertNotSame($attributes->updatedAt, $attributes->createdAt);
    }

    /**
     * @test
     */
    public function canGetTicketById()
    {
        $postResponse = $this->canCreateTicket();
        $postAttributes = $postResponse->data->attributes;

        $response = $this->get('/service-desk/v2/ticket/' . $postAttributes->id);
        $attributes = $response->data->attributes;

        $this->assertSame($postAttributes->id, $attributes->id);
        $this->assertSame($postAttributes->title, $attributes->title);
    }

    /**
     * @test
     */
    public function canGetTicketByTicketNumber()
    {
        $postResponse = $this->canCreateTicket();
        $postAttributes = $postResponse->data->attributes;

        $response = $this->get('/service-desk/v2/ticket/findByTicketNumber/' . $postAttributes->ticketNumber);
        $attributes = $response->data->attributes;

        $this->assertSame($postAttributes->id, $attributes->id);
        $this->assertSame($postAttributes->ticketNumber, $attributes->ticketNumber);
    }

    /**
     * @test
     */
    public function canFindTicketByDescription()
    {
        $postResponses = [
            $this->canCreateTicket('Test ticket 1'),
            $this->canCreateTicket('Test ticket 2')
        ];

        /** @var \stdClass $response */
        $response = $this->get('/service-desk/v2/ticket', ['description' => 'work']);

        $this->assertObjectHasAttribute('data', $response);
        $data = $response->data;
        $this->assertArrayHasKey(0, $data);
        foreach ($data as $index => $record) {
            $postResponse = $postResponses[$index];
            $postAttributes = $postResponse->data->attributes;
            $this->assertEquals($postAttributes->id, $record->id);
            $this->assertObjectHasAttribute('attributes', $record);
            $attributes = $record->attributes;
            $this->assertSame($postAttributes->description, $attributes->description);
        }
        $this->assertObjectHasAttribute('meta', $response);
        $this->assertObjectHasAttribute('total', $response->meta);
        $this->assertSame(2, $response->meta->total);
    }

    /**
     * @test
     */
    public function canFindTicketByStatus()
    {
        $postResponses = [
            $this->canCreateTicket('Test ticket 1'),
            $this->canCreateTicket('Test ticket 2')
        ];

        /** @var \stdClass $response */
        $response = $this->get('/service-desk/v2/ticket', ['status' => 'open']);

        $this->assertObjectHasAttribute('data', $response);
        $data = $response->data;
        $this->assertArrayHasKey(0, $data);
        foreach ($data as $index => $record) {
            $postResponse = $postResponses[$index];
            $postAttributes = $postResponse->data->attributes;
            $this->assertEquals($postAttributes->id, $record->id);
            $this->assertObjectHasAttribute('attributes', $record);
            $attributes = $record->attributes;
            $this->assertSame($postAttributes->description, $attributes->description);
        }

        $this->assertObjectHasAttribute('meta', $response);
        $this->assertObjectHasAttribute('total', $response->meta);
        $this->assertSame(2, $response->meta->total);
    }
}
