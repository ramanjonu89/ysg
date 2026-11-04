<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
    */
    public function run()
    {
        DB::table('cities')->truncate();
        $cities = [
            // Phil
            ['country_id' => 174,'name' => 'Abra','lat' => '17.580586517', 'lng'=> '120.803778104', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Agusan del Norte','lat' => '8.941555', 'lng'=> '125.518814', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Agusan del Sur','lat' => '8.379808026000035', 'lng'=> '125.74422292700001', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Aklan','lat' => '11.746161', 'lng'=> '122.231354', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Albay','lat' => '13.177483', 'lng'=> '123.528008', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Antique','lat' => '11.16667', 'lng'=> '122.08333', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Apayao','lat' => '18.08913328600005', 'lng'=> '121.1931516410001', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Aurora','lat' => '15.759318', 'lng'=> '121.563370', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Basilan','lat' => '6.56664289', 'lng'=> '122.028432784', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Bataan','lat' => '14.66667', 'lng'=> '120.41667', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Batanes','lat' => '20.448507', 'lng'=> '121.970813', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Batangas','lat' => '13.76235', 'lng'=> '121.05707', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Benguet','lat' => '16.422480', 'lng'=> '120.599007', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Biliran','lat' => '11.598058214', 'lng'=> '124.473333649', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Bohol','lat' => '9.851988126', 'lng'=> '124.19733955', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Bukidnon','lat' => '8.051505', 'lng'=> '124.922997', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Bulacan','lat' => '15', 'lng'=> '121.08333', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Cagayan','lat' => '18.248962', 'lng'=> '121.878784', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Camarines Norte','lat' => '14.104113', 'lng'=> '122.957787', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Camarines Sur','lat' => '13.66667', 'lng'=> '123.33333', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Camiguin','lat' => '9.1726303900001', 'lng'=> '124.719121795', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Capiz','lat' => '11.429194', 'lng'=> '122.597092', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Catanduanes','lat' => '13.782979932', 'lng'=> '124.236461079', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Cavite','lat' => '14.115286', 'lng'=> '120.962112', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Cebu','lat' => '10.31121', 'lng'=> '123.89237', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Cotabato','lat' => '7.012603', 'lng'=> '125.091766', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Davao de Oro','lat' => '7.196276', 'lng'=> '125.461807', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Davao del Norte','lat' => '7.196276', 'lng'=> '125.461807', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Davao del Sur','lat' => '7.207573,', 'lng'=> '125.395874', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Davao Occidental','lat' => '7.207573', 'lng'=> '125.395874', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Davao Oriental','lat' => '7.222524836', 'lng'=> '126.300757674', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Dinagat Islands','lat' => '10.170992998', 'lng'=> '125.605288279', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Eastern Samar','lat' => '11.656530106', 'lng'=> '125.380644842', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Guimaras','lat' => '10.500837', 'lng'=> '122.650352', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Ifugao','lat' => '16.988522', 'lng'=> '121.491318', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Ilocos Norte','lat' => '18.197214912', 'lng'=> '120.72842566', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Ilocos Sur','lat' => '17.33333', 'lng'=> '120.58333', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Kalinga','lat' => '17.492973', 'lng'=> '121.605492', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Iloilo','lat' => '10.720321', 'lng'=> '122.562019', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Isabela','lat' => '6.69073', 'lng'=> '122.02324', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'La Union','lat' => '16.584761340000057', 'lng'=> '120.42445308200001', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Laguna','lat' => '14.16667', 'lng'=> '121.33333', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Lanao del Norte','lat' => '8.021705749', 'lng'=> '124.052534468', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Lanao del Sur','lat' => '7.823176', 'lng'=> '124.419823', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Leyte','lat' => '10.862454', 'lng'=> '124.881119', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Maguindanao','lat' => '7.04005582', 'lng'=> '124.381137732', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Marinduque','lat' => '13.41667', 'lng'=> '121.95', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Masbate','lat' => '12.33404', 'lng'=> '123.57781', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Misamis Occidental','lat' => '8.150000', 'lng'=> '123.849998', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Misamis Oriental','lat' => '8.477217', 'lng'=> '124.645920', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Mountain Province','lat' => '17.099670454', 'lng'=> '121.133746115', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Negros Occidental','lat' => '10.309462339', 'lng'=> '122.986406344', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Negros Oriental','lat' => '9.239182', 'lng'=> '123.264412', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Northern Samar','lat' => '12.33333', 'lng'=> '124.66667', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Nueva Ecija','lat' => '15.58333', 'lng'=> '21', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Nueva Vizcaya','lat' => '16.302700302', 'lng'=> '121.158662346', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Occidental Mindoro','lat' => '12.967234981', 'lng'=> '120.891399852', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Oriental Mindoro','lat' => '13', 'lng'=> '121.08333', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Palawan','lat' => '9.981077885', 'lng'=> '118.748469306', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Pampanga','lat' => '15.058517442000038', 'lng'=> '120.64434449800001', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Pangasinan','lat' => '15.920000', 'lng'=> '120.330002', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Quezon','lat' => '14.676208', 'lng'=> '121.043861', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Quirino','lat' => '6.7015900000001', 'lng'=> '124.74599', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Rizal','lat' => '14.625483', 'lng'=> '121.124481', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Romblon','lat' => '12.57555', 'lng'=> '122.27022', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Samar','lat' => '11.83333', 'lng'=> '25', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Sarangani','lat' => '6.125194', 'lng'=> '124.488831', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Siquijor','lat' => '9.2131200000001', 'lng'=> '123.51548', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Sorsogon','lat' => '12.933333', 'lng'=> '123.683334', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'South Cotabato','lat' => '6.116243', 'lng'=> '125.171738', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Southern Leyte','lat' => '10.131400', 'lng'=> '124.858482', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Sultan Kudarat','lat' => '7.2707800000001', 'lng'=> '124.32612', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Sulu','lat' => '5.9496100530001', 'lng'=> '121.054129062', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Surigao del Norte','lat' => '9.5256417420001', 'lng'=> '125.657565', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Surigao del Sur','lat' => '8.383715', 'lng'=> '126.371475', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 174,'name' => 'Tarlac','lat' => '15.47799', 'lng'=> '120.58631', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Tawi-Tawi','lat' => '5.125599654', 'lng'=> '119.922785', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Zamboanga del Norte','lat' => '8.048020337', 'lng'=> '122.805433077', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Zambales','lat' => '15.290193519', 'lng'=> '120.141741172', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Zamboanga del Sur','lat' => '7.9043', 'lng'=> '123.3194', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 174,'name' => 'Zamboanga Sibugay','lat' => '7.7627000000001', 'lng'=> '122.54356', 'created_at' => date('Y-m-d h:i:s')],


            // malay
            ['country_id' => 133,'name' => 'Johor','lat' => '1.937344', 'lng'=> '103.366585', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Kedah','lat' => '6.155672', 'lng'=> '100.569649', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Kelantan','lat' => '6.127463', 'lng'=> '102.242287', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Malacca','lat' => '2.200844', 'lng'=> '102.240143', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Negeri Sembilan','lat' => '2.731813', 'lng'=> '102.252502', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 133,'name' => 'Pahang','lat' => '3.974341', 'lng'=> '102.438057', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Penang','lat' => '5.285153', 'lng'=> '100.456238', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Perak','lat' => '4.693950', 'lng'=> '101.117577', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Perlis','lat' => '6.443589', 'lng'=> '100.216599', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Sabah','lat' => '5.420404', 'lng'=> '116.796783', 'created_at' => date('Y-m-d h:i:s')],

            ['country_id' => 133,'name' => 'Sarawak','lat' => '2.592310411', 'lng'=> '113.30026644', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Selangor','lat' => '3.509247', 'lng'=> '101.524803', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Terengganu','lat' => '5.3310300000001', 'lng'=> '103.13828', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Kuala Lumpur','lat' => '3.140853', 'lng'=> '101.693207', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Labuan','lat' => '5.285153', 'lng'=> '115.247787', 'created_at' => date('Y-m-d h:i:s')],
            ['country_id' => 133,'name' => 'Putrajaya','lat' => '2.93527', 'lng'=> '101.69112', 'created_at' => date('Y-m-d h:i:s')],

            // singapore
            ['country_id' => 195,'name' => 'Singapore','lat' => '1.3521', 'lng'=> '103.8198', 'created_at' => date('Y-m-d h:i:s')],

        ];
        DB::table('cities')->insert($cities);
    }
}
