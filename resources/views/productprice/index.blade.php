@extends('adminlte::page')

@section('title','Precios de Productos')
@section('content_header') &nbsp; @endsection

@section('css')
<style>
  .price-input {
    width: 100%;
    text-align: right;
    border: none;
    background: transparent;
    border-bottom: 1px dashed #aaa;
    padding: 2px 6px;
  }
  .price-input:focus {
    outline: none;
    background: #ffffcc;
  }
</style>
@endsection

@section('content')
<div class="row justify-content-md-center">
  <div class="col-12">
    <div class="card card-secondary">
      <div class="card-header sidebar-dark-primary d-flex justify-content-between align-items-center">
        <h2 class="card-title">Mantenedor de Precios</h2>
      </div>
      <div class="card-body">
        <div class="form-group mb-4">
          <label>Seleccione producto</label>
          <select id="product_id" class="form-control select2" style="width:100%"></select>
        </div>

        <table id="priceTable" class="table table-bordered table-striped table-hover w-100">
          <thead><tr id="thead-dynamic"></tr></thead>
          <tbody></tbody>
        </table>

        <div id="no-data" class="alert alert-secondary text-center d-none mt-3">
          No hay combinaciones disponibles para este producto.
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
$(function() {
  const token = $('meta[name="csrf-token"]').attr('content');
  $.ajaxSetup({ headers: {'X-CSRF-TOKEN': token} });

  const $select = $('#product_id');
  let combos = [];
  let table = null;

  // =========================
  // 1. Inicializar Select2
  // =========================
  $select.select2({
    placeholder: "Seleccione un producto...",
    width: '100%',
    ajax: {
      url: '/product',
      data: params => ({ search: params.term }),
      processResults: data => ({ results: data.data })
    },
    templateResult: d => d.loading ? d.text : $('<div>' + d.name + '</div>'),
    templateSelection: d => d.name || d.text
  });

  // =========================
  // 2. Al seleccionar producto
  // =========================
  $select.on('change', function() {
    const productId = $(this).val();
    if (!productId) return;

    Swal.fire({
      title: 'Cargando combinaciones...',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    $.getJSON(`/product-prices/load/${productId}`, function(res) {
      Swal.close();

      if (res.status === 204 || !res.data.length) {
        $('#priceTable').addClass('d-none');
        $('#no-data').removeClass('d-none');
        if (table) table.clear().draw();
        return;
      }

      $('#no-data').addClass('d-none');
      $('#priceTable').removeClass('d-none');
      combos = res.data;
      renderDataTable(combos, res.labels); 
    }).fail(() => Swal.fire('Error', 'No se pudieron cargar las combinaciones.', 'error'));
  });

  // =========================
  // 3. Renderizar DataTable dinámicamente
  // =========================
    function renderDataTable(rows, labels = {}) {
        if (table) {
            table.destroy();
            $('#priceTable tbody').empty();
            $('#thead-dynamic').empty();
        }

        if (!rows.length) return;

        const first = rows[0];
        const dynamicCols = Object.keys(first).filter(k => k.endsWith('_name'));
        const header = $('#thead-dynamic');
        
        dynamicCols.forEach(k => {
            const code = k.replace('_name', '');
            const label = labels[code] || code.toUpperCase();
            header.append(`<th>${label}</th>`);
        });
        header.append('<th>Precio</th>');

        const tbody = $('#priceTable tbody');
        rows.forEach((r, i) => {
            let tr = '<tr>';
            dynamicCols.forEach(k => tr += `<td>${r[k]}</td>`);
            tr += `<td><input type="text" class="price-input" data-index="${i}" value="${formatPrice(r.price)}"></td>`;
            tr += '</tr>';
            tbody.append(tr);
        });

        table = $('#priceTable').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            scrollY: '400px',
            language: { url: "{{ asset('plugins/table/datatable/es-ES.json') }}" }
        });
    }


  // =========================
  // 4. Guardar precio al editar
  // =========================
    $(document).on('keydown blur', '.price-input', function(e) {
        if (e.type === 'keydown' && e.key !== 'Enter') return;
        e.preventDefault();

        const index = $(this).data('index');
        // limpiar formato, convertir a entero
        let val = $(this).val().replace(/\./g,'');
        val = parseInt(val) || 0;

        combos[index].price = val;

        $.ajax({
            url: '/product-prices',
            type: 'POST',
            data: { rows: [ combos[index] ] },
            success: function(r) {
            if (r.status === 200) {
                $(e.target).val(formatPrice(val));
                Swal.fire({
                toast: true, icon: 'success', title: 'Precio guardado',
                position: 'bottom-end', showConfirmButton: false, timer: 1200
                });
            } else {
                Swal.fire('Error', r.message || 'No se pudo guardar', 'error');
            }
            },
            error: () => Swal.fire('Error', 'No se pudo guardar el precio', 'error')
        });
    });


  // =========================
  // 5. Helper para formatear números
  // =========================
  function formatPrice(num) {
    num = parseInt(num || 0);
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

});
</script>
@endsection
