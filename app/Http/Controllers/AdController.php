<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Repositories\AdRepository;
use App\Services\ActivityLog;
use App\Services\ImageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdController extends Controller
{
    public function __construct(
        private AdRepository $adRepository,
        private ImageService $imageService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.Ads.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active' => ['nullable']
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $this->imageService->saveImage($request->file('image'), 'ads');
            }
            $data['is_active'] = $request->input('is_active', 0) == 1 ? true : false;
            $this->adRepository->store($data);
            ActivityLog::log('create', 'Ad', 'تم إضافة إعلان جديد');
            return WebResponseClass::sendResponse('تم الإضافة!', 'تم إضافة الإعلان بنجاح وتم تحديث الكاش.');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $ad = $this->adRepository->getById($id);
            return redirect()->back()
                ->with('openModalEdit', true)
                ->with('Ad', $ad);
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active' => ['nullable']
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();
            $ad = $this->adRepository->getById($id);
            if ($request->hasFile('image')) {
                $this->imageService->deleteImage($ad->image);
                $data['image'] = $this->imageService->saveImage($request->file('image'), 'ads');
            }
            $data['is_active'] = $request->input('is_active', 0) == 1 ? true : false;
            $this->adRepository->update($data, $id);
            ActivityLog::log('update', 'Ad', 'تم تعديل بيانات الإعلان');
            return WebResponseClass::sendResponse('تم التعديل!', 'تم تعديل الإعلان بنجاح وتحديث كاش الموبايل.');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $ad = $this->adRepository->getById($id);
            $this->imageService->deleteImage($ad->image);
            $this->adRepository->delete($id);
            ActivityLog::log('delete', 'Ad', 'تم حذف الإعلان');
            return WebResponseClass::sendResponse('تم الحذف!', 'تم حذف الإعلان وتنظيف الكاش.');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
