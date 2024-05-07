<?php

function getLanguageField($lang = 'en') {
    // Define the mapping from language code to database field
    $langToField = [
        'en' => 'CountryNameEN',
        'de' => 'CountryNameDE',
        'fr' => 'CountryNameFR',
        'es' => 'CountryNameES',
        'it' => 'CountryNameIT',
        'zh' => 'CountryNameZH',
        'ja' => 'CountryNameJA',
        'ru' => 'CountryNameRU'
    ];

    // Return the appropriate field, defaulting to English
    return $langToField[$lang] ?? $langToField['en'];
}

function getTranslations($lang = 'en') {
    return [
        'raceTypes' => [
            'fixedTime' => [
                'en' => 'Fixed Time',
                'de' => 'Zeitlauf',
                'fr' => 'Course de durée',
                'es' => 'Tiempo fijo',
                'it' => 'Tempo fisso',
                'ru' => 'Фиксированное время',
                'zh' => '固定时间',
                'ja' => '固定時間',
                'sv' => 'Tidslopp'
            ],
            'fixedDistance' => [
                'en' => 'Fixed Distance',
                'de' => 'Distanzlauf',
                'fr' => 'Course de distance',
                'es' => 'Distancia fija',
                'it' => 'Distanza fissa',
                'ru' => 'Фиксированное расстояние',
                'zh' => '固定距离',
                'ja' => '固定距離',
                'sv' => 'Distanslopp'
            ],
            'backyardUltra' => [
                'en' => 'Backyard Ultra',
                'de' => 'Backyard Ultra',
                'fr' => 'Backyard Ultra',
                'es' => 'Backyard Ultra',
                'it' => 'Backyard Ultra',
                'ru' => 'Ультрамарафон на заднем дворе',
                'zh' => '后院超级马拉松',
                'ja' => 'バックヤードウルトラ',
                'sv' => 'Backyard Ultra'
            ],
            'stageRace' => [
                'en' => 'Stage Race',
                'de' => 'Etappenlauf',    
                'fr' => 'Course par étapes',
                'es' => 'Carrera por etapas',
                'it' => 'Corsa a tappe',
                'ru' => 'Этапная гонка',
                'zh' => '分段赛',
                'ja' => 'ステージレース',
                'sv' => 'Etapplopp'    
            ],

            'other' => [
                'en' => 'Other',
                'de' => 'Andere',
                'fr' => 'Autre',
                'es' => 'Otro',
                'it' => 'Altro',
                'ru' => 'Другое',
                'zh' => '其他',
                'ja' => 'その他',
                'sv' => 'Övrigt'
            ]
        ],
        'raceSurfaces' => [
            'trail' => [
                'en' => 'Trail',
                'de' => 'Trail',
                'fr' => 'Sentier',
                'es' => 'Sendero',
                'it' => 'Sentiero',
                'ru' => 'Трейл',
                'zh' => '小径',
                'ja' => 'トレイル',
                'sv' => 'Terräng'
            ],
            'road' => [
                'en' => 'Road',
                'de' => 'Straße',
                'fr' => 'Route',
                'es' => 'Carretera',
                'it' => 'Strada',
                'ru' => 'Дорога',
                'zh' => '道路',
                'ja' => 'ロード',
                'sv' => 'Väg'
            ],
            'track' => [
                'en' => 'Track',
                'de' => 'Bahn',
                'fr' => 'Piste',
                'es' => 'Pista',
                'it' => 'Pista',
                'ru' => 'Трек',
                'zh' => '跑道',
                'ja' => 'トラック',
                'sv' => 'Bana'
            ],
            'indoor' => [
                'en' => 'Indoor',
                'de' => 'Halle',
                'fr' => 'Intérieur',
                'es' => 'Interior',
                'it' => 'Interno',
                'ru' => 'В помещении',
                'zh' => '室内',
                'ja' => '屋内',
                'sv' => 'Inomhus'
            ]
        ],
        'disciplines' => [
            '50km' => [
                'en' => '50 km',
                'de' => '50 km',
                'fr' => '50 km',
                'es' => '50 km',
                'it' => '50 km',
                'ru' => '50 км',
                'zh' => '50公里',
                'ja' => '50キロメートル',
                'sv' => '50 km'
            ],
            '100mi' => [
                'en' => '100 miles',
                'de' => '100 Meilen',
                'fr' => '100 miles',
                'es' => '100 millas',
                'it' => '100 miglia',
                'ru' => '100 миль',
                'zh' => '100英里',
                'ja' => '100マイル',
                'sv' => '100 miles'
            ],
            '6h' => [
                'en' => '6 hours',
                'de' => '6 Stunden',
                'fr' => '6 heures',
                'es' => '6 horas',
                'it' => '6 ore',
                'ru' => '6 часов',
                'zh' => '6小时',
                'ja' => '6時間',
                'sv' => '6 timmar'
            ],
            '12h' => [
                'en' => '12 hours',
                'de' => '12 Stunden',
                'fr' => '12 heures',
                'es' => '12 horas',
                'it' => '12 ore',
                'ru' => '12 часов',
                'zh' => '12小时',
                'ja' => '12時間',
                'sv' => '12 timmar'
            ],
            '24h' => [
                'en' => '24 hours',
                'de' => '24 Stunden',
                'fr' => '24 heures',
                'es' => '24 horas',
                'it' => '24 ore',
                'ru' => '24 часа',
                'zh' => '24小时',
                'ja' => '24時間',
                'sv' => '24 timmar'
            ],
            '48h' => [
                'en' => '48 hours',
                'de' => '48 Stunden',
                'fr' => '48 heures',
                'es' => '48 horas',
                'it' => '48 ore',
                'ru' => '48 часов',
                'zh' => '48小时',
                'ja' => '48時間',
                'sv' => '48 timmar'
            ],
            '72h' => [
                'en' => '72 hours',
                'de' => '72 Stunden',
                'fr' => '72 heures',
                'es' => '72 horas',
                'it' => '72 ore',
                'ru' => '72 часа',
                'zh' => '72小时',
                'ja' => '72時間',
                'sv' => '72 timmar'
            ],
            '6d' => [
                'en' => '6 days',
                'de' => '6 Tage',
                'fr' => '6 jours',
                'es' => '6 días',
                'it' => '6 giorni',
                'ru' => '6 дней',
                'zh' => '6天',
                'ja' => '6日間',
                'sv' => '6 dagar'
            ],
            '10d' => [
                'en' => '10 days',
                'de' => '10 Tage',
                'fr' => '10 jours',
                'es' => '10 días',
                'it' => '10 giorni',
                'ru' => '10 дней',
                'zh' => '10天',
                'ja' => '10日間',
                'sv' => '10 dagar'
            ],
            '1000km' => [
                'en' => '1000 km',
                'de' => '1000 km',
                'fr' => '1000 km',
                'es' => '1000 km',
                'it' => '1000 km',
                'ru' => '1000 км',
                'zh' => '1000公里',
                'ja' => '1000キロメートル',
                'sv' => '1000 km'
            ],
            '1000mi' => [
                'en' => '1000 miles',
                'de' => '1000 Meilen',
                'fr' => '1000 miles',
                'es' => '1000 millas',
                'it' => '1000 miglia',
                'ru' => '1000 миль',
                'zh' => '1000英里',
                'ja' => '1000マイル',
                'sv' => '1000 miles'
            ]
        ]

    ];
}

function translate($category, $key, $lang = 'en') {
    $translations = getTranslations($lang);
    return $translations[$category][$key][$lang] ?? $key;  // Fallback to the key if no translation found
}
