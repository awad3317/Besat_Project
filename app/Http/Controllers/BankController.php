<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\ImageService;

class BankController extends Controller
{
    public function __construct(private ImageService $imageService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.banks.index'); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'account_name'   => ['nullable', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'logo'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'], 
        ], [
            'name.required'           => 'اسم البنك حقل مطلوب.',
            'account_number.required' => 'رقم الحساب أو الآيبان مطلوب.',
            'logo.image'              => 'الملف المرفق يجب أن يكون صورة فقط.',
            'logo.mimes'              => 'صيغ الصور المدعومة هي: jpeg, png, jpg, svg.',
            'logo.max'                => 'حجم الشعار كبير جداً، الحد الأقصى المسموح به هو 2 ميجابايت.',
        ]);
        try {
            if ($request->hasFile('logo')) {
                $validated['logo'] = $this->imageService->saveImage($request->file('logo'), 'banks');
            }
            Bank::create($validated);
            Cache::forget('active_banks_list');
            return redirect()->route('Bank.index')
                ->with('success', 'تم إضافة الحساب البنكي بنجاح وتنشيطه في النظام.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('isModalOpen', true)
                ->with('error', 'حدث خطأ تقني أثناء الحفظ: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $bank = Bank::findOrFail($id);
        return redirect()->route('Bank.index')
        ->with('openModalEdit', true)
        ->with('Bank', $bank);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'account_name'   => ['nullable', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'logo'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
        ], [
            'name.required'           => 'اسم البنك حقل مطلوب.',
            'account_number.required' => 'رقم الحساب مطلوب.',
            'logo.image'              => 'يجب أن يكون الملف صورة فقط.',
        ]);

        try {
            if ($request->hasFile('logo')) {
                if ($bank->logo) {
                    $this->imageService->deleteImage($bank->logo);
                }
                $validated['logo'] = $this->imageService->saveImage($request->file('logo'), 'banks');
            }
            $validated['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;
            $bank->update($validated);
            Cache::forget('active_banks_list');
            return redirect()->route('Bank.index')
                ->with('success', 'تم تحديث بيانات الحساب البنكي بنجاح.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('openModalEdit', true)
                ->with('Bank', $bank)
                ->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
