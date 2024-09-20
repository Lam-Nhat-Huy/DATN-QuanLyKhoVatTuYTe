<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImportRequest;
use App\Models\Inventories;
use App\Models\Receipt_details;
use App\Models\Receipts;
use App\Models\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    protected $route = 'warehouse';

    public function import()
    {
        $title = 'Nhập Kho';

        $receipts = Receipts::with(['supplier', 'user', 'details.equipments'])->paginate(10);

        return view("{$this->route}.import_warehouse.import", [
            'title' => $title,
            'receipts' => $receipts
        ]);
    }

    public function create_import()
    {
        $title = 'Tạo Phiếu Nhập Kho';

        $inventories = Inventories::with('equipments')->get();

        $suppliers = Suppliers::all();

        return view("{$this->route}.import_warehouse.add_import", [
            'title' => $title,
            'inventories' => $inventories,
            'suppliers' => $suppliers
        ]);
    }

    public function store_import(Request $request)
    {
        // Lấy dữ liệu từ JSON materialData đã gửi từ form
        $materialData = json_decode($request->input('materialData'), true);

        if (empty($materialData)) {
            return redirect()->back()->with('error', 'Không có dữ liệu vật tư được gửi.');
        }

        // Lấy thông tin của phiếu nhập từ phần tử đầu tiên của materialData
        $receiptData = [
            'supplier_code' => $materialData[0]['supplier_code'],
            'receipt_date' => $materialData[0]['receipt_date'],
            'note' => $materialData[0]['note'],
            'created_by' => $materialData[0]['created_by'],
            'receipt_no' => $materialData[0]['receipt_no'] ?? null
        ];


        // Lấy mã phiếu nhập cuối cùng từ cơ sở dữ liệu
        $lastReceipt = Receipts::orderBy('created_at', 'desc')->first();

        // Kiểm tra nếu có phiếu nhập trước đó
        if ($lastReceipt) {
            // Lấy số phía sau mã "REC" và tăng lên 1
            $lastReceiptNumber = intval(substr($lastReceipt->code, 3));
            $newReceiptNumber = $lastReceiptNumber + 1;
        } else {
            // Nếu không có phiếu nhập trước, bắt đầu với số 1
            $newReceiptNumber = 1;
        }

        // Tạo mã phiếu nhập mới với tiền tố "REC" và số mới (đảm bảo đủ 4 chữ số)
        $newReceiptCode = 'REC' . str_pad($newReceiptNumber, 4, '0', STR_PAD_LEFT);

        // Thêm mã phiếu nhập vào dữ liệu phiếu nhập (khóa chính là 'code')
        $receiptData['code'] = $newReceiptCode;

        // Lưu dữ liệu vào bảng receipts
        $receipt = Receipts::create($receiptData);

        // Chuẩn bị dữ liệu cho bảng receipt_details
        $receiptDetailsData = [];
        foreach ($materialData as $material) {
            $receiptDetailsData[] = [
                'receipt_code' => $receipt->code,
                'equipment_code' => $material['equipment_code'],
                'batch_number' => $material['batch_number'],
                'expiry_date' => $material['expiry_date'],
                'price' => $material['price'],
                'quantity' => $material['quantity'],
                'discount' => $material['discount'],
                'VAT' => $material['VAT'],
            ];
        }

        // dd([
        //     'receipt' => $receiptData,
        //     'receipt_details' => $receiptDetailsData,
        // ]);

        // Lưu dữ liệu vào bảng receipt_details
        Receipt_details::insert($receiptDetailsData);

        // Sau khi lưu, có thể chuyển hướng hoặc trả về thông báo thành công
        return redirect()->route('warehouse.import')->with('success', 'Đã lưu phiếu nhập kho thành công với mã ' . $newReceiptCode);
    }
}
