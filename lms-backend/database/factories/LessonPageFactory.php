<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LessonPageFactory extends BaseFactory
{
    private $youtubeUrls = [
        '88nF129NUlI',
        'A4mV-okHF3o',
        'uyeUvLju9jo',
        'S_4PrI8gB-k',
        'KI_hDS4WiP8',
        'KNnsZcYtrNY',
        'NQUBD8xpdXg',
        'WPZn4rbiB8g',
        '8iIv8ugfNhk',
        'DoPKssRaoIQ',
        'i9GPRUpSW0U',
        'mfqyCf896JA',
        'FTdCDfEu6x0',
        '4KB1805XT3o',
        'c8dcJUc0avQ',
        'fU_mbDy6NYs',
        '1yuRLKfbTZ8',
        'F5Gfzz6bVzU',
        'ZzAEdyZKnDA',
        'nV86RL_nR54',
        '0veLrwd9CK4',
        'zwDfwrHuMRI',
        'P1dEjQn2nwQ',
        't5jg87Xb1_I',
        'kqhhukkGbF8',
        '0nw05UkBAXQ',
        'xOt2GYAD2Mg',
        'F9U_dGTd0Mw',
        'whNfVjn5_vA',
        'gu_TAhYWVms',
        'wtk_i17hqKA',
        '6mhnZrCZc-I',
        'RhsakEMQP6o',
        'cP6LmPGsYMQ',
        'E_li_TNwOEk',
        'IKGIrQWSs8o',
        'jyMvQEOtGPc',
        'hh6DmEUXh5g',
        'vVuss7Yh3Ck',
        'N3zablkrjJQ',
        '7f8CWUA3uHQ',
        'E6Vrvunjccc',
        '_i0E65hzW2M',
        'JZ1ZoR0Y4SU',
        'SefH7e0eu3w',
        'YP28IJxR2Ns',
        '6qYKXLr28zo',
        'DmjPwXK9o9k',
        'wjxYTl2d7xg',
        '9w6ZXq9rgmo',
        'z_ZnXIMKj30',
        'rkpOQTTgpKk',
        'vhv21r6r7Zc',
        'Ndo7rrM9njU',
        'oGE9MmG4zhk',
        'C-s5t8Mu0_8',
        'LZM18ZK9mOY',
        'oZkHXI_aONE',
        'yyelkrBWE2Q',
        'Ora_1kTKFzk',
        'E58CucUPCUw',
        '5Vc2FHwMZeo',
        'SGsGYcnpV50',
        'bfHJUPrahkY',
        'mHrtgJJcN30',
        'jsGCdgqVQvQ',
        'TaWSqboZr1w',
        'aNT7BqX4raY',
        'UGQqviwDnLQ',
        'jj3Rz4h-DRs',
        'y4wXSmo3jpQ',
        'OTipLmqUSRY',
        'UCgERDCYii8',
        'rm8mHeGh5pM',
        'VYIl9EJXJ5c',
        'Ev373c7wSRg',
        'WcTbl5qHkCc',
        'auDVnBW0Kd8',
        'SJjhzyV7Wak',
        'Y6SpccveFi8',
        '5arOANbW-L4',
        'xlU4wCtOlm4',
        '_dm5_2SwShc',
        'c1OHHfZv-dI',
        'wKWr-RL2h_8',
        'xipEx9AOfR8',
        'N7vgcCcEtts',
        'q_cVDVbY6Nc',
        'MqGLHluDoe0',
        '6nu9b9Wf0W4',
        '1IHb_8RV_cc',
        'QTuoq6Tr3gE',
        'HXkC4om1150',
        'GWk-ZpJdRFg',
        'AKbISnolNYA',
        'UnyLfqpyi94',
        'CVxP1oF4LdI',
        's8bcYrPn-X4',
        'PA1EAM2FxxQ',
        'M2FFYlKA_yE',
    ];
    /**
     * Indicate that the lesson page media is using image.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function imageMedia()
    {
        return $this->state(function (array $attributes) {
            return [
                'media_link' => $this->getRandomImagePath(1920, 1080),
                'media_type' => 'image',
            ];
        });
    }

    /**
     * Indicate that the lesson page media is using video.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function videoMedia()
    {
        return $this->state(function (array $attributes) {
            return [
                'media_link' => $this->faker->randomElement($this->youtubeUrls),
                'media_type' => 'video',
            ];
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $data = [
            "content" => $this->faker->paragraph(4),
        ];
        return [
            'name' => $this->faker->text(20),
            'data' => json_encode($data),
            'media_type' => 'null',
            'content_type' => 'content',
            'tooltips' => '[]',
            'sorter' => 0
        ];
    }
}
