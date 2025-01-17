<?php

/*
 * Copyright 2018-2025 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class boost
{
    private $partner_key = '';
    private $service_url = '';

    public function __construct($key, $url)
    {
        $this->partner_key = $key;
        $this->service_url = $url;
    }

    public function def($data)
    {
        $aData = [
            'service' => 'boost',
            'period' => $data['period'],
            'address' => $data['address'],
            'game' => 'cs16',
        ];

        $out = json_decode($this->defaultcurl(json_encode($aData)), true);

        if ($out['message'] == 'Услуга уже присутствует') {
            $out = json_decode($this->defaultcurl(json_encode($aData), 'prolong'), true);
        }

        if (!array_key_exists('status', $out)) {
            ['error' => 'Не удалось приобрести услугу, повторите запрос позже.'];
        }

        if (!$out['status']) {
            return true;
        }

        return ['error' => $out['message']];
    }

    private function defaultcurl($data, $action = 'buy')
    {
        if (!($curl = curl_init())) {
            ['error' => 'FAIL: curl_init().'];
        }

        curl_setopt($curl, CURLOPT_URL, $this->service_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'key=' . $this->partner_key . '&action=' . $action . '&data=' . $data);

        $out = curl_exec($curl);

        curl_close($curl);

        return $out;
    }

    public function vipms($data)
    {
        $aData = [
            'format' => 'POST',
            'country' => 'RU',
            'hoster_id' => 1,
            'key' => $this->partner_key,
            'full_address' => $data['address'],
            'service_id' => $data['period'],
        ];

        return $this->othercurl($aData);
    }

    public function fulls($data)
    {
        $aData = [
            'format' => 'POST',
            'country' => 'RU',
            'hoster_id' => 1,
            'key' => $this->partner_key,
            'full_address' => $data['address'],
            'service_id' => $data['period'],
        ];

        return $this->othercurl($aData);
    }

    private function othercurl($aData)
    {
        if (!($curl = curl_init())) {
            ['error' => 'FAIL: curl_init().'];
        }

        curl_setopt($curl, CURLOPT_URL, $this->service_url . '?' . urldecode(http_build_query($aData)));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($aData)));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($curl);

        curl_close($curl);

        if ($result == 'OK') {
            return true;
        }

        $aErr = [
            1 => 'BAD_HOSTER_ID',
            2 => 'HOSTER_NOT_FOUND',
            3 => 'BAD_HOSTER_IP',
            4 => 'FORM_INVALID',
            5 => 'BAD_SERVICE_ID',
        ];

        return ['error' => $aErr[$result]];
    }
}
