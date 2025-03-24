<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Event;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Models\PurchasedTicket;
use App\Models\TicketOffer;
use App\Models\TicketOption;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Categories
        $categories = [
            ['name' => 'Khmer Traditional Festivals'],
            ['name' => 'Cambodian Cultural Performances'],
            ['name' => 'Religious Ceremonies'],
            ['name' => 'Modern Khmer Events'],
            ['name' => 'Cambodian Food Festivals'],
            ['name' => 'Historical Reenactments'],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }

        // 2. Create Payment Methods
        $paymentMethods = [
            ['name' => 'ACLEDA Bank', 'code' => 'ACLEDA', 'is_active' => true],
            ['name' => 'Wing', 'code' => 'WING', 'is_active' => true],
            ['name' => 'Pi Pay', 'code' => 'PIPAY', 'is_active' => true],
            ['name' => 'Canadia Bank', 'code' => 'CANADIA', 'is_active' => true],
            ['name' => 'PayPal', 'code' => 'PAYPAL', 'is_active' => true],
            ['name' => 'Cash on Delivery', 'code' => 'COD', 'is_active' => true],
        ];
        $paymentMethodIds = [];
        foreach ($paymentMethods as $method) {
            $paymentMethodIds[] = PaymentMethod::create($method)->id;
        }

        // 3. Create Users
        $admins = [];
        for ($i = 1; $i <= 2; $i++) {
            $admins[] = User::create([
                'name' => "Admin $i",
                'email' => "admin$i@example.com",
                'phone_number' => '855' . sprintf('%08d', rand(10000000, 99999999)),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        $vendors = [];
        for ($i = 1; $i <= 8; $i++) {
            $vendors[] = User::create([
                'name' => "Vendor $i",
                'email' => "vendor$i@example.com",
                'phone_number' => '855' . sprintf('%08d', rand(10000000, 99999999)),
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'email_verified_at' => now(),
            ]);
        }

        $buyers = [];
        for ($i = 1; $i <= 15; $i++) {
            $buyers[] = User::create([
                'name' => "Buyer $i",
                'email' => "buyer$i@example.com",
                'phone_number' => '855' . sprintf('%08d', rand(10000000, 99999999)),
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'email_verified_at' => now(),
            ]);
        }

        // 4. Create 30 Khmer Events (5-6 events per category)
        $khmerEvents = [
            1 => [ // Khmer Traditional Festivals
                ['name' => 'Khmer New Year Celebration', 'description' => 'Chaul Chnam Thmey with traditional games and dances', 'startDate' => now()->addDays(45), 'endDate' => now()->addDays(48)],
                ['name' => 'Pchum Ben Ancestors Festival', 'description' => '15-day ceremony to honor the dead', 'startDate' => now()->addMonths(3), 'endDate' => now()->addMonths(3)->addDays(15)],
                ['name' => 'Bon Om Touk Water Festival', 'description' => 'Boat racing and fireworks', 'startDate' => now()->subMonths(5), 'endDate' => now()->subMonths(5)->addDays(3)],
                ['name' => 'Kathen Ceremony', 'description' => 'Offering robes to monks', 'startDate' => now()->addMonths(4), 'endDate' => now()->addMonths(4)->addDays(1)],
                ['name' => 'Royal Ploughing Day', 'description' => 'Traditional agricultural festival', 'startDate' => now()->addMonths(2), 'endDate' => now()->addMonths(2)],
            ],
            2 => [ // Cambodian Cultural Performances
                ['name' => 'Apsara Dance Night', 'description' => 'Classical Khmer dance performance', 'startDate' => now(), 'endDate' => now()->addDays(3)],
                ['name' => 'Sbek Thom Shadow Play', 'description' => 'Traditional leather puppet show', 'startDate' => now()->addDays(20), 'endDate' => now()->addDays(21)],
                ['name' => 'Lakhaon Khol Drama', 'description' => 'Masked dance-drama performance', 'startDate' => now()->addDays(30), 'endDate' => now()->addDays(31)],
                ['name' => 'Khmer Music Concert', 'description' => 'Traditional music showcase', 'startDate' => now()->addDays(15), 'endDate' => now()->addDays(16)],
                ['name' => 'Robam Nesat Folk Dance', 'description' => 'Fishermen’s dance performance', 'startDate' => now()->addMonths(1), 'endDate' => now()->addMonths(1)],
            ],
            3 => [ // Religious Ceremonies
                ['name' => 'Visak Bochea Day', 'description' => "Buddha's birth, enlightenment, and death", 'startDate' => now()->addMonths(2), 'endDate' => now()->addMonths(2)],
                ['name' => 'Meak Bochea Day', 'description' => 'Commemoration of Buddha’s sermon', 'startDate' => now()->subMonths(1), 'endDate' => now()->subMonths(1)],
                ['name' => 'Chol Vassa Retreat', 'description' => 'Monks’ rainy season retreat', 'startDate' => now()->addMonths(5), 'endDate' => now()->addMonths(8)],
                ['name' => 'Bon Dak Ben Offering', 'description' => 'Offerings during Pchum Ben', 'startDate' => now()->addMonths(3)->addDays(5), 'endDate' => now()->addMonths(3)->addDays(6)],
                ['name' => 'Magha Puja Ceremony', 'description' => 'Buddhist community gathering', 'startDate' => now()->addDays(50), 'endDate' => now()->addDays(50)],
            ],
            4 => [ // Modern Khmer Events
                ['name' => 'Phnom Penh Film Festival', 'description' => 'Cambodian cinema showcase', 'startDate' => now()->addDays(60), 'endDate' => now()->addDays(65)],
                ['name' => 'Khmer Tech Expo', 'description' => 'Technology and innovation fair', 'startDate' => now()->addDays(40), 'endDate' => now()->addDays(42)],
                ['name' => 'Cambodia Fashion Week', 'description' => 'Modern Khmer fashion showcase', 'startDate' => now()->addMonths(2), 'endDate' => now()->addMonths(2)->addDays(3)],
                ['name' => 'Phnom Penh Marathon', 'description' => 'City-wide running event', 'startDate' => now()->addDays(25), 'endDate' => now()->addDays(25)],
                ['name' => 'Khmer Startup Summit', 'description' => 'Entrepreneurship and startup event', 'startDate' => now()->addMonths(1), 'endDate' => now()->addMonths(1)->addDays(2)],
            ],
            5 => [ // Cambodian Food Festivals
                ['name' => 'Khmer Street Food Fest', 'description' => 'Taste of Cambodian street cuisine', 'startDate' => now()->addDays(10), 'endDate' => now()->addDays(12)],
                ['name' => 'Amok Cooking Festival', 'description' => 'Celebration of Khmer fish curry', 'startDate' => now()->addDays(35), 'endDate' => now()->addDays(36)],
                ['name' => 'Prahok Festival', 'description' => 'Fermented fish paste culinary event', 'startDate' => now()->addMonths(1), 'endDate' => now()->addMonths(1)->addDays(1)],
                ['name' => 'Num Banh Chok Day', 'description' => 'Khmer noodle celebration', 'startDate' => now()->subDays(10), 'endDate' => now()->subDays(9)],
                ['name' => 'Kuy Teav Soup Fest', 'description' => 'Traditional noodle soup event', 'startDate' => now()->addDays(70), 'endDate' => now()->addDays(71)],
            ],
            6 => [ // Historical Reenactments
                ['name' => 'Angkor Empire Days', 'description' => 'Reenactment of Khmer Empire glory', 'startDate' => now()->addDays(25), 'endDate' => now()->addDays(27)],
                ['name' => 'Jayavarman VII Victory', 'description' => 'Battle reenactment of the great king', 'startDate' => now()->addMonths(2), 'endDate' => now()->addMonths(2)->addDays(1)],
                ['name' => 'Chenla Kingdom Rise', 'description' => 'Early Khmer history reenactment', 'startDate' => now()->addDays(15), 'endDate' => now()->addDays(16)],
                ['name' => 'Funan Era Showcase', 'description' => 'Ancient Cambodian kingdom display', 'startDate' => now()->subMonths(2), 'endDate' => now()->subMonths(2)->addDays(1)],
                ['name' => 'Suryavarman II Legacy', 'description' => 'Angkor Wat builder reenactment', 'startDate' => now()->addDays(90), 'endDate' => now()->addDays(91)],
            ],
        ];

        $cities = ['Phnom Penh', 'Siem Reap', 'Battambang', 'Sihanoukville', 'Kampot'];
        foreach ($khmerEvents as $categoryId => $events) {
            foreach ($events as $index => $eventData) {
                $vendorIndex = $index % count($vendors);
                $event = Event::create([
                    'user_id' => $vendors[$vendorIndex]->id,
                    'category_id' => $categoryId,
                    'name' => $eventData['name'],
                    'description' => $eventData['description'],
                    'image' => "images/events/{$categoryId}_{$index}.jpg",
                    'startDate' => $eventData['startDate'],
                    'endDate' => $eventData['endDate'],
                    'status' => 'upcoming', // Let updateStatus handle the actual status
                ]);
                $event->updateStatus(); // Ensure status aligns with dates

                Address::create([
                    'event_id' => $event->id,
                    'street' => "Street " . rand(1, 100),
                    'city' => $cities[array_rand($cities)],
                    'country' => 'Cambodia',
                    'venue_name' => "Venue {$event->name}",
                    'extra_info' => 'Near local market or pagoda',
                ]);

                $ticketTypes = ['VIP Pass', 'Regular Entry', 'Early Bird Special', 'Group Bundle (5 pax)', 'Student Discount', 'Family Package (4 pax)'];
                for ($t = 0; $t < 6; $t++) {
                    $ticket = TicketOption::create([
                        'event_id' => $event->id,
                        'type' => $ticketTypes[$t],
                        'description' => "Access to {$ticketTypes[$t]} features for {$event->name}",
                        'refund_policy' => rand(0, 1) ? 'Refundable 7 days prior' : 'Non-refundable',
                        'image' => "images/tickets/{$event->id}_{$t}.jpg",
                        'price' => rand(5, 150) * 1000, // More realistic pricing in KHR or USD
                        'quantity' => rand(50, 500),
                        'startDate' => $event->startDate->subDays(rand(30, 60)),
                        'endDate' => $event->endDate,
                        'is_active' => true,
                    ]);

                    $offers = ['Early Bird Discount', 'Buy 2 Get 1 Free', 'VIP Upgrade'];
                    for ($o = 0; $o < 3; $o++) {
                        TicketOffer::create([
                            'ticket_id' => $ticket->id,
                            'name' => $offers[$o],
                            'details' => json_encode([
                                'discount' => rand(5, 30) . '%',
                                'valid_until' => $event->startDate->toDateString(),
                            ]),
                            'quantity' => rand(10, 100) * 1.00, // Decimal for consistency
                        ]);
                    }
                }
            }
        }

        // 5. Create Purchased Tickets, Carts, Orders, and Transactions
        $allEvents = Event::with('ticketOptions')->get();
        foreach ($buyers as $buyer) {
            $pastEvents = $allEvents->where('status', 'passed')->shuffle()->take(2);
            $upcomingEvents = $allEvents->whereIn('status', ['upcoming', 'active'])->shuffle()->take(3);

            foreach ($pastEvents as $event) {
                if ($event->ticketOptions->isEmpty()) continue;
                $ticket = $event->ticketOptions->random();
                PurchasedTicket::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $buyer->id,
                    'qr_code' => uniqid('QR_PAST_'),
                    'status' => rand(0, 1) ? 'used' : 'invalid',
                ]);
            }

            foreach ($upcomingEvents as $event) {
                if ($event->ticketOptions->isEmpty()) continue;
                $ticket = $event->ticketOptions->random();
                PurchasedTicket::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $buyer->id,
                    'qr_code' => uniqid('QR_UPCOMING_'),
                    'status' => 'valid',
                ]);
            }

            // Carts (Initially all set to pending)
            $ticketOptions = TicketOption::inRandomOrder()->take(rand(5, 8))->get();
            $carts = [];

            foreach ($ticketOptions as $ticketOption) {
                $available = $ticketOption->quantity - Cart::where('ticket_id', $ticketOption->id)->sum('quantity');
                $cartQuantity = min(rand(1, 5) * 1.00, $available); // Ensure we don't exceed stock

                if ($cartQuantity > 0) {
                    $carts[] = Cart::create([
                        'user_id'   => $buyer->id,
                        'ticket_id' => $ticketOption->id,
                        'quantity'  => $cartQuantity,
                        'event_id'  => $ticketOption->event_id,
                        'status'    => Cart::STATUS_PENDING, // All carts start as pending
                    ]);
                }
            }

            // Orders (Using order_cart pivot table)
            $statuses = ['pending', 'completed', 'cancel_request', 'cancelled'];
            $cartCollection = collect($carts);

            if ($cartCollection->count() >= 3) {
                $selectedCarts = $cartCollection->shuffle()->take(rand(3, 5)); // Ensure at least 3 carts per order

                $totalAmount = $selectedCarts->sum(function ($cart) {
                    return $cart->ticketOption->price * $cart->quantity;
                });

                $order = Order::create([
                    'user_id' => $buyer->id,
                    'total'   => $totalAmount,
                    'status'  => $statuses[array_rand($statuses)],
                ]);

                // Attach carts to order using order_cart pivot table
                $order->carts()->attach($selectedCarts->pluck('id'));

                // Update status of carts used in the order to "completed"
                foreach ($selectedCarts as $cart) {
                    $cart->update(['status' => Cart::STATUS_COMPLETED]);
                }

                // Determine payment status
                $paymentStatus = match ($order->status) {
                    'completed'      => 'completed',
                    'pending'        => 'pending',
                    default          => 'failed',
                };

                // Create a payment transaction
                PaymentTransaction::create([
                    'order_id'  => $order->id,
                    'user_id'   => $buyer->id,
                    'method_id' => $paymentMethodIds[array_rand($paymentMethodIds)],
                    'status'    => $paymentStatus,
                    'amount'    => $order->total,
                    'reference' => 'TXN_' . strtoupper(uniqid()),
                    'date'      => now()->subDays(rand(1, 30)), // More randomized dates
                    'currency'  => 'USD',
                ]);
            }
        }

        // 6. Add banned user
        $buyers[0]->ban();
    }
}