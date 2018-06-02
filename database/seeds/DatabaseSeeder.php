<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('RentcarTableSeeder');
    }
}

class RentcarTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // JUMLAH
        $jumlah_m_merk = 1;
        $jumlah_m_car = 6;
        $jumlah_m_city = 4;
        $jumlah_admin = 3;
    	$jumlah_user_roles = 2;
        $jumlah_users = 3;
        $jumlah_service = 2;
        $jumlah_order = 2;

        // TRUNCATE
        DB::table('m_merk')->truncate();
        DB::table('m_car')->truncate();
        DB::table('m_city')->truncate();
        DB::table('admin')->truncate();
    	DB::table('user_role')->truncate();
        DB::table('users')->truncate();
        DB::table('service')->truncate();
        DB::table('order')->truncate();
        
        // FAKER
        $faker_m_merk = Faker::create();
        $faker_m_car = Faker::create();
        $faker_m_city = Faker::create();
        $faker_admin = Faker::create();
        $faker_user_role = Faker::create();
        $faker_users = Faker::create();
        $faker_service = Faker::create();
        $faker_order = Faker::create();
        
        foreach (range(1,$jumlah_m_merk) as $key => $value) {
            $name_merk = $faker_m_merk->unique()->randomElement(['Toyota', 'Honda', 'Daihatsu', 'Mitsubishi', 'Suzuki', 'Nissan']);
            DB::table('m_merk')->insert([
                'name' => $name_merk,
            ]);

            foreach (range(1,$jumlah_m_car) as $key => $value) {
                $name_car = $faker_m_car->unique()->randomElement(['Avanza 1.3 G MT', 'Mobilio 2017 S MT', 'Ertiga Dreza GS MT', 'Grand Livina 1.5 HWS CVT Autech', 'Mirage 2016 Exceed', 'Terios R AT Adventure']);
                DB::table('m_car')->insert([
                    'name' => $name_car,
                    'merk' => $name_merk,
                    'seat' => $faker_m_car->randomElement([6, 8, 9]),
                    'loading' => $faker_m_car->randomElement([2, 4]),
                    'year' => $faker_m_car->year,
                ]); 
            }

            foreach (range(1,$jumlah_m_city) as $key => $value) {
                $city = $faker_m_city->unique()->city;
                DB::table('m_city')->insert([
                    'name' => $city,
                ]); 
            }
        
            foreach (range(1,$jumlah_user_roles) as $key => $value) {
                $roles = $faker_user_role->unique()->randomElement(['Vendor', 'User']);
                DB::table('user_role')->insert([
                    'name' => $roles,
                ]);

                foreach (range(1,$jumlah_admin) as $key => $value) {
                    $name_admin = $faker_admin->lastName;
                    $username_admin = $name_admin.$faker_admin->unique()->numberBetween(100,500);
                    $status_admin = $faker_admin->numberBetween(0,-1,1);
                    $id_users = DB::table('admin')->insertGetId([
                        'register_time' => $faker_admin->DateTime,
                        'image' => $faker_admin->randomElement(['http://192.168.1.3/rentcar/public/image/rentcar.jpg']),
                        'pin' => $faker_admin->numberBetween(1000,5000),
                        'name' => $name_admin,
                        'username' => $username_admin,
                        'city' => $faker_admin->city,
                        'address' => $faker_admin->address,
                        'email' => $name_admin.$faker_admin->randomElement(['@gmail.com']),
                        'password' => $faker_admin->randomElement(['$2y$10$vr6ZIP3Jp1g2YHn5VSSFkuaUKsJXHTmkfU3cM5EpFtQ7HSp1wfbna']),
                        'phone' => "082".$faker_admin->numberBetween(100000000,500000000),
                        'birthday' => $faker_admin->date,
                        'gender' => $faker_admin->randomElement(['Male', 'Female']),
                        'roles' => $roles,
                        'remember_token' => $faker_admin->sha1,
                        'status' => $status_admin,
                    ]);
                }
    
                foreach (range(1,$jumlah_users) as $key => $value) {
                    $name = $faker_users->lastName;
                    $username = $name.$faker_users->unique()->numberBetween(100,500);
                    $status = $faker_users->numberBetween(0,-1,1);
                    $id_users = DB::table('users')->insertGetId([
                        'register_time' => $faker_users->DateTime,
                        'image' => $faker_admin->randomElement(['http://192.168.1.3/rentcar/public/image/rentcar.jpg']),
                        'pin' => $faker_users->numberBetween(1000,5000),
                        'name' => $name,
                        'username' => $username,
                        'city' => $faker_users->city,
                        'address' => $faker_users->address,
                        'email' => $name.$faker_users->randomElement(['@gmail.com']),
                        'password' => $faker_users->randomElement(['$2y$10$vr6ZIP3Jp1g2YHn5VSSFkuaUKsJXHTmkfU3cM5EpFtQ7HSp1wfbna']),
                        'phone' => "082".$faker_users->numberBetween(100000000,500000000),
                        'birthday' => $faker_users->date,
                        'gender' => $faker_users->randomElement(['Male', 'Female']),
                        'roles' => $roles,
                        'remember_token' => $faker_users->sha1,
                        'status' => $status,
                    ]);

                    foreach (range(1,$jumlah_service) as $key => $value) {
                        $code = "S-".$faker_service->unique()->numberBetween(10000,50000);
                        if ($roles == 'Vendor') {
                            DB::table('service')->insert([
                                'code' => $code,
                                'vendor' => $username,
                                'car' => $name_car,
                                'description' => $faker_service->text,
                                'city' => $city,
                                'price' => $faker_service->randomElement(['50000', '100000', '150000', '200000']),
                                'is_active' => $faker_service->numberBetween(0,1),
                            ]);
                        }

                        foreach (range(1,$jumlah_order) as $key => $value) {
                            DB::table('order')->insert([
                                'code' => "O-".$faker_order->unique()->numberBetween(10000,50000),
                                'service_code' => $code,
                                'user' => $username,
                                'order_date' => $faker_order->date,
                                'order_time' => $faker_order->time,
                                'days' => $faker_order->randomDigit,
                                'end_date' => $faker_order->date,
                                'address_order' => $faker_order->address,
                                'city' => $city,
                                'car' => $name_car,
                                'description' => $faker_order->text,
                                'price_total' => $faker_order->randomElement(['50000', '100000', '150000', '200000']),
                                'status' => $faker_order->numberBetween(1,2),
                            ]); 
                        }  
                    }
                }
            }
        }
    }

}