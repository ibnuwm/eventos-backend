<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhaseSeeder extends Seeder {
    public function run(): void {
        // Blog posts
        DB::table('blog_posts')->insert([
            [
                'id' => Str::uuid(), 'title' => '10 Tips Memilih Wedding Organizer Profesional',
                'slug' => 'tips-memilih-wedding-organizer-profesional',
                'excerpt' => 'Temukan cara terbaik memilih wedding organizer yang sesuai dengan budget dan konsep pernikahan impian Anda.',
                'content' => '## Kenali Kebutuhan Anda\n\nSetiap pasangan memiliki kebutuhan berbeda. Sebelum memilih WO, tentukan konsep, budget, dan jumlah tamu.\n\n## Cek Portofolio\n\nLihat portofolio WO untuk memastikan gaya mereka sesuai dengan keinginan Anda. Perhatikan detail-de-tail kecil seperti dekorasi, pencahayaan, dan tata rias.\n\n## Baca Review\n\nReview dari pasangan sebelumnya bisa memberikan gambaran nyata tentang kualitas layanan WO.\n\n## Komunikasi\n\nPastikan WO responsif dan mudah dihubungi. Komunikasi yang baik adalah kunci kesuksesan acara.',
                'featured_image' => null, 'author' => 'EventOS', 'tags' => json_encode(['wedding', 'tips', 'WO']),
                'category' => 'tips', 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(), 'title' => 'Panduan Lengkap Membuat Quotation Event',
                'slug' => 'panduan-quotation-event',
                'excerpt' => 'Pelajari cara menyusun quotation event yang profesional dan meningkatkan closing rate bisnis WO Anda.',
                'content' => '## Struktur Quotation yang Baik\n\n1. **Header** - Logo perusahaan, informasi kontak\n2. **Data Klien** - Nama, tanggal acara, lokasi\n3. **Rincian Layanan** - Deskripsi per item dengan harga\n4. **Total** - Subtotal, pajak, grand total\n5. **Syarat & Ketentuan** - DP, pembatalan, kebijakan reschedule\n\n## Tips Meningkatkan Closing Rate\n\n- Berikan opsi paket (Basic, Premium, VIP)\n- Tampilkan foto portofolio terkait\n- Sertakan testimonial klien sebelumnya\n- Kirim quotation dalam format PDF profesional',
                'featured_image' => null, 'author' => 'EventOS', 'tags' => json_encode(['quotation', 'bisnis', 'WO']),
                'category' => 'bisnis', 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(), 'title' => 'Manajemen Vendor untuk Event Sukses',
                'slug' => 'manajemen-vendor-event-sukses',
                'excerpt' => 'Strategi efektif mengelola vendor event untuk memastikan kolaborasi yang harmonis dan hasil maksimal.',
                'content' => '## Pentingnya Manajemen Vendor\n\nEvent yang sukses membutuhkan koordinasi sempurna antara berbagai vendor: dekorasi, katering, dokumentasi, MUA, dan hiburan.\n\n## Tips Manajemen Vendor\n\n1. **Kontrak Jelas** - Tentukan scope of work, timeline, dan pembayaran\n2. **Komunikasi Terpusat** - Gunakan platform chat per-divisi\n3. **Checklist** - Buat checklist tugas per vendor\n4. **Backup Plan** - Siapkan vendor cadangan untuk kebutuhan kritis\n5. **Evaluasi** - Lakukan post-event evaluation dengan setiap vendor',
                'featured_image' => null, 'author' => 'EventOS', 'tags' => json_encode(['vendor', 'manajemen', 'tips']),
                'category' => 'manajemen', 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(), 'title' => 'AI untuk Event Organizer: Masa Depan Industri Event',
                'slug' => 'ai-untuk-event-organizer',
                'excerpt' => 'Bagaimana Artificial Intelligence mengubah cara kerja event organizer dan meningkatkan efisiensi operasional.',
                'content' => '## Revolusi AI di Industri Event\n\nArtificial Intelligence telah membawa perubahan besar dalam cara event organizer bekerja. Dari perencanaan hingga eksekusi, AI membantu mengotomatiskan tugas-tugas repetitif.\n\n## Penerapan AI\n\n1. **AI Project Manager** - Generate rencana proyek otomatis\n2. **Surge Pricing** - Analisis harga optimal berdasarkan data cuaca dan kalender\n3. **AI Copilot** - Asisten virtual untuk menjawab pertanyaan operasional\n4. **Knowledge Base** - Query kontrak dan dokumen dengan bahasa alami\n5. **Compliance Audit** - Verifikasi portofolio vendor dengan AI',
                'featured_image' => null, 'author' => 'EventOS', 'tags' => json_encode(['AI', 'teknologi', 'inovasi']),
                'category' => 'teknologi', 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        // Demo vendor reviews
        DB::table('vendor_reviews')->insert([
            [
                'id' => Str::uuid(), 'vendor_id' => 'v-1', 'reviewer_name' => 'Anisa Putri',
                'reviewer_whatsapp' => '6281234567890', 'rating' => 5,
                'comment' => 'Fotografer profesional banget! Hasil fotonya luar biasa dan tepat waktu. Sangat recommended!',
                'is_verified' => true, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(), 'vendor_id' => 'v-1', 'reviewer_name' => 'Budi Santoso',
                'reviewer_whatsapp' => '6281234567891', 'rating' => 4,
                'comment' => 'Kualitas foto bagus, harga sesuai. Komunikasi lancar.',
                'is_verified' => true, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(), 'vendor_id' => 'v-2', 'reviewer_name' => 'Citra Dewi',
                'reviewer_whatsapp' => '6281234567892', 'rating' => 5,
                'comment' => 'Dekorasinya cantik banget! Sesuai dengan tema yang kami inginkan. Tim sangat profesional.',
                'is_verified' => true, 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        // Forum topics
        $topic1Id = Str::uuid();
        $topic2Id = Str::uuid();
        DB::table('forum_topics')->insert([
            [
                'id' => $topic1Id, 'title' => 'Tips Mencari Vendor Dekorasi Murah di Jakarta',
                'content' => 'Halo teman-teman, ada rekomendasi vendor dekorasi untuk budget di bawah 50 juta? Kami sedang mencari untuk acara pernikahan bulan depan.',
                'author_name' => 'Rina', 'category' => 'vendor', 'view_count' => 45, 'reply_count' => 3,
                'is_pinned' => false, 'created_at' => now()->subDays(2), 'updated_at' => now()->subDays(2),
            ],
            [
                'id' => $topic2Id, 'title' => 'Pengalaman Pakai EventOS untuk Management Event',
                'content' => 'Baru pertama kali pakai EventOS, ternyata fiturnya lengkap banget! Ada AI, escrow, floorplan, semua dalam satu platform. Recommended!',
                'author_name' => 'Dian', 'category' => 'umum', 'view_count' => 120, 'reply_count' => 7,
                'is_pinned' => true, 'created_at' => now()->subDays(1), 'updated_at' => now()->subDays(1),
            ],
        ]);

        // Forum replies
        DB::table('forum_replies')->insert([
            [
                'id' => Str::uuid(), 'topic_id' => $topic1Id,
                'content' => 'Coba cek vendor di storefront EventOS, banyak pilihan dengan range harga yang beragam.',
                'author_name' => 'Ahmad', 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        // Virtual expo
        $expoId = Str::uuid();
        DB::table('virtual_expos')->insert([
            'id' => $expoId, 'tenant_id' => 'tenant-demo-uuid', 'title' => 'EventOS Virtual Expo 2026',
            'description' => 'Pameran vendor event online terbesar di Indonesia. Temukan vendor terbaik untuk acara Anda!',
            'event_date' => '2026-08-20', 'registration_end' => '2026-08-15',
            'status' => 'upcoming', 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Expo booths
        DB::table('virtual_expo_booths')->insert([
            [
                'id' => Str::uuid(), 'expo_id' => $expoId, 'vendor_id' => 'v-1',
                'booth_title' => 'Wedding Photography Pro', 'description' => 'Jasa fotografi pernikahan profesional dengan harga terjangkau.',
                'visitor_count' => 12, 'lead_count' => 3, 'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(), 'expo_id' => $expoId, 'vendor_id' => 'v-2',
                'booth_title' => 'Dekorasi Impian', 'description' => 'Dekorasi pernikahan mewah dengan konsep personal.',
                'visitor_count' => 8, 'lead_count' => 2, 'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        // Premium profiles
        DB::table('vendor_premium_profiles')->insert([
            [
                'id' => Str::uuid(), 'vendor_id' => 'v-1', 'badge_type' => 'premium',
                'is_featured' => true, 'priority_score' => 100,
                'subscription_start' => '2026-01-01', 'subscription_end' => '2026-12-31',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(), 'vendor_id' => 'v-2', 'badge_type' => 'enterprise',
                'is_featured' => true, 'priority_score' => 90,
                'subscription_start' => '2026-01-01', 'subscription_end' => '2026-12-31',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        // Sponsor content
        DB::table('sponsored_contents')->insert([
            [
                'id' => Str::uuid(), 'vendor_id' => 'v-1', 'title' => 'Paket Foto Prewedding Diskon 30%',
                'content' => 'Dapatkan paket foto prewedding dengan diskon 30% untuk pemesanan bulan ini. Terms & conditions apply.',
                'type' => 'banner', 'target_url' => '/storefront/v-1', 'image_url' => null,
                'is_active' => true, 'start_date' => '2026-07-01', 'end_date' => '2026-08-31',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        // Wishlist
        DB::table('wishlists')->insert([
            'id' => Str::uuid(), 'session_id' => 'demo-session-123', 'vendor_id' => 'v-1',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Inspiration board
        DB::table('inspiration_boards')->insert([
            'id' => Str::uuid(), 'session_id' => 'demo-session-123', 'title' => 'Pernikahan Impianku',
            'items' => json_encode(['vendor_id' => 'v-1', 'image_url' => null, 'note' => 'Fotografer untuk akad']),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // UGC gallery
        DB::table('ugc_galleries')->insert([
            'id' => Str::uuid(), 'session_id' => 'demo-session-456', 'uploader_name' => 'Sari',
            'photo_url' => null, 'caption' => 'Pernikahan kami dengan dekorasi dari vendor favorit!',
            'tagged_vendor_ids' => json_encode(['v-2']), 'is_approved' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Ad campaigns
        DB::table('vendor_ad_campaigns')->insert([
            'id' => Str::uuid(), 'vendor_id' => 'v-1', 'campaign_name' => 'Promo Juli 2026',
            'daily_budget' => 50000, 'total_spent' => 150000, 'impressions' => 1200, 'clicks' => 45,
            'status' => 'active', 'start_date' => '2026-07-01', 'end_date' => '2026-07-31',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // API keys
        DB::table('api_keys')->insert([
            'id' => Str::uuid(), 'tenant_id' => 'tenant-demo-uuid', 'name' => 'Development Key',
            'key' => 'dev_' . Str::random(60), 'permissions' => json_encode(['read', 'write']),
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->command->info('Phase 1-4 seed data created successfully!');
    }
}
