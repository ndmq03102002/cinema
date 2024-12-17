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
            <th class="text-center">Avatar</th>
            <th class="text-center">Address</th>
            <th class="text-center">Manager</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($cinemas) && $cinemas->isNotEmpty())
            @foreach($cinemas as $cine)
        <tr>
            <td>
                <input type="checkbox" value="{{ $cine->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                {{ $cine->name }}
            </td>
            <td>
                <img src="{{ $cine->avatar }}" alt="{{ $cine->name }}" width="50" height="50" />
            </td>
            <td>
                {{ $cine->address }}
            </td>
       
            
            <td class="text-center">
                <a href="{{ route('cinema.edit', $cine->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                <form action="{{ route('cinema.delete', $cine->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
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
    {{-- {{$movies->links('pagination::bootstrap-4')}} --}}
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.3.1.min.js" ></script>

    



