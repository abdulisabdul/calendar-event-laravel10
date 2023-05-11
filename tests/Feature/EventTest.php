<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $baseUrl;
    public function setup() : void
    {
        parent::setup();
        $this->baseUrl = 'events';
    }

    public function test_can_show_event_page()
    {
        $this->get('events')->assertOk();
    }

    public function test_can_show_create_event_page()
    {
        $this->get('events/create')->assertOk()
        ->assertSee('start_date')
        ->assertSee('title')
        ->assertSee('category')
        ->assertSee('end_date');
    }

    public function test_sussess_store_event()
    {
        $this->postJson($this->baseUrl, $this->data())->assertOk();
    }

    public function test_success_update_event()
    {
        $this->postJson($this->baseUrl, $this->data())->assertOk();

        $event = Event::orderByDesc('id')->first();
        $this->putJson($this->baseUrl. '/'. $event->id, $this->data())
        ->assertStatus(200);
    }

    private function data()
    {
        return [
            'title' => $this->faker->sentence(3),
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'category' => $this->faker->randomElement(['success', 'danger', 'warning', 'info']),
        ];
    }

    public function test_success_delete_event()
    {
        $this->postJson($this->baseUrl, $this->data())->assertOk();
        
        $event = Event::orderByDesc('id')->first();
        $this->putJson($this->baseUrl. '/'. $event->id, array_merge($this->data(),['delete' => 'on']))
        ->assertStatus(200);
    }

    public function test_validation_store_event()
    {
        $this->postJson($this->baseUrl)->assertStatus(422)
        ->assertSeeInOrder([
            'start_date', 'end_date', 'title', 'category'
        ]);
    }
}
