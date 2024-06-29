<?php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Http;
    function dataGet() {
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
        Bet()->where('addedToCoupon', EnumProjectAddedToCoupon::No)->where('eventDate', '<', now()->toIso8601String())->delete();
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
    }
    function dataCheck() {
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
        function helperFetchAndProcessData($date) {
            $response = Http::get(env("API_URL_CHECK").$date->format("Y-m-d"));
            if ($response->successful()) {
                $data = $response->json();
                if ($data["isSuccess"]) {
                    $matches = $data["data"]["matches"];
                    foreach (Bet()->get() as $bet) {
                        $search = helperSearchInArray($matches, "sgId", $bet->eventId);
                        if (!empty($search)) {
                            $home       = $search["sc"]["ht"]["c"] ?? "-";
                            $away       = $search["sc"]["at"]["c"] ?? "-";
                            $bet->score = $home.":".$away;
                            $bet->save();
                        }
                    }
                }
            }
        }
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
        # ->
        helperFetchAndProcessData(now());
        helperFetchAndProcessData(now()->subDay());
        helperUpdateBetStatus();
        # ->
        foreach (Coupon()->where([status => EnumProjectStatus::Pending])->get() as $coupon) {
            $statusCoupon = EnumProjectStatus::Pending;
            foreach (Bet()->whereIn(marketNo, explode(",", $coupon->data))->orderBy(eventDate, "ASC")->get() as $bet) {
                if ($bet->finish == EnumProjectFinish::Yes and $bet->status == EnumProjectStatus::Lost):
                    $statusCoupon = EnumProjectStatus::Lost;
                endif;
            }
            $coupon->status = $statusCoupon;
            $coupon->save();
        }
    }
    # ->
    function isBetLive($matchStartTimeString) {
        $now            = now();
        $matchStartTime = now()::parse($matchStartTimeString);
        if ($now->lt($matchStartTime)) {
            return false;
        }
        $minutesPassed = $now->diffInMinutes($matchStartTime);
        return $minutesPassed >= 0 && $minutesPassed <= 150;
    }
    function isBetFinished($matchStartTimeString) {
        $now            = now();
        $matchStartTime = now()::parse($matchStartTimeString);
        if ($now->lt($matchStartTime)) {
            return false;
        }
        $minutesPassed = $now->diffInMinutes($matchStartTime);
        return $minutesPassed > 150;
    }
    # ->
    function checkBetLive() {
        foreach (Bet()->get() as $bet) {
            if (isBetLive($bet->eventDate)) {
                $bet->live = EnumProjectLive::Yes;
            }
            else {
                $bet->live = EnumProjectLive::No;
            }
            $bet->save();
        }
    }
    function checkBetFinished() {
        foreach (Bet()->get() as $bet) {
            if (isBetFinished($bet->eventDate)) {
                $bet->finish = EnumProjectLive::Yes;
            }
            else {
                $bet->finish = EnumProjectLive::No;
            }
            $bet->save();
        }
    }
    # ->
    Route::get('get', function () {
        try {
            dataGenerate();
            dataCheck();
            checkBetLive();
            checkBetFinished();
            $couponUpdate                = CouponUpdate();
            $couponUpdate->status_update = EnumProjectStatusUpdate::Success;
            $couponUpdate->save();
        } catch (\Exception $Ex) {
            $couponUpdate                = CouponUpdate();
            $couponUpdate->status_update = EnumProjectStatusUpdate::Error;
        }
    });
