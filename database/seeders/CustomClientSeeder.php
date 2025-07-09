<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class CustomClientSeeder extends Seeder
{
    public function run()
    {
        $clientCompanies = [
            'Mabini Realty Group', 'Bayanihan Foods Corp.', 'Lakbay Travel Agency', 'Luntian Eco Solutions',
            'Silangan Tech Ventures', 'Gintong Ani Farms', 'Kaagapay Lending Inc.', 'Dalisay Water Refilling',
            'Matatag Logistics', 'Sikap Construction', 'Bagong Bayan Marketing', 'Pinoy Prime Holdings',
            'Agila Security Services', 'Malasakit Medical Clinic', 'Tibay Insurance Brokers', 'Bughaw Digital Solutions',
            'Pilipinas Prime Realty', 'Lakambini Events Management', 'Tagumpay Retailers', 'Isla Resort Group',
            'Kapitbahay Cooperative', 'Sulong Energy Corp.', 'Bayanihan Transport', 'Luzviminda Trading', 'Dalisay Learning Center'
        ];
        $contactPersons = [
            'Maria Santos', 'Jose Dela Cruz', 'Juanita Reyes', 'Antonio Ramos', 'Luzviminda Garcia',
            'Ramon Bautista', 'Carmela Aquino', 'Roberto Mendoza', 'Ligaya Flores', 'Andres Villanueva',
            'Rosario Castillo', 'Emilio Torres', 'Corazon Navarro', 'Benigno Salazar', 'Estrella Lim',
            'Fernando Tan', 'Isabel Mercado', 'Gregorio Uy', 'Victoria Chua', 'Pedro Sy',
            'Juliana Go', 'Manuel Ong', 'Patricia Co', 'Alfredo Dy', 'Teresa Lao'
        ];
        $cities = ['Quezon City', 'Manila', 'Makati', 'Pasig', 'Taguig', 'Cebu City', 'Davao City', 'Baguio', 'Iloilo City', 'Cagayan de Oro'];
        $provinces = ['Metro Manila', 'Cebu', 'Davao del Sur', 'Benguet', 'Iloilo', 'Misamis Oriental', 'Pampanga', 'Batangas', 'Laguna', 'Bulacan'];
        $businessSizes = ['Small', 'Medium', 'Large'];
        $paymentTerms = ['30 days', '45 days', '60 days', 'COD', 'EOM'];
        foreach ($clientCompanies as $i => $companyName) {
            $contactPerson = $contactPersons[$i] ?? 'Maria Santos';
            $email = Str::slug($companyName, '_') . '@client.ph';
            $username = Str::slug($companyName, '_');
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $companyName,
                    'username' => $username,
                    'password' => Hash::make('PASS_123'),
                    'role' => 'client',
                    'user_type' => 'company',
                    'email_verified_at' => now(),
                ]
            );
            Company::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $companyName,
                    'contact_person' => $contactPerson,
                    'email' => $email,
                    'username' => $username,
                    'mobile_number' => '+63' . rand(900, 999) . rand(1000000, 9999999),
                    'telephone_number' => '+63' . rand(2, 8) . rand(1000000, 9999999),
                    'street' => rand(1, 999) . ' ' . Arr::random(['Rizal', 'Bonifacio', 'Aguinaldo', 'Luna', 'Mabini', 'Burgos', 'Jacinto', 'Del Pilar']) . ' Street',
                    'city' => Arr::random($cities),
                    'state' => Arr::random($provinces),
                    'postal' => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'years_operation' => rand(1, 30),
                    'business_size' => Arr::random($businessSizes),
                    'service_areas' => Arr::random($provinces) . ', ' . Arr::random($cities),
                    'vat_registered' => rand(0, 1),
                    'use_sureprice' => rand(0, 1),
                    'payment_terms' => Arr::random($paymentTerms),
                    'designation' => 'client',
                    'status' => 'approved',
                ]
            );
        }
    }
} 