<?php

namespace App\Http\Controllers;

use App\Models\PurchaseListEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// workaround so that error Maximum execution time of 120 seconds exceeded does not happen
// meaning SSE works after 2 minutes
ini_set('max_execution_time', 0);

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

                // don't clear whole table, as it is possible new events might occur in between
                DB::table(TABLE_PURCHASE_LIST_EVENTS)->whereIn('id', $notifications->pluck('id')->all())->delete();

                // Flush the output buffer
                ob_flush();
                flush();

                // Sleep for a few seconds before checking again
                sleep(5);
            }
        }, 200, $headers);
    }
}
