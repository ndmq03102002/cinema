@extends('admin.dashboard.layout')
@section('content')

<div class="table-responsive">
    <table class="table table-striped table-bordered" id="table-movie">
        <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Name</th>
            <th class="text-center">Description</th>
            <th class="text-center">Slug</th>
            <th class="text-center">Genre</th>
            <th class="text-center">Country</th>
            <th class="text-center">Ngày khởi chiếu</th>
            <th class="text-center">Avatar</th>
            <th class="text-center">Duration</th>
            <th class="text-center">Performer</th>
            <th class="text-center">Director</th>
            <th class="text-center">Status</th>
            <th class="text-center">Manager</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($movies) && !empty($movies))
            @foreach($movies as $mov)
        <tr>
            <td>
                <input type="checkbox" value="{{ $mov->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                {{ $mov->name }}
            </td>
            <td>
                {{ $mov->description }}
            </td>
            <td>
                {{ $mov->slug }}
            </td>

            <td>
                @foreach($mov->movie_genre as $genre)
                    {{ $genre->name }}
                    @if (!$loop->last), @endif  
                @endforeach
            </td>
            <td>
                {{ $mov->country->name }}
            </td>
            <td>
                {{ $mov->date }}
            </td>
            
            <td>
                <img src="{{ $mov->avatar }}" alt="{{ $mov->name }}" width="50" height="50" />
            </td>
            <td>
                {{ $mov->duration }}
            </td>
            <td>
                {{ $mov->performer }}
            </td>
            <td>
                {{ $mov->director }}
            </td>


            <td>
                <select class="form-control select-status" data-movie-id="{{ $mov->id }}">
                    <option value="1" {{ $mov->status == 1 ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ $mov->status == 0 ? 'selected' : '' }}>Không hiển thị</option>
                </select>
            </td>
            
            <td class="text-center">
                <a href="{{ route('movie.edit', $mov->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                <form action="{{ route('movie.destroy', $mov->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
                <br>
                
            </td>
            
        </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    {{-- {{$movies->links('pagination::bootstrap-4')}} --}}
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>

<script>
    $(document).ready(function() {
    // Lắng nghe sự thay đổi của trạng thái
    $('#table-movie').on('change', '.select-status', function() {
        var movieId = $(this).data('movie-id');
        var status = $(this).val();

        $.ajax({
            url: '{{ route('movie.updateajax', '') }}/' + movieId,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                alert('Trạng thái đã được cập nhật thành công');
            },
            error: function(xhr) {
                alert('Đã xảy ra lỗi khi cập nhật trạng thái');
            }
        });
    });

});

</script>


    



