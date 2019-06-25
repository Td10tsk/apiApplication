<?php


namespace Tests\apiTests;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class PageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRoot()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testApiDocument()
    {
        $response = $this->get('/api/v1/document/09e3db2a-fe85-46fa-9d76-eccd10092ccb');

        $response->assertStatus(404);
    }

    public function testApiPatchDocument()
    {
        $response = $this->patch('/api/v1/document/09e3db2a-fe85-46fa-9d76-eccd10092ccb');

        $response->assertStatus(401);
    }
}