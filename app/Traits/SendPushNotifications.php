<?php

namespace App\Traits;

use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

trait SendPushNotifications
{

    public string $fcm_server_key = "AAAAHc09_fE:APA91bHax86bRguGyLQ4__RFesdDF2MX158tnsXHC9mijcqNQZMl4Q0tMqu7H_JKz5Co2a8yiON5Mo0KZFHcAXjlsHP8KZ1jfA3SFgyoZwyKs35-QdqNpD7g5uIZm-sEqsOgWZYcAZ-B";

    //send a push notification to a user using their device token
    public function sendPushNotification($deviceToken, $title, $message, $data = null)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
      "to": "' . $deviceToken . '",
      "notification":{
    "title":"' . $title . '",
     "body":"' . $message . '",
     "icon":"https://adfamedicareservices.com/wp-content/uploads/2022/11/rsz_cropped-rsz_cropped-adfa-logo-prooved-png.png",
     "color":"#ffffff",
    "sound":"default"
},
   "data":{


   }

}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=' . $this->fcm_server_key,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
}
