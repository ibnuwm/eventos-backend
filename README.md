# Vendor Event OS (EventOS.id) - Laravel 12 Backend API
**Backend Enterprise Berskala Tinggi dengan Integrasi MySQL, Meilisearch, Reverb, MinIO S3, & OpenAI API**

Proyek ini adalah sistem layanan *Backend REST API* dan *Realtime WebSocket Server* yang dibangun menggunakan **Laravel 12 (PHP 8.3+)** dan terintegrasi penuh dengan aplikasi frontend Next.js 15 (`vendor-event-os`).

---

## 🌟 Implementasi 100% dari 8 Rekomendasi Improvement (Keunggulan Kompetitif)

Backend API ini tidak sekadar menyediakan CRUD standar, melainkan telah dirancang secara arsitektural untuk menggerakkan **8 inovasi keunggulan kompetitif utama**:

| No | Poin Improvement | Kelas Service Backend / Engine | Endpoint REST API Terkait | Mekanisme & Logika Backend |
| :---: | :--- | :--- | :--- | :--- |
| **1** | **AI Project Manager** | `App\Services\AiProjectManagerService` | `POST /api/v1/ai/project-manager/generate` | Meracik otomatis struktur pohon tugas *Milestone T-Minus* (T-180 s.d. H-1) dan menghitung estimasi anggaran HPP 65% + Overhead 10% di dalam transaksi MySQL (`DB::transaction`). |
| **2** | **WhatsApp-Native Workflow** | `App\Services\WhatsAppWebhookService` | `POST /api/v1/webhooks/whatsapp` | Menerima *payload* tombol interaktif dari WhatsApp Cloud API (`CONFIRM_ATTENDANCE` / `START_LOADING`), memperbarui tabel `project_tasks`, lalu menyiarkan event live via **Laravel Reverb WebSocket**. |
| **3** | **Vendor Performance Score** | `App\Services\VendorPerformanceScoringService` | Di-trigger otomatis pasca-event | Mengevaluasi rasio penyelesaian tepat waktu dari log absensi GPS lapangan dan memperbarui atribut `sla_punctuality` & `rating` di model `Vendor`. |
| **4** | **Predictive Conflict Detection** | `App\Services\InventoryConflictEngine` | Terintegrasi di `DashboardController` | Melakukan *cross-check* alokasi peminjaman barang (`allocated_qty` vs `total_stock`) pada tanggal yang sama antar-proyek dan menandai `has_conflict = true`. |
| **5** | **Auto Accounting Engine** | `App\Services\AutoAccountingService` | Dipicu dari `ClientPortalController` | Saat Quotation disetujui klien, transaksi database membuat **3 Faktur Penagihan Bertahap** (`DP_30`, `TERMIN_2_50`, `PELUNASAN_20`) dengan referensi virtual account Midtrans. |
| **6** | **Marketplace Berbasis Data Operasional** | `App\Models\Vendor` (Scout Meilisearch) | `GET /api/v1/marketplace/vendors` | Kueri pencarian Meilisearch (< 50ms) diurutkan secara eksplisit berdasarkan `orderBy('sla_punctuality', 'desc')`, sehingga vendor paling disiplin naik ke peringkat #1. |
| **7** | **Client Portal Digital Approvals** | `App\Http\Controllers\Api\V1\ClientPortalController` | `POST /api/v1/client-portal/approve` | Endpoint verifikasi nirkontak aplikasi yang mencatat *E-Signature Timestamp* dan otomatis memicu engine akuntansi tanpa admin harus membuat faktur manual. |
| **8** | **Knowledge Base AI (RAG Engine)** | `App\Services\KnowledgeBaseRagService` | `POST /api/v1/ai/knowledge-base/query` | Mesin *Retrieval-Augmented Generation* yang menelusuri isi kontrak hukum di brankas **MinIO S3 (`Modul 10`)** dan log obrolan untuk menjawab kueri bahasa alami. |

---

## 🏗️ Arsitektur Integrasi Teknologi

| Layer / Layanan | Teknologi yang Diterapkan | Peran Khusus di dalam Sistem Event OS |
| :--- | :--- | :--- |
| **Backend Core** | **Laravel 12 (PHP 8.3+)** | Pusat logika bisnis, otentikasi multi-tenant SaaS (**Sanctum**), penjadwalan (*Scheduler*), dan sistem antrean (*Queue Redis*). |
| **Relational DB** | **MySQL 8.0+** | Penyimpanan utama transaksional relasional (Tenants, Leads CRM, Proyek Acara, Tagihan Invoice, Aset Gudang). |
| **Realtime Engine** | **Laravel Reverb (Native WebSocket)** | Server WebSocket berkinerja tinggi pengganti Pusher berbayar untuk menyiarkan penekanan tombol WhatsApp secara instan ke dasbor Next.js. |
| **Object Storage** | **MinIO (S3 Compatible)** | Brankas penyimpanan file awan (*cloud storage*) untuk dokumen kontrak hukum, CAD layout panggung 3D, dan foto bukti loading. |
| **Search Engine** | **Meilisearch (<50ms Search)** | Mesin pencari instan ber-toleransi typo yang diutamakan (*Ranked Algorithmic*) berdasarkan **SLA Punctuality** vendor. |
| **Generative AI** | **OpenAI API / OpenRouter** | Engine kecerdasan buatan (*claude-3.5-sonnet* / *gpt-4o*) untuk merancang rundown, quotation, dan merangkum kontrak. |

---

## 🚀 Cara Menjalankan Server Backend

```bash
# Masuk ke folder backend
cd vendor-event-os-backend

# Salin konfigurasi environment
cp .env.example .env

# Jalankan migrasi database MySQL
php artisan migrate --seed

# Aktifkan server pengembangan Laravel REST API
php artisan serve --port=8000

# Di terminal terpisah, aktifkan server WebSocket Laravel Reverb
php artisan reverb:start --port=8080
```
