<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;
use \Validator;
use App\Http\Resources\ItemsResource;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ItemsController extends Controller {

    public function index() {
        $items = Items::all()->where('deleted_at', '=', null);
        return $this->sendResponse(ItemsResource::collection($items), 'Items retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $item = new Items();
        $rules = array(
            'name' => 'required',
            'price' => 'required',
            'description' => 'required'
        );
        $messages = array(
            'name.required' => 'Please enter a title.',
            'price.required' => 'Please enter a price.',
            'description.required' => 'Please enter a short description.'
        );
        $validator=Validator::make($request->all(),$rules,$messages);
        if($validator->fails())
        {
            $messages=$validator->messages();
            $errors=$messages->all();
        return $this->sendError($errors);
        }
        if ($request->name)
            $item->name = $request->name;
        if ($request->description)
            $item->description = $request->description;
        if ($request->price)
            $item->price = $request->price;
        if ($request->price)
            $item->name = $request->name;

        if ($request->image) {
            $request->validate([
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
            $item->image_location = $request->file('image')->store('public/itemsImages');
        }
        $item->updated_at = null;

        if ($item->save()) {
            return $this->sendResponse(new ItemsResource($item), 'Item created successfully.');
        }
        return $this->sendError('Creation Error .');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $items = Items::find($id);
        if (is_null($items)) {
            return $this->sendError('Item not found.');
        }
        return $this->sendResponse(new ItemsResource($items), 'Items retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $item = Items::find($id);
        if (!$item) {
            return $this->sendError('Id not found');
        }
        $validatedData = Validator::make($request->all(), [
                    'image_location' => 'mimes:jpg,png,jpeg|max:3048'
        ]);
        if ($validatedData->fails()) {
            return $this->sendError('Validation Error', $validatedData->errors());
        }

        if ($request->name)
            $item->name = $request->name;
        if ($request->description)
            $item->description = $request->description;
        if ($request->price)
            $item->price = $request->price;
        if ($request->image_location) {
            $item->image_location = $request->file('image_location')->store('public/itemsImages');
        }
        $item->updated_at = date('Y-m-d H:i:s');
        if ($item->save()) {
            return $this->sendResponse(new ItemsResource($item), 'Item updated successfully.');
        }
        return $this->sendError('Updating Error Error.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $item = Items::find($id);
        $item->deleted_at = date('Y-m-d H:i:s');
        $item->save();

        return $this->sendResponse([], 'Item deleted successfully.');
    }

}
