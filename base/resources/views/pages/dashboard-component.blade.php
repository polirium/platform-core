<div>
    {{-- Period Filter --}}
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ trans('Dashboard') }}
                    </h2>
                </div>
                <div class="col-auto">
                    <div class="btn-group">
                        <button type="button" wire:click="setPeriod('today')"
                            class="btn {{ $period === 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ trans('Hôm nay') }}
                        </button>
                        <button type="button" wire:click="setPeriod('week')"
                            class="btn {{ $period === 'week' ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ trans('Tuần này') }}
                        </button>
                        <button type="button" wire:click="setPeriod('month')"
                            class="btn {{ $period === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ trans('Tháng này') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            {{-- Stats Cards --}}
            <div class="row row-deck row-cards mb-4">
                {{-- Invoice Count --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ trans('Số hóa đơn') }}</div>
                            </div>
                            <div class="h1 mb-3">{{ number_format($stats['invoice_count']) }}</div>
                            <div class="d-flex mb-2">
                                <div>
                                    <span class="text-muted">{{ trans('Đã thanh toán thành công') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Revenue --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ trans('Doanh thu') }}</div>
                            </div>
                            <div class="h1 mb-3">{{ core_number_format($stats['revenue']) }}đ</div>
                            <div class="d-flex mb-2">
                                <div>
                                    @if($stats['revenue_change'] > 0)
                                        <span class="text-green d-inline-flex align-items-center lh-1">
                                            {{ $stats['revenue_change'] }}%
                                            {!! tabler_icon('trending-up') !!}
                                        </span>
                                    @elseif($stats['revenue_change'] < 0)
                                        <span class="text-red d-inline-flex align-items-center lh-1">
                                            {{ $stats['revenue_change'] }}%
                                            {!! tabler_icon('trending-down') !!}
                                        </span>
                                    @else
                                        <span class="text-muted">{{ trans('So với kỳ trước') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Sold --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ trans('Sản phẩm bán') }}</div>
                            </div>
                            <div class="h1 mb-3">{{ number_format($stats['products_sold']) }}</div>
                            <div class="d-flex mb-2">
                                <div>
                                    <span class="text-muted">{{ trans('Tổng số lượng') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Methods Stats --}}
            <div class="row row-deck row-cards mb-4">
                {{-- Cash --}}
                <div class="col-sm-6 col-lg-4">
                    <div class="card bg-green-lt">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <span class="avatar bg-green text-white me-3">
                                    {!! tabler_icon('cash') !!}
                                </span>
                                <div>
                                    <div class="subheader">{{ trans('Tiền mặt') }}</div>
                                    <div class="h2 mb-0">{{ core_number_format($stats['payment_cash']) }}đ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card --}}
                <div class="col-sm-6 col-lg-4">
                    <div class="card bg-blue-lt">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <span class="avatar bg-blue text-white me-3">
                                    {!! tabler_icon('credit-card') !!}
                                </span>
                                <div>
                                    <div class="subheader">{{ trans('Thẻ') }}</div>
                                    <div class="h2 mb-0">{{ core_number_format($stats['payment_card']) }}đ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bank Transfer --}}
                <div class="col-sm-6 col-lg-4">
                    <div class="card bg-purple-lt">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <span class="avatar bg-purple text-white me-3">
                                    {!! tabler_icon('building-bank') !!}
                                </span>
                                <div>
                                    <div class="subheader">{{ trans('Chuyển khoản') }}</div>
                                    <div class="h2 mb-0">{{ core_number_format($stats['payment_bank']) }}đ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-deck row-cards">
                {{-- Revenue Chart --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('Doanh thu 7 ngày gần đây') }}</h3>
                        </div>
                        <div class="card-body">
                            <div id="revenue-chart" wire:ignore></div>
                        </div>
                    </div>
                </div>

                {{-- Top Products --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('Top sản phẩm bán chạy') }}</h3>
                        </div>
                        <div class="card-body card-body-scrollable card-body-scrollable-shadow" style="max-height: 300px;">
                            <div class="divide-y">
                                @forelse($topProducts as $item)
                                    <div class="row py-2">
                                        <div class="col-auto">
                                            <span class="avatar bg-primary-lt">{{ $loop->iteration }}</span>
                                        </div>
                                        <div class="col">
                                            <div class="text-truncate">
                                                <strong>{{ $item->product?->name ?? 'N/A' }}</strong>
                                            </div>
                                            <div class="text-muted">
                                                {{ number_format($item->total_sold) }} {{ trans('đã bán') }}
                                            </div>
                                        </div>
                                        <div class="col-auto text-end">
                                            <div class="text-success">{{ core_number_format($item->total_revenue) }}đ</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-4">
                                        {{ trans('Chưa có dữ liệu') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Invoices --}}
            <div class="row row-deck row-cards mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ trans('Hóa đơn gần đây') }}</h3>
                            <div class="card-actions">
                                <a href="{{ route('accountings.payment.index') }}" class="btn btn-link">
                                    {{ trans('Xem tất cả') }}
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>{{ trans('Mã hóa đơn') }}</th>
                                        <th>{{ trans('Khách hàng') }}</th>
                                        <th>{{ trans('Tổng tiền') }}</th>
                                        <th>{{ trans('Thanh toán') }}</th>
                                        <th>{{ trans('Trạng thái') }}</th>
                                        <th>{{ trans('Thời gian') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentInvoices as $invoice)
                                        <tr>
                                            <td><strong>{{ $invoice->code }}</strong></td>
                                            <td>{{ $invoice->customer?->name ?? 'Khách lẻ' }}</td>
                                            <td>{{ core_number_format($invoice->total_cost) }}đ</td>
                                            <td>{{ core_number_format($invoice->value_payment) }}đ</td>
                                            <td>
                                                @if($invoice->status === 'success')
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                @elseif($invoice->status === 'pending')
                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $invoice->status }}</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">{{ $invoice->created_at?->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                {{ trans('Chưa có hóa đơn nào') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    document.addEventListener('livewire:initialized', function() {
        const chartData = @json($chartData);

        if (typeof ApexCharts !== 'undefined') {
            var options = {
                series: [{
                    name: 'Doanh thu',
                    data: chartData.data
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.1,
                    }
                },
                xaxis: {
                    categories: chartData.labels,
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            if (val >= 1000000) {
                                return (val / 1000000).toFixed(1) + 'M';
                            } else if (val >= 1000) {
                                return (val / 1000).toFixed(0) + 'K';
                            }
                            return val;
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
                        }
                    }
                },
                colors: ['#206bc4']
            };

            var chart = new ApexCharts(document.querySelector("#revenue-chart"), options);
            chart.render();
        }
    });
</script>
@endscript
