<?php

use App\Models\HistoryPayment;
use Illuminate\Support\Facades\DB;

if (!function_exists('updateProcess')) {
    function updateProcess($user_id, $note, $coin, $type_coin, $type_account)
    {
        $data = [
            'user_id' => $user_id,
            'note' => $note,
            'coin' => $coin,
            'type_coin' => $type_coin,
            'type_account' => $type_account,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        HistoryPayment::create($data);
    }
}
