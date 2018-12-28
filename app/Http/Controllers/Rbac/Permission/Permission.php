<?php

namespace App\Http\Controllers\Rbac\Permission;

use App\Exceptions\NotFoundException;
use App\Exceptions\ParamterErrorException;
use App\Http\Controllers\Controller;
use App\Services\Rbac\Permission\PermissionService;
use Illuminate\Http\Request;
use SuperHappysir\Support\Utils\Response\JsonResponseBodyInterface;

class Permission extends Controller
{
    /**
     * 权限service
     * @var PermissionService
     */
    private $permissionService;
    
    /**
     * Permission constructor.
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    
    /**
     * Display a listing of the resource.
     * @return JsonResponseBodyInterface
     */
    public function index() : JsonResponseBodyInterface
    {
        $paginate = $this->permissionService->paginate((int) \request('limit', 15));
        
        return json_success_response($paginate);
    }
    
    
    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponseBodyInterface
     */
    public function store(Request $request) : JsonResponseBodyInterface
    {
        $roleModel = $this->permissionService->create(
            $request->only(['name','path','method','description','per_type','state'])
        );
        
        return json_success_response($roleModel->toArray());
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return JsonResponseBodyInterface|mixed
     * @throws \App\Exceptions\NotFoundException
     */
    public function show($id)
    {
        $roleModel = $this->permissionService->find($id, [
            'id',
            'name'
        ]);
        if (!$roleModel) {
            throw new NotFoundException();
        }
        
        return json_success_response($roleModel->toArray());
    }
    
    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     * @return JsonResponseBodyInterface
     */
    public function update(Request $request, $id) : JsonResponseBodyInterface
    {
        $roleModel = $this->permissionService->update(
            $request->only(['name','path','method','description','per_type','state']),
            $id
        );
        
        return json_success_response($roleModel->toArray());
    }
    
    /**
     * Remove the specified resource from storage.
     * @param  int $id
     * @return JsonResponseBodyInterface
     */
    public function destroy($id) : JsonResponseBodyInterface
    {
        $this->permissionService->softDelete($id);
        
        return json_success_response();
    }
    
    /**
     * 批量禁用角色
     * @param \Illuminate\Http\Request $request
     * @return JsonResponseBodyInterface
     */
    public function batchDisabled(Request $request) : JsonResponseBodyInterface
    {
        $ids = $request->json('params.ids');
        if (!$ids) {
            throw new ParamterErrorException('请指定需要批量操作的选项ID');
        }
        
        $affectedRows = $this->permissionService->batchDisabled(explode(',', $ids));
        
        return json_success_response([
            'affected_rows' => $affectedRows
        ]);
    }
    
    /**
     * batchEnable
     * @param \Illuminate\Http\Request $request
     * @return JsonResponseBodyInterface
     */
    public function batchEnable(Request $request) : JsonResponseBodyInterface
    {
        $ids = $request->json('params.ids');
        if (!$ids) {
            throw new ParamterErrorException('请指定需要批量操作的选项ID');
        }
        
        $affectedRows = $this->permissionService->batchEnabled(explode(',', $ids));
        
        return json_success_response([
            'affected_rows' => $affectedRows
        ]);
    }
    
    /**
     * 前端所有path数组
     *
     * @return JsonResponseBodyInterface
     */
    public function theFrontEndPath() : JsonResponseBodyInterface
    {
        $frontEndPathArr = $this->permissionService->getTheFrontEndPath();
    
        return json_success_response([
            'frontend_path_arr' => $frontEndPathArr
        ]);
    }
}
