<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $gender = $request->get('gender', 0);
        $field = $request->get('field', 'created_at');
        $order = $request->get('order', 'desc');
        $customers = Customer::when($gender > 0, function ($query) use ($gender) {
            $query->where('gender', $gender);
        })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->orderBy($field, $order)->paginate(5)->withQueryString();
        return view('customer.index', compact('customers'));
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('customer.form', compact('customer'));
    }

    public function submit(Request $request)
    {
        // validation
        $request->validate([
            'name' => 'required|unique:customers,name,' . $request->id,
            'gender' => 'required',
            'email' => 'required|email:rfc,dns',
        ]);

        // save to database
        if ($request->id > 0) {
            $data = Customer::find($request->id);
        } else {
            $data = new Customer();
        }

        $data->name = $request->name;
        $data->gender = $request->gender;
        $data->email = $request->email;

        // delete uploaded file
        if ($request->is_deleted_image == 1 && $request->id > 0) {
            if (Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = '';
        }
        // upload file
        else if ($request->hasFile('image')) {
            if ($data->image && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = Storage::disk('public')->put('customer', $request->image);
        }

        $data->save();
        return redirect('/');
    }

    public function delete(Request $request)
    {
        $data = Customer::find($request->delete_id);
        // delete uploaded file
        if ($data->image && Storage::disk('public')->exists($data->image)) {
            Storage::disk('public')->delete($data->image);
        }
        $data->delete();
        return redirect('/');
    }
}
