<?php

namespace Tests\AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\ApiControllerTestTrait;
use Tests\AppBundle\Controller\ControllerTestTrait;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group functional
 * @group api
 */
class LegislativesControllerTest extends WebTestCase
{
    use ControllerTestTrait;
    use ApiControllerTestTrait;

    public function testApiWonCandidates()
    {
        $this->client->request(Request::METHOD_GET, '/api/candidates');

        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());

        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);

        // Check the payload
        $this->assertSame(1, \count(\GuzzleHttp\json_decode($content, true)));
        $this->assertEachJsonItemContainsKey('id', $content);
        $this->assertEachJsonItemContainsKey('name', $content);
        $this->assertEachJsonItemContainsKey('district', $content);
        $this->assertEachJsonItemContainsKey('picture', $content);
        $this->assertEachJsonItemContainsKey('url', $content);
        $this->assertEachJsonItemContainsKey('geojson', $content);
    }

    public function setUp()
    {
        parent::setUp();

        $this->init();
    }

    public function tearDown()
    {
        $this->kill();

        parent::tearDown();
    }
}
