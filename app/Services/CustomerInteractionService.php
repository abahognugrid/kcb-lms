<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CustomerInteractionService
{
    public function getCustomerTimelineOptimized($customerId)
    {
        $results = DB::select("
    SELECT 'customer' AS type, id, created_at, 
    CONCAT(
        'Customer #', id, ': ',
        \"First_Name\", ' ', \"Last_Name\",
        ' | DOB: ', \"Date_of_Birth\",
        ' | ID: ', \"ID_Number\",
        ' | Tel: ', \"Telephone_Number\",
        ' | Email: ', \"Email_Address\"
    ) AS description
    FROM customers
    WHERE id = ?
    ORDER BY created_at DESC
", [$customerId]);

        return collect($results);
    }
}
