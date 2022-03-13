<?php

namespace Acewings\EasyApi\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

trait BaseApi
{
    /**
     * @var array
     */
    protected array $ValidationFields=[];

    /**
     * @var array
     */
    protected array $ValidationMessages=[];

    /**
     * @return Response
     */
    public function paginateAll(): Response
    {
        return response( $this::paginate());
    }

    /**
     * @param $required
     * @return array
     */
    private function GetValidator($required): array
    {
        if ($required){
            foreach ($this->ValidationFields as $key => $value)
                array_push($this->ValidationFields[$key],'required');
        }
        return $this->ValidationFields;
    }

    /**
     * @param $id
     * @return Response
     */
    public function getOneById($id): Response
    {
        if($this->find($id)!=null)
            return response($this->find($id));
        return response([
            'success'=>false,
            'message'=>'ops, resource not found recheck the id existence'
        ], 404);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function updateResource(Request $request, $id): Response
    {
        return $this->FindValidateAndUpdate($id, $request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function patchResource(Request $request, $id): Response
    {
        return $this->FindValidateAndUpdate($id, $request, true);
    }

    /**
     * @param $id
     * @return Response
     */
    public function deleteOneById($id): Response
    {
        $resource = $this->find($id);
        if ($resource != null) {
            $resource->deleteOrFail();
            return response([
                'success' => true,
                'message' => 'deleted successfully !'
            ], 200);
        }
        return response([
            'success'=>false,
            'message'=>'ops, resource not found recheck the id existence'
        ], 404);
    }

    /**
     * @param $id
     * @param Request $request
     * @param bool $Patch
     * @return Response
     */
    public function FindValidateAndUpdate($id, Request $request, bool $Patch = false): Response
    {
        $resource = $this->find($id);
        if ($resource == null)
            return response([
                'success' => false,
                'message' => 'ops, resource not found recheck the id existence'
            ], 404);
        $Validator = Validator::make($request->all(), $this->GetValidator(!$Patch), $this->ValidationMessages);
        if ($Validator->fails())
            return response($Validator->errors());

        if ($Patch)
        {
            foreach ($request->all() as $key => $value)
                $resource->{$key} = $value;
            if($resource->isDirty())
                $resource->update();
        }
        else
            $resource->update($request->all());

        return response([
            'success' => true,
            'message' => 'updated successfully !'
        ], 200);
    }


}
