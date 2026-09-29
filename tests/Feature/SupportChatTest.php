<?php

namespace Tests\Feature;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupportChatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Guest user visiting init gets unauthenticated response with login prompt and FAQs.
     */
    public function test_guest_user_support_init_requires_login(): void
    {
        $response = $this->getJson(route('support-chat.init'));

        $response->assertStatus(200);
        $response->assertJson([
            'authenticated' => false,
        ]);
        $response->assertJsonStructure([
            'authenticated',
            'faqs',
            'login_url',
            'register_url',
            'message',
        ]);
    }

    /**
     * Test Guest user cannot send a message.
     */
    public function test_guest_user_cannot_send_support_message(): void
    {
        $user = User::factory()->create();
        $conversation = SupportConversation::create([
            'user_id' => $user->id,
            'subject' => 'Help Request',
            'status' => 'open',
        ]);

        $response = $this->postJson(route('support-chat.send', $conversation->id), [
            'message' => 'Hello from guest',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test Authenticated user visiting init initializes conversation and welcome message.
     */
    public function test_authenticated_user_initializes_conversation(): void
    {
        $user = User::factory()->create([
            'name' => 'Jean Tremblay',
            'email' => 'jean@example.com',
        ]);

        $response = $this->actingAs($user)->getJson(route('support-chat.init'));

        $response->assertStatus(200);
        $response->assertJson([
            'authenticated' => true,
            'user' => [
                'name' => 'Jean Tremblay',
                'email' => 'jean@example.com',
            ],
        ]);

        $this->assertDatabaseHas('support_conversations', [
            'user_id' => $user->id,
            'status' => 'open',
        ]);
    }

    /**
     * Test Authenticated user can send a support message.
     */
    public function test_authenticated_user_can_send_support_message(): void
    {
        $user = User::factory()->create();
        $conversation = SupportConversation::create([
            'user_id' => $user->id,
            'subject' => 'Verification Help',
            'status' => 'open',
        ]);

        $response = $this->actingAs($user)->postJson(route('support-chat.send', $conversation->id), [
            'message' => 'How long does Quebec ID verification take?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => [
                'message' => 'How long does Quebec ID verification take?',
                'sender_type' => 'user',
            ],
        ]);

        $this->assertDatabaseHas('support_messages', [
            'support_conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => 'user',
            'message' => 'How long does Quebec ID verification take?',
        ]);
    }

    /**
     * Test Admin can view support inbox, view tickets, and send a reply.
     */
    public function test_admin_can_view_and_reply_to_support_ticket(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create();
        $conversation = SupportConversation::create([
            'user_id' => $user->id,
            'subject' => 'Payment Issue',
            'status' => 'open',
        ]);

        SupportMessage::create([
            'support_conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => 'user',
            'message' => 'I was charged twice for Featured boost.',
        ]);

        // Admin views index
        $indexResponse = $this->actingAs($admin)->get(route('admin.support.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($user->name);
        $indexResponse->assertSee('I was charged twice for Featured boost.');

        // Admin sends reply
        $replyResponse = $this->actingAs($admin)->postJson(route('admin.support.reply', $conversation->id), [
            'message' => 'We have refunded the duplicate charge. Please check your Stripe statement.',
        ]);

        $replyResponse->assertStatus(200);
        $replyResponse->assertJson([
            'status' => 'success',
            'message' => [
                'message' => 'We have refunded the duplicate charge. Please check your Stripe statement.',
                'sender_type' => 'admin',
            ],
        ]);

        $this->assertDatabaseHas('support_messages', [
            'support_conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'sender_type' => 'admin',
        ]);
    }

    /**
     * Test Admin can update ticket status and priority.
     */
    public function test_admin_can_update_ticket_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create();
        $conversation = SupportConversation::create([
            'user_id' => $user->id,
            'subject' => 'Listing Question',
            'status' => 'open',
            'priority' => 'normal',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.support.updateStatus', $conversation->id), [
            'status' => 'resolved',
            'priority' => 'urgent',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'new_status' => 'resolved',
            'new_priority' => 'urgent',
        ]);

        $this->assertDatabaseHas('support_conversations', [
            'id' => $conversation->id,
            'status' => 'resolved',
            'priority' => 'urgent',
        ]);
    }

    /**
     * Test User can send documents (PDF, DOCX) and image attachments.
     */
    public function test_user_can_send_document_and_image_attachments(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $conversation = SupportConversation::create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        $imageFile = UploadedFile::fake()->image('screenshot.png', 400, 300);
        $pdfFile = UploadedFile::fake()->create('contract.pdf', 150, 'application/pdf');

        $response = $this->actingAs($user)->postJson(route('support-chat.send', $conversation->id), [
            'message' => 'Here are the requested documents',
            'attachments' => [$imageFile, $pdfFile],
        ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
        ]);

        $message = SupportMessage::where('support_conversation_id', $conversation->id)->first();
        $this->assertNotNull($message);
        $this->assertCount(2, $message->attachment_files);

        $this->assertTrue($message->attachment_files[0]['is_image']);
        $this->assertTrue($message->attachment_files[1]['is_pdf']);
    }

    /**
     * Test Admin can poll inbox and receive real-time conversations list and stats.
     */
    public function test_admin_can_poll_inbox_and_receive_live_conversations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Alice Martin']);

        $conversation = SupportConversation::create([
            'user_id' => $user->id,
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        SupportMessage::create([
            'support_conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => 'user',
            'message' => 'Need help with boosted ad',
            'is_read' => false,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.support.pollInbox'));

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'stats' => ['unread_messages', 'unread_conversations', 'total'],
            'conversations',
            'new_messages',
        ]);

        $this->assertEquals(1, $response->json('stats.unread_conversations'));
        $this->assertStringContainsString('Alice Martin', $response->json('conversations.0.user_name'));
        $this->assertStringContainsString('Need help with boosted ad', $response->json('conversations.0.latest_message'));
    }
}
