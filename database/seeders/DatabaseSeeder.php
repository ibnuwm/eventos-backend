<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PhaseSeeder::class);
        $tenantId = 'tenant-demo-uuid';
        $now = now();

        // ====================================================================
        // APPROVAL TOKENS
        // ====================================================================
        DB::table('approval_tokens')->insert([
            [
                'id' => 'at-1',
                'tenant_id' => $tenantId,
                'project_id' => 'proj-1',
                'client_name' => 'Anisa & Budi',
                'client_whatsapp' => '0812-8899-1234',
                'token' => 'demo-token-royal-wedding-abc123def456',
                'approved_documents' => json_encode(['3D_LAYOUT']),
                'expires_at' => now()->addDays(7),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'at-2',
                'tenant_id' => $tenantId,
                'project_id' => 'proj-1',
                'client_name' => 'Chikita Meidy & Reza',
                'client_whatsapp' => '0812-8899-4321',
                'token' => 'demo-token-chikita-reza-xyz789abc012',
                'approved_documents' => json_encode([]),
                'expires_at' => now()->addDays(7),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // ====================================================================
        // TENANT & USER
        // ====================================================================
        DB::table('tenants')->insert([
            'id' => $tenantId,
            'company_name' => 'EventOS Wedding Organizer',
            'domain_slug' => 'eventos-demo',
            'subscription_tier' => 'business',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('users')->insert([
            'tenant_id' => $tenantId,
            'name' => 'Anisa Rahma',
            'email' => 'anisa@eventos.id',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ====================================================================
        // LEADS
        // ====================================================================
        $leads = [
            [
                'id' => 'lead-1',
                'name' => 'Chikita Meidy & Reza',
                'whatsapp' => '0812-8899-1234',
                'email' => 'chikita@gmail.com',
                'event_date' => '2026-10-12',
                'pax_count' => 800,
                'budget_estimation' => 180000000,
                'status' => 'new',
                'notes' => 'Adat Minang modern, request pelaminan marun emas.',
            ],
            [
                'id' => 'lead-2',
                'name' => 'PT Maju Jaya Group (Gala Dinner)',
                'whatsapp' => '0811-2233-4455',
                'email' => 'hrd@majujaya.co.id',
                'event_date' => '2026-08-28',
                'pax_count' => 350,
                'budget_estimation' => 95000000,
                'status' => 'quotation_sent',
                'notes' => 'Ballroom Hotel Bintang 5, live band jazz, stage LED P3.',
            ],
            [
                'id' => 'lead-3',
                'name' => 'Raditya Dika & Anissa',
                'whatsapp' => '0813-9988-7766',
                'email' => 'raditya@event.id',
                'event_date' => '2026-11-20',
                'pax_count' => 500,
                'budget_estimation' => 150000000,
                'status' => 'negotiation',
                'notes' => 'Intimate concept outdoor garden, photobooth 360.',
            ],
        ];

        foreach ($leads as $lead) {
            DB::table('leads')->insert(array_merge($lead, [
                'tenant_id' => $tenantId,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ====================================================================
        // PROJECTS & TASKS
        // ====================================================================
        $project1Id = 'proj-1';
        $project2Id = 'proj-2';

        DB::table('projects')->insert([
            [
                'id' => $project1Id,
                'tenant_id' => $tenantId,
                'lead_id' => 'lead-1',
                'title' => 'Royal Wedding Anisa & Budi',
                'client_name' => 'Anisa Rahma & Budi Santoso',
                'event_date' => '2026-08-14',
                'venue_name' => 'Grand Hotel Ballroom Jakarta',
                'contract_value' => 180000000,
                'vendor_cost' => 110000000,
                'operational_cost' => 18000000,
                'payment_status' => 'dp_80',
                'days_remaining' => 38,
                'progress_percentage' => 75,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => $project2Id,
                'tenant_id' => $tenantId,
                'lead_id' => 'lead-3',
                'title' => 'Intimate Garden Wedding Clara & Dave',
                'client_name' => 'Clara Gopa & Dave Putra',
                'event_date' => '2026-11-10',
                'venue_name' => 'Pine Hill Bandung',
                'contract_value' => 135000000,
                'vendor_cost' => 85000000,
                'operational_cost' => 12000000,
                'payment_status' => 'dp_30',
                'days_remaining' => 126,
                'progress_percentage' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $tasks = [
            // Project 1 tasks
            ['id' => 't-1', 'project_id' => $project1Id, 'division' => 'Photography', 'title' => 'Pre-Wedding Concept & Execution di Bali', 'due_date' => '2026-05-10', 'is_completed' => true, 'assigned_vendor_name' => 'Lumiere Photography'],
            ['id' => 't-2', 'project_id' => $project1Id, 'division' => 'Decoration', 'title' => 'Final Approval Sketsa 3D Pelaminan 15m', 'due_date' => '2026-06-15', 'is_completed' => true, 'assigned_vendor_name' => 'Grand Rose Decor'],
            ['id' => 't-3', 'project_id' => $project1Id, 'division' => 'Catering', 'title' => 'Food Testing Bersama Keluarga VIP (10 Pax)', 'due_date' => '2026-06-25', 'is_completed' => true, 'assigned_vendor_name' => 'Gourmet Catering'],
            ['id' => 't-4', 'project_id' => $project1Id, 'division' => 'Sound & MC', 'title' => 'Technical Meeting & Penentuan Daftar Lagu Request', 'due_date' => '2026-07-20', 'is_completed' => false, 'assigned_vendor_name' => 'ProSound Entertainment'],
            ['id' => 't-5', 'project_id' => $project1Id, 'division' => 'Photography', 'title' => 'Briefing Tim D-Day & Cek Backup Memory Cards', 'due_date' => '2026-08-07', 'is_completed' => false, 'assigned_vendor_name' => 'Lumiere Photography'],
            // Project 2 tasks
            ['id' => 't-6', 'project_id' => $project2Id, 'division' => 'Venue', 'title' => 'Pembayaran Booking Fee Venue Pine Hill', 'due_date' => '2026-06-01', 'is_completed' => true, 'assigned_vendor_name' => null],
            ['id' => 't-7', 'project_id' => $project2Id, 'division' => 'Decoration', 'title' => 'Penyusunan Moodboard Rustic Botanical', 'due_date' => '2026-07-15', 'is_completed' => false, 'assigned_vendor_name' => null],
        ];

        foreach ($tasks as $task) {
            DB::table('project_tasks')->insert(array_merge($task, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ====================================================================
        // VENDORS
        // ====================================================================
        $vendors = [
            [
                'id' => 'v-1',
                'name' => 'Lumiere Photography Indonesia',
                'category' => 'Photography',
                'pic_name' => 'Mas Rio',
                'whatsapp' => '0812-1111-2222',
                'rating' => 4.9,
                'sla_punctuality' => 99.2,
                'starting_price' => 18000000,
                'area' => 'Jabodetabek & Bali',
                'npwp' => '99.999.999.9-999.999',
                'bank_account_info' => 'BCA 1234567890 a.n. Lumiere Photo',
            ],
            [
                'id' => 'v-2',
                'name' => 'Grand Rose Decoration',
                'category' => 'Decoration',
                'pic_name' => 'Mba Siska',
                'whatsapp' => '0812-3333-4444',
                'rating' => 4.8,
                'sla_punctuality' => 97.5,
                'starting_price' => 40000000,
                'area' => 'Jabodetabek & Bandung',
                'npwp' => '88.888.888.8-888.888',
                'bank_account_info' => 'Mandiri 9876543210 a.n. Grand Rose Decor',
            ],
            [
                'id' => 'v-3',
                'name' => 'ProSound Audio & LED Screen',
                'category' => 'Sound & Lighting',
                'pic_name' => 'Pak Anton',
                'whatsapp' => '0812-5555-6666',
                'rating' => 4.7,
                'sla_punctuality' => 98.0,
                'starting_price' => 12000000,
                'area' => 'Seluruh Jawa',
                'npwp' => '77.777.777.7-777.777',
                'bank_account_info' => 'BNI 5555555555 a.n. ProSound',
            ],
            [
                'id' => 'v-4',
                'name' => 'Chef Gourmet Catering Service',
                'category' => 'Catering',
                'pic_name' => 'Ibu Indah',
                'whatsapp' => '0812-7777-8888',
                'rating' => 4.9,
                'sla_punctuality' => 100.0,
                'starting_price' => 85000,
                'area' => 'Jakarta & Sekitarnya',
                'npwp' => '66.666.666.6-666.666',
                'bank_account_info' => 'BCA 4444444444 a.n. Chef Gourmet',
            ],
        ];

        foreach ($vendors as $vendor) {
            DB::table('vendors')->insert(array_merge($vendor, [
                'tenant_id' => $tenantId,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ====================================================================
        // QUOTATIONS & ITEMS
        // ====================================================================
        $quotationId = 'quote-1';

        DB::table('quotations')->insert([
            'id' => $quotationId,
            'tenant_id' => $tenantId,
            'lead_id' => 'lead-1',
            'title' => 'Paket Royal Emerald Wedding (800 Pax)',
            'subtotal' => 180000000,
            'tax' => 19800000,
            'grand_total' => 199800000,
            'status' => 'draft',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $quotationItems = [
            ['id' => 'q-1', 'category' => 'Venue & Catering', 'title' => 'Ballroom Bintang 5 & Buffet 800 Pax', 'vendor_name' => 'Grand Hotel & Chef Gourmet', 'price' => 95000000, 'is_optional' => false, 'is_selected' => true],
            ['id' => 'q-2', 'category' => 'Decoration', 'title' => 'Dekorasi Pelaminan Custom 15m & Lorong Masuk', 'vendor_name' => 'Grand Rose Decor', 'price' => 40000000, 'is_optional' => false, 'is_selected' => true],
            ['id' => 'q-3', 'category' => 'Documentation', 'title' => 'Paket Foto & Video Sinematik (3 Cam + Drone)', 'vendor_name' => 'Lumiere Photography', 'price' => 18000000, 'is_optional' => false, 'is_selected' => true],
            ['id' => 'q-4', 'category' => 'Entertainment', 'title' => 'MC Profesional & Acoustic Jazz Band', 'vendor_name' => 'ProSound Entertainment', 'price' => 12000000, 'is_optional' => false, 'is_selected' => true],
            ['id' => 'q-5', 'category' => 'Add-On', 'title' => 'Upgrade Multimedia LED Screen P3 3x4 Meter', 'vendor_name' => 'ProVisual Indonesia', 'price' => 15000000, 'is_optional' => true, 'is_selected' => false],
        ];

        foreach ($quotationItems as $item) {
            DB::table('quotation_items')->insert(array_merge($item, [
                'quotation_id' => $quotationId,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ====================================================================
        // RUNDOWN ITEMS
        // ====================================================================
        $rundownItems = [
            ['id' => 'r-1', 'project_id' => $project1Id, 'time_slot' => '05.00 - 07.30', 'duration_minutes' => 150, 'activity_title' => 'Persiapan Makeup & Hairdo Pengantin Wanita & Pria', 'division_pic' => 'Divisi MUA & Usher Lead', 'notes' => null, 'sequence_order' => 1],
            ['id' => 'r-2', 'project_id' => $project1Id, 'time_slot' => '07.30 - 08.30', 'duration_minutes' => 60, 'activity_title' => 'Sesi Foto Morning Preparation & First Look Pengantin', 'division_pic' => 'Lumiere Photography Team', 'notes' => null, 'sequence_order' => 2],
            ['id' => 'r-3', 'project_id' => $project1Id, 'time_slot' => '08.30 - 09.30', 'duration_minutes' => 60, 'activity_title' => 'Pengondisian Tamu VIP Akad Nikah & Cek Mikrofon Penghulu', 'division_pic' => 'ProSound & Usher Lead', 'notes' => null, 'sequence_order' => 3],
            ['id' => 'r-4', 'project_id' => $project1Id, 'time_slot' => '09.30 - 11.00', 'duration_minutes' => 90, 'activity_title' => 'Prosesi Akad Nikah, Ijab Kabul, & Sungkeman Keluarga', 'division_pic' => 'MC Penghulu & Video Cam', 'notes' => null, 'sequence_order' => 4],
            ['id' => 'r-5', 'project_id' => $project1Id, 'time_slot' => '11.30 - 14.30', 'duration_minutes' => 180, 'activity_title' => 'Grand Entrance Resepsi, First Dance, & Makan Siang Bersama', 'division_pic' => 'Acoustic Band & Catering', 'notes' => null, 'sequence_order' => 5],
        ];

        foreach ($rundownItems as $item) {
            DB::table('rundown_items')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ====================================================================
        // CHAT MESSAGES
        // ====================================================================
        $messages = [
            ['id' => 'm-1', 'project_id' => $project1Id, 'channel' => '#dekorasi-layout', 'sender_name' => 'Mba Rina', 'sender_role' => 'Lead WO', 'text' => 'Halo tim Grand Rose, untuk penempatan standing flower di lorong pintu utama mohon digeser 50cm ke kanan agar jalur kursi roda aman ya.'],
            ['id' => 'm-2', 'project_id' => $project1Id, 'channel' => '#dekorasi-layout', 'sender_name' => 'Mba Siska', 'sender_role' => 'Vendor Decor', 'text' => 'Siap Mba Rina! Sudah dicatat di sketsa revisi v3. Besok saat loading jam 04.00 pagi tim langsung sesuaikan.'],
            ['id' => 'm-3', 'project_id' => $project1Id, 'channel' => '#foto-video', 'sender_name' => 'Mas Rio', 'sender_role' => 'Vendor Photo', 'text' => 'Mohon konfirmasi saat Akad apakah lighting panggung bisa diredupkan sedikit jadi 4000K agar skin tone pengantin natural?'],
        ];

        foreach ($messages as $msg) {
            DB::table('chat_messages')->insert(array_merge($msg, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ====================================================================
        // INVENTORY ITEMS
        // ====================================================================
        $inventoryItems = [
            [
                'id' => 'inv-1',
                'tenant_id' => $tenantId,
                'name' => 'Kursi Tiffany Emas Premium',
                'category' => 'Furniture',
                'total_stock' => 500,
                'booked_for_date' => '2026-08-14',
                'allocated_qty' => 550,
                'conflicting_project' => 'Royal Wedding vs Gala Dinner PT Maju Jaya',
                'has_conflict' => true,
            ],
            [
                'id' => 'inv-2',
                'tenant_id' => $tenantId,
                'name' => 'Lampu Par LED 54W RGBW',
                'category' => 'Lighting',
                'total_stock' => 40,
                'booked_for_date' => '2026-08-14',
                'allocated_qty' => 32,
                'conflicting_project' => null,
                'has_conflict' => false,
            ],
            [
                'id' => 'inv-3',
                'tenant_id' => $tenantId,
                'name' => 'Standing Flower Acrylic 1.5m',
                'category' => 'Floral',
                'total_stock' => 24,
                'booked_for_date' => '2026-08-14',
                'allocated_qty' => 20,
                'conflicting_project' => null,
                'has_conflict' => false,
            ],
        ];

        foreach ($inventoryItems as $item) {
            DB::table('inventory_items')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ====================================================================
        // STAFF CREWS
        // ====================================================================
        $staffs = [
            [
                'id' => 'st-1',
                'tenant_id' => $tenantId,
                'name' => 'Dimas Anggara',
                'role' => 'Stage Manager',
                'assigned_event_title' => 'Royal Wedding Anisa & Budi',
                'check_in_time' => '04.15 WIB (On Time)',
                'location' => 'Grand Hotel Ballroom',
                'status' => 'checked_in',
            ],
            [
                'id' => 'st-2',
                'tenant_id' => $tenantId,
                'name' => 'Sinta Maharani',
                'role' => 'Usher Lead',
                'assigned_event_title' => 'Royal Wedding Anisa & Budi',
                'check_in_time' => '05.00 WIB (On Time)',
                'location' => 'Grand Hotel Lobby',
                'status' => 'checked_in',
            ],
            [
                'id' => 'st-3',
                'tenant_id' => $tenantId,
                'name' => 'Bagus Putra',
                'role' => 'Sound Technician',
                'assigned_event_title' => 'Royal Wedding Anisa & Budi',
                'check_in_time' => null,
                'location' => 'Grand Hotel Loading Dock',
                'status' => 'on_way',
            ],
        ];

        foreach ($staffs as $staff) {
            DB::table('staff_crews')->insert(array_merge($staff, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // ====================================================================
        // INVOICES
        // ====================================================================
        DB::table('invoices')->insert([
            [
                'id' => 'inv-101',
                'tenant_id' => $tenantId,
                'project_id' => $project1Id,
                'invoice_number' => 'INV/2026/ROYAL/001',
                'termin_type' => 'DP_30',
                'amount' => 54000000,
                'status' => 'paid',
                'payment_gateway_ref' => 'MIDTRANS-TRX-001',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 'inv-102',
                'tenant_id' => $tenantId,
                'project_id' => $project1Id,
                'invoice_number' => 'INV/2026/ROYAL/002',
                'termin_type' => 'DP_50',
                'amount' => 90000000,
                'status' => 'paid',
                'payment_gateway_ref' => 'MIDTRANS-TRX-002',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // ====================================================================
        // GUESTS (#3)
        // ====================================================================
        DB::table('guests')->insert([
            [
                'id' => 'g-1',
                'tenant_id' => $tenantId,
                'project_id' => 'proj-1',
                'name' => 'Ibu Sari Dewi',
                'whatsapp' => '0812-1111-2222',
                'category' => 'Keluarga',
                'guest_count' => 3,
                'rsvp_status' => 'confirmed',
                'menu_choice' => 'Nasi Goreng Seafood',
                'table_number' => 'Meja 5',
                'token' => 'demo-rsvp-ibu-sari',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'g-2',
                'tenant_id' => $tenantId,
                'project_id' => 'proj-1',
                'name' => 'Pak Budi Hartono',
                'whatsapp' => '0812-3333-4444',
                'category' => 'VIP',
                'guest_count' => 2,
                'rsvp_status' => 'pending',
                'menu_choice' => null,
                'table_number' => 'Meja 1',
                'token' => 'demo-rsvp-pak-budi',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 'g-3',
                'tenant_id' => $tenantId,
                'project_id' => 'proj-2',
                'name' => 'Teman Kantor Clara',
                'whatsapp' => '0812-5555-6666',
                'category' => 'Umum',
                'guest_count' => 1,
                'rsvp_status' => 'declined',
                'menu_choice' => 'Vegetarian',
                'table_number' => null,
                'token' => 'demo-rsvp-teman-clara',
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);

        // ====================================================================
        // EVENT TICKETS (#5)
        // ====================================================================
        $eventId = 'evt-1';
        DB::table('event_tickets')->insert([
            'id' => $eventId,
            'tenant_id' => $tenantId,
            'project_id' => 'proj-1',
            'event_title' => 'Royal Wedding Anisa & Budi - Resepsi',
            'event_date' => '2026-08-14',
            'venue' => 'Grand Hotel Ballroom Jakarta',
            'description' => 'Resepsi pernikahan mewah 800 pax dengan akustik band, live DJ, dan fine dining.',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('ticket_tiers')->insert([
            ['id' => 'tier-1', 'event_ticket_id' => $eventId, 'tier_name' => 'Early Bird', 'price' => 150000, 'quota' => 50, 'sold' => 45, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 'tier-2', 'event_ticket_id' => $eventId, 'tier_name' => 'Regular', 'price' => 250000, 'quota' => 100, 'sold' => 62, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 'tier-3', 'event_ticket_id' => $eventId, 'tier_name' => 'VIP (All-Inclusive)', 'price' => 500000, 'quota' => 30, 'sold' => 28, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('ticket_orders')->insert([
            'id' => 'to-1',
            'tier_id' => 'tier-1',
            'buyer_name' => 'Rina Amelia',
            'buyer_email' => 'rina@gmail.com',
            'buyer_whatsapp' => '0812-7777-8888',
            'quantity' => 2,
            'total' => 300000,
            'status' => 'paid',
            'qr_token' => 'demo-qr-rina-amelia-abc123',
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }
}
