<?php
namespace SLiMS\Wa\Notification;
use SLiMS\Http\Client;

abstract class BaseHook {
    public function send(array $data, string $message)
    {
        if (substr($data['member_phone'], 0,1) == 0) {
            $data['member_phone'] = '+62' . substr($data['member_phone'], 1, strlen($data['member_phone']));
        }

        // whacenter request
        $request = Client::post('https://app.whacenter.com/api/send', [
            'device_id' => config('whacenter.device_id'),
            'number' => $data['member_phone'],
            'message' => $message
        ]);

        $response = json_decode($request->getContent());

        // if (isDev()) dd($request->getContent());

        writeWaLog($message, $request->getContent());
    }
}