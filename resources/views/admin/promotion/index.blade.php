@extends('admin.dashboard.layout')
@section('content')
<table class="table table-striped table-bordered" id="table-movie">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th class="text-center">Name</th>
        <th class="text-center">Avatar</th>
        <th class="text-center">Description</th>
        <th class="text-center">Start_date</th>
        <th class="text-center">End_date</th>
        <th class="text-center">Discount</th>
        <th class="text-center">Quantity</th>
        <th class="text-center">Status</th>
        <th class="text-center">Manager</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($promotions) && $promotions->isNotEmpty())
        @foreach($promotions as $promo)
    <tr>
        <td>
            <input type="checkbox" value="{{ $promo->id }}" class="input-checkbox checkBoxItem">
        </td>
        <td>
            {{ $promo->promo_name }}
        </td>
        <td>
            <img src="{{ $promo->avatar }}" alt="{{ $promo->name }}" width="50" height="50" />
        </td>
        <td>
            {{ $promo->description }}
        </td>
        <td>
            {{ $promo->start_date }}
        </td>
        <td>
            {{ $promo->end_date }}
        </td>
        <td>
            {{ $promo->discount }}
        </td>
        <td>
            {{ $promo->quantity }}
        </td>
        <td>
            <select class="form-control select-status" data-promotion-id="{{ $promo->id }}">
                <option value="1" {{ $promo->status == 1 ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ $promo->status == 0 ? 'selected' : '' }}>Không hiển thị</option>
            </select>
        </td>
        
        <td class="text-center">
            <a href="{{ route('promotion.edit', $promo->id) }}" class="btn btn-success" ><i class="fa fa-edit"></i></a>
            <form action="{{ route('promotion.destroy', $promo->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
            
        </td>
    </tr>
        @endforeach
    @endif
    </tbody>
</table>
@endsection
<script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>

<script>
    $(document).ready(function() {
    // Sử dụng event delegation để lắng nghe sự thay đổi của danh mục
    $('#table-movie').on('change', '.select-status', function() {
        var promoId = $(this).data('promotion-id');
        var status = $(this).val();

        $.ajax({
            url: '{{ route('promotion.updateajax', '') }}/' + promoId,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                alert('Trạng thái đã được cập nhật thành công');
            },
            error: function(xhr) {
                alert('Đã xảy ra lỗi khi cập nhật danh mục');
            }
        });
    });

});

</script>