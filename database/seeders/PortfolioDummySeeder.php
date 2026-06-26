<?php

namespace Database\Seeders;

use App\Models\BoardingHouse;
use App\Models\Bonus;
use App\Models\Category;
use App\Models\City;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Testimonial;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PortfolioDummySeeder extends Seeder
{
    public function run(): void
    {
        $cities = collect([
            [
                'name' => 'Jakarta Selatan',
                'slug' => 'jakarta-selatan',
                'image' => 'city/placeholder-jakarta.svg',
            ],
            [
                'name' => 'Bandung',
                'slug' => 'bandung',
                'image' => 'city/placeholder-bandung.svg',
            ],
            [
                'name' => 'Yogyakarta',
                'slug' => 'yogyakarta',
                'image' => 'city/placeholder-yogyakarta.svg',
            ],
        ])->mapWithKeys(fn (array $city) => [
            $city['slug'] => City::updateOrCreate(
                ['slug' => $city['slug']],
                $city,
            ),
        ]);

        $categories = collect([
            [
                'name' => 'Kos Putra',
                'slug' => 'kos-putra',
                'image' => 'category/placeholder-putra.svg',
            ],
            [
                'name' => 'Kos Putri',
                'slug' => 'kos-putri',
                'image' => 'category/placeholder-putri.svg',
            ],
            [
                'name' => 'Kos Campur',
                'slug' => 'kos-campur',
                'image' => 'category/placeholder-campur.svg',
            ],
            [
                'name' => 'Hotel',
                'slug' => 'hotel',
                'image' => 'category/01KJXPPD65MS8PADQMJ2SP4Y59.jpg',
            ],
        ])->mapWithKeys(fn (array $category) => [
            $category['slug'] => Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            ),
        ]);

        $boardingHouses = [
            [
                'name' => 'Nusa Residence Kemang',
                'slug' => 'nusa-residence-kemang',
                'thumbnail' => 'boarding_house/placeholder-kemang.svg',
                'city_slug' => 'jakarta-selatan',
                'category_slug' => 'kos-campur',
                'description' => '<p>Kos modern di area Kemang dengan akses cepat ke kantor, coworking space, dan kuliner malam.</p><p>Cocok untuk pekerja muda yang ingin tinggal praktis dan rapi.</p>',
                'price' => 1850000,
                'address' => 'Jl. Kemang Raya No. 18, Jakarta Selatan',
                'bonuses' => [
                    [
                        'name' => 'WiFi 100 Mbps',
                        'description' => 'Internet stabil untuk kerja remote dan streaming.',
                        'image' => 'bonuses/placeholder-wifi.svg',
                    ],
                    [
                        'name' => 'Laundry Mingguan',
                        'description' => 'Layanan laundry 4 kg per minggu untuk penghuni aktif.',
                        'image' => 'bonuses/placeholder-laundry.svg',
                    ],
                ],
                'rooms' => [
                    [
                        'name' => 'Deluxe A',
                        'room_type' => 'Kamar Mandi Dalam',
                        'square_feet' => 18,
                        'capacity' => 2,
                        'price_per_month' => 1850000,
                        'is_available' => true,
                        'images' => [
                            'rooms/placeholder-room-deluxe-a.svg',
                            'rooms/placeholder-room-deluxe-b.svg',
                        ],
                    ],
                    [
                        'name' => 'Studio Compact',
                        'room_type' => 'Fully Furnished',
                        'square_feet' => 16,
                        'capacity' => 1,
                        'price_per_month' => 1650000,
                        'is_available' => true,
                        'images' => [
                            'rooms/placeholder-room-studio-a.svg',
                        ],
                    ],
                ],
                'testimonials' => [
                    [
                        'name' => 'Raka Pratama',
                        'photo' => 'testimonials/placeholder-raka.svg',
                        'content' => 'Lokasi dekat tempat kerja dan proses booking di web-nya jelas. Cocok buat demo portfolio.',
                        'rating' => 5,
                    ],
                    [
                        'name' => 'Nadia Putri',
                        'photo' => 'testimonials/placeholder-nadia.svg',
                        'content' => 'Interface listing mudah dipahami, detail kamar juga informatif.',
                        'rating' => 4,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => '100001',
                        'room_name' => 'Deluxe A',
                        'name' => 'Aditya Firmansyah',
                        'email' => 'aditya@example.test',
                        'phone_number' => '081234567801',
                        'payment_method' => 'full_payment',
                        'payment_status' => 'paid',
                        'start_date' => '2026-07-01',
                        'duration' => 6,
                    ],
                    [
                        'code' => '100002',
                        'room_name' => 'Studio Compact',
                        'name' => 'Meysa Anindita',
                        'email' => 'meysa@example.test',
                        'phone_number' => '081234567802',
                        'payment_method' => 'down_payment',
                        'payment_status' => 'pending',
                        'start_date' => '2026-07-10',
                        'duration' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Puri Braga Living',
                'slug' => 'puri-braga-living',
                'thumbnail' => 'boarding_house/placeholder-braga.svg',
                'city_slug' => 'bandung',
                'category_slug' => 'kos-putri',
                'description' => '<p>Kos estetik dekat area Braga dan kampus kreatif, ideal untuk mahasiswi dan freelancer.</p><p>Nuansa bangunan dibuat hangat dan tenang untuk mendukung aktivitas harian.</p>',
                'price' => 1450000,
                'address' => 'Jl. Braga Kulon No. 22, Bandung',
                'bonuses' => [
                    [
                        'name' => 'Pantry Bersama',
                        'description' => 'Area masak sederhana yang bersih dan mudah dipakai.',
                        'image' => 'bonuses/placeholder-pantry.svg',
                    ],
                    [
                        'name' => 'Area Jemur Indoor',
                        'description' => 'Lebih aman saat musim hujan dan tetap rapi.',
                        'image' => 'bonuses/placeholder-drying.svg',
                    ],
                ],
                'rooms' => [
                    [
                        'name' => 'Braga Single',
                        'room_type' => 'Single Bed',
                        'square_feet' => 14,
                        'capacity' => 1,
                        'price_per_month' => 1450000,
                        'is_available' => true,
                        'images' => [
                            'rooms/placeholder-room-braga.svg',
                        ],
                    ],
                    [
                        'name' => 'Corner Plus',
                        'room_type' => 'Sudut Jendela Besar',
                        'square_feet' => 20,
                        'capacity' => 2,
                        'price_per_month' => 1750000,
                        'is_available' => false,
                        'images' => [
                            'rooms/placeholder-room-corner.svg',
                        ],
                    ],
                ],
                'testimonials' => [
                    [
                        'name' => 'Salma Zahra',
                        'photo' => 'testimonials/placeholder-salma.svg',
                        'content' => 'Katalog kos di halaman depan terasa hidup karena data kota, kategori, dan kamar sudah terhubung.',
                        'rating' => 5,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => '100003',
                        'room_name' => 'Corner Plus',
                        'name' => 'Tiara Maharani',
                        'email' => 'tiara@example.test',
                        'phone_number' => '081234567803',
                        'payment_method' => 'full_payment',
                        'payment_status' => 'paid',
                        'start_date' => '2026-08-01',
                        'duration' => 12,
                    ],
                ],
            ],
            [
                'name' => 'Malioboro Student House',
                'slug' => 'malioboro-student-house',
                'thumbnail' => 'boarding_house/placeholder-malioboro.svg',
                'city_slug' => 'yogyakarta',
                'category_slug' => 'kos-putra',
                'description' => '<p>Hunian mahasiswa dekat pusat kota dengan harga terjangkau dan fasilitas dasar lengkap.</p><p>Sangat pas untuk demo fitur pencarian kos berdasarkan kota dan kategori.</p>',
                'price' => 950000,
                'address' => 'Jl. Sosrowijayan No. 9, Yogyakarta',
                'bonuses' => [
                    [
                        'name' => 'Parkir Motor',
                        'description' => 'Area parkir cukup untuk penghuni harian.',
                        'image' => 'bonuses/placeholder-parking.svg',
                    ],
                    [
                        'name' => 'Free Air Galon',
                        'description' => 'Isi ulang galon bersama untuk kebutuhan harian.',
                        'image' => 'bonuses/placeholder-water.svg',
                    ],
                ],
                'rooms' => [
                    [
                        'name' => 'Student Basic',
                        'room_type' => 'Non AC',
                        'square_feet' => 12,
                        'capacity' => 1,
                        'price_per_month' => 950000,
                        'is_available' => true,
                        'images' => [
                            'rooms/placeholder-room-basic.svg',
                        ],
                    ],
                    [
                        'name' => 'Student Comfort',
                        'room_type' => 'AC + Meja Belajar',
                        'square_feet' => 15,
                        'capacity' => 1,
                        'price_per_month' => 1200000,
                        'is_available' => true,
                        'images' => [
                            'rooms/placeholder-room-comfort.svg',
                        ],
                    ],
                ],
                'testimonials' => [
                    [
                        'name' => 'Bagas Saputra',
                        'photo' => 'testimonials/placeholder-bagas.svg',
                        'content' => 'Data dummy ini enak dipakai presentasi karena relasi kamar, bonus, dan transaksi semua jalan.',
                        'rating' => 5,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => '100004',
                        'room_name' => 'Student Basic',
                        'name' => 'Fauzan Akbar',
                        'email' => 'fauzan@example.test',
                        'phone_number' => '081234567804',
                        'payment_method' => 'down_payment',
                        'payment_status' => 'paid',
                        'start_date' => '2026-06-28',
                        'duration' => 1,
                    ],
                    [
                        'code' => '100005',
                        'room_name' => 'Student Comfort',
                        'name' => 'Dimas Maulana',
                        'email' => 'dimas@example.test',
                        'phone_number' => '081234567805',
                        'payment_method' => 'full_payment',
                        'payment_status' => 'paid',
                        'start_date' => '2026-07-05',
                        'duration' => 6,
                    ],
                ],
            ],
            [
                'name' => 'Savana Hotel Sudirman',
                'slug' => 'savana-hotel-sudirman',
                'thumbnail' => 'boarding_house/01KW0G3RVW79MQX06FFYMMDGY4.jpg',
                'city_slug' => 'jakarta-selatan',
                'category_slug' => 'hotel',
                'description' => '<p>Hotel nyaman di pusat bisnis dengan kamar modern, sarapan, dan akses cepat ke transportasi umum.</p><p>Cocok untuk kebutuhan demo listing kategori hotel di halaman utama dan halaman kategori.</p>',
                'price' => 725000,
                'address' => 'Jl. Jenderal Sudirman No. 88, Jakarta Selatan',
                'bonuses' => [
                    [
                        'name' => 'Breakfast Buffet',
                        'description' => 'Sarapan prasmanan setiap pagi untuk tamu menginap.',
                        'image' => 'bonuses/01KW0G3RWJSR819APPPGPGSQG0.jpg',
                    ],
                    [
                        'name' => 'City View Lounge',
                        'description' => 'Area santai dengan pemandangan kota dan internet cepat.',
                        'image' => 'bonuses/01KW0G3RWPNE3XEQYYKJ8PAH3D.jpg',
                    ],
                ],
                'rooms' => [
                    [
                        'name' => 'Superior Queen',
                        'room_type' => 'Queen Bed + Breakfast',
                        'square_feet' => 24,
                        'capacity' => 2,
                        'price_per_month' => 725000,
                        'is_available' => true,
                        'images' => [
                            'rooms/01KW0GV8V06X4XCK1PJJ7NYK45.jpg',
                            'rooms/01KW0GV8V83FPZW0FDAC7MGYS0.jpg',
                        ],
                    ],
                    [
                        'name' => 'Executive Twin',
                        'room_type' => 'Twin Bed + Work Desk',
                        'square_feet' => 28,
                        'capacity' => 2,
                        'price_per_month' => 860000,
                        'is_available' => true,
                        'images' => [
                            'rooms/01KW0GNENQBBZBTHE6WR539SA2.jpg',
                            'rooms/01KW0GNENWRXA7ZGA0NQGDDFAW.jpg',
                        ],
                    ],
                ],
                'testimonials' => [
                    [
                        'name' => 'Rizky Ramadhan',
                        'photo' => 'testimonials/placeholder-raka.svg',
                        'content' => 'Kategori hotel sekarang ada isinya, jadi demo homepage dan browse category terasa lebih realistis.',
                        'rating' => 5,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => '100006',
                        'room_name' => 'Superior Queen',
                        'name' => 'Kevin Prakoso',
                        'email' => 'kevin@example.test',
                        'phone_number' => '081234567806',
                        'payment_method' => 'full_payment',
                        'payment_status' => 'paid',
                        'start_date' => '2026-07-12',
                        'duration' => 2,
                    ],
                    [
                        'code' => '100007',
                        'room_name' => 'Executive Twin',
                        'name' => 'Farhan Ilham',
                        'email' => 'farhan@example.test',
                        'phone_number' => '081234567807',
                        'payment_method' => 'down_payment',
                        'payment_status' => 'pending',
                        'start_date' => '2026-07-20',
                        'duration' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Braga Boutique Hotel',
                'slug' => 'braga-boutique-hotel',
                'thumbnail' => 'boarding_house/01KW0FCTB42Z1B78QHB8PJKS7G.jpg',
                'city_slug' => 'bandung',
                'category_slug' => 'hotel',
                'description' => '<p>Hotel butik di kawasan Braga dengan interior hangat, kamar compact modern, dan akses mudah ke pusat wisata kota.</p><p>Pas untuk memperkaya kategori hotel dengan nuansa staycation yang berbeda dari listing Jakarta.</p>',
                'price' => 680000,
                'address' => 'Jl. Braga No. 45, Bandung',
                'bonuses' => [
                    [
                        'name' => 'Coffee Corner',
                        'description' => 'Kopi dan teh gratis di lobi untuk tamu hotel.',
                        'image' => 'bonuses/01KJMT4GZEDED6QAM4B5K7XPEA.jpg',
                    ],
                    [
                        'name' => 'Late Check-out',
                        'description' => 'Fleksibilitas check-out lebih siang untuk tamu tertentu.',
                        'image' => 'bonuses/01KW0FTE3HS0K7YE3001QFZ29N.jpg',
                    ],
                ],
                'rooms' => [
                    [
                        'name' => 'Deluxe Loft',
                        'room_type' => 'King Bed + Sofa',
                        'square_feet' => 26,
                        'capacity' => 2,
                        'price_per_month' => 680000,
                        'is_available' => true,
                        'images' => [
                            'rooms/01KW0FCTDR6ANVWZJZV7MZR758.jpg',
                            'rooms/01KW0FCTDVS715QQCERMM76E0E.jpg',
                        ],
                    ],
                    [
                        'name' => 'Braga Suite',
                        'room_type' => 'Suite + Bathtub',
                        'square_feet' => 32,
                        'capacity' => 2,
                        'price_per_month' => 910000,
                        'is_available' => false,
                        'images' => [
                            'rooms/01KW0FCTE1EWJ89Q9691EQGKS3.jpg',
                        ],
                    ],
                ],
                'testimonials' => [
                    [
                        'name' => 'Cindy Larasati',
                        'photo' => 'testimonials/placeholder-nadia.svg',
                        'content' => 'Pilihan hotel jadi lebih dari satu, jadi halaman kategori hotel sekarang terasa lebih meyakinkan untuk demo.',
                        'rating' => 5,
                    ],
                ],
                'transactions' => [
                    [
                        'code' => '100008',
                        'room_name' => 'Deluxe Loft',
                        'name' => 'Mira Khairunnisa',
                        'email' => 'mira@example.test',
                        'phone_number' => '081234567808',
                        'payment_method' => 'full_payment',
                        'payment_status' => 'paid',
                        'start_date' => '2026-07-18',
                        'duration' => 3,
                    ],
                    [
                        'code' => '100009',
                        'room_name' => 'Braga Suite',
                        'name' => 'Rian Kurniawan',
                        'email' => 'rian@example.test',
                        'phone_number' => '081234567809',
                        'payment_method' => 'down_payment',
                        'payment_status' => 'pending',
                        'start_date' => '2026-08-03',
                        'duration' => 2,
                    ],
                ],
            ],
        ];

        foreach ($boardingHouses as $boardingHouseData) {
            $boardingHouse = BoardingHouse::updateOrCreate(
                ['slug' => $boardingHouseData['slug']],
                [
                    'name' => $boardingHouseData['name'],
                    'slug' => $boardingHouseData['slug'],
                    'thumbnail' => $boardingHouseData['thumbnail'],
                    'city_id' => $cities[$boardingHouseData['city_slug']]->id,
                    'category_id' => $categories[$boardingHouseData['category_slug']]->id,
                    'description' => $boardingHouseData['description'],
                    'price' => $boardingHouseData['price'],
                    'address' => $boardingHouseData['address'],
                ],
            );

            foreach ($boardingHouseData['bonuses'] as $bonusData) {
                Bonus::updateOrCreate(
                    [
                        'boarding_house_id' => $boardingHouse->id,
                        'name' => $bonusData['name'],
                    ],
                    [
                        'image' => $bonusData['image'],
                        'description' => $bonusData['description'],
                    ],
                );
            }

            $roomsByName = collect();

            foreach ($boardingHouseData['rooms'] as $roomData) {
                $room = Room::updateOrCreate(
                    [
                        'boarding_house_id' => $boardingHouse->id,
                        'name' => $roomData['name'],
                    ],
                    [
                        'room_type' => $roomData['room_type'],
                        'square_feet' => $roomData['square_feet'],
                        'capacity' => $roomData['capacity'],
                        'price_per_month' => $roomData['price_per_month'],
                        'is_available' => $roomData['is_available'],
                    ],
                );

                foreach ($roomData['images'] as $imagePath) {
                    RoomImage::updateOrCreate(
                        [
                            'room_id' => $room->id,
                            'image' => $imagePath,
                        ],
                        [],
                    );
                }

                $roomsByName->put($room->name, $room);
            }

            foreach ($boardingHouseData['testimonials'] as $testimonialData) {
                Testimonial::updateOrCreate(
                    [
                        'boarding_house_id' => $boardingHouse->id,
                        'name' => $testimonialData['name'],
                    ],
                    [
                        'photo' => $testimonialData['photo'],
                        'content' => $testimonialData['content'],
                        'rating' => $testimonialData['rating'],
                    ],
                );
            }

            foreach ($boardingHouseData['transactions'] as $transactionData) {
                $room = $roomsByName->get($transactionData['room_name']);
                $startDate = Carbon::parse($transactionData['start_date']);

                Transaction::updateOrCreate(
                    ['code' => $transactionData['code']],
                    [
                        'boarding_house_id' => $boardingHouse->id,
                        'room_id' => $room?->id,
                        'name' => $transactionData['name'],
                        'email' => $transactionData['email'],
                        'phone_number' => $transactionData['phone_number'],
                        'payment_method' => $transactionData['payment_method'],
                        'payment_status' => $transactionData['payment_status'],
                        'start_date' => $startDate->toDateString(),
                        'duration' => $transactionData['duration'],
                        'total_amount' => $room?->price_per_month * $transactionData['duration'],
                        'transaction_date' => $startDate->copy()->subDays(3),
                    ],
                );
            }
        }
    }
}
