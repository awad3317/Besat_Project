<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\ActivityLog;
use Illuminate\Validation\Rule;
use App\Classes\WebResponseClass;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('users.index')->with('isModalOpen',true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>['required','string'],
            'phone'=>['required'],
            'whatsapp_number'=>['nullable'],
            'password'=>['required','min:8']
        ]);
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            $validatedData = $validator->validated();
            $user=$this->userRepository->store($validatedData);
            ActivityLog::log('create', 'User', 'تم أضافة مستخدم جديد');
            return WebResponseClass::sendResponse('تم الإضافة!', 'تم إضافة مستخدم بنجاح','حسناً','request.create');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = $this->userRepository->getById($id);
        return view('pages.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Add balance to the user's wallet.
     */
    public function addBalance(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $amount = floatval($request->input('amount'));
            
            \Illuminate\Support\Facades\DB::transaction(function () use ($id, $amount) {
                $user = $this->userRepository->getById($id);
                $user->increment('wallet_balance', $amount);

                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'deposit',
                ]);
                
                ActivityLog::log('update', 'User', "تم إضافة رصيد {$amount} إلى محفظة المستخدم {$user->name}");
            });

            return WebResponseClass::sendResponse('تم الإضافة!', 'تم إضافة الرصيد إلى محفظة المستخدم بنجاح');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
