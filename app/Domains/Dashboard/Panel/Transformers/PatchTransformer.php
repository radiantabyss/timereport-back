<?php
namespace App\Domains\Dashboard\Panel\Transformers;

class PatchTransformer extends Transformer
{
    public static function run($data) {
        $data['filters'] = encode_json($data['filters']);
        $data['layout'] = encode_json($data['layout']);
        $data = self::settings($data);
        return $data;
    }
}
