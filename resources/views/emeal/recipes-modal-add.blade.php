<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addProductModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form method="POST">
                @csrf
                <input type="hidden" name="recipe" value="{{ $recipe->id }}"> <!-- Ukryte pole z ID przepisu -->
                <div class="form-group">
                    <label for="product">Produkt:</label>
                    <!-- Pole wyboru produktu z Select2 -->
                    <select class="form-control select2" name="product" style="width: 100%;">
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity">Ilość:</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Dodaj Produkt do Przepisu</button>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
            <button type="submit" class="btn btn-primary">Dodaj Produkt do Przepisu</button>
        </div>
      </div>
    </div>
  </div>