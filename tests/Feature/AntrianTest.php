<?php

namespace Tests\Feature;

use App\Models\Antrian;
use App\Models\Pasien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AntrianTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all queues
     */
    public function test_can_get_all_antrians(): void
    {
        $response = $this->get('/api/antrian');
        
        $response->assertStatus(200);
    }

    /**
     * Test creating a new queue
     */
    public function test_can_create_antrian(): void
    {
        $pasien = Pasien::factory()->create();
        
        $data = [
            'pasien_id' => $pasien->id,
            'tanggal' => now()->toDateString(),
            'jam' => now()->toTimeString(),
            'status' => 'menunggu',
            'keterangan' => 'Test queue',
        ];

        $response = $this->post('/api/antrian', $data);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('antrians', $data);
    }
}
