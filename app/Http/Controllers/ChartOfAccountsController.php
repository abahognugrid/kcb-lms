<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Accounts\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChartOfAccountsController extends Controller
{
  public function index()
  {
    $user = Auth::user();
    $query = Account::query()->with(['partner', 'parent']);

    if (!$user->is_admin) {
      $query->where('partner_id', $user->partner_id);
    }

    $accounts = $query->select(['*', DB::raw('ROW_NUMBER() OVER (ORDER BY partner_id, name) AS rownum')])
      ->orderBy('partner_id')
      ->orderBy('name')
      ->get();
    dd($accounts);
    return view('chart-of-accounts.index', compact('accounts'));
  }

  public function show($slug)
  {
    $user = Auth::user();
    $query = Account::query()->with(['partner', 'parent'])->where('slug', $slug);

    if (!$user->is_admin) {
      $account = $query->where('partner_id', $user->partner_id)->first();
    } else {
      $account = $query->get();
    }
    return view('chart-of-accounts.show', compact('account'));
  }

  public function store(Request $request)
  {
    try {
      $request->validate([
        'name' => 'required|string|min:2|max:50',
        'parent_id' => 'nullable|exists:accounts,id',
        'identifier' => 'required|string|min:2|max:50',
      ]);

      $parent_gla = Account::find($request->parent_id);

      $account = new Account;
      $account->name = $request->name;
      $account->partner_id = $parent_gla->partner_id;
      $account->parent_id = $request->parent_id;
      $account->identifier = $parent_gla->identifier . '.' . $request->identifier;
      $account->type_letter = $parent_gla->type_letter;
      $account->slug = Str::slug($request->name);
      $account->position = 3;
      $account->save();


      $request->session()->put('success', "Account {$account->name} created successfully.");
      return redirect()->route('chart-of-accounts.index');
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());
    }
  }

  public function update(Request $request, $accountId)
  {
    $data = $request->validate([
      'name' => 'required|string|min:2|max:50',
    ]);

    $account = Account::find($accountId);
    $account->slug = Str::slug($request->name);
    $account->update($data);

    session()->flash("success", "Account details updated successfully.");
    return redirect()->back();
  }
}
