<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::select([
                'id',
                'created_at',
                'buyer_name',
                'grand_total',
                'payment_status',
                'status'
            ])->orderByDesc('id');

            return DataTables::of($query)
                ->editColumn('created_at', fn($r) => $r->created_at->format('d/m/Y H:i'))
                ->editColumn('grand_total', fn($r) => number_format($r->grand_total, 0, ',', '.'))
                ->addColumn('payment_status', function ($r) {
                    $map = [
                        'unpaid' => '<span class="badge bg-warning">No pagado</span>',
                        'paid'   => '<span class="badge bg-success">Pagado</span>',
                        'refunded' => '<span class="badge bg-secondary">Reembolsado</span>',
                    ];
                    return $map[$r->payment_status] ?? '<span class="badge bg-light text-dark">Desconocido</span>';
                })
                ->addColumn('status', function ($r) {
                    $map = [
                        'pending_payment' => '<span class="badge bg-warning">Pendiente de pago</span>',
                        'processing'      => '<span class="badge bg-info">Procesando</span>',
                        'completed'       => '<span class="badge bg-success">Completada</span>',
                        'cancelled'       => '<span class="badge bg-danger">Cancelada</span>',
                    ];
                    return $map[$r->status] ?? '<span class="badge bg-light text-dark">Sin estado</span>';
                })
                ->addColumn('action', function ($r) {
                    return '
                        <button class="btn btn-sm bg-gradient-dark btn-detail" data-id="'.$r->id.'">
                            <i class="fa fa-eye"></i> Detalle
                        </button>
                        <button class="btn btn-sm bg-gradient-dark btn-status" data-id="'.$r->id.'">
                            <i class="fa fa-refresh"></i> Cambiar Estado
                        </button>
                    ';
                })
                ->rawColumns(['payment_status','status','action'])
                ->make(true);
        }

        return view('sales.index');
    }

    public function show($id)
    {
        $order = Order::with([
            'items.product.photos',
            'items.files',
            'addresses.country',
            'addresses.region',
            'addresses.commune',
            'documents',
            'logs.user'
        ])->findOrFail($id);

        // Productos
        $items = $order->items->map(function ($i) {
            //dd($i);
            $options = [];
            if (is_array($i->options_display)) {
                foreach ($i->options_display as $opt) {
                    $options[] = "{$opt['group']}: {$opt['value']}";
                }
            }

            return [
                'id'            => $i->id,
                'product_name'  => $i->product_name,
                'qty_real'      => $i->qty_display,
                'unit_price'    => number_format($i->unit_price_gross, 0, ',', '.'),
                'line_total'    => number_format($i->line_total_gross, 0, ',', '.'),
                'tax_total'     => number_format($i->line_tax_total, 0, ',', '.'),
                'options'       => $i->options_display ?? [],
                'thumb'         => $i->product_thumb,
                'attachments' => $i->files->map(fn($f)=>[
                    'url'  => Storage::disk('public_uploads')->url($f->path),
                    'name' => $f->original_name,
                    'mime' => $f->mime
                ]),

            ];
        });

        // Dirección principal
        $address = $order->addresses->where('type', 'shipping')->first();

        // Documentos tributarios y adjuntos
        $docs = $order->documents->map(function ($d) {
            return [
                'type'  => ucfirst($d->type),
                'status'=> ucfirst($d->status),
                'pdf'   => $d->pdf_path ? Storage::disk('public_uploads')->url($d->pdf_path) : null,
                'issued_at' => optional($d->issued_at)->format('d/m/Y H:i'),
            ];
        });

        // Logs
        $logs = $order->logs->map(fn($l) => [
            'from'   => $l->from_status,
            'to'     => $l->to_status,
            'user'   => optional($l->user)->name,
            'msg'    => $l->message,
            'date'   => $l->created_at->format('d/m/Y H:i'),
        ]);

        return response()->json([
            'order' => [
                'id'             => $order->id,
                'created_at'     => $order->created_at->format('d/m/Y H:i'),
                'buyer_name'     => $order->buyer_name,
                'buyer_email'    => $order->buyer_email,
                'buyer_phone'    => $order->buyer_phone,
                'notes'          => $order->notes,
                'doc_type'       => $order->doc_type,
                'currency'       => $order->currency,
                'grand_total'    => number_format($order->grand_total, 0, ',', '.'),
                'subtotal_net'   => number_format($order->subtotal_net, 0, ',', '.'),
                'tax_total'      => number_format($order->tax_total, 0, ',', '.'),
                'payment_status' => $order->payment_status,
                'status'         => $order->status,
                'meta'           => $order->meta_json,
            ],
            'items'     => $items,
            'address'   => $address,
            'documents' => $docs,
            'logs'      => $logs,
        ]);
    }

    public function getStatus($id)
    {
        $o = Order::select('id','status','payment_status')->findOrFail($id);
        return response()->json([
            'status'         => $o->status,
            'payment_status' => $o->payment_status,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'           => 'nullable|in:pending_payment,processing,completed,cancelled',
            'payment_status'   => 'nullable|in:unpaid,pending,paid,refunded',
            'old_status'       => 'nullable|string',
            'old_payment_status'=> 'nullable|string',
        ]);

        $order = Order::findOrFail($id);

        $oldStatus        = $request->input('old_status', $order->status);
        $oldPaymentStatus = $request->input('old_payment_status', $order->payment_status);
        $newStatus        = $request->input('status');
        $newPaymentStatus = $request->input('payment_status');

        $changes = [];

        if ($newStatus && $newStatus !== $oldStatus) {
            $order->status = $newStatus;
            $changes[] = 'Estado: '.$this->labelStatus($oldStatus).' → '.$this->labelStatus($newStatus);
        }

        if ($newPaymentStatus && $newPaymentStatus !== $oldPaymentStatus) {
            $order->payment_status = $newPaymentStatus;
            $changes[] = 'Pago: '.$this->labelPayment($oldPaymentStatus).' → '.$this->labelPayment($newPaymentStatus);
        }

        if (empty($changes)) {
            return response()->json(['status'=>400,'message'=>'No hubo cambios.']);
        }

        $order->save();

        // Log de auditoría (si tu relación existe)
        $order->logs()->create([
            'from_status' => $oldStatus,
            'to_status'   => $order->status,
            'message'     => implode(' | ', $changes),
            'created_by'  => auth()->id(),
        ]);

        return response()->json(['status'=>200, 'message'=>'Estados actualizados']);
    }

    /** Opcional: helpers para mostrar nombres bonitos en el log */
    private function labelStatus(?string $s): string
    {
        return [
            'pending_payment' => 'Pendiente de Pago',
            'processing'      => 'Procesando',
            'completed'       => 'Completada',
            'cancelled'       => 'Cancelada',
        ][$s] ?? ($s ?: '-');
    }

    private function labelPayment(?string $s): string
    {
        return [
            'unpaid'   => 'No pagado',
            'pending'  => 'Pendiente de Confirmación',
            'paid'     => 'Pagado',
            'refunded' => 'Reembolsado',
        ][$s] ?? ($s ?: '-');
    }



}
