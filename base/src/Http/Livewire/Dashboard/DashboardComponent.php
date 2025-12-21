<?php

namespace Polirium\Core\Base\Http\Livewire\Dashboard;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Polirium\Modules\Product\Http\Model\Payment\Payment;
use Polirium\Modules\Product\Http\Model\Payment\PaymentProduct;

class DashboardComponent extends Component
{
    public string $period = 'today';

    public function mount()
    {
        // Default period
    }

    public function render()
    {
        return view('core/base::pages.dashboard-component', [
            'stats' => $this->getStats(),
            'chartData' => $this->getChartData(),
            'topProducts' => $this->getTopProducts(),
            'recentInvoices' => $this->getRecentInvoices(),
        ]);
    }

    public function setPeriod($period)
    {
        $this->period = $period;
    }

    protected function getDateRange()
    {
        return match($this->period) {
            'today' => [Carbon::today(), Carbon::now()],
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()],
            default => [Carbon::today(), Carbon::now()],
        };
    }

    protected function getStats()
    {
        [$start, $end] = $this->getDateRange();
        $branchId = user_branch();

        // Số hóa đơn
        $invoiceCount = Payment::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'success')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Doanh thu
        $revenue = Payment::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'success')
            ->whereBetween('created_at', [$start, $end])
            ->sum('value_payment');

        // Số sản phẩm bán
        $productsSold = PaymentProduct::query()
            ->whereHas('payment', function ($q) use ($branchId, $start, $end) {
                $q->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                  ->where('status', 'success')
                  ->whereBetween('created_at', [$start, $end]);
            })
            ->sum('amount');

        // So sánh với kỳ trước
        $previousRevenue = $this->getPreviousPeriodRevenue();
        $revenueChange = $previousRevenue > 0
            ? round((($revenue - $previousRevenue) / $previousRevenue) * 100, 1)
            : 0;

        // Thống kê theo phương thức thanh toán
        $paymentMethods = $this->getPaymentMethodStats($branchId, $start, $end);

        return [
            'invoice_count' => $invoiceCount,
            'revenue' => $revenue,
            'revenue_change' => $revenueChange,
            'products_sold' => $productsSold,
            'payment_cash' => $paymentMethods['cash'],
            'payment_card' => $paymentMethods['card'],
            'payment_bank' => $paymentMethods['bank'],
        ];
    }

    protected function getPaymentMethodStats($branchId, $start, $end)
    {
        $payments = Payment::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'success')
            ->whereBetween('created_at', [$start, $end])
            ->get(['type_payment', 'value_payment']);

        $cash = 0;
        $card = 0;
        $bank = 0;

        foreach ($payments as $payment) {
            $types = $payment->type_payment ?? [];
            $value = $payment->value_payment ?? 0;

            // Nếu chỉ có 1 phương thức
            if (count($types) == 1) {
                if (in_array('cash', $types)) {
                    $cash += $value;
                } elseif (in_array('card', $types)) {
                    $card += $value;
                } elseif (in_array('bank', $types)) {
                    $bank += $value;
                }
            } else {
                // Nếu có nhiều phương thức, chia đều (cần logic chính xác hơn nếu lưu chi tiết từng phương thức)
                $count = count($types);
                $perMethod = $count > 0 ? $value / $count : 0;
                if (in_array('cash', $types)) $cash += $perMethod;
                if (in_array('card', $types)) $card += $perMethod;
                if (in_array('bank', $types)) $bank += $perMethod;
            }
        }

        return [
            'cash' => $cash,
            'card' => $card,
            'bank' => $bank,
        ];
    }

    protected function getPreviousPeriodRevenue()
    {
        $branchId = user_branch();

        [$currentStart, $currentEnd] = $this->getDateRange();
        $periodLength = $currentStart->diffInDays($currentEnd) + 1;

        $previousStart = $currentStart->copy()->subDays($periodLength);
        $previousEnd = $currentStart->copy()->subDay();

        return Payment::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'success')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('value_payment');
    }

    protected function getChartData()
    {
        $branchId = user_branch();
        $days = 7;

        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d/m');

            $dayRevenue = Payment::query()
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('status', 'success')
                ->whereDate('created_at', $date)
                ->sum('value_payment');

            $data[] = (int) $dayRevenue;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    protected function getTopProducts()
    {
        [$start, $end] = $this->getDateRange();
        $branchId = user_branch();

        return PaymentProduct::query()
            ->select('product_id', DB::raw('SUM(amount) as total_sold'), DB::raw('SUM(total) as total_revenue'))
            ->whereHas('payment', function ($q) use ($branchId, $start, $end) {
                $q->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                  ->where('status', 'success')
                  ->whereBetween('created_at', [$start, $end]);
            })
            ->with('product:id,name,code')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
    }

    protected function getRecentInvoices()
    {
        $branchId = user_branch();

        return Payment::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with('customer:id,name,phone')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }
}
