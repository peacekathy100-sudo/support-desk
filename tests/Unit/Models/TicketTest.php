<?php

namespace Tests\Unit\Models;

use App\Models\Ticket;
use App\Models\SysUser;
use App\Models\Client;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->category = TicketCategory::factory()->create();
        $this->client = Client::factory()->create();
        $this->user = SysUser::factory()->create();
    }

    public function test_generate_ticket_number_returns_correct_format(): void
    {
        $ticketNumber = Ticket::generateTicketNumber();
        
        $this->assertMatchesRegularExpression('/^TKT-\d{8}-\d{5}$/', $ticketNumber);
    }

    public function test_generate_ticket_number_increments_sequence(): void
    {
        $ticket1 = Ticket::factory()->create([
            'ticket_number' => 'TKT-' . now()->format('Ymd') . '-00001',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
            'status' => 'open',
        ]);

        $ticket2 = Ticket::factory()->create([
            'ticket_number' => 'TKT-' . now()->format('Ymd') . '-00002',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
            'status' => 'open',
        ]);

        $this->assertEquals('TKT-' . now()->format('Ymd') . '-00003', Ticket::generateTicketNumber());
    }

    public function test_get_priority_color_attribute_returns_correct_color(): void
    {
        $ticket = Ticket::factory()->create([
            'priority' => 'urgent',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'status' => 'open',
        ]);

        $this->assertEquals('#dc3545', $ticket->priority_color);
    }

    public function test_get_status_color_attribute_returns_correct_color(): void
    {
        $ticket = Ticket::factory()->create([
            'status' => 'resolved',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
        ]);

        $this->assertEquals('#1cc88a', $ticket->status_color);
    }

    public function test_get_is_overdue_attribute_returns_true_when_overdue(): void
    {
        $ticket = Ticket::factory()->create([
            'due_at' => now()->subDay(),
            'status' => 'open',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
        ]);

        $this->assertTrue($ticket->is_overdue);
    }

    public function test_get_is_overdue_attribute_returns_false_when_resolved(): void
    {
        $ticket = Ticket::factory()->create([
            'due_at' => now()->subDay(),
            'status' => 'resolved',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
        ]);

        $this->assertFalse($ticket->is_overdue);
    }

    public function test_get_is_open_attribute_returns_true_for_open_statuses(): void
    {
        foreach (['open', 'in_progress', 'on_hold'] as $status) {
            $ticket = Ticket::factory()->create([
                'status' => $status,
                'category_id' => $this->category->category_id,
                'client_id' => $this->client->client_id,
                'created_by' => $this->user->user_id,
                'priority' => 'normal',
            ]);

            $this->assertTrue($ticket->is_open, "Status {$status} should be open");
        }
    }

    public function test_get_is_open_attribute_returns_false_for_closed_statuses(): void
    {
        foreach (['resolved', 'closed'] as $status) {
            $ticket = Ticket::factory()->create([
                'status' => $status,
                'category_id' => $this->category->category_id,
                'client_id' => $this->client->client_id,
                'created_by' => $this->user->user_id,
                'priority' => 'normal',
            ]);

            $this->assertFalse($ticket->is_open, "Status {$status} should not be open");
        }
    }

    public function test_scope_open_returns_only_open_tickets(): void
    {
        Ticket::factory()->create(['status' => 'open', 'category_id' => $this->category->category_id, 'client_id' => $this->client->client_id, 'created_by' => $this->user->user_id, 'priority' => 'normal']);
        Ticket::factory()->create(['status' => 'in_progress', 'category_id' => $this->category->category_id, 'client_id' => $this->client->client_id, 'created_by' => $this->user->user_id, 'priority' => 'normal']);
        Ticket::factory()->create(['status' => 'closed', 'category_id' => $this->category->category_id, 'client_id' => $this->client->client_id, 'created_by' => $this->user->user_id, 'priority' => 'normal']);

        $this->assertEquals(2, Ticket::open()->count());
    }

    public function test_scope_overdue_returns_only_overdue_tickets(): void
    {
        Ticket::factory()->create([
            'due_at' => now()->subDay(),
            'status' => 'open',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
        ]);
        Ticket::factory()->create([
            'due_at' => now()->addDay(),
            'status' => 'open',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
        ]);

        $this->assertEquals(1, Ticket::overdue()->count());
    }

    public function test_scope_for_user_returns_tickets_created_by_or_assigned_to_user(): void
    {
        $otherUser = SysUser::factory()->create();

        Ticket::factory()->create([
            'created_by' => $this->user->user_id,
            'status' => 'open',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'priority' => 'normal',
        ]);

        $ticket = Ticket::factory()->create([
            'created_by' => $otherUser->user_id,
            'status' => 'open',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'priority' => 'normal',
        ]);
        $ticket->assignees()->attach($this->user->user_id, [
            'assigned_by' => $this->user->user_id,
            'assigned_at' => now(),
        ]);

        $this->assertEquals(2, Ticket::forUser($this->user->user_id)->count());
    }

    public function test_scope_for_client_returns_tickets_for_specific_client(): void
    {
        $otherClient = Client::factory()->create();

        Ticket::factory()->create([
            'client_id' => $this->client->client_id,
            'status' => 'open',
            'category_id' => $this->category->category_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
        ]);
        Ticket::factory()->create([
            'client_id' => $otherClient->client_id,
            'status' => 'open',
            'category_id' => $this->category->category_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
        ]);

        $this->assertEquals(1, Ticket::forClient($this->client->client_id)->count());
    }

    public function test_scope_assigned_to_returns_tickets_assigned_to_user(): void
    {
        $otherUser = SysUser::factory()->create();

        Ticket::factory()->create([
            'assigned_to' => $this->user->user_id,
            'status' => 'open',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
        ]);
        Ticket::factory()->create([
            'assigned_to' => $otherUser->user_id,
            'status' => 'open',
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
        ]);

        $this->assertEquals(1, Ticket::assignedTo($this->user->user_id)->count());
    }

    public function test_scope_by_priority_returns_tickets_with_specific_priority(): void
    {
        Ticket::factory()->create(['priority' => 'low', 'status' => 'open', 'category_id' => $this->category->category_id, 'client_id' => $this->client->client_id, 'created_by' => $this->user->user_id]);
        Ticket::factory()->create(['priority' => 'urgent', 'status' => 'open', 'category_id' => $this->category->category_id, 'client_id' => $this->client->client_id, 'created_by' => $this->user->user_id]);

        $this->assertEquals(1, Ticket::byPriority('urgent')->count());
    }

    public function test_ticket_belongs_to_creator(): void
    {
        $ticket = Ticket::factory()->create([
            'created_by' => $this->user->user_id,
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'priority' => 'normal',
            'status' => 'open',
        ]);

        $this->assertInstanceOf(SysUser::class, $ticket->creator);
        $this->assertEquals($this->user->user_id, $ticket->creator->user_id);
    }

    public function test_ticket_belongs_to_category(): void
    {
        $ticket = Ticket::factory()->create([
            'category_id' => $this->category->category_id,
            'client_id' => $this->client->client_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
            'status' => 'open',
        ]);

        $this->assertInstanceOf(TicketCategory::class, $ticket->category);
        $this->assertEquals($this->category->category_id, $ticket->category->category_id);
    }

    public function test_ticket_belongs_to_client(): void
    {
        $ticket = Ticket::factory()->create([
            'client_id' => $this->client->client_id,
            'category_id' => $this->category->category_id,
            'created_by' => $this->user->user_id,
            'priority' => 'normal',
            'status' => 'open',
        ]);

        $this->assertInstanceOf(Client::class, $ticket->client);
        $this->assertEquals($this->client->client_id, $ticket->client->client_id);
    }
}
