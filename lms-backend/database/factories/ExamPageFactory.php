<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExamPageFactory extends BaseFactory
{
    private static $youtubeUrls = [
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
     * Indicate that the exam media is using image.
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
     * Indicate that the exam media is using video.
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
     * Indicate that the exam is using answer content type.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function answer()
    {
        return $this->state(function (array $attributes) {
            $data = json_decode($attributes['data'], true);
            $data["keyword"] = $this->faker->text(10);
            $data["content"] = $data['content'] . '<p>
            Jawaban benar: `' . $data['keyword'] . '`</p>';
            return [
                'data' => json_encode($data),
                'content_type' => 'answer',
            ];
        });
    }

    /**
     * Indicate that the exam is using fill_blank content type.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function fillBlank()
    {
        return $this->state(function (array $attributes) {
            $data = json_decode($attributes['data'], true);
            $randomWordsLength = rand(10, 20);
            $randomWords = $this->faker->unique()->words($randomWordsLength);
            $data["answer"] = [];

            $random_fillable_number = rand(2, 5);
            $random_index = array_rand($randomWords, $random_fillable_number);
            $random_values = $this->faker->unique()->words($random_fillable_number);
            sort($random_index);
            for ($i = 0; $i < $random_fillable_number; $i++) {
                $index = $random_index[$i];
                $key = $randomWords[$index];
                $value = $random_values[$i];
                array_push($data['answer'], [
                    "key" => '___' . $key . '___',
                    "value" => $value,
                ]);
                $randomWords[$index] = '___' . $randomWords[$index] . '___' . ' (jawaban: ' . $value . ')';
            }
            $data["content"] = '<p>' . join(' ', $randomWords) . '</p>';
            return [
                'data' => json_encode($data),
                'content_type' => 'fill_blank',
            ];
        });
    }

    /**
     * Indicate that the exam is using multiple choice.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function multipleChoice()
    {
        return $this->state(function (array $attributes) {
            $data = json_decode($attributes['data'], true);
            $data["choices"] = [];
            $minimum_have_one_answer = false;
            $minimum_have_one_wrong_answer = false;
            for ($i = 0; $i < rand(2, 4); $i++) {
                $checked = $this->faker->boolean();
                $answer = $this->faker->text(20);
                if ($checked) {
                    $minimum_have_one_answer = true;
                    $answer = $answer . ' (pilihan benar)';
                } else {
                    $minimum_have_one_wrong_answer = true;
                }
                array_push(
                    $data['choices'],
                    [
                        'checked' => $checked,
                        'answer' => $answer,
                    ],
                );
            }

            if (!$minimum_have_one_answer) {
                $data['choices'][0]['checked'] = true;
                $data['choices'][0]['answer'] = $data['choices'][0]['answer'] . ' (pilihan benar)';
            }

            if (!$minimum_have_one_wrong_answer) {
                $data['choices'][0]['checked'] = false;
                $data['choices'][0]['answer'] = $data['choices'][0]['answer'] . ' (pilihan salah)';
            }

            return [
                'data' => json_encode($data),
                'content_type' => 'multiple_choice',
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
        $contentList = [
            "content",
            "multiple_choice",
            "fill_blank",
            "answer",
        ];
        $data = [
            "content" => $this->faker->paragraph(),
        ];
        $tooltips = [
            // [
            //     "key" => "",
            //     "value" => "",
            //     "message" => "",
            // ],
        ];
        return [
            'name' => $this->faker->text(20),
            'data' => json_encode($data),
            'media_type' => 'null',
            'media_link' => '',
            'content_type' => 'content',
            'tooltips' => json_encode($tooltips),
        ];
    }
}
