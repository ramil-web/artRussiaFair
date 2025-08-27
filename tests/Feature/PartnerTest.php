<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_a_partner()
    {
        $this->withExceptionHandling();
        $data = [
            'name' => 'Name',
            'url' => ' https://test/domen/1',
            'partner_category_id' => 1
        ];
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
            'Authorization' => ' Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbmV3YXBpYXJ0cnVzc2lhZmFpci9hcGkvdjEvYWRtaW4vYXV0aC9sb2dpbiIsImlhdCI6MTcwNjI3MTY5NSwiZXhwIjoxNzA4ODYzNjk1LCJuYmYiOjE3MDYyNzE2OTUsImp0aSI6IlZIczFQeUZleHV1ZFBkR2IiLCJzdWIiOiIxIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.Du53cAtAU6dDwbN5ZrhJdwv7r1OezrcgKX_w5g1F9y4'
        ];
        $res = $this->json('POST', '/api/v1/admin/partner/store', $data, $headers);
        $res->assertOk();
        $this->assertDatabaseCount('partners', 1);
    }
}
