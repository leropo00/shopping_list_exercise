<?php

namespace App\Http\Controllers;

use App\Models\PurchaseListEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function get(Request $request)
    {
        $headers = [
            "Content-Type" => "text/event-stream",
            "Cache-Control" => "no-cache",
            "Connection" => "keep-alive",
            "X-Accel-Buffering" => "no",
        ];

        return response()->stream(function () {
            while (true) {
                // Fetch the unread notifications for the authenticated user
                $notifications = PurchaseListEvent::all();

                // If there are notifications, send them to the frontend
                if ($notifications->isNotEmpty()) {
                    // Format notifications as JSON and send them via SSE
                    echo "data: " . json_encode($notifications) . "\n\n";
                }

                PurchaseListEvent::truncate();

                // Flush the output buffer
                ob_flush();
                flush();

                // Sleep for a few seconds before checking again
                sleep(5);
            }
        }, 200, $headers);
    }
}
