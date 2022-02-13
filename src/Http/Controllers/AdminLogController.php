<?php

namespace VolistxTeam\VSkeletonKernel\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use VolistxTeam\VSkeletonKernel\Facades\Messages;
use VolistxTeam\VSkeletonKernel\Facades\Permissions;
use VolistxTeam\VSkeletonKernel\Repositories\Interfaces\IAdminLogRepository;

class AdminLogController extends Controller
{
    private IAdminLogRepository $adminLogRepository;

    public function __construct(IAdminLogRepository $adminLogRepository)
    {
        $this->module = 'logs';
        $this->adminLogRepository = $adminLogRepository;
    }

    public function GetAdminLog(Request $request, $log_id): JsonResponse
    {
        if (!Permissions::check($request->X_ACCESS_TOKEN, $this->module, 'view')) {
            return response()->json(Messages::E401(), 401);
        }

        $validator = Validator::make([
            'log_id' => $log_id,
        ], [
            'log_id' => ['bail', 'required', 'uuid', 'exists:admin_logs,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(Messages::E400($validator->errors()->first()), 400);
        }

        try {
            $log = $this->adminLogRepository->Find($log_id);

            if (!$log) {
                return response()->json(Messages::E404(), 404);
            }

            return response()->json($log->toArray());
        } catch (Exception $ex) {
            return response()->json(Messages::E500(), 500);
        }
    }

    public function GetAdminLogs(Request $request): JsonResponse
    {
        if (!Permissions::check($request->X_ACCESS_TOKEN, $this->module, 'view-all')) {
            return response()->json(Messages::E401(), 401);
        }

        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 50);

        $validator = Validator::make([
            'page'  => $page,
            'limit' => $limit,
        ], [
            '$page' => ['bail', 'sometimes', 'integer'],
            'limit' => ['bail', 'sometimes', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json(Messages::E400($validator->errors()->first()), 400);
        }

        try {
            $logs = $this->adminLogRepository->FindAll($search, $page, $limit);
            if (!$logs) {
                return response()->json(Messages::E500(), 500);
            }

            return response()->json($logs);
        } catch (Exception $ex) {
            return response()->json(Messages::E500(), 500);
        }
    }
}
