<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerLoansApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "loan_account"=> $this->Credit_Account_Reference,
            "loan_amount"=> $this->Credit_Amount,
            "start_date"=> $this->Credit_Account_Date->toDateString(),
        ];
    }
}
