<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePrescriptionRequest;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
        /**
     * Display a listing of the resource.
     * @param Request $request
     */
    public function index(Request $request)
    {
        if($request->user()->role == 'user'){
            self::ok(Prescription::where('user_id',$request->user()->id)->latest()->get());
        }else{
            self::ok([
                'prescriptions' => Prescription::filter(request(['take','skip','search','sort', 'user_id' ,'status']))->get(),
                'count' => Prescription::filter(request(['search','status', 'user_id']))->count()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     */
    public function create(CreatePrescriptionRequest $request)
    {
        $data = $request->validated();

        $image = self::save_image_to_public_directory($request);

        if($image !== false)
            $data['image'] = $image;

        self::ok(
            Prescription::create([
                'user_id' => $request->user()->id,
                'description' =>  $data['description'],
                'image' => $data['image']
            ])
        );
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $order_id
     */
    public function show($prescription_id): void
    {
        $prescription = Prescription::find($prescription_id);

        if($prescription)
            self::ok();

        self::notFound();
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param $order_id
     */
    public function update($prescription_id, $order_id): void
    {
        Prescription::find($prescription_id)->update(['order_id' => $order_id]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param $order_id
     */
    public function destroy($prescription_id): void
    {
        $prescription = Prescription::find($prescription_id);
        $is_deleted = $prescription->delete();

        (new NotificationController)->notify(
            'Prescription deleted',
            'your Prescription has been removed because it not valid, please try again',
            User::find($prescription->user_id)
        );

        $is_deleted ? self::ok() : self::unHandledError();
    }
}
