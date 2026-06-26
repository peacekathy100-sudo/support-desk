<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Models\SysUser;
use App\Models\TicketCategory;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SysUser::factory()->count(3)->create();
        TicketCategory::factory()->count(2)->create();
        Client::factory()->count(2)->create();
    }

    public function test_ticket_number_is_auto_generated_on_create(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith('TKT-', $ticket->ticket_number);
        $this->assertMatchesRegularExpression('/^TKT-\d{8}-\d{5}$/', $ticket->ticket_number);
    }

    public function test_ticket_numbers_are_sequentially_incremented(): void
    {
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();

        $num1 = (int) substr($ticket1->ticket_number, -5);
        $num2 = (int) substr($ticket2->ticket_number, -5);

        $this->assertEquals(1, $num2 - $num1);
    }

    public function test_priority_color_returns_correct_color(): void
    {
        $ticket = Ticket::factory()->create(['priority' => 'urgent']);

        $this->assertEquals('#dc3545', $ticket->priorityColor);
    }

    public function test_priority_color_returns_default_for_unknown_priority(): void
    {
        $ticket = Ticket::factory()->make(['priority' => 'unknown']);

        $this->assertEquals('#6c757d', $ticket->priorityColor);
    }

    public function test_status_color_returns_correct_color(): void
    {
        $ticket = Ticket::factory()->create(['status' => 'resolved']);

        $this->assertEquals('#1cc88a', $ticket->statusColor);
    }

    public function test_status_color_returns_default_for_unknown_status(): void
    {
        $ticket = Ticket::factory()->make(['status' => 'unknown']);

        $this->assertEquals('#6c757d', $ticket->statusColor);
    }

    public function test_is_overdue_returns_true_for_past_due_open_ticket(): void
    {
        $ticket = Ticket::factory()->create([
            'status' => 'open',
            'due_at' => now()->subDay(),
        ]);

        $this->assertTrue($ticket->isOverdue);
    }

    public function test_is_overdue_returns_false_for_resolved_ticket(): void
    {
        $ticket = Ticket::factory()->create([
            'status' => 'resolved',
            'due_at' => now()->subDay(),
        ]);

        $this->assertFalse($ticket->isOverdue);
    }

    public function test_is_overdue_returns_false_for_closed_ticket(): void
    {
        $ticket = Ticket::factory()->create([
            'status' => 'closed',
            'due_at' => now()->subDay(),
        ]);

        $this->assertFalse($ticket->isOverdue);
    }

    public function test_is_overdue_returns_false_when_no_due_date(): void
    {
        $ticket = Ticket::factory()->create([
            'status' => 'open',
            'due_at' => null,
        ]);

        $this->assertFalse($ticket->isOverdue);
    }

    public function test_is_open_returns_true_for_open_status(): void
    {
        $ticket = Ticket::factory()->create(['status' => 'open']);

        $this->assertTrue($ticket->isOpen);
    }

    public function test_is_open_returns_true_for_in_progress_status(): void
    {
        $ticket = Ticket::factory()->create(['status' => 'in_progress']);

        $this->assertTrue($ticket->isOpen);
    }

    public function test_is_open_returns_true_for_on_hold_status(): void
    {
        $ticket = Ticket::factory()->create(['status' => 'on_hold']);

        $this->assertTrue($ticket->isOpen);
    }

    public function test_is_open_returns_false_for_resolved_status(): void
    {
        $ticket = Ticket::factory()->create(['status' => 'resolved']);

        $this->assertFalse($ticket->isOpen);
    }

    public function test_is_open_returns_false_for_closed_status(): void
    {
        $ticket = Ticket::factory()->create(['status' => 'closed']);

        $this->assertFalse($ticket->isOpen);
    }

    public function test_scope_open_returns_only_open_tickets(): void
    {
        Ticket::factory()->create(['status' => 'open']);
        Ticket::factory()->create(['status' => 'in_progress']);
        Ticket::factory()->create(['status' => 'resolved']);
        Ticket::factory()->create(['status' => 'closed']);

        $openTickets = Ticket::open()->get();

        $this->assertCount(2, $openTickets);
    }

    public function test_scope_overdue_returns_only_overdue_tickets(): void
    {
        Ticket::factory()->create([
            'status' => 'open',
            'due_at' => now()->subDay(),
        ]);
        Ticket::factory()->create([
            'status' => 'open',
            'due_at' => now()->addDay(),
        ]);
        Ticket::factory()->create([
            'status' => 'resolved',
            'due_at' => now()->subDay(),
        ]);

        $overdueTickets = Ticket::overdue()->get();

        $this->assertCount(1, $overdueTickets);
    }

    public function test_scope_for_user_returns_tickets_created_by_user(): void
    {
        $user = SysUser::first();
        Ticket::factory()->create(['created_by' => $user->user_id]);
        Ticket::factory()->create();

        $userTickets = Ticket::forUser($user->user_id)->get();

        $this->assertGreaterThanOrEqual(1, $userTickets->count());
    }

    public function test_scope_for_client_returns_tickets_for_client(): void
    {
        $client = Client::first();
        Ticket::factory()->create(['client_id' => $client->client_id]);
        Ticket::factory()->create();

        $clientTickets = Ticket::forClient($client->client_id)->get();

        $this->assertCount(1, $clientTickets);
    }

    public function test_scope_assigned_to_returns_tickets_assigned_to_user(): void
    {
        $user = SysUser::first();
        Ticket::factory()->create(['assigned_to' => $user->user_id]);
        Ticket::factory()->create();

        $assignedTickets = Ticket::assignedTo($user->user_id)->get();

        $this->assertCount(1, $assignedTickets);
    }

    public function test_scope_by_priority_returns_tickets_with_matching_priority(): void
    {
        Ticket::factory()->create(['priority' => 'high']);
        Ticket::factory()->create(['priority' => 'low']);
        Ticket::factory()->create(['priority' => 'high']);

        $highPriorityTickets = Ticket::byPriority('high')->get();

        $this->assertCount(2, $highPriorityTickets);
    }

    public function test_ticket_belongs_to_creator(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(SysUser::class, $ticket->creator);
    }

    public function test_ticket_belongs_to_assignee(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(SysUser::class, $ticket->assignee);
    }

    public function test_ticket_belongs_to_category(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(TicketCategory::class, $ticket->category);
    }

    public function test_ticket_belongs_to_client(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(Client::class, $ticket->client);
    }

    public function test_ticket_has_many_comments(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $ticket->comments);
    }

    public function test_ticket_has_many_attachments(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $ticket->attachments);
    }

    public function test_ticket_has_many_history(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $ticket->history);
    }

    public function test_chargeable_is_cast_to_boolean(): void
    {
        $ticket = Ticket::factory()->create(['chargeable' => 1]);

        $this->assertIsBool($ticket->chargeable);
        $this->assertTrue($ticket->chargeable);
    }

    public function test_dates_are_cast_to_carbon(): void
    {
        $ticket = Ticket::factory()->create([
            'resolved_at' => '2024-01-15 10:00:00',
            'closed_at' => '2024-01-20 15:30:00',
            'due_at' => '2024-02-01 00:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $ticket->resolved_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $ticket->closed_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $ticket->due_at);
    }
}
