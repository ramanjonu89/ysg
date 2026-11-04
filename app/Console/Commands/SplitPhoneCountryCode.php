<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SplitPhoneCountryCode extends Command
{
    protected $signature = 'phone:split-country-code';
    protected $description = 'Split country_code from phone for users and addresses tables (users&addresses store "+84", "+65")';

    protected $countryCodes = [];

    /**
     * Load numeric country codes from countries table (84, 65, 880...)
     */
    private function loadCountryCodes()
    {
        $this->countryCodes = DB::table('countries')
            ->whereNotNull('country_code')
            ->pluck('country_code')
            ->map(function ($code) {
                return (string) $code;
            })
            ->toArray();

        usort($this->countryCodes, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        $this->info('Country codes (numeric) loaded from database.');
    }

    public function handle()
    {
        $this->info('Start splitting phone numbers for USERS table...');

        $this->loadCountryCodes();
        DB::table('users')->orderBy('id')->chunk(200, function ($users) {
            foreach ($users as $user) {
                $this->processRow('users', $user);
            }
        });

        $this->info('Start splitting phone numbers for ADDRESSES table...');

        DB::table('addresses')->orderBy('id')->chunk(200, function ($addresses) {
            foreach ($addresses as $address) {
                $this->processRow('addresses', $address);
            }
        });

        $this->info('Country code and phone number have been successfully split!');
        return 0;
    }

    /**
     * Process each row
     */
    private function processRow(string $table, $row)
    {
        if (!empty($row->country_code)) {
            $this->info("Skipped | Table: {$table} | ID: {$row->id} | Already has country_code");
            return;
        }

        if (empty($row->phone)) {
            return;
        }

        $result = $this->splitPhone($row->phone);

        if (!$result) {
            $this->warn("Country code not detected | Table: {$table} | ID: {$row->id} | Phone: {$row->phone}");
            return;
        }

        DB::table($table)
            ->where('id', $row->id)
            ->update([
                'country_code' => $result['country_code'],
                'phone'        => $result['phone'],
            ]);

        $this->info("Updated successfully | Table: {$table} | ID: {$row->id}");
    }

    /**
     * Split phone using numeric country codes from DB
     * Input example: +84901234567
     */
    private function splitPhone(string $phone): ?array
    {
        $phone = trim($phone);

        if (!str_starts_with($phone, '+')) {
            return null;
        }

        $number = substr($phone, 1);

        if (!ctype_digit($number)) {
            return null;
        }

        foreach ($this->countryCodes as $code) {
            if (str_starts_with($number, $code)) {
                $remainPhone = substr($number, strlen($code));

                if (!ctype_digit($remainPhone)) {
                    return null;
                }

                return [
                    'country_code' => '+' . $code,
                    'phone'        => substr($number, strlen($code)),
                ];
            }
        }

        return null;
    }
}
