@extends('admin.dashboard.layout')
@section('content')
<table class="table table-striped table-bordered" id="table-movie">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th class="text-center">Name</th>
        <th class="text-center">Age</th>
        <th class="text-center">PhoneNumber</th>
        <th class="text-center">Avatar</th>
        <th class="text-center">Role</th>
        @if (Auth::user()->role_id == 1)
            <th class="text-center">Chi nhánh</th>
        @endif
        <th class="text-center">Status</th>
        <th class="text-center">Manager</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($accounts) && $accounts->isNotEmpty())
        @foreach($accounts as $acc)
    <tr>
        <td>
            <input type="checkbox" value="{{ $acc->id }}" class="input-checkbox checkBoxItem">
        </td>
        <td>
            {{-- Hiển thị username từ account --}}
            {{ $acc->profile->name ?? 'N/A' }}
        </td>
        <td>
            {{-- Hiển thị age từ profile --}}
            {{ $acc->profile->age ?? 'N/A' }}
        </td>
        <td>
            {{-- Hiển thị phone number từ profile --}}
            {{ $acc->profile->phone_number ?? 'N/A' }}
        </td>
        <td>
            {{-- Hiển thị avatar từ profile (giả sử avatar là đường dẫn ảnh) --}}
            @if($acc->profile && $acc->profile->avatar)
                <img src="{{ asset($acc->profile->avatar) }}" alt="Avatar" width="50">
            @else
                N/A
            @endif
        </td>
        <td>     
            {{ $acc->role->name ?? 'N/A' }}
        </td>
        @if (Auth::user()->role_id == 1)
        <td>
            {{ $acc->cinema ?? 'N/A' }}
        </td>
        @endif
        <td>
            {{ $acc->status == 1 ? 'Hoạt Động' : 'Không hoạt động' }}
        </td>
        <td class="text-center">
            <a href="{{ route('account.restore', $acc->id) }}" class="btn btn-success"><i class="fa-solid fa-backward"></i></a>
            <form action="{{ route('account.forceDelete', $acc->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
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
