@if($row->photos->first())
    <img src="{{ asset($row->photos->first()->path) }}" class="w-16 h-16 object-cover rounded-md mx-auto" alt="Фото">
@else
    <span class="text-gray-400">немає фото</span>
@endif
