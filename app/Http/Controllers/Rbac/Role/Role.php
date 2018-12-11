<?php

namespace App\Http\Controllers\Rbac\Role;

use App\Exceptions\ParamterErrorException;
use App\Http\Controllers\Controller;
use App\Services\Rbac\Role\RoleService;
use Illuminate\Http\Request;

class Role extends Controller
{
    /**
     * 角色service
     */
    private $roleService;
    
    /**
     * Role constructor.
     * @param \App\Services\Rbac\Role\RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    
        $this->middleware('auth:api');
    }
    
    /**
     * Display a listing of the resource.
     * @return array
     */
    public function index() : array
    {
        
        $paginate = $this->roleService->paginate((int) \request('limit', 15));
        
        return json_response()->success($paginate);
    }
    
    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function store(Request $request) : array
    {
        $roleModel = $this->roleService->create($request->only(['name']));
        
        return json_response()->success($roleModel->toArray());
    }
    
    /**
     * Display the specified resource.
     * @param  int $id
     * @return array|mixed
     */
    public function show($id)
    {
        $roleModel = $this->roleService->find($id, [
            'id',
            'name'
        ]);
        if (!$roleModel) {
            throw new ParamterErrorException('无指定资源');
        }
        
        return json_response()->success($roleModel->toArray());
    }
    
    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     * @return array
     */
    public function update(Request $request, $id) : array
    {
        $roleModel = $this->roleService->update($request->only(['name']), $id);
        
        return json_response()->success($roleModel->toArray());
    }
    
    /**
     * Remove the specified resource from storage.
     * @param  int $id
     * @return array
     */
    public function destroy($id) : array
    {
        $this->roleService->softDelete($id);
        
        return json_response()->success();
    }
    
    /**
     * 批量禁用角色
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function batchDisabled(Request $request) : array
    {
        $ids = $request->json('params.ids');
        if (!$ids) {
            throw new ParamterErrorException('请指定需要批量操作的选项ID');
        }
        
        $affectedRows = $this->roleService->batchDisabled(explode(',', $ids));
        
        return json_response()->success([
            'affected_rows' => $affectedRows
        ]);
    }
    
    /**
     * batchEnable
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function batchEnable(Request $request) : array
    {
        $ids = $request->json('params.ids');
        if (!$ids) {
            throw new ParamterErrorException('请指定需要批量操作的选项ID');
        }
        
        $affectedRows = $this->roleService->batchEnabled(explode(',', $ids));
        
        return json_response()->success([
            'affected_rows' => $affectedRows
        ]);
    }
}
