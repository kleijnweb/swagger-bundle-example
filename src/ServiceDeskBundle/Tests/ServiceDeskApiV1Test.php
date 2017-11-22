<?php declare(strict_types=1);
/*
 * This file is part of the kleijnweb/swagger-bundle-example package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KleijnWeb\Examples\SwaggerBundle\ServiceDeskBundle\Tests;

use KleijnWeb\SwaggerBundle\Test\ApiResponseErrorException;
use KleijnWeb\SwaggerBundle\Test\ApiTestCase;
use KleijnWeb\SwaggerBundle\Test\ApiTestClient;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @author John Kleijn <john@kleijnweb.nl>
 */
class ServiceDeskApiV1Test extends WebTestCase
{
    use ApiTestCase;

    // @codingStandardsIgnoreStart
    const ADMIN_TOKEN = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJhZG1pbiIsImlzcyI6InRlc3RpbmdfaXNzdWVyIiwiYXVkIjpbImFkbWluIl19.V1gFDD_g2m_ZZwkfbsEAHkkcZbhihhyYRJ6RevqAii4';
    const USER_TOKEN  = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJ1c2VyIiwiaXNzIjoidGVzdGluZ19pc3N1ZXIiLCJhdWQiOlsidXNlciJdfQ.LeQPlbd1roNEu0IHU51bSU1PG0qoOXKi0O-yacPA8xI';

    // @codingStandardsIgnoreEnd

    protected function setUp()
    {
        $this->loadFixtures([]);
    }

    /**
     * @test
     */
    public function canGetTicketByIdAsAdmin()
    {
        $this->createAdminClient();

        $postResponse = $this->canCreateTicketAsAdmin();

        $response = $this->get('/service-desk/v1/ticket/' . $postResponse->id);

        $this->assertSame($postResponse->id, $response->id);
        $this->assertSame($postResponse->title, $response->title);
    }

    /**
     * @test
     */
    public function cannotCreateTicketAnonymously()
    {
        $this->createAnonClient();
        $this->expectException(ApiResponseErrorException::class);
        $this->expectExceptionMessage("Unauthorized");
        $this->post('/service-desk/v1/ticket', []);
    }

    /**
     * @test
     */
    public function canCreateTicketAsAdmin()
    {
        $this->createAdminClient();

        $data     = [
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
    public function canCreateTicketAsUser()
    {
        $this->createUserClient();

        $data     = [
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
        $this->createAdminClient();

        $postResponse = $this->canCreateTicketAsAdmin();
        $this->assertInternalType('string', $postResponse->ticketNumber);
        $this->assertRegExp("/T" . date('Y') . "\\.\\d{2}\\.0{4}$postResponse->id/", $postResponse->ticketNumber);
    }

    /**
     * @test
     */
    public function createdTicketWillHaveTimestamp()
    {
        $this->createAdminClient();

        $postResponse = $this->canCreateTicketAsAdmin();
        $this->assertInternalType('string', $postResponse->createdAt);
    }

    /**
     * @test
     */
    public function canUpdateTicketAsAdmin()
    {
        $this->createAdminClient();

        $postResponse        = $this->canCreateTicketAsAdmin();
        $postResponse->title = "Updated title";

        $response = $this->put('/service-desk/v1/ticket/' . $postResponse->id, (array)$postResponse);

        $this->assertSame($postResponse->title, $response->title);
    }

    /**
     * @test
     */
    public function canUpdateTicketAsUserWhenOwner()
    {
        $this->createUserClient();

        $postResponse        = $this->canCreateTicketAsUser();
        $postResponse->title = "Updated title";

        $response = $this->put('/service-desk/v1/ticket/' . $postResponse->id, (array)$postResponse);

        $this->assertSame($postResponse->title, $response->title);
    }

    /**
     * @test
     */
    public function cannotUpdateTicketAsUserWhenNotOwner()
    {
        $postResponse        = $this->canCreateTicketAsAdmin();
        $postResponse->title = "Updated title";

        // Switch to using user token
        $this->createUserClient();

        $this->expectException(ApiResponseErrorException::class);
        $this->expectExceptionCode(403);

        $response = $this->put('/service-desk/v1/ticket/' . $postResponse->id, (array)$postResponse);

        $this->assertSame($postResponse->title, $response->title);
    }

    /**
     * @test
     */
    public function updatedTicketWillHaveTimestamps()
    {
        $this->createAdminClient();

        $postResponse        = $this->canCreateTicketAsAdmin();
        $postResponse->title = "Updated title";

        sleep(1);
        $response = $this->put('/service-desk/v1/ticket/' . $postResponse->id, (array)$postResponse);

        $this->assertInternalType('string', $response->updatedAt);
        $this->assertNotSame($response->updatedAt, $response->createdAt);
    }

    /**
     * @test
     */
    public function canGetTicketByIdAnonymously()
    {
        $this->createAnonClient();
        $this->get('/service-desk/v1/ticket/77777777777777777777777');
    }

    /**
     * @test
     */
    public function canGetTicketByTicketNumberAsAdmin()
    {
        $this->createAdminClient();

        $postResponse = $this->canCreateTicketAsAdmin();

        $response = $this->get('/service-desk/v1/ticket/findByTicketNumber/' . $postResponse->ticketNumber);

        $this->assertSame($postResponse->id, $response->id);
        $this->assertSame($postResponse->ticketNumber, $response->ticketNumber);
    }

    /**
     * @test
     */
    public function canFindTicketByDescriptionAsAdmin()
    {
        $postResponse = $this->canCreateTicketAsAdmin();

        /** @var array $response */
        $response = $this->get('/service-desk/v1/ticket', ['description' => 'work']);

        $this->assertArrayHasKey(0, $response);
        $this->assertSame($postResponse->id, $response[0]->id);
        $this->assertSame($postResponse->description, $response[0]->description);
    }

    /**
     * @test
     */
    public function canFindTicketByStatusAsAdmin()
    {
        $this->createAdminClient();

        $postResponse = $this->canCreateTicketAsAdmin();

        $response = $this->get('/service-desk/v1/ticket', ['status' => 'open']);

        /** @var array $response */
        $this->assertArrayHasKey(0, $response);
        $this->assertSame($postResponse->id, $response[0]->id);
        $this->assertSame($postResponse->status, $response[0]->status);
    }

    private function createAdminClient()
    {
        $this->createClientWithToken(self::ADMIN_TOKEN);
    }

    private function createUserClient()
    {
        $this->createClientWithToken(self::USER_TOKEN);
    }

    private function createAnonClient()
    {
        $this->createClientWithToken('');
    }

    private function createClientWithToken(string $token)
    {
        $this->client = new ApiTestClient(
            static::createClient(
                ['environment' => $this->getEnv(), 'debug' => true],
                $token ? ['HTTP_AUTHORIZATION' => $token] : []
            )
        );
    }
}
