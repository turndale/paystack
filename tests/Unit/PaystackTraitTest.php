<?php

namespace StephenAsare\Paystack\Tests\Unit;

use StephenAsare\Paystack\Models\PaystackCustomer;
use StephenAsare\Paystack\Models\PaystackSubscription;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use StephenAsare\Paystack\Traits\HasPaystack;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Fake User Model for testing
 */
class TestUser extends Model
{
    use HasPaystack;
    protected $guarded = [];
    protected $table = 'test_users';
}

/**
 * Fake Team Model for testing
 */
class TestTeam extends Model
{
    use HasPaystack;
    protected $guarded = [];
    protected $table = 'test_teams';
}

class PaystackTraitTest extends PaystackTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $schema = $this->app['db']->connection()->getSchemaBuilder();

        // Use a try-catch or 'hasTable' check to be extra safe for test-only tables
        if (!$schema->hasTable('test_users')) {
            $schema->create('test_users', function ($table) {
                $table->id();
                $table->string('email');
                $table->timestamps();
            });
        }

        if (!$schema->hasTable('test_teams')) {
            $schema->create('test_teams', function ($table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }
    }

    #[Test]
    public function it_can_record_a_subscription()
    {
        $user = TestUser::create(['email' => 'steve@example.com']);

        $user->recordSubscription('main', 'PLN_monthly', 'SUB_123');

        $this->assertCount(1, $user->subscriptions);
        $this->assertEquals('PLN_monthly', $user->subscriptions->first()->paystack_plan);
        $this->assertEquals(TestUser::class, $user->subscriptions->first()->billable_type);
    }

    #[Test]
    public function it_knows_if_a_user_is_subscribed()
    {
        $user = TestUser::create(['email' => 'steve@example.com']);

        // Assert initially false
        $this->assertFalse($user->subscribed('main'));

        // Record active subscription
        $user->recordSubscription('main', 'PLN_monthly', 'SUB_123');

        $this->assertTrue($user->subscribed('main'));
    }

    #[Test]
    public function it_is_not_subscribed_if_ends_at_is_in_the_past()
    {
        $user = TestUser::create(['email' => 'steve@example.com']);

        $user->subscriptions()->create([
            'name' => 'main',
            'paystack_plan' => 'PLN_monthly',
            'paystack_id' => 'SUB_123',
            'paystack_status' => 'active',
            'ends_at' => now()->subDay(), // Expired yesterday
        ]);

        $this->assertFalse($user->subscribed('main'));
    }

    #[Test]
    public function user_is_still_subscribed_during_grace_period()
    {
        $user = TestUser::create(['email' => 'steve@example.com']);

        $user->subscriptions()->create([
            'name' => 'main',
            'paystack_plan' => 'PLN_monthly',
            'paystack_id' => 'SUB_123',
            'paystack_status' => 'cancelled', // Status changed but date remains
            'ends_at' => now()->addDays(7),   // Still has 7 days left
        ]);

        $this->assertTrue($user->subscribed('main'));
    }

    #[Test]
    public function it_can_belong_to_different_model_types_polymorphically()
    {
        // Create actual records in their respective tables
        $user = TestUser::create(['email' => 'user@test.com']);
        $team = TestTeam::create(['name' => 'Acme Corp']);

        // Link them to Paystack Customers
        $userCustomer = PaystackCustomer::create([
            'billable_id' => $user->id,
            'billable_type' => TestUser::class,
            'paystack_id' => 'CUS_USER_123',
            'email' => $user->email
        ]);

        $teamCustomer = PaystackCustomer::create([
            'billable_id' => $team->id,
            'billable_type' => TestTeam::class,
            'paystack_id' => 'CUS_TEAM_456',
            'email' => 'billing@acme.com'
        ]);

        // Assertions
        $this->assertInstanceOf(TestUser::class, $userCustomer->billable);
        $this->assertEquals($user->email, $userCustomer->billable->email);

        $this->assertInstanceOf(TestTeam::class, $teamCustomer->billable);
        $this->assertEquals('Acme Corp', $teamCustomer->billable->name);
    }

    #[Test]
    public function it_can_find_the_billable_model_by_paystack_id()
    {
        $user = TestUser::create(['email' => 'steve@example.com']);

        PaystackCustomer::create([
            'billable_id' => $user->id,
            'billable_type' => TestUser::class,
            'paystack_id' => 'CUS_999',
            'email' => $user->email
        ]);

        $found = TestUser::findByPaystackId('CUS_999');

        $this->assertInstanceOf(TestUser::class, $found);
        $this->assertEquals($user->id, $found->id);
    }

    #[Test]
    public function it_can_check_if_subscription_is_on_trial()
    {
        $user = TestUser::create(['email' => 'steve@example.com']);
        
        $user->subscriptions()->create([
            'name' => 'default',
            'paystack_id' => 'sub_123',
            'paystack_plan' => 'plan_123',
            'paystack_status' => 'active',
            'trial_ends_at' => now()->addDays(5),
        ]);

        $this->assertTrue($user->onTrial('default'));
        $this->assertFalse($user->onTrial('other'));
        
        // Test with plan
        $this->assertTrue($user->onTrial('default', 'plan_123'));
        $this->assertFalse($user->onTrial('default', 'plan_456'));
    }

    #[Test]
    public function it_can_set_trial_via_billable_model()
    {
        $user = TestUser::create(['email' => 'steve@example.com']);
        
        $user->subscriptions()->create([
            'name' => 'default',
            'paystack_id' => 'sub_123',
            'paystack_plan' => 'plan_123',
            'paystack_status' => 'active',
        ]);

        $this->assertFalse($user->onTrial('default'));

        $user->setTrial('default', 7);

        $this->assertTrue($user->onTrial('default'));
        $this->assertEquals(
            now()->addDays(7)->format('Y-m-d'), 
            $user->subscription('default')->trial_ends_at->format('Y-m-d')
        );
    }
}