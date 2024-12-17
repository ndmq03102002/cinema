@extends('admin.dashboard.layout')
@section('content')
<table class="table table-striped table-bordered" id="table-movie">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th class="text-center">Movie</th>
        @if(Auth::user()->role_id == 1)
            <th class="text-center">Cinema</th>
        @endif
        <th class="text-center">Room</th>
        <th class="text-center">Start_time</th>
        <th class="text-center">End_time</th>
        <th class="text-center">Start_date</th>
        <th class="text-center">Manager</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($movieshowtimes) && $movieshowtimes->isNotEmpty())
            @foreach($movieshowtimes as $movieshow)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $movieshow->id }}" class="input-checkbox checkBoxItem">
                    </td>
                    <td>
                        {{ $movieshow->movie->name }}
                    </td>
                    @if(Auth::user()->role_id == 1)
                        <td>
                            {{ $movieshow->room->cinema->name }}
                        </td>
                    @endif
                    <td>
                        {{ $movieshow->room->name }}
                    </td>
                    <td>
                        {{ date('H:i', strtotime($movieshow->start_time)) }}
                    </td>
                    <td>
                        {{ date('H:i', strtotime($movieshow->end_time)) }}
                    </td>
                    <td>
                        {{ $movieshow->start_date }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('movieshowtime.edit', $movieshow->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <form action="{{ route('movieshowtime.destroy', $movieshow->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
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