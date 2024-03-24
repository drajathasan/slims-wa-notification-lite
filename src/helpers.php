<?php
use SLiMS\DB;

if (!function_exists('writeWaLog')) {
    function writeWaLog(string $content, string $providerResponse)
    {
        $insert = DB::query('insert into `whacenter_log` set `content` = ?, `provider_response` = ?, `created_at` = ?', [
            $content,
            $providerResponse,
            date('Y-m-d H:i:s')
        ]);

        $insert->run();
    }
}

if (!function_exists('parseMustache')) {
    function parseMustache(string $template, array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) continue;
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }
}

if (!function_exists('loadTemplate')) {
    function loadTemplate(string $pathTemplate)
    {
        $info = pathinfo($pathTemplate);
        $pathTemplate = WA_TEMPLATE . $info['filename'] . '.pt';
        if (file_exists($pathTemplate)) return file_get_contents($pathTemplate);
        throw new Exception(str_replace('{path}', $pathTemplate, __('Template {path} tidak ditemukan')));
    }
}
