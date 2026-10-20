<?php

namespace Tests\Feature;

use App\Enums\ServiceInquiryStatus;
use App\Mail\NewServiceInquiryAlert;
use App\Models\Service;
use App\Models\ServiceInquiry;
use App\Models\User;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class ServicesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function requestDetails(array $overrides = []): array
    {
        return [
            'name' => 'Nimal Perera',
            'email' => 'nimal@example.com',
            'phone' => '077 123 4567',
            'company' => 'Perera Hardware',
            'budget' => 'Rs. 50,000 - 150,000',
            'timeline' => 'Within 1 month',
            'message' => 'We need a website for our hardware shop in Kandy.',
            ...$overrides,
        ];
    }

    public function test_services_page_lists_all_service_groups(): void
    {
        $this->seed(ServiceSeeder::class);

        $this->get(route('services.index'))
            ->assertOk()
            ->assertSee('Website Development')
            ->assertSee('ERP Software')
            ->assertSee('Business Management Systems')
            ->assertSee('Software Installation &amp; Maintenance', false)
            ->assertSee('E-commerce Websites')
            ->assertSee('Rs. 45,000.00');

        $this->assertSame(29, Service::count());
    }

    public function test_service_page_shows_details_and_quote_form(): void
    {
        $service = Service::factory()->create(['name' => 'CRM Setup', 'features' => ['Lead pipeline', 'Follow-up reminders']]);

        $this->get(route('services.show', $service))
            ->assertOk()
            ->assertSee('CRM Setup')
            ->assertSee('Follow-up reminders')
            ->assertSee('name="service_id" value="'.$service->id.'"', false);
    }

    public function test_hidden_services_are_not_shown(): void
    {
        $service = Service::factory()->create(['is_active' => false]);

        $this->get(route('services.show', $service))->assertNotFound();
    }

    public function test_guest_can_request_a_quote(): void
    {
        $service = Service::factory()->create();

        $this->post(route('services.request.store'), $this->requestDetails(['service_id' => $service->id]))
            ->assertRedirect(route('services.thanks'));

        $inquiry = ServiceInquiry::sole();
        $this->assertSame($service->id, $inquiry->service_id);
        $this->assertSame(ServiceInquiryStatus::New, $inquiry->status);
        $this->assertStringStartsWith('SRV', $inquiry->reference);
        $this->assertNull($inquiry->user_id);

        $this->withSession(['service_reference' => $inquiry->reference])
            ->get(route('services.thanks'))
            ->assertOk()
            ->assertSee($inquiry->reference);
    }

    public function test_store_is_alerted_about_new_quote_requests(): void
    {
        Mail::fake();
        config(['shop.order_alert_email' => 'orders@odin.test']);
        $service = Service::factory()->create(['name' => 'CRM Setup']);

        $this->post(route('services.request.store'), $this->requestDetails(['service_id' => $service->id]));

        $inquiry = ServiceInquiry::sole();
        Mail::assertSent(NewServiceInquiryAlert::class, fn (NewServiceInquiryAlert $mail): bool => $mail->hasTo('orders@odin.test'));

        Mail::sent(NewServiceInquiryAlert::class)->sole()
            ->assertSeeInHtml($inquiry->reference)
            ->assertSeeInHtml('CRM Setup')
            ->assertSeeInHtml('Nimal Perera')
            ->assertSeeInHtml('nimal@example.com')
            ->assertSeeInHtml('We need a website for our hardware shop in Kandy.')
            ->assertSeeInHtml(route('admin.service-inquiries.show', $inquiry), false);
    }

    public function test_quote_request_is_saved_even_when_the_alert_email_fails(): void
    {
        Exceptions::fake();
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP server unavailable'));

        $this->post(route('services.request.store'), $this->requestDetails())
            ->assertRedirect(route('services.thanks'));

        $this->assertSame(1, ServiceInquiry::count());
        Exceptions::assertReported(RuntimeException::class);
    }

    public function test_logged_in_customer_sees_their_requests(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('services.request.store'), $this->requestDetails());

        $this->actingAs($user)->get(route('account.services'))
            ->assertOk()
            ->assertSee('General enquiry')
            ->assertSee(ServiceInquiryStatus::New->description());
    }

    public function test_request_form_is_validated_and_spam_is_blocked(): void
    {
        $this->post(route('services.request.store'), $this->requestDetails(['phone' => 'abc', 'message' => 'hi']))
            ->assertSessionHasErrors(['phone', 'message']);

        $this->post(route('services.request.store'), $this->requestDetails(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertSame(0, ServiceInquiry::count());
    }

    public function test_admin_can_manage_service_requests(): void
    {
        $admin = User::factory()->admin()->create();
        $this->post(route('services.request.store'), $this->requestDetails());
        $inquiry = ServiceInquiry::sole();

        $this->actingAs($admin)->get(route('admin.service-inquiries.index'))->assertOk()->assertSee($inquiry->reference);
        $this->actingAs($admin)->get(route('admin.service-inquiries.show', $inquiry))->assertOk()->assertSee('Perera Hardware');
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('new service request');

        $this->actingAs($admin)
            ->put(route('admin.service-inquiries.update', $inquiry), ['status' => 'quoted', 'admin_notes' => 'Sent quote Rs. 75,000'])
            ->assertSessionHas('success');

        $this->assertSame(ServiceInquiryStatus::Quoted, $inquiry->fresh()->status);
        $this->assertSame('Sent quote Rs. 75,000', $inquiry->fresh()->admin_notes);
    }

    public function test_admin_can_create_and_edit_services(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = Service::factory()->create();

        $this->actingAs($admin)->get(route('admin.services.index'))->assertOk()->assertSee($existing->name);

        $this->actingAs($admin)->post(route('admin.services.store'), [
            'service_category_id' => $existing->service_category_id,
            'name' => 'Mobile App Development',
            'icon' => 'bi-phone',
            'features' => "Android & iOS apps\n- Admin dashboard\n\n",
            'starting_price' => 250000,
            'price_note' => 'one-time',
            'delivery_time' => '6 - 10 weeks',
            'is_active' => '1',
        ])->assertRedirect(route('admin.services.index'));

        $service = Service::where('name', 'Mobile App Development')->sole();
        $this->assertSame('mobile-app-development', $service->slug);
        $this->assertSame(['Android & iOS apps', 'Admin dashboard'], $service->features);

        $this->actingAs($admin)->get(route('admin.services.edit', $service))->assertOk();
    }

    public function test_service_groups_with_services_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $service = Service::factory()->create();

        $this->actingAs($admin)->delete(route('admin.service-categories.destroy', $service->category))->assertSessionHas('error');

        $this->assertModelExists($service->category);
    }

    public function test_customers_cannot_manage_services(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.services.index'))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('admin.service-inquiries.index'))->assertForbidden();
    }
}
