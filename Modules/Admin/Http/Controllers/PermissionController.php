<?php

namespace Admin\Http\Controllers;


use Admin\Http\Requests\Permission\PermissionRequest;
use Admin\Http\Resources\Permission\PermissionCollection;
use Admin\Http\Resources\Permission\PermissionResource;
use Admin\Services\PermissionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionController extends Controller
{
    private PermissionService $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function store(PermissionRequest $request): PermissionResource
    {
        $Data = $request->validated();
        return new PermissionResource($this->permissionService->store($Data));
    }

    public function update(PermissionRequest $request, int $id,): PermissionResource
    {
        return new PermissionResource($this->permissionService->update($request, $id));
    }

    public function destroy(int $id): Response
    {
        $this->permissionService->destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function index(Request $request): PermissionCollection
    {
        return new PermissionCollection($this->permissionService->list($request));
    }

    public function show(int $id): PermissionResource
    {
        return new PermissionResource($this->permissionService->get($id));
    }
}
