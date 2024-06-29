<?php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Http;
    function dataGet() {
        // Api'den Veri Çekme
        $response = Http::get(env("API_URL_GENERATE"));
        if ($response->successful()) {
            $response = $response->json();
            if ($response["isSuccess"]) {
                $marketNameAllowed = ["Maç Sonucu", "Altı/Üstü 2,5", "Karşılıklı Gol"];
                foreach ($response["data"] as $value) {
                    if (in_array($value['marketName'], $marketNameAllowed)) {
                        $bet = Bet()->where('marketNo', $value['marketNo'])->first();
                        if (!$bet) {
                            $value['addedToCoupon'] = EnumProjectAddedToCoupon::No;
                            $value['score']         = "-:-";
                            $value['status']        = EnumProjectStatus::Pending;
                            $value['created_at']    = now()->format("Y-m-d H:i:s");
                            $value['updated_at']    = now()->format("Y-m-d H:i:s");
                            Bet()->insert($value);
                        }
                    }
                }
            }
        }
        // Kupona Eklenmemiş Ve Tarihi Geçmiş Bahisleri Silme
        Bet()->where('addedToCoupon', EnumProjectAddedToCoupon::No)->where('eventDate', '<', now()->toIso8601String())->delete();
        // Kupona Eklenmemiş Bahisleri Geri Döndürme
        return Bet()->where('addedToCoupon', EnumProjectAddedToCoupon::No);
    }
    function dataGenerate() {
        $data = array_unique(dataGet()->pluck(eventName, marketNo)->toArray());
        $data = array_chunk($data, 4, true);
        foreach ($data as $value) {
            if (count($value) == 4) {
                $odd          = 1;
                $marketNoData = [];
                foreach ($value as $marketNo => $eventName):
                    $bet                = Bet()->where([marketNo => $marketNo])->first();
                    $bet->addedToCoupon = EnumProjectAddedToCoupon::Yes;
                    $bet->score         = "-:-";
                    $bet->status        = EnumProjectStatus::Pending;
                    $bet->save();
                    $odd            = $odd * $bet->odd;
                    $marketNoData[] = $marketNo;
                endforeach;
                $coupon         = Coupon();
                $coupon->no     = rand(123456789, 999999999);
                $coupon->status = EnumProjectStatus::Pending;
                $coupon->data   = implode(",", $marketNoData);
                $coupon->odd    = number_format($odd, 2);
                $coupon->save();
            }
        }
        echo "dataGenerate-Ok<br>";
    }
    function dataCheck() {
        // Array Içinde Arama Yapan Yardımcı Fonksiyon
        function helperSearchInArray($array, $key, $value) {
            if (is_array($array)) {
                if (isset($array[$key]) && $array[$key] == $value) {
                    return $array;
                }
                foreach ($array as $child) {
                    $result = helperSearchInArray($child, $key, $value);
                    if ($result) {
                        return $result;
                    }
                }
            }
            return [];
        }
        // Belirli Bir Tarih Için Api'den Veri Çekme Ve Işleme Fonksiyonu
        function helperFetchAndProcessData($date) {
            $response = Http::get(env("API_URL_CHECK").$date->format("Y-m-d"));
            if ($response->successful()) {
                $data = $response->json();
                if ($data["isSuccess"]) {
                    $matches = $data["data"]["matches"];
                    foreach (Bet()->get() as $bet) {
                        $search = helperSearchInArray($matches, "sgId", $bet->eventId);
                        if (!empty($search)) {
                            $home       = $search["sc"]["ht"]["r"] ?? "-";
                            $away       = $search["sc"]["at"]["r"] ?? "-";
                            $bet->score = $home.":".$away;
                            $bet->save();
                        }
                    }
                }
            }
            echo $date."<br>";
        }
        // Eski Bahislerin Durumlarını Güncelleme Fonksiyonu
        function helperUpdateBetStatus() {
            foreach (Bet()->get() as $bet) {
                $score = explode(":", $bet->score);
                $home  = $score[0];
                $away  = $score[1];
                if ($home != "-" && $away != "-") {
                    switch ($bet->marketName) {
                        case "Maç Sonucu":
                            if (($bet->outcomeName == "1" && $home > $away) || ($bet->outcomeName == "0" && $home == $away) || ($bet->outcomeName == "2" && $home < $away)) {
                                $bet->status = EnumProjectStatus::Win;
                            }
                            else {
                                $bet->status = EnumProjectStatus::Lost;
                            }
                            break;
                        case "Karşılıklı Gol":
                            if (($bet->outcomeName == "Var" && $home > 0 && $away > 0) || ($bet->outcomeName == "Yok" && ($home < 1 || $away < 1))) {
                                $bet->status = EnumProjectStatus::Win;
                            }
                            else {
                                $bet->status = EnumProjectStatus::Lost;
                            }
                            break;
                        case "Altı/Üstü 2,5":
                            if (($bet->outcomeName == "Üst" && ($home + $away) > 2) || ($bet->outcomeName == "Alt" && ($home + $away) < 3)) {
                                $bet->status = EnumProjectStatus::Win;
                            }
                            else {
                                $bet->status = EnumProjectStatus::Lost;
                            }
                            break;
                    }
                    $bet->save();
                }
            }
        }
        // Önceki Gün Için Bahisleri Çek Ve Işle
        helperFetchAndProcessData(now()->subDay());
        // Bahisleri Güncelle
        helperUpdateBetStatus();
        // Bugün Için Bahisleri Çek Ve Işle
        helperFetchAndProcessData(now());
        // Bahisleri Güncelle
        helperUpdateBetStatus();
        echo "dataCheck-Ok<br>";
    }
    Route::get('get', function () {
        dataCheck();
        dataGenerate();
    });







