{{-- Modal de Configuración de Producto (visual) --}}
<div class="modal fade" id="productConfigModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title d-flex align-items-center gap-2">
          <i class="bi bi-sliders2-vertical"></i>
          <span id="pc_title">Producto</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-4 align-items-start">
          {{-- Galería / fotos --}}
          <div class="col-12 col-lg-6">
            <div id="pc_carousel" class="carousel slide rounded-4 overflow-hidden border" data-bs-ride="false">
              <div class="carousel-inner">
                {{-- se rellenará por JS --}}
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#pc_carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#pc_carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            </div>
            <div class="small text-secondary mt-2" id="pc_subtitle"></div>
          </div>

          {{-- Configuración --}}
        <div class="col-12 col-lg-6">
          <div class="vstack gap-3" id="pc_dynamic"><!-- JS insertará grupos aquí --></div>

          <div class="vstack gap-3 mt-3">
            {{-- Cantidad --}}
            <div id="div_quantity" class="d-flex align-items-center gap-3">
              <div class="fw-semibold" style="min-width: 140px">Cantidad*</div>
              <div class="flex-grow-1">
                <input type="number" id="pc_qty" class="form-control" min="1" step="1" value="1">
              </div>
            </div>

            {{-- Subir archivo --}}
            <div class="d-flex align-items-center gap-3">
              <div class="fw-semibold" style="min-width: 140px">Subir Archivo</div>
              <div class="flex-grow-1">
                <div class="d-flex gap-2 align-items-center">
                  <label class="btn btn-outline-light mb-0" for="pc_file">Subir Archivo...</label>
                  <input type="file" id="pc_file" class="d-none" />
                  <div id="pc_filename" class="text-secondary small">No se eligió ningún archivo</div>
                </div>
              </div>
            </div>

            {{-- Comentarios --}}
            <div class="d-flex align-items-center gap-3">
              <div class="fw-semibold" style="min-width: 140px">Comentarios</div>
              <div class="flex-grow-1">
                <input type="text" id="pc_notes" class="form-control" placeholder="Instrucciones para el trabajo…">
              </div>
            </div>

            {{-- Total + CTA --}}
            <div class="d-flex align-items-center gap-3 mt-2">
              <div class="fw-semibold" style="min-width: 140px">Total (iva inc.)</div>
              <div class="flex-grow-1 d-flex align-items-center gap-3">
                <div class="input-group" style="max-width: 220px;">
                  <span class="input-group-text">$</span>
                  <input type="text" id="pc_total" class="form-control" value="0.000" readonly>
                </div>
                <button type="button" class="btn btn-primary" id="pc_add" disabled>
                  <i class="bi bi-bag-plus me-1"></i> Agregar al Carrito
                </button>
              </div>
            </div>

          </div>
        </div>

        </div> {{-- row --}}
      </div>

      <div class="modal-footer border-0">
        <button class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
