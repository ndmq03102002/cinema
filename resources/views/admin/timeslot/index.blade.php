@extends('admin.dashboard.layout')
@section('content')
<table class="table table-striped table-bordered" id="table-movie">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th class="text-center">Slot_name</th>
        <th class="text-center">Start_time</th>
        <th class="text-center">End_time</th>
        <th class="text-center">Extra_fee</th>
        <th class="text-center">Manager</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($timeslots) && $timeslots->isNotEmpty())
        @foreach($timeslots as $timeslot)
    <tr>
        <td>
            <input type="checkbox" value="{{ $timeslot->id }}" class="input-checkbox checkBoxItem">
        </td>
        <td>
            {{ $timeslot->slot_name }}
        </td>
        <td>
            {{ $timeslot->start_time }}
        </td>
        <td> 
            {{ $timeslot->end_time }}
        </td>
        <td>
            {{ $timeslot->extra_fee }}
        </td>
        
        <td class="text-center">
            <a href="{{ route('timeslot.edit', $timeslot->id) }}" class="btn btn-success" ><i class="fa fa-edit"></i></a>
            <form action="{{ route('timeslot.destroy', $timeslot->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
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