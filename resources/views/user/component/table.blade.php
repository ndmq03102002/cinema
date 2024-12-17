<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th class="text-center">Username</th>
        <th class="text-center">Email</th>
        <th class="text-center">Role</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($users) && is_object($users))
        @foreach($users as $user)
    <tr>
        <td>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox checkBoxItem">
        </td>
        <td>
            {{$user->username}}
        </td>
        <td>
            {{$user->email}}
        </td>
        <td>
            {{$user->role}}
        </td>
        <td class="text-center">
            <form action="{{ route('user.updatePublishStatus', $user->id) }}" method="POST" id="publishForm-{{ $user->id }}">
                @csrf
                <input type="hidden" name="publish" value="0">
                <input type="checkbox" name="publish" value="1" class="js-switch" onchange="document.getElementById('publishForm-{{ $user->id }}').submit();" {{ ($user->publish == 1) ? 'checked' : '' }} />
            </form>
        </td>
        <td class="text-center">
            <a href="{{route('user.edit', $user->id)}}" class="btn btn-success"><i class="fa fa-edit"></i></a>
            <a href="{{route('user.delete',$user->id)}}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
        </td>

    </tr>
        @endforeach
    @endif

    </tbody>
</table>

{{$users->links('pagination::bootstrap-4')}}

