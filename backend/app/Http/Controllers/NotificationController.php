<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\PurchaseListEvent;

// workaround so that error Maximum execution time of 120 seconds exceeded does not happen
// meaning SSE works after 2 minutes
ini_set('max_execution_time', 0);

class NotificationController extends Controller
{

    /**
     *  Return notification to the FE application about data changes, implemented via SSE, server sent events.
     *
     *  @param  \Illuminate\Http\Request  $request
     *  @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function get(Request $request): StreamedResponse
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

                if ($notifications->isEmpty()) {
                    sleep(5);
                    return;
                }

                // If there are notifications, send them to the frontend
                // Format notifications as JSON and send them via SSE
                echo "data: " . json_encode($notifications) . "\n\n";  
				
                // Flush the output buffer
                flush();

                // clearing occurs before flushing the buffer if action would take time
                // don't clear whole table, as it is possible new events might occur in between
                DB::table(TABLE_PURCHASE_LIST_EVENTS)->whereIn('id', $notifications->pluck('id')->all())->delete();

                // Sleep for a few seconds before checking again
                sleep(5);

            }
        }, 200, $headers);
    }
}
