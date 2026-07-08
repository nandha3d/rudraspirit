<?php

namespace App\Utility;

use App\Mail\InvoiceEmailManager;
use App\Models\User;
use App\Models\SmsTemplate;
use App\Http\Controllers\OTPVerificationController;
use App\Models\EmailTemplate;
use Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderNotification;
use App\Models\FirebaseNotification;

class NotificationUtility
{
    public static function sendOrderPlacedNotification($order, $request = null)
    {       
        //sends email to Customer, Seller and Admin with the invoice pdf attached
        $adminId = get_admin()->id;
        $userIds = array($order->seller_id);
        if($order->user->email != null){
            array_push($userIds, $order->user_id);
        }
        if ($order->seller_id != $adminId) {
            array_push($userIds, $adminId);
        }
        $users = User::findMany($userIds);
        foreach($users as $user){
            $emailIdentifier = 'order_placed_email_to_'.$user->user_type;
            $emailTemplate = EmailTemplate::whereIdentifier($emailIdentifier)->first();

            if($emailTemplate != null && $emailTemplate->status == 1){
                $emailSubject = $emailTemplate->subject;
                $emailSubject = str_replace('[[order_code]]', $order->code, $emailSubject);

                $array['view']      = 'emails.invoice';
                $array['subject']   = $emailSubject;
                $array['order']     = $order;
                if($emailTemplate->status == 1){
                    try {
                        Mail::to($user->email)->queue(new InvoiceEmailManager($array));
                    } catch (\Exception $e) {}
                }
            }   
        }

        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'order_placement')->first()->status == 1) {
            try {
                $otpController = new OTPVerificationController;
                $otpController->send_order_code($order);
            } catch (\Exception $e) {

            }
        }

        //sends Notifications to user
        self::sendNotification($order, 'placed');
        if ($request !=null && get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order placed !";
            $request->text = "An order {$order->code} has been placed";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            self::sendFirebaseNotification($request);
        }
    }

    public static function sendNotification($order, $order_status)
    {     
        $adminId = get_admin()->id;
        $userIds = array($order->user->id, $order->seller_id);
        if ($order->seller_id != $adminId) {
            array_push($userIds, $adminId);
        }
        $users = User::findMany($userIds);
        
        $order_notification = array();
        $order_notification['order_id'] = $order->id;
        $order_notification['order_code'] = $order->code;
        $order_notification['user_id'] = $order->user_id;
        $order_notification['seller_id'] = $order->seller_id;
        $order_notification['status'] = $order_status;

        foreach($users as $user){
            $notificationType = get_notification_type('order_'.$order_status.'_'.$user->user_type, 'type');
            if($notificationType != null && $notificationType->status == 1){
                $order_notification['notification_type_id'] = $notificationType->id;
                Notification::send($user, new OrderNotification($order_notification));
            }
        }
    }

    public static function sendFirebaseNotification($req)
    {
        // Push via FCM HTTP v1 (see config/firebase.php). Fails safe: if Firebase
        // is unconfigured or the send errors, it is logged and skipped so the
        // calling order flow is never affected.
        try {
            app(\App\Services\Firebase\FcmV1Client::class)->send(
                (string) $req->device_token,
                (string) $req->title,
                (string) $req->text,
                [
                    'item_type'    => (string) $req->type,
                    'item_type_id' => (string) $req->id,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
            );
        } catch (\Throwable $e) {
            \Log::warning('sendFirebaseNotification failed: ' . $e->getMessage());
        }

        // Persist the in-app notification record regardless of push delivery
        // (unchanged behaviour — this powers the app's notification list).
        $firebase_notification = new FirebaseNotification;
        $firebase_notification->title = $req->title;
        $firebase_notification->text = $req->text;
        $firebase_notification->item_type = $req->type;
        $firebase_notification->item_type_id = $req->id;
        $firebase_notification->receiver_id = $req->user_id;

        $firebase_notification->save();
    }
}
