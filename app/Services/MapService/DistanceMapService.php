<?php

namespace App\Services\MapService;

use App\Enums\Constant;
use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Support\Facades\Log;

class DistanceMapService
{
    public function setLocation($data)
    {
        $location = json_decode($this->getLocation($data));

        if ($location) {

            // Location by detail
            $lat = $location->results[0]->geometry->location->lat;
            $lon = $location->results[0]->geometry->location->lng;

            $address['lat'] = $lat;
            $address['lon'] = $lon;
        } else {

            // Location not available
            $address['lat'] = null;
            $address['lon'] = null;
        }

        return $address;
    }

    public function getLocation($address): bool|string
    {
        $ward = Ward::where('code', $address['ward_given_code'])->first()->full_name ?? '';
        $district = District::where('code', $address['district_given_code'])->first()->full_name ?? '';
        $province = Province::where('code', $address['province_given_code'])->first()->full_name ?? '';
        $detail = $address['detail_address'] ?? '';

        $endpoint = env('GOONG_API_ENDPOINT', 'https://rsapi.goong.io/geocode');;
        $data['address'] = "{$province}, {$district}, {$ward}, {$detail}";
        $data['api_key'] = env('GOONG_API_KEY', 'GLWFmwpnMPaiXMqtTwJfly2hrxzhhnPZ6D5lztWS');

        $url = $endpoint . '?' . http_build_query($data);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        return curl_exec($ch);
    }

    public function measureDistance($data, $lat, $lon, $distance): bool|array
    {
        $endpoint = env('GOONG_API_DISTANCT_ENDPOINT', 'https://rsapi.goong.io/DistanceMatrix');
        $origins = "{$lat},{$lon}";
        $destinations = "{$data['lat']},{$data['lon']}";
        $car= 'car';
        $token = env('GOONG_API_KEY', 'GLWFmwpnMPaiXMqtTwJfly2hrxzhhnPZ6D5lztWS');

        $data['origins'] = '&origins=' . $origins;
        $data['destinations'] = '&destinations=' . $destinations;
        $data['vehicle'] = '&vehicle=' . $car;
        $data['api_key'] = '&api_key=' . $token;

        $url = $endpoint . '?' . $data['origins'] . $data['destinations'] . $data['vehicle'] . $data['api_key'];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        $status = $result->status ?? true;

        if ($status === Constant::DISTANCE_MAP_NOT_FOUND) {
            return false;
        } else {
            $actualDistance = ($result)->rows[0]->elements[0]->distance->value;
        }

        return $actualDistance >= $distance;
    }
}
