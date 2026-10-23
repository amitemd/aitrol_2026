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

    'accepted'             => 'O :atributo deve ser aceito.',
    'active_url'           => 'O atributo :não é um URL válido.
    ',
    'after'                => 'O :attribute deve ser uma data após :data.
    ',
    'after_or_equal'       => 'O atributo :deve ser uma data após ou igual a :data.',
    'alpha'                => 'O :atributo :só pode conter letras',
    'alpha_dash'           => 'O :atributo pode conter apenas letras, números e traços',
    'alpha_num'            => 'O :atributo pode conter apenas letras e números.',
    'array'                => 'O :atributo deve ser uma matriz.',
    'before'               => 'O :atributo deve ser uma data antes de :data.',
    'before_or_equal'      => 'O :atributo :deve ser uma data antes ou igual a :data.',
    'between'              => [
        'numeric' => 'O :atributo :deve estar entre :min e :max.',
        'file'    => 'O :atributo :deve estar entre :min e :max kilobytes.',
        'string'  => 'O atributo :deve estar entre os caracteres :min e :max.',
        'array'   => 'O atributo :deve ter entre :min e :max.',
    ],
    'boolean'              => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'A confirmação do atributo :não coincide.',
    'date'                 => 'O atributo :não é uma data válida.',
    'date_format'          => 'O atributo :não coincide com o formato :formato.',
    'different'            => 'O :atributo :e :outro deve ser diferente.',
    'digits'               => 'O :atributo :deve ser :digitos.',
    'digits_between'       => 'O atributo :deve estar entre os dígitos :min e :max.',
    'dimensions'           => 'O :atributo :tem dimensões de imagem inválidas.',
    'distinct'             => 'O campo :attribute tem um valor duplicado.',
    'email'                => 'O :atributo :deve ser um endereço de e-mail válido.',
    'exists'               => 'O :atributo :selecionado é inválido.',
    'file'                 => 'O :atributo :deve ser um arquivo.',
    'filled'               => 'O campo :attribute deve ter um valor.',
    'image'                => 'O :atributo :deve ser uma imagem.',
    'in'                   => 'O :atributo :selecionado é inválido.',
    'in_array'             => 'O campo :attribute não existe em :outro.',
    'integer'              => 'O :atributo :deve ser um número inteiro.',
    'ip'                   => 'O :atributo :deve ser um endereço IP válido.',
    'json'                 => 'O :atributo :deve ser uma string JSON válida.',
    'max'                  => [
        'numeric' => 'O atributo :não pode ser maior do que :max.',
        'file'    => 'O atributo :não pode ser maior do que :max. kilobytes.',
        'string'  => 'O atributo :não pode ser maior do que :max. caracteres.',
        'array'   => 'O atributo :não pode ter mais do que :max itens.',
    ],
    'mimes'                => 'O atributo :deve ser um arquivo do tipo: :valores.',
    'mimetypes'            => 'O atributo :deve ser um arquivo do tipo: :valores.',
    'min'                  => [
        'numeric' => 'O atributo :deve ser pelo menos :min.',
        'file'    => 'O :atributo deve ser pelo menos :min kilobytes.',
        'string'  => 'O atributo :deve ter, no mínimo, caracteres :min.',
        'array'   => 'O atributo :deve ter pelo menos :min itens.',
    ],
    'not_in'               => 'O atributo :selecionado é inválido.',
    'numeric'              => 'O :atributo :deve ser um número.',
    'present'              => 'O campo :attribute deve estar presente.',
    'regex'                => 'O formato :attribute é inválido.',
    'required'             => 'O campo :attribute é obrigatório.',
    'required_if'          => 'O campo :attribute é obrigatório quando :outro é :valor.',
    'required_unless'      => 'O campo :attribute é obrigatório a menos que :outro esteja em :valores.',
    'required_with'        => 'O campo :attribute é obrigatório quando :valores está presente.',
    'required_with_all'    => 'O campo :attribute é obrigatório quando :valores está presente.',
    'required_without'     => 'O campo :attribute é obrigatório quando :valores não estão presentes.',
    'required_without_all' => 'O campo :attribute é obrigatório quando nenhum dos :valores estiver presente.',
    'same'                 => 'O :atributo e :outros devem ser iguais.',
    'size'                 => [
        'numeric' => 'O :atributo :deve ser :tamanho.',
        'file'    => 'O :atributo :deve ser :size kilobytes.',
        'string'  => 'O :atributo :deve ser :size characters.',
        'array'   => 'O :atributo :deve conter :size items.',
    ],
    'string'               => 'O :atributo :deve ser uma cadeia.',
    'timezone'             => 'O :atributo :deve ser uma zona válida.',
    'unique'               => 'O :atributo :já foi tomado.',
    'uploaded'             => 'O :atributo :não foi carregado.',
    'url'                  => 'O formato :attribute é inválido.',

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
            'rule-name' => 'personalizado - mensagem',
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
