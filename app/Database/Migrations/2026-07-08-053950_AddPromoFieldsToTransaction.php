<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPromoFieldsToTransaction extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transactions', [
            'biaya_jasa' => [
                'type'       => 'DOUBLE',
                'null'       => true,
            ],
            'voucher_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'diskon_voucher' => [
                'type'       => 'DOUBLE',
                'null'       => true,
            ],
            'free_mouse' => [
                'type'       => 'DOUBLE',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', ['biaya_jasa', 'voucher_code', 'diskon_voucher', 'free_mouse']);
    }
}