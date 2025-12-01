<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    // In Phiếu Nghiệm thu kỹ thuật & Bàn giao
    public function printWorkOrder($id)
    {
        $workOrder = WorkOrder::with(['customer', 'tasks.items', 'tasks.performer'])
            ->findOrFail($id);

        // Tính toán tổng tiền
        $totalAmount = 0;
        foreach ($workOrder->tasks as $task) {
            $totalAmount += $task->collected_amount;
        }

        $data = [
            'order' => $workOrder,
            'totalAmount' => $totalAmount,
            'date' => now()->format('d/m/Y'),
        ];

        // Load view và render ra PDF
        $pdf = Pdf::loadView('pdf.work-order', $data);
        
        // Setup khổ giấy A5 ngang (cho tiết kiệm) hoặc A4 tùy anh
        $pdf->setPaper('a5', 'landscape');

        // Trả về trình duyệt để xem trước (stream) thay vì tải xuống luôn
        return $pdf->stream('Phieu-nghiem-thu-' . $workOrder->code . '.pdf');
    }
}