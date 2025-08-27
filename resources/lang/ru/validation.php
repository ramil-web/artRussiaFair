<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Поле :attribute должно быть принято.',
    'active_url' => 'Поле :attribute должно содержать правильный URL.',
    'after' => 'Поле :attribute должно быть датой после :date.',
    'alpha' => 'Поле :attribute должно содержать только буквы.',
    'alpha_dash' => 'Поле :attribute должно содержать только буквы, числа и тире.',
    'alpha_num' => 'Поле :attribute должно содержать только буквы и числа.',
    'array' => 'Поле :attribute должно быть массивом.',
    'before' => 'Поле :attribute должно быть датой до :date.',
    'between' => [
        'numeric' => 'Поле :attribute должно быть от :min до :max.',
        'file' => 'Размер файла :attribute должен быть от :min до :max Кб.',
        'string' => 'Поле :attribute должно содержать от :min до :max символов.',
        'array' => 'Поле :attribute должно содержать от :min до :max позиций.',
    ],
    'boolean' => 'Поле :attribute должно содержать логическое значение истина или ложь.',
    'confirmed' => 'Поле :attribute должно быть отмечено.',
    'date' => 'Поле :attribute должно содержать дату.',
    'date_format' => 'Поле :attribute должно содержать дату в формате :format.',
    'different' => 'Поле :attribute и :other должны отличаться.',
    'digits' => 'Поле :attribute должно содержать :digits чисел.',
    'digits_between' => 'Поле :attribute должно содержать от :min до :max чисел.',
    'email' => 'Поле :attribute должно содержать правильный E-mail.',
    'filled' => 'Поле :attribute обязательно для заполнения.',
    'exists' => 'Поле выбора :attribute не существует.',
    'image' => 'Поле :attribute должно быть изображением.',
    'in' => 'Поле выбора :attribute содержит неправильные значения.',
    'integer' => 'Поле :attribute должно содержать целое число.',
    'ip' => 'Поле :attribute должно быть правильным IP адресом.',
    'max' => [
        'numeric' => 'Поле :attribute должно быть меньше :max.',
        'file' => 'Размер файла :attribute должен быть меньше :max Кб.',
        'string' => 'Поле :attribute должно содержать меньше :max символов.',
        'array' => 'Поле :attribute должно содержать меньше :max позиций.',
    ],
    'mimes' => 'Поле :attribute должно быть файлом типа: :values.',
    'min' => [
        'numeric' => 'Поле :attribute должно быть больше :min.',
        'file' => 'Размер файла :attribute должен быть больше :min Кб.',
        'string' => 'Поле :attribute должно содержать больше :min символов.',
        'array' => 'Поле :attribute должно содержать больше :min позиций.',
    ],
    'not_in' => 'Поле выбора :attribute содержит неправильные значения.',
    'numeric' => 'Поле :attribute должно быть числом.',
    'regex' => 'Поле :attribute имеет неправильный формат.',
    'required' => 'Поле :attribute обязательно для заполнения.',
    'required_if' => 'Поле :attribute обязательно для заполнения, так как :other имеет значение :value.',
    'required_with' => 'Поле :attribute обязательно для заполнения, так как присутствует :values.',
    'required_with_all' => 'Поле :attribute обязательно для заполнения, так как присутствуют :values.',
    'required_without' => 'Поле :attribute обязательно для заполнения, так как значение :values не присутствует.',
    'required_without_all' => 'Поле :attribute обязательно для заполнения, так как значения :values не присутствуют.',
    'same' => 'Поле :attribute и :other должны совпадать.',
    'size' => [
        'numeric' => 'Поле :attribute должно быть :size.',
        'file' => 'Размер файла :attribute должен быть :size Кб.',
        'string' => 'Поле :attribute должно содержать :size символов.',
        'array' => 'Поле :attribute должно содержать :size позиций.',
    ],
    'timezone' => 'Поле :attribute должно содержать правильный часовой пояс.',
    'unique' => 'Поле :attribute уже используется (не уникально).',
    'url' => 'Поле :attribute имеет некорректный формат.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
