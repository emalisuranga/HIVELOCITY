<?php

class FinalResult {

    public static $error_msg = [
        "ACCT_NUM_MISSING" => "Bank account number missing",
        "BANK_CODE_MISSING" => "Bank branch code missing",
        "E2E_ID_MISSING" => "End to end id missing",
    ];

    function results($f) {
        $d = fopen($f, "r");
        $h = fgetcsv($d);
        $rcs = [];
        while (($r = fgetcsv($d)) !== false) {
            if(count($r) == 16) {
                $amt = !$r[8] || $r[8] == "0" ? 0 : (float) $r[8];
                $ban = !$r[6] || $r[6] == "0" ? self::$error_msg['ACCT_NUM_MISSING'] : (int) $r[6];
                $bac = !$r[2] || $r[2] == "0" ? self::$error_msg['BANK_CODE_MISSING'] : $r[2];
                $e2e = !$r[10] && !$r[11] ? self::$error_msg['E2E_ID_MISSING'] : $r[10] . $r[11];
                $rcd = [
                    "amount" => [
                        "currency" => $h[0],
                        "subunits" => (int) ($amt * 100)
                    ],
                    "bank_account_name" => str_replace(" ", "_", strtolower($r[7])),
                    "bank_account_number" => $ban,
                    "bank_branch_code" => $bac,
                    "bank_code" => $r[0],
                    "end_to_end_id" => $e2e,
                ];
                $rcs[] = $rcd;
            }
        }
        $rcs = array_filter($rcs);
        return [
            "filename" => basename($f),
            "document" => $d,
            "failure_code" => $h[1],
            "failure_message" => $h[2],
            "records" => $rcs
        ];
    }
}

?>
