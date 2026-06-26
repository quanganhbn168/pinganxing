<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Settings\GeneralSettings;

class SettingSeeder extends Seeder
{
    public function run(GeneralSettings $settings): void
    {
        $settings->site_name = 'Ping An Xing';
        $settings->company_name = 'CÔNG TY TNHH DỊCH VỤ PING AN XING';
        $settings->description = 'PING AN XING SERVICE COMPANY LIMITED';
        $settings->address = 'Căn nhà T1-07, KĐT Belhomes, KCN ĐT và DV VSIP Bắc Ninh, Phường Từ Sơn, Tỉnh Bắc Ninh, Việt Nam';
        $settings->phone = '0812161236';
        $settings->phone_display = '0812 161 236';
        $settings->email = 'pinganxingvn@gmail.com';
        $settings->business_code = '2301409047';
        
        $settings->save();
    }
}
