<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\ActivityLog;
use App\Services\ImageService;
use Illuminate\Validation\Rule;
use App\Classes\WebResponseClass;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct(private UserRepository $userRepository, private ImageService $imageService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins=$this->userRepository->getAdmins();
        return view('pages.admin.index',compact('admins'));
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
        $validator = Validator::make($request->all(), [
            'name'=>['required','string'],
            'phone'=>['required',Rule::unique('users','phone')],
            'password'=>['required'],
            'whatsapp_number'=>['nullable'],
            'image'=>['nullable','image','max:2048']
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            $validatData = $validator->validated();
            if($request->hasFile('image')){
                $image_path=$this->imageService->saveImage($request->file('image'),'users');
                $validatData['image'] = $image_path;
            }
            $validatData['type']='admin';
            $admin=$this->userRepository->store($validatData);
            return WebResponseClass::sendResponse('تم الإضافة!','تم إضافة المستخدم بنجاح');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
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
    public function edit(string $id)
    {
        try {
            $admin=$this->userRepository->getById($id);
            return redirect()->back()
                ->with('openModalEdit',true)
                ->with('admin', $admin);
        } catch (Exception $e) {
            return WebResponseClass::sendError($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name'=>['required','string'],
            'phone'=>['required',Rule::unique('users','phone')->ignore($id)],
            'whatsapp_number'=>['nullable'],
            'image'=>['nullable','image','max:2048']
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        try {
            $admin = $this->userRepository->getById($id);
            $validatData = $validator->validated();
            if ($request->hasFile('image')) {
                if ($admin->image) {
                    $this->imageService->deleteImage($admin->image);
                }
                $image_path=$this->imageService->saveImage($request->file('image'),'users');
                $validatData['image'] = $image_path;
            }
            $this->userRepository->update($validatData,$id);
            ActivityLog::log('update','User','تم تحديث بيانات المسئول ');
            return WebResponseClass::sendResponse( 'تم التحديث!', 'تم تحديث بيانات المسئول بنجاح');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
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
