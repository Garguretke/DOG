<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addProductModalLabel">Modal title</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('emeal.recipes-addProduct', ['recipe' => $recipe->id]) }}" id="addProduct-Recipe-eMeal">
				@csrf
				<input type="hidden" name="recipe_id" value="{{ $recipe->id }}">

				<div class="form-group mb-3">
					<label for="product_id">Produkt:</label>
					<select class="form-control select2" name="product_id" style="width: 100%;" required>
						@foreach($products as $product)
						<option value="{{ $product->id }}">{{ $product->name }}</option>
						@endforeach
					</select>
				</div>

					<div class="form-group">
						<label for="quantity">Ilość:</label>
						<input type="number" min="1" step=".25" placeholder="0,00" name="quantity" class="form-control" required>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<input type="submit" form="addProduct-Recipe-eMeal" class="btn btn-primary" value="Dodaj produkt">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
			</div>
		</div>
	</div>
</div>