<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\CreateReportRequest;
use App\Http\Requests\Report\UpdateReportRequest;
use App\Models\Report_types;
use App\Models\Reports;
use App\Models\Users;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $route = 'report';

    protected $callModel;

    public function __construct()
    {
        $this->callModel = new Reports();
    }

    public function index(Request $request)
    {
        $title = 'Báo Cáo';

        $AllReportType = Report_types::orderBy('created_at', 'DESC')->get();

        $AllUser = Users::all();

        $AllReport = $this->callModel::with(['report_types', 'users'])
            ->orderBy('created_at', 'DESC')
            ->where('deleted_at', null);

        if (isset($request->ur)) {
            $AllReport = $AllReport->where("user_code", $request->ur);
        }

        if (isset($request->rt)) {
            $AllReport = $AllReport->where("report_type", $request->rt);
        }

        if (isset($request->st)) {
            $AllReport = $AllReport->where("status", $request->st);
        }

        if (isset($request->kw)) {
            $AllReport = $AllReport->where(function ($query) use ($request) {
                $query->where('content', 'like', '%' . $request->kw . '%')
                    ->orWhere('code', 'like', '%' . $request->kw . '%');
            });
        }

        $AllReport = $AllReport->paginate(10);

        if (isset($request->report_codes)) {

            if ($request->action_type === 'browse') {

                $this->callModel::whereIn('code', $request->report_codes)->update(['status' => 1]);

                toastr()->success('Duyệt thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                $this->callModel::whereIn('code', $request->report_codes)->delete();

                toastr()->success('Xóa thành công');

                return redirect()->back();
            }
        }

        if (!empty($request->browse_report)) {

            $this->callModel::where('code', $request->browse_report)->update(['status' => 1]);

            toastr()->success('Đã duyệt');

            return redirect()->route('report.index');
        }

        if (!empty($request->delete_report)) {

            $this->callModel::where('code', $request->delete_report)->delete();

            toastr()->success('Đã xóa');

            return redirect()->route('report.index');
        }

        return view("admin.{$this->route}.index", compact('title', 'AllReport', 'AllReportType', 'AllUser'));
    }

    public function report_trash(Request $request)
    {
        $title = 'Báo Cáo';

        $AllReportTrash = $this->callModel::with(['report_types', 'users'])
            ->orderBy('deleted_at', 'DESC')
            ->onlyTrashed()
            ->paginate(10);

        if (isset($request->report_codes)) {

            if ($request->action_type === 'restore') {

                $this->callModel::whereIn('code', $request->report_codes)->restore();

                toastr()->success('Khôi phục thành công');

                return redirect()->back();
            } elseif ($request->action_type === 'delete') {

                $reports = $this->callModel::onlyTrashed()->whereIn('code', $request->report_codes)->get();

                foreach ($reports as $report) {

                    Storage::disk('public')->delete('reports/' . $report->file);

                    $report->forceDelete();
                }

                toastr()->success('Xóa thành công');

                return redirect()->back();
            }
        }

        if (isset($request->restore_report)) {

            $this->callModel::where('code', $request->restore_report)->restore();

            toastr()->success('Khôi phục thành công');

            return redirect()->back();
        }

        if (isset($request->delete_report)) {

            $report = $this->callModel::onlyTrashed()->where('code', $request->delete_report)->first();

            Storage::disk('public')->delete('reports/' . $report->file);

            $report->forceDelete();

            toastr()->success('Xóa vĩnh viễn thành công');

            return redirect()->back();
        }

        return view("admin.{$this->route}.trash", compact('title', 'AllReportTrash'));
    }

    public function insert_report()
    {
        $title = 'Báo Cáo';

        $title_form = 'Tạo Báo Cáo';

        $action = 'create';

        $AllReportType = Report_types::orderBy('created_at', 'DESC')->get();

        return view("admin.{$this->route}.form", compact('title', 'title_form', 'action', 'AllReportType'));
    }

    public function create_report_type(Request $request)
    {
        if (isset($request->name)) {

            Report_types::create(['name' => $request->name]);

            toastr()->success('Đã thêm loại báo cáo');

            return redirect()->route('report.insert_report');
        }
    }

    public function delete_report_type($id)
    {
        $checkExists = Reports::where('report_type', $id)->exists();

        if ($checkExists) {

            toastr()->error('Không thể xóa');

            return redirect()->back();
        }

        Report_types::find($id)->delete();

        toastr()->success('Đã xóa loại báo cáo');

        return redirect()->route('report.insert_report');
    }

    public function create(CreateReportRequest $request)
    {
        $data = $request->validated();

        if ($data) {

            $fileName = time() . '.pdf';

            $request->file->storeAs('public/reports', $fileName);

            $data['file'] = $fileName;

            $data['code'] = 'RP' . $this->generateRandomString(8);

            $data['user_code'] = session('user_code');

            $data['created_at'] = now();

            $data['updated_at'] = null;

            $this->callModel::create($data);
        }

        toastr()->success('Đã thêm báo cáo');

        return redirect()->route('report.index');
    }

    public function update_report($code)
    {
        $title = 'Báo Cáo';

        $title_form = 'Cập Nhật Báo Cáo';

        $action = 'update';

        $AllReportType = Report_types::orderBy('created_at', 'DESC')->get();

        $FirstReport = $this->callModel::where('code', $code)->first();

        return view("admin.{$this->route}.form", compact('title', 'title_form', 'action', 'AllReportType', 'FirstReport'));
    }

    public function edit(UpdateReportRequest $request, $code)
    {
        $data = $request->validated();

        $record = $this->callModel::where('code', $code)->first();

        if ($data) {

            if ($record) {

                if (!empty($request->file)) {

                    if ($record->file) {

                        Storage::disk('public')->delete('reports/' . $record->file);
                    }

                    $fileName = time() . '.pdf';

                    $request->file->storeAs('public/reports', $fileName);

                    $data['file'] = $fileName;
                } else {

                    unset($data['file']);
                }

                $data['updated_at'] = now();

                $record->update($data);


                toastr()->success('Đã cập nhật báo cáo');

                return redirect()->route('report.index');
            }


            toastr()->error('Không thể cập nhật, thử lại sau');

            return redirect()->route('report.index');
        }
    }

    function generateRandomString($length = 9)
    {
        $characters = '0123456789';

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
