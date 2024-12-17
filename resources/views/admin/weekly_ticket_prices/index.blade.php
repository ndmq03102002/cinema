@extends('admin.dashboard.layout')
@section('content')
<table class="table table-striped table-bordered" id="table-movie">
    <thead>
    <tr>
       
        <th class="text-center">Name</th>
        <th class="text-center">Description</th>
        <th class="text-center">Start_time</th>
        <th class="text-center">Extra_fee</th>
        <th class="text-center">Manager</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($weeklyticketprices) && $weeklyticketprices->isNotEmpty())
        @foreach($weeklyticketprices as $weeklyticket)
    <tr>
       
        <td>
            {{ $weeklyticket->name }}
        </td>
        <td>
            {{ $weeklyticket->description }}
        </td>
        <td>
            {{ $weeklyticket->start_time }}
        </td>
        
        <td>
            {{ $weeklyticket->extra_fee }}
        </td>
        
        <td class="text-center">
            <a href="{{ route('weeklyticketprice.edit', $weeklyticket->id) }}" class="btn btn-success" ><i class="fa fa-edit"></i></a>
            <form action="{{ route('weeklyticketprice.destroy', $weeklyticket->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
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